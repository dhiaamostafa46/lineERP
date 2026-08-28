<?php

namespace Modules\HR\App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrReward;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrMonthlyPayment;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrPenalty;
use Spatie\Permission\Models\Permission;
use Modules\HR\App\Models\HrSalaryDeduct;
use Modules\HR\App\Models\HrPayrollApproval;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Models\HrSalaryAllowance;
use Modules\HR\App\Models\HrPayrollTransaction;

trait PayrollTrait
{
    // used in payroll controller
    public function preparing_payroll()
    {
        $now = Carbon::now();
        $month = $now->month;
        return DB::table('hr_employees as he')
        ->select(
            'he.id as employee_id',
            'hs.id as salary_id',
            DB::raw("COALESCE(hdt.name, 'N/A') as department_name"),
            DB::raw("COALESCE(hjt.name, 'N/A') as job_name"),
            'e.username',
            'hs.basic as basic_wage',
            DB::raw('COALESCE(p.total_penalties, 0) as total_penalties'),
            DB::raw('COALESCE(a.total_advances, 0) as total_advances'),
            DB::raw('COALESCE(al.total_allowances, 0) as total_allowances'),
            DB::raw('COALESCE(d.total_deducts, 0) as total_deducts'),
            DB::raw('COALESCE(r.total_rewards, 0) as total_rewards'),
            DB::raw('(hs.basic + COALESCE(al.total_allowances, 0) + COALESCE(r.total_rewards, 0)) - (COALESCE(p.total_penalties, 0) + COALESCE(a.total_advances, 0) + COALESCE(d.total_deducts, 0)) as net_wage')
        )
            ->join('employees as e', 'he.employee_id', '=', 'e.id')
            ->join('hr_departments as hd', 'he.department_id', '=', 'hd.id')
            ->leftJoin('hr_department_translations as hdt', function ($join) {
                $join->on('hd.id', '=', 'hdt.hr_department_id')
                ->where('hdt.locale', '=', 'ar');
            })
            ->join('hr_jobs as hj', 'he.job_id', '=', 'hj.id')
            ->leftJoin('hr_job_translations as hjt', function ($join) {
                $join->on('hj.id', '=', 'hjt.hr_job_id')
                ->where('hjt.locale', '=', 'ar');
            })
            ->join('hr_salaries as hs', 'he.id', '=', 'hs.employee_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_penalties FROM hr_penalties where MONTH(due_date) >='.$month.' GROUP BY employee_id) as p'), 'he.id', '=', 'p.employee_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_advances FROM hr_monthly_payments  where MONTH(due_date) >='.$month.' GROUP BY employee_id) as a'), 'he.id', '=', 'a.employee_id')
            ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_allowances FROM hr_salary_allowances  GROUP BY salary_id) as al'), 'hs.id', '=', 'al.salary_id')
            ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_deducts FROM hr_salary_deducts  GROUP BY salary_id) as d'), 'hs.id', '=', 'd.salary_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_rewards FROM hr_rewards  GROUP BY employee_id) as r'), 'he.id', '=', 'r.employee_id')
            ->groupBy('he.id', 'hs.basic', 'hdt.name', 'hjt.name', 'e.username', 'hs.id')
            ->orderBy('net_wage', 'desc')
            ->get();
    }

    //unused
    public function payroll_year($total, $delivery_at)
    {
        $year = date('Y');
        if (HrPayroll::whereYear('payroll_date', $year)->count() < 12) {
            for ($i = 1; $i <= 12; $i++) {
                HrPayroll::updateOrCreate([
                    'preparing_at'  => Carbon::now()->format('Y-m-d'),
                    'payroll_date'  => Carbon::parse("$year-$i")->month($i)->firstOfMonth()->format('Y-m-d'),
                ], [
                    'total'       => $total,
                    'delivery_at' => $delivery_at,
                    'status'      => HrPayroll::STATUS_PREPARING,
                ]);
            }
        }
    }

    public function store_payroll_employees($payroll_id)
    {
        return HrPayrollEmployee::create([
            'employee_id'      => request('employee_id'),
            'payroll_id'       => $payroll_id,
            'salary_id'        => request('salary_id'),
            'total_allowances' => request('total_allowances'),
            'total_deducts'    => request('total_deducts'),
            'basic_salary'     => request('basic_salary'),
            'status'           => HrPayrollEmployee::STATUS_PENDING,
        ]);
    }

    public function store_approved_employee($payroll_id, $employee_id, $status)
    {
        HrPayrollApproval::updateOrCreate([
            'payroll_id'    => $payroll_id,
            'employee_id'   => $employee_id,
        ], ['status'   => $status]);

        return true;
    }


    public function count_pending_payroll_transactions($payroll_employee_id)
    {
        return HrPayrollTransaction::where('payroll_employee_id', $payroll_employee_id)
            ->where('status', HrPayrollTransaction::STATUS_PENDING)
            ->get()
            ->count();
    }

    public function count_pending_payroll_employees($payroll_id)
    {
        return HrPayrollEmployee::where('payroll_id', $payroll_id)
            ->where('status', HrPayrollEmployee::STATUS_PENDING)
            ->get()
            ->count();
    }

    public function update_or_create_payroll_transaction($payroll_employee_id, $forable, $amount, $is_deduct, $type , $name="")
    {
        HrPayrollTransaction::updateOrCreate([
            'payroll_employee_id' => $payroll_employee_id,
            'forable_id'          => $forable->id,
            'forable_type'        => get_class($forable),
            'is_deduct'           => $is_deduct,
        ], [
            'amount'              => $amount,
            'type'                => $type,
            'name'                => $name,
        ]);
        $payroll_employee = HrPayrollEmployee::find($payroll_employee_id);
        if ($payroll_employee) {
            if ($is_deduct) {
                $payroll_employee->increment('total_deducts', $amount);
            } else {
                $payroll_employee->increment('total_allowances', $amount);
            }
        }
    }

    public function sync_payroll($employees, $payroll)
    {
        foreach ($employees as $employee) {
            $payroll_employee = HrPayrollEmployee::updateOrCreate([
                'employee_id'        => $employee->id,
                'payroll_id'         => $payroll->id,
            ], [
                'salary_id'          => $employee->salary_id,
                'total_allowances'   => $employee->total_allowances,
                'total_deducts'      => $employee->total_deducts,
                'basic_salary'       => $employee->basic_salary,
                'status'             => $employee->status,
            ]);
            $this->create_payroll_transactions($payroll_employee);
        }
        $payroll->update([
            'total' => $payroll->employees()->get()->sum('remaining_salary'),
        ]);
    }

    private function create_payroll_transactions(HrPayrollEmployee $payroll_employee)
    {
        $salary = HrSalary::where('employee_id', $payroll_employee->employee_id)->first();
        if (!empty($salary)) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $salary, $salary->basic_salary, false, HrPayrollTransaction::TYPE_SALARY ,'');
        }

        $allowances = HrSalaryAllowance::where('salary_id', $salary->id)->get();

        foreach ($allowances as $allowance) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $allowance, $allowance->value, false, HrPayrollTransaction::TYPE_ALLOWANCE ,$allowance->name );
            $allowance->update(['payroll_id' => $payroll_employee->payroll_id]);
        }

        $deducts = HrSalaryDeduct::where('employee_id', $payroll_employee->employee_id)->get();

        foreach ($deducts as $deduct) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $deduct, $deduct->value, true, HrPayrollTransaction::TYPE_DEDUCT ,$deduct->name);
            $deduct->update(['payroll_id' => $payroll_employee->payroll_id]);
        }

        $penalties = HrPenalty::where('employee_id', $payroll_employee->employee_id)
            ->whereMonth('dueDate', $payroll_employee->payroll_date)
            ->outPayroll()
            ->get();

        foreach ($penalties as $penalty) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $penalty, $penalty->amount, true, HrPayrollTransaction::TYPE_PENALTY , $penalty->description );
            $penalty->update(['payroll_id' => $payroll_employee->payroll_id]);
        }

        $advances = HrMonthlyPayment::where('employee_id', $payroll_employee->employee_id)
            ->whereMonth('dueDate', $payroll_employee->payroll_date)
            ->outPayroll()
            ->get();

        foreach ($advances as $advance) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $advance, $advance->amount, true, HrPayrollTransaction::TYPE_ADVANCE  , $advance->description );
            $advance->update(['payroll_id' => $payroll_employee->payroll_id]);
        }

        $rewards = HrReward::where('employee_id', $payroll_employee->employee_id)
            ->get();

        foreach ($rewards as $reward) {
            $this->update_or_create_payroll_transaction($payroll_employee->id, $reward, $reward->amount, false, HrPayrollTransaction::TYPE_REWARD  , $reward->description );
            $reward->update(['payroll_id' => $payroll_employee->payroll_id]);
        }

        $total_deducts = HrPayrollTransaction::where('payroll_employee_id', $payroll_employee->id)
            ->where('is_deduct', true)
            ->get()->sum('amount');

        $total_allowances = HrPayrollTransaction::where('payroll_employee_id', $payroll_employee->id)
            ->where('is_deduct', false)
            ->get()->sum('amount');

        $payroll_employee->update([
            'total_allowances' => $total_allowances,
            'total_deducts'    => $total_deducts,
            'net_salary'       => $salary->basic_salary,
        ]);
    }

    public function all_payroll_approved($payroll_id)
    {
        if ($this->count_payroll_approvals($payroll_id) == $this->count_can_payroll_approvals()) {
            return true;
        } else {
            return false;
        }
    }

    private function count_payroll_approvals($payroll_id)
    {
        return HrPayrollApproval::where('payroll_id', $payroll_id)->get()->count();
    }

    private function count_can_payroll_approvals()
    {
        return Permission::where('name', 'hr.payroll.approval')->get()->count();
    }
}
