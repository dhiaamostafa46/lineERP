<?php

namespace Modules\HR\App\Livewire\Payrolls;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\HR\App\Repositories\HrPayrollRepository;

class Show extends Component
{
    use LivewireAlert;
    public HrPayroll $payroll;
    public $tab;

    protected HrPayrollRepository $hrPayrollRepo;

    public function boot(HrPayrollRepository $hrPayrollRepo)
    {
        $this->hrPayrollRepo = $hrPayrollRepo;
    }

    public function mount()
    {
        $this->tab = $this->payroll->tab;
    }

    #[On('payroll-updated')]
    public function render()
    {
        $this->payroll = $this->hrPayrollRepo->find($this->payroll->id);

        return view('hr::livewire.payrolls.show');
    }

    public function changeTab($tab)
    {
        $this->tab = $tab;
        $this->hrPayrollRepo->update(['tab' => $tab], $this->payroll->id, false);
    }

    #[On('payroll-accredited')]
    public function updatePayroll()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $this->hrPayrollRepo->update(['status' => HrPayroll::STATUS_ACCREDITED], $this->payroll->id, false);
            
            // --- Accounting Integration ---
            $details = [];
            foreach ($this->payroll->payroll_employees as $emp) {
                if ($emp->basic_wage > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_salaries_expense_account',
                        'debit' => $emp->basic_wage,
                        'credit' => 0,
                        'employee_id' => $emp->employee_id,
                        'description' => 'راتب أساسي: ' . $emp->username
                    ];
                }
                if ($emp->total_allowances > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_allowances_expense_account',
                        'debit' => $emp->total_allowances,
                        'credit' => 0,
                        'employee_id' => $emp->employee_id,
                        'description' => 'بدلات: ' . $emp->username
                    ];
                }
                if ($emp->total_rewards > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_rewards_expense_account',
                        'debit' => $emp->total_rewards,
                        'credit' => 0,
                        'employee_id' => $emp->employee_id,
                        'description' => 'مكافآت: ' . $emp->username
                    ];
                }
                if ($emp->total_deducts > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_deductions_account',
                        'debit' => 0,
                        'credit' => $emp->total_deducts,
                        'employee_id' => $emp->employee_id,
                        'description' => 'استقطاعات: ' . $emp->username
                    ];
                }
                if ($emp->total_penalties > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_penalties_account',
                        'debit' => 0,
                        'credit' => $emp->total_penalties,
                        'employee_id' => $emp->employee_id,
                        'description' => 'جزاءات: ' . $emp->username
                    ];
                }
                if ($emp->total_advances > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_advance_receivable_account',
                        'debit' => 0,
                        'credit' => $emp->total_advances,
                        'employee_id' => $emp->employee_id,
                        'description' => 'سداد سلفة: ' . $emp->username
                    ];
                }
                if ($emp->net_wage > 0) {
                    $details[] = [
                        'mapping_key' => 'hr_accrued_salaries_payable_account',
                        'debit' => 0,
                        'credit' => $emp->net_wage,
                        'employee_id' => $emp->employee_id,
                        'description' => 'صافي مستحق: ' . $emp->username
                    ];
                }
            }

            if (!empty($details)) {
                \Modules\HR\App\Services\HrJournalEntryService::createComplexEntry(
                    'اعتماد رواتب شهر ' . $this->payroll->payroll_date->format('Y-m'),
                    $details,
                    get_class($this->payroll),
                    $this->payroll->id
                );
            }
            // ------------------------------

            activity()
                ->causedBy(auth()->user())
                ->on($this->payroll)
                ->event('Accredited Payroll')
                ->log(__('hr::models/hr_payrolls.fields.payroll_accredited'));

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Payroll Accreditation Failed: ' . $e->getMessage());
            $this->alert('error', 'حدث خطأ: ' . $e->getMessage());
            return;
        }

        $this->alert('success', __('hr::models/hr_payrolls.fields.payroll_accredited'));
        $this->render();
        $this->alert('success', __('hr::models/hr_payrolls.fields.payroll_accredited'));

        $setting = HrSetting::first();
        $setting->update([
            'due_payroll_at' => null,
            'payroll_id'     => null,
            'preparing_payroll'     => 0,

        ]);


    }
}



