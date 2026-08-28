<?php

namespace Modules\HR\App\Livewire\Payrolls;

use Livewire\Component;
use Modules\HR\App\Models\HrSetting;
use Modules\HR\App\Models\HrPayrollEmployee;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\HR\App\Models\HrPayrollTransaction;
use Modules\HR\App\Repositories\HrPayrollTransactionRepository;

class AddTransactions extends Component
{
    use LivewireAlert;

    public $hr_setting;
    public $model;
    public $query;
    public $list;
    public $showAddButton = false;
    public $canBeAdd = false;
    protected HrPayrollTransactionRepository $transactionRepo;

    public function boot(HrPayrollTransactionRepository $transactionRepo)
    {
        $this->transactionRepo = $transactionRepo;
    }

    public function mount()
    {
        if ($this->hr_setting->payroll_id) {
            $payroll_employee = HrPayrollEmployee::updateOrCreate([
                'employee_id' => $this->model->employee_id,
                'payroll_id'  => $this->hr_setting->payroll_id,
            ], [
                'basic_wage'       => 0,
                'total_allowances' => 0,
                'total_deducts'    => 0,
                'total_penalties'  => 0,
                'total_advances'   => 0,
                'total_rewards'    => 0,
                'net_wage'         => 0,
                'status'         => 1,

            ]);
            $this->query = [
                'payroll_employee_id' => $payroll_employee->id,
                'forable_id'          => $this->model->id,
                'forable_type'        => get_class($this->model),
            ];
            $transaction = $this->transactionRepo->allQuery($this->query)->first();
            if (!$transaction) {
                $this->canBeAdd = true;
            }
        }
    }

    public function render()
    {
        return view('hr::livewire.payrolls.add-transactions');
    }

    public function addTransaction()
    {


        $data = $this->query;
        $list = [
            'Modules\HR\App\Models\HrMonthlyPayment' => [
                'class' => 'Modules\HR\App\Models\HrMonthlyPayment',
                'is_deduct' => true,
                'type' => HrPayrollTransaction::TYPE_ADVANCE
            ],
            'Modules\HR\App\Models\HrSalaryDeduct' => [
                'class' => 'Modules\HR\App\Models\HrSalaryDeduct',
                'is_deduct' => true,
                'type' => HrPayrollTransaction::TYPE_DEDUCT
            ],
            'Modules\HR\App\Models\HrSalaryAllowance' => [
                'class' => 'Modules\HR\App\Models\HrSalaryAllowance',
                'is_deduct' => false,
                'type' => HrPayrollTransaction::TYPE_ALLOWANCE
            ],
            'Modules\HR\App\Models\HrReward' => [
                'class' => 'Modules\HR\App\Models\HrReward',
                'is_deduct' => false,
                'type' => HrPayrollTransaction::TYPE_REWARD
            ],
            'Modules\HR\App\Models\HrPenalty' => [
                'class' => 'Modules\HR\App\Models\HrPenalty',
                'is_deduct' => true,
                'type' => HrPayrollTransaction::TYPE_PENALTY
            ],
            'Modules\HR\App\Models\HrSalary' => [
                'class' => 'Modules\HR\App\Models\HrSalary',
                'is_deduct' => false,
                'type' => HrPayrollTransaction::TYPE_SALARY
            ],
        ];
        $item = $list[get_class($this->model)];
        $data['amount'] = $this->model->amount;
        $data['is_deduct'] = $item['is_deduct'];
        $data['type'] = $item['type'];
        $data['status'] = HrPayrollTransaction::STATUS_APPROVED ;
        $this->transactionRepo->create($data);
        $this->alert('success', __('hr::models/hr_payrolls.fields.addtopayroll'), [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true,
        ]);
        $this->canBeAdd = false;
        $hr_setting = HrSetting::first();
        $hr_setting->update([
            'payroll_updated' => true
        ]);
    }
}
