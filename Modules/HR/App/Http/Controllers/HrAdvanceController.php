<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrAdvanceRequest;
use Modules\HR\App\Http\Requests\UpdateHrAdvanceRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrAdvanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Keep DB facade for now, might be removed later if all transactions are in repo
use Modules\HR\App\Repositories\HrMonthlyPaymentRepository; // Keep HrMonthlyPaymentRepository for now, might be removed later if all monthly payment logic is in repo
use App\Services\Firebase\FirebaseNotificationService;
class HrAdvanceController extends AppBaseController
{
    /** @var HrAdvanceRepository $hrAdvanceRepository*/
    private $hrAdvanceRepository;

    private $HrMonthlyPaymentRepository; // This might not be needed in the controller anymore

    public function __construct(HrAdvanceRepository $hrAdvanceRepo, HrMonthlyPaymentRepository $HrMonthlyPaymentRepository)
    {
        $this->hrAdvanceRepository = $hrAdvanceRepo;
        $this->HrMonthlyPaymentRepository = $HrMonthlyPaymentRepository; // This might not be needed in the controller anymore
    }

    /**
     * Display a listing of the HrAdvance.
     */
    public function index(Request $request)
    {
        $data['advances'] = $this->hrAdvanceRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['employees'] = $this->hrAdvanceRepository->employees();

        return view('hr::advances.index', $data);
    }

    /**
     * Show the form for creating a new HrAdvance.
     */
    public function create()
    {
        $data['employees'] = $this->hrAdvanceRepository->employees();
        $data['statuses'] = $this->hrAdvanceRepository->statuses();
        return view('hr::advances.create', $data);
    }

    /**
     * Store a newly created HrAdvance in storage.
     */
    public function store(CreateHrAdvanceRequest $request)
    {
        try {
            //dd($request->get('monthly_payments'));
            $input = $request->all();
            $monthlyPaymentsData = $request->has('monthly_payments') ? $request->input('monthly_payments') : null;

            // dd(  $input);
            $advance = $this->hrAdvanceRepository->createAdvanceWithMonthlyPayments($input, $monthlyPaymentsData);

            flash()->success(__('messages.saved', ['model' => __('hr:models/hr_advances.singular')]));

            if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'employeeDashboard')) {
                return redirect()->back();
            }

