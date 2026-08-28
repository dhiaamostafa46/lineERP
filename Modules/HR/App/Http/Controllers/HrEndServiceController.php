<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\HrEndServiceRequest;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrEndServiceRepository;

class HrEndServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $HrEndServiceRepository;

    public function __construct(HrEndServiceRepository $HrEndServiceRepository)
    {
        $this->HrEndServiceRepository = $HrEndServiceRepository;
    }

    /**
     * Display a listing of the HrEndService.
     */
    public function index(Request $request)
    {
        $data['EndServices'] = $this->HrEndServiceRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['employees'] = $this->HrEndServiceRepository->employees();
        $data['statuses'] = $this->HrEndServiceRepository->statuses();

        return view('hr::EndService.index', $data);
    }

    public function getEmployeessalaries(Request $request)
    {
        $EmployessSalary = $this->HrEndServiceRepository->EmployessSalary($request);
        return response()->json($EmployessSalary);
    }
    /**
     * Show the form for creating a new HrEndService.
     */
    public function create()
    {
        $data['employees'] = $this->HrEndServiceRepository->employees();

        $data['statuses'] = $this->HrEndServiceRepository->statuses();
        $data['reasons'] = $this->HrEndServiceRepository->reasons();
        return view('hr::EndService.create', $data);
    }

    /**
     * Store a newly created HrEndService in storage.
     */
    public function store(HrEndServiceRequest $request)
    {
        $emp = \Modules\HR\App\Models\HrEmployee::find($request->employee_id);

        $custodiesCount = $emp->Custodies()->whereIn('status', [1, 2, 3])->count();
        if ($custodiesCount > 0) {
            return back()->with('msg', ['status' => false, 'messages' => [__('hr::models/hr_end_service.employee_messages.has_commitments')]]);
        }

        // Securely recalculate EOSB in backend

        $salary = (float) ($emp->salary->basic ?? 0);
        $calcResult = \Modules\HR\App\Services\EosbCalculatorService::calculate(
            $emp->id,
            $emp->start_at,
            $request->end,
            $salary,
            $request->reason
        );

        $input = $request->all();
        $input['reward_amount'] = $calcResult['reward_amount'];
        $input['total_penalties'] = $calcResult['total_penalties'] ?? 0;
        $input['total_advances'] = $calcResult['total_advances'] ?? 0;
        $input['total_deducts'] = $calcResult['total_deducts'] ?? 0;
        $input['net_reward'] = $calcResult['net_reward'] ?? 0;

        $hrEndService = $this->HrEndServiceRepository->create($input);

        // Liabilities are now permanently recorded in the $hrEndService record.
        // We do not need to hack payroll_id = -1, as the employee is being terminated 
        // and will not appear in future payrolls.

        // Accounting Integration for EOSB (End of Service Benefit)
        if (!empty($hrEndService->reward_amount) && $hrEndService->reward_amount > 0) {
            try {
                \Modules\HR\App\Services\HrJournalEntryService::createEntry(
                    (float) $hrEndService->reward_amount,
                    'hr_eosb_expense_account', // Debit
                    'hr_accrued_eosb_payable_account', // Credit
                    'مكافأة نهاية الخدمة للموظف: ' . ($hrEndService->employee->username ?? 'Unknown'),
                    get_class($hrEndService),
                    $hrEndService->id,
                    $hrEndService->employee_id
                );
            } catch (\Exception $e) {
                \Log::error('EOSB Journal Entry failed: ' . $e->getMessage());
                // We don't rollback the whole termination process if just accounting fails, or do we?
                // Let's just log it and maybe show a message.
            }
        }

        $check = $this->HrEndServiceRepository->RemoveEmpData($request->employee_id);


        return redirect(route('hr.EndService.index'));
    }

    /**
     * Display the specified HrEndService.
     */
    public function show($id)
    {
        $data['EndService'] = $this->HrEndServiceRepository->find($id);

        if (empty($data['EndService'])) {
            flash()->error(__('hr::models/hr_EndService.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.EndService.index'));
        }

        return view('hr::EndService.show', $data);
    }

    /**
     * Show the form for editing the specified HrEndService.
     */
    public function edit($id)
    {
        $data['EndService'] = $this->HrEndServiceRepository->find($id);
        
        if (empty($data['EndService'])) {
            flash()->error(__('hr::models/hr_EndService.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.EndService.index'));
        }

        $data['employees'] = $this->HrEndServiceRepository->employees($data['EndService']->employee_id);

        $data['statuses'] = $this->HrEndServiceRepository->statuses();
        $data['reasons'] = $this->HrEndServiceRepository->reasons();

        return view('hr::EndService.edit', $data);
    }

    /**
     * Update the specified HrEndService in storage.
     */
    public function update($id, HrEndServiceRequest $request)
    {
        $hrEndService = $this->HrEndServiceRepository->find($id);

        if (empty($hrEndService)) {
            flash()->error(__('hr::models/hr_EndService.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.EndService.index'));
        }

        $old_employee_id = $hrEndService->employee_id;
        $new_employee_id = $request->employee_id ?? $old_employee_id;

        // Securely recalculate EOSB in backend
        $emp = \Modules\HR\App\Models\HrEmployee::withTrashed()->find($new_employee_id);
        $salary = (float) ($emp->salary->basic ?? 0);
        $calcResult = \Modules\HR\App\Services\EosbCalculatorService::calculate(
            $emp->id,
            $emp->start_at,
            $request->end ?? $hrEndService->end,
            $salary,
            $request->reason ?? $hrEndService->reason
        );

        $input = $request->all();
        $input['reward_amount'] = $calcResult['reward_amount'];
        $input['total_penalties'] = $calcResult['total_penalties'] ?? 0;
        $input['total_advances'] = $calcResult['total_advances'] ?? 0;
        $input['total_deducts'] = $calcResult['total_deducts'] ?? 0;
        $input['net_reward'] = $calcResult['net_reward'] ?? 0;

        $hrEndService = $this->HrEndServiceRepository->update($input, $id);

        // Handle employee change (Restore old, archive new)
        if ($old_employee_id != $new_employee_id) {
            $this->HrEndServiceRepository->RestoreEmployee($old_employee_id);
            $this->HrEndServiceRepository->RemoveEmpData($new_employee_id);
        }

        // Update Associated Journal Entry
        $journalEntry = \App\Models\AccuSoft\JournalEntry::where('reference_type', get_class($hrEndService))
            ->where('reference_id', $hrEndService->id)
            ->first();

        if ($journalEntry) {
            $journalEntry->update([
                'total_debit' => $hrEndService->reward_amount,
                'total_credit' => $hrEndService->reward_amount,
                'description' => 'مكافأة نهاية الخدمة للموظف: ' . ($emp->username ?? 'Unknown'),
            ]);

            // Update amounts in details
            foreach ($journalEntry->details as $detail) {
                if ($detail->debit > 0) {
                    $detail->update(['debit' => $hrEndService->reward_amount]);
                }
                if ($detail->credit > 0) {
                    $detail->update(['credit' => $hrEndService->reward_amount]);
                }
            }
        } elseif ($hrEndService->reward_amount > 0) {
            // Create if it didn't exist for some reason
            try {
                \Modules\HR\App\Services\HrJournalEntryService::createEntry(
                    (float) $hrEndService->reward_amount,
                    'hr_eosb_expense_account',
                    'hr_accrued_eosb_payable_account',
                    'مكافأة نهاية الخدمة للموظف: ' . ($emp->username ?? 'Unknown'),
                    get_class($hrEndService),
                    $hrEndService->id,
                    $hrEndService->employee_id
                );
            } catch (\Exception $e) {
                \Log::error('EOSB Update Journal Entry failed: ' . $e->getMessage());
            }
        }

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_EndService.singular')]));

        return redirect(route('hr.EndService.index'));
    }

    /**
     * Remove the specified HrEndService from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrEndService = $this->HrEndServiceRepository->find($id);

        if (empty($hrEndService)) {
            flash()->error(__('hr::models/hr_EndService.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.EndService.index'));
        }

        // Restore the employee and their liabilities
        $this->HrEndServiceRepository->RestoreEmployee($hrEndService->employee_id);

        // Delete associated Journal Entry if exists
        $journalEntry = \App\Models\AccuSoft\JournalEntry::where('reference_type', get_class($hrEndService))
            ->where('reference_id', $hrEndService->id)
            ->first();
            
        if ($journalEntry) {
            $journalEntry->details()->delete();
            $journalEntry->delete();
        }

        $this->HrEndServiceRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_EndService.singular')]));
        return redirect(route('hr.EndService.index'));
    }
    /**
     * Calculate EOSB securely from backend
     */
    public function calculateEosb(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'end_date' => 'required|date',
            'reason' => 'required|integer'
        ]);

        $emp = \Modules\HR\App\Models\HrEmployee::withTrashed()->find($request->employee_id);
        if (!$emp || !$emp->start_at) {
            return response()->json([
                'success' => false,
                'message' => __('hr::models/hr_end_service.employee_messages.employee_data_unavailable')
            ]);
        }

        $custodiesCount = $emp->Custodies()->whereIn('status', [2, 3])->count();
        if ($custodiesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => __('hr::models/hr_end_service.employee_messages.cannot_create_has_custodies')
            ]);
        }

        $salary = (float) ($emp->salary->basic ?? 0);
        
        $result = \Modules\HR\App\Services\EosbCalculatorService::calculate(
            $emp->id,
            $emp->start_at,
            $request->end_date,
            $salary,
            $request->reason
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
