<?php

namespace Modules\HR\App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\HR\App\Models\HrSetting;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Helpers\PayrollTrait;
use Modules\HR\App\Repositories\HrPayrollRepository;
use Modules\HR\App\Repositories\HrSettingRepository;
use Modules\HR\App\Http\Requests\CreateHrPayrollRequest;
use Modules\HR\App\Http\Requests\UpdateHrPayrollRequest;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;
use Modules\HR\App\Repositories\HrPayrollTransactionRepository;
use Modules\HR\App\Repositories\HrPayrollApprovalRepository;
use Modules\HR\App\Models\HrPayrollApproval;
use Modules\HR\App\Models\HrPayrollTransaction;

class HrPayrollController extends AppBaseController
{
    /** @var HrPayrollRepository $hrPayrollRepository*/
    private $hrPayrollRepository;

    /** @var HrPayrollEmployeeRepository $hrPayrollRepository*/
    private $hrPayrollEmployeeRepository;

    /** @var HrPayrollTransactionRepository $hrPayrollRepository*/
    private $hrPayrollTransactionRepository;

    public function __construct(HrPayrollRepository $hrPayrollRepo, HrPayrollEmployeeRepository $hrPayrollEmployeeRepo, HrPayrollTransactionRepository $hrPayrollTransactionRepo)
    {
        $this->hrPayrollRepository = $hrPayrollRepo;
        $this->hrPayrollEmployeeRepository = $hrPayrollEmployeeRepo;
        $this->hrPayrollTransactionRepository = $hrPayrollTransactionRepo;
    }

    /**
     * Display a listing of the HrPayroll.
     */
    public function index(Request $request)
    {



    //     $payroll_employees = $this->hrPayrollRepository->payroll_employees();

    //  dd(   $payroll_employees );



        // $payroll = $this->hrPayrollRepository->find(1);

        // $employees = $this->hrPayrollEmployeeRepository->updateOrCreateMany($payroll_employees, $payroll->id);

        //   dd($this->hrPayrollTransactionRepository->syncEmployees($employees, $payroll->id ,$payroll->payroll_date));



        $data['payrolls'] = $this->hrPayrollRepository
            ->allQuery([])
            ->when($request->payroll_date, function ($query) use ($request) {
                return $query->whereMonth('payroll_date', Carbon::parse($request->payroll_date)->format('m-Y'));
            })
            ->latest()
            ->paginate(12);

        return view('hr::payrolls.index', $data);
    }

    /**
     * Show the form for creating a new HrPayroll.
     */
    public function create()
    {
        $data['payroll_employees'] = $this->hrPayrollRepository->payroll_employees();
        $data['currency'] = $this->hrPayrollRepository->currency();
        $last_payroll = $this->hrPayrollRepository->allQuery([])->latest()->first();
        if ($last_payroll) {
            $data['payroll_date'] = Carbon::parse($last_payroll->payroll_date)->addMonth(1)->format('Y-m');
        } else {
            $data['payroll_date'] = Carbon::now()->addMonth(1)->format('Y-m');
        }
        return view('hr::payrolls.create', $data);
    }

    /**
     * Store a newly created HrPayroll in storage.
     */
    public function store(CreateHrPayrollRequest $request)
    {
        $setting = HrSetting::first();
        $input = $request->all();
        $input['preparing_at'] = Carbon::now()->format('Y-m') . '-' . $setting->preparing_payroll_at;
        $input['payroll_date'] = Carbon::now()->addMonth(1)->format('Y-m');
        $payroll = $this->hrPayrollRepository->create($input, false);
        $employees = $this->hrPayrollEmployeeRepository->updateOrCreateMany($this->hrPayrollRepository->payroll_employees(), $payroll->id);
        $this->hrPayrollTransactionRepository->syncEmployees($employees, $payroll->id ,$payroll->payroll_date);
        $this->hrPayrollRepository->create_approvals($payroll, $setting->approval_payroll ?? []);
        $setting->update([
            'payroll_id' => $payroll->id,
            'due_payroll_at' => $payroll->delivery_at,
        ]);
        flash()->success(__('messages.saved', ['model' => __('models/hrPayrolls.singular')]));
        activity()
            ->causedBy(auth()->user())
            ->on($payroll)
            ->withProperties($input)
            ->event('preparing Payroll')
            ->log(__('hr::models/hr_payrolls.fields.preparing_Payroll'));
        return redirect(route('hr.payrolls.index'));
    }

