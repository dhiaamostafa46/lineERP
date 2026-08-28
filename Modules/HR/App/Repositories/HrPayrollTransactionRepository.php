<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrReward;
use Modules\HR\App\Models\HrSalary;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Models\HrSalaryDeduct;
use Modules\HR\App\Models\HrSalaryAllowance;
use Modules\HR\App\Models\HrPayrollTransaction;
use Carbon\Carbon;
use Modules\HR\App\Models\HrMonthlyPayment;

class HrPayrollTransactionRepository extends BaseRepository
{
    protected $fieldSearchable = ['payroll_employee_id', 'forable_id', 'forable_type', 'amount', 'currency', 'is_deduct', 'type', 'status', 'note', 'name'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPayrollTransaction::class;
    }

    public function updateOrCreateMany(object|array $attributes = [], int $payroll_employee_id)
    {
        foreach ($attributes as $key => $value) {
            $this->updateOrCreate($value, $payroll_employee_id);
        }
    }

    public function updateOrCreate(array $inputs = [], int $payroll_employee_id)
    {
        // Set a default value for 'amount' if it is null (for example, 0)
        $amount = $inputs['amount'] ?? 0;

        HrPayrollTransaction::updateOrCreate(
            [
                'payroll_employee_id' => $payroll_employee_id,
                'forable_id' => $inputs['forable_id'],
                'forable_type' => $inputs['forable_type'],
                'is_deduct' => $inputs['is_deduct'],
                'type' => $inputs['type'],
                'name' => $inputs['name'],
            ],
            [
                'amount' => $amount,
            ],
        );
    }
    public function syncEmployees(array $employees, int $payroll_id, $month = null)
    {

        $month = $month ?? Carbon::now();
        foreach ($employees as $payroll_employee) {
            $this->syncAllNotApproved($payroll_employee->id, $payroll_employee->employee_id, $payroll_id, $month);
        }
    }

    public function syncAllNotApproved(int $payroll_employee_id, int $employee_id, int $payroll_id, $month = null)
    {
        $month = $month ?? Carbon::now();

        $now = Carbon::parse($month);
        $month = $now->month;
        $salary = HrSalary::where('employee_id', $employee_id)->first();
        if (!empty($salary)) {
            $this->updateOrCreate(
                [
                    'forable_id' => $salary->id,
                    'forable_type' => get_class($salary),
                    'is_deduct' => false,
                    'amount' => $salary->basic,
                    'type' => HrPayrollTransaction::TYPE_SALARY,
                    'name' => '',
                ],
                $payroll_employee_id,
            );
        }

        $allowances = HrSalaryAllowance::where('salary_id', $salary->id)->get();
        foreach ($allowances as $allowance) {
            $this->updateOrCreate(
                [
                    'forable_id' => $allowance->id,
                    'forable_type' => get_class($allowance),
                    'is_deduct' => false,
                    'amount' => $allowance->amount,
                    'type' => HrPayrollTransaction::TYPE_ALLOWANCE,
                    'name' => $allowance->name,
                ],
                $payroll_employee_id,
            );
            $allowance->update(['payroll_id' => $payroll_id]);
        }

        $deducts = HrSalaryDeduct::where('salary_id', $salary->id)->get();

      

        foreach ($deducts as $deduct) {
            $this->updateOrCreate(
                [
                    'forable_id' => $deduct->id,
                    'forable_type' => get_class($deduct),
                    'is_deduct' => false,
                    'amount' => $deduct->amount,
                    'type' => HrPayrollTransaction::TYPE_DEDUCT,
                    'name' => $deduct->name,
                ],
                $payroll_employee_id,
            );
            $deduct->update(['payroll_id' => $payroll_id]);
        }

        $penalties = HrPenalty::where('employee_id', $employee_id)->whereMonth('due_date', '=', $month)->outPayroll()->get();

        foreach ($penalties as $penalty) {
            $this->updateOrCreate(
                [
                    'forable_id' => $penalty->id,
                    'forable_type' => get_class($penalty),
                    'is_deduct' => false,
                    'amount' => $penalty->amount,
                    'type' => HrPayrollTransaction::TYPE_PENALTY,
                    'name' => $penalty->description,
                ],
                $payroll_employee_id,
            );
            $penalty->update(['payroll_id' => $payroll_id]);
        }

        $advances = HrMonthlyPayment::where('employee_id', $employee_id)->whereMonth('due_at', '=', $month)->outPayroll()->get();

        foreach ($advances as $advance) {
            $this->updateOrCreate(
                [
                    'forable_id' => $advance->id,
                    'forable_type' => get_class($advance),
                    'is_deduct' => false,
                    'amount' => $advance->amount,
                    'type' => HrPayrollTransaction::TYPE_ADVANCE,
                    'name' => $advance->description,
                ],
                $payroll_employee_id,
            );
            $advance->update(['payroll_id' => $payroll_id, 'type' => HrMonthlyPayment::TYPE_REPAID]);
        }

        $rewards = HrReward::where('employee_id', $employee_id)->whereMonth('due_date', '=', $month)->get();

        foreach ($rewards as $reward) {
            $this->updateOrCreate(
                [
                    'forable_id' => $reward->id,
                    'forable_type' => get_class($reward),
                    'is_deduct' => false,
                    'amount' => $reward->amount,
                    'type' => HrPayrollTransaction::TYPE_REWARD,
                    'name' => $reward->description,
                ],
                $payroll_employee_id,
            );
            $reward->update(['payroll_id' => $payroll_id]);
        }
    }
}