            if ($request->type_page == 'emp') {
                return redirect()->route('hr.empdashboard.index');
            } else {
                return redirect()->route('hr.advances.index');
            }
        } catch (\Exception $e) {
            flash()->error(__('hr::models/hr_advances.error_creating_advance') . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update the specified HR Advance in storage.
     *
     * @param int $id
     * @param UpdateHrAdvanceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateHrAdvanceRequest $request, $id)
    {
        try {
            $input = $request->all();
            $monthlyPaymentsData = $request->has('monthly_payments') ? $request->input('monthly_payments') : null;

            $advance = $this->hrAdvanceRepository->updateAdvanceWithMonthlyPayments($input, $id, $monthlyPaymentsData);

            flash()->success(__('messages.updated', ['model' => __('hr:models/hr_advances.singular')]));

            return redirect()->route('hr.advances.index');
        } catch (\Exception $e) {
            flash()->error(__('hr::models/hr_advances.error_updating_advance') . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update a specific monthly payment for an advance (e.g., delay or mark as repaid).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


    public function updateMonthlyPayment(Request $request)
{
    $hrMonthlyPayment = $this->HrMonthlyPaymentRepository->find($request->payment_id);

    if (empty($hrMonthlyPayment)) {
        flash()->error(__('hr::models/hr_monthly_payments.singular') . ' ' . __('messages.not_found'));
        return redirect(route('hr.advances.index'));
    }

    DB::beginTransaction();
     try {
        // تحديث تاريخ الاستحقاق فقط إذا تم تمريره
        if ($request->filled('new_due_date')) {
            $hrMonthlyPayment->due_at = $request->new_due_date . '-15';
        }

        if ($request->type_class == 'Repaid') {
            $isNewlyRepaid = ($hrMonthlyPayment->type != \Modules\HR\App\Models\HrMonthlyPayment::TYPE_REPAID);
            
            $hrMonthlyPayment->type = \Modules\HR\App\Models\HrMonthlyPayment::TYPE_REPAID;

            // الـ mutator سيعمل تلقائياً هنا
            if ($request->hasFile('attachment')) {
                $hrMonthlyPayment->attachment = $request->file('attachment');
            }

            if ($isNewlyRepaid) {
                // Accounting Integration for Direct Repayment
                \Modules\HR\App\Services\HrJournalEntryService::createEntry(
                    (float) $hrMonthlyPayment->amount,
                    'hr_default_cash_bank_account', // Debit
                    'hr_advance_receivable_account', // Credit
                    'سداد مباشر لقسط سلفة الموظف: ' . ($hrMonthlyPayment->employee->username ?? 'Unknown'),
                    get_class($hrMonthlyPayment),
                    $hrMonthlyPayment->id,
                    $hrMonthlyPayment->employee_id
                );
            }
        }

        $hrMonthlyPayment->save();

        DB::commit();
        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_monthly_payments.singular')]));
    } catch (\Exception $e) {
        DB::rollBack();
        flash()->error(__('hr::models/hr_advances.error_updating_advance') . ': ' . $e->getMessage());
    }

    return redirect()->back();
}

    /**
     * حذف السلفة
     */
    public function destroy($id)
    {
        try {
            $this->hrAdvanceRepository->deleteAdvanceWithMonthlyPayments($id);

            flash()->success(__('messages.deleted', ['model' => __('hr:models/hr_advances.singular')]));

            return redirect()->route('hr.advances.index');
        } catch (\Exception $e) {
            flash()->error(__('hr::lang.error_deleting_advance') . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified HrAdvance.
     */
    public function show($id)
    {
        try {
            // Assuming employeeId can be derived from the authenticated user or passed as a parameter
            // For now, let's assume it's available from the advance itself or a default.
            // You might need to adjust how $employeeId is obtained based on your application's logic.
            $advance = $this->hrAdvanceRepository->find($id);
            if (empty($advance)) {
                flash()->error(__('hr:models/hr_advances.singular') . ' ' . __('messages.not_found'));
                return redirect(route('hr.advances.index'));
            }
            $employeeId = $advance->employee_id; // Or get from auth()->user()->employee->id;

            $data = $this->hrAdvanceRepository->getAdvanceDetails($id, $employeeId);

            return view('hr::advances.show', $data);
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect(route('hr.advances.index'));
        }
    }

    /**
     * Show the form for editing the specified HrAdvance.
     */
    public function edit($id)
    {
        $data['advance'] = $this->hrAdvanceRepository->find($id);
        if (empty($data['advance'])) {
            flash()->error(__('hr:models/hr_advances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.advances.index'));
        }
        $data['employees'] = $this->hrAdvanceRepository->employees();
        $data['statuses'] = $this->hrAdvanceRepository->statuses();

        return view('hr::advances.edit', $data);
    }

    // Approve
    public function approve($id)
    {
        $advance = $this->hrAdvanceRepository->find($id);
       
        if (empty($advance)) {
            flash()->error(__('hr:models/hr_advances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.advances.index'));
        }

        $advance = $this->hrAdvanceRepository->approve($id);

        flash()->success(__('messages.updated', ['model' => __('hr:models/hr_advances.singular')]));
        

        return redirect(route('hr.advances.index'));
    }

    // Reject
    public function reject($id)
    {
        $advance = $this->hrAdvanceRepository->find($id);

        if (empty($advance)) {
            flash()->error(__('hr:models/hr_advances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.advances.index'));
        }

        $advance = $this->hrAdvanceRepository->reject($id);

        flash()->success(__('messages.updated', ['model' => __('hr:models/hr_advances.singular')]));

        return redirect(route('hr.advances.index'));
    }
}