    /**
     * Display the specified HrPayroll.
     */
    public function show($id)
    {
        $data['payroll'] = $this->hrPayrollRepository->find($id);
        //dd( $data['payroll']);
        if (empty($data['payroll'])) {
            flash()->error(__('hr::models/hr_payrolls.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.payrolls.index'));
        }
        //Get Payroll Approvals from settnings ---by saeed
        $setting = HrSetting::first();

        //Check is there approval users or no
        if ($setting->approval_payroll != null) {
            //if there approvals user copy them to table hr_payroll_approvals ---by saeed
            $hrApprovalRepo = new HrPayrollApprovalRepository();
            $payrol = HrPayrollApproval::where('payroll_id', $data['payroll']->id)->first();
            //check if there data on table hr_payroll_approvals ---by saeed
            if ($payrol == null) {
                //if no data copy data from hr_setting ---by saeed
                for ($x = 0; $x < count($setting->approval_payroll); $x++) {
                    $hrApprovalRepo->create([
                        'payroll_id' => $data['payroll']->id,
                        'user_id' => $setting->approval_payroll[$x]['user_id'],
                        'status' => HrPayrollApproval::STATUS_PENDING,
                        'sort' => $setting->approval_payroll[$x]['sort'],
                        'is_current' => $setting->approval_payroll[$x]['sort'] == 1 ? 1 : 0,
                    ]);
                }
            }

            $data['payroll']->approvals_is_ready = 1;
            $data['payroll']->save();
        }

        return view('hr::payrolls.show', $data);
    }

    /**
     * Show the form for editing the specified HrPayroll.
     */
    public function edit($id)
    {
        $hrPayroll = $this->hrPayrollRepository->find($id);

        if (empty($hrPayroll)) {
            flash()->error(__('hr::models/hr_payrolls.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.payrolls.index'));
        }

        return view('hr::payrolls.edit')->with('hrPayroll', $hrPayroll);
    }

    /**
     * Update the specified HrPayroll in storage.
     */
    public function update($id, UpdateHrPayrollRequest $request)
    {
        $hrPayroll = $this->hrPayrollRepository->find($id);

        if (empty($hrPayroll)) {
            flash()->error(__('hr::models/hr_payrolls.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.payrolls.index'));
        }

        $hrPayroll = $this->hrPayrollRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_payrolls.singular')]));

        return redirect(route('hr.payrolls.index'));
    }

    /**
     * Remove the specified HrPayroll from storage.
     *
     * @throws \Exception
     */
    // public function destroy($id)
    // {
    //     $hrPayroll = $this->hrPayrollRepository->find($id);

    //     if (empty($hrPayroll)) {
    //         flash()->error(__('models/hrPayrolls.singular') . ' ' . __('messages.not_found'));

    //         return redirect(route('hr.payrolls.index'));
    //     }

    //     $this->hrPayrollRepository->delete($id);

    //     flash()->success(__('messages.deleted', ['model' => __('models/hrPayrolls.singular')]));

    //     return redirect(route('hr.payrolls.index'));
    // }

    public function destroy($id)
    {
        $hrPayroll = $this->hrPayrollRepository->find($id);

        if (empty($hrPayroll)) {
            flash()->error(__('hr::models/hr_payrolls.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.payrolls.index'));
        }

        $this->hrPayrollRepository->destroy($hrPayroll);
        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_payrolls.singular')]));
        return redirect(route('hr.payrolls.index'));
    }
}
