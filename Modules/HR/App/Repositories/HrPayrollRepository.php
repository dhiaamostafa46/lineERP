<?php

namespace Modules\HR\App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrSetting;
use Modules\HR\App\Models\HrEmployee;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Modules\HR\App\Models\HrMonthlyPayment;

use function PHPUnit\Framework\isNull;

class HrPayrollRepository extends BaseRepository
{
    protected $fieldSearchable = ['total', 'payroll_date', 'delivery_at', 'preparing_at', 'status'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPayroll::class;
    }

    public function employees(): object
    {
        return HrEmployee::with(['job:id', 'job.translations:hr_job_id,locale,name', 'salary:id,employee_id,basic', 'salary.salary_allowances:salary_id,amount', 'salary.salary_deducts:salary_id,amount', 'department:id', 'department.translations:hr_department_id,locale,name'])->get();
    }

    public function payroll_employees(): object
    {
        // $now = Carbon::now();
        // $month = $now->month;

        $setting = HrSetting::first();
        //$currentPayrollMonth = Carbon::parse($setting->next_payroll_date)->subMonth();
         $currentPayrollMonth = Carbon::parse($setting->next_payroll_date);


        $month = $currentPayrollMonth->month;
//$month = 5;
        // return DB::table('hr_employees as he')
        //     ->select(
        //         'he.id as employee_id',
        //         'hs.id as salary_id',
        //         DB::raw("COALESCE(hdt.name, 'N/A') as department_name"),
        //         DB::raw("COALESCE(hjt.name, 'N/A') as job_name"),
        //         'e.username',
        //         'hs.basic as basic_wage',
        //         DB::raw('COALESCE(p.total_penalties, 0) as total_penalties'),
        //         DB::raw('COALESCE(a.total_advances, 0) as total_advances'),
        //         DB::raw('COALESCE(al.total_allowances, 0) as total_allowances'),
        //         DB::raw('COALESCE(d.total_deducts, 0) as total_deducts'),
        //         DB::raw('COALESCE(r.total_rewards, 0) as total_rewards'),
        //         DB::raw('(hs.basic + COALESCE(al.total_allowances, 0) + COALESCE(r.total_rewards, 0)) - (COALESCE(p.total_penalties, 0) + COALESCE(a.total_advances, 0) + COALESCE(d.total_deducts, 0)) as net_wage')
        //     )
        //     ->join('employees as e', 'he.employee_id', '=', 'e.id')
        //     ->join('hr_departments as hd', 'he.department_id', '=', 'hd.id')
        //     ->leftJoin('hr_department_translations as hdt', function ($join) {
        //         $join->on('hd.id', '=', 'hdt.hr_department_id')
        //             ->where('hdt.locale', '=', 'ar');
        //     })
        //     ->join('hr_jobs as hj', 'he.job_id', '=', 'hj.id')
        //     ->leftJoin('hr_job_translations as hjt', function ($join) {
        //         $join->on('hj.id', '=', 'hjt.hr_job_id')
        //             ->where('hjt.locale', '=', 'ar');
        //     })
        //     ->join('hr_salaries as hs', 'he.id', '=', 'hs.employee_id')
        //     ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_penalties FROM hr_penalties WHERE payroll_id = NULL GROUP BY employee_id) as p'), 'he.id', '=', 'p.employee_id')
        //     ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_advances FROM hr_advances WHERE payroll_id = NULL GROUP BY employee_id) as a'), 'he.id', '=', 'a.employee_id')
        //     ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_allowances FROM hr_salary_allowances WHERE payroll_id = NULL GROUP BY salary_id) as al'), 'hs.id', '=', 'al.salary_id')
        //     ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_deducts FROM hr_salary_deducts WHERE payroll_id = NULL GROUP BY salary_id) as d'), 'hs.id', '=', 'd.salary_id')
        //     ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_rewards FROM hr_rewards WHERE payroll_id = NULL GROUP BY employee_id) as r'), 'he.id', '=', 'r.employee_id')
        //     ->groupBy('he.id', 'hs.basic', 'hdt.name', 'hjt.name', 'e.username', 'hs.id')
        //     ->orderBy('net_wage', 'desc')
        //     ->get();

        return DB::table('hr_employees as he')
            ->select('he.id as employee_id', 'hs.id as salary_id',
            DB::raw("COALESCE(hdt.name, 'N/A') as department_name"),
            DB::raw("COALESCE(hjt.name, 'N/A') as job_name"), 'e.username', 'hs.basic as basic_wage',

            DB::raw('COALESCE(p.total_penalties, 0) as total_penalties'),
            DB::raw('COALESCE(a.total_advances, 0) as total_advances'),
            DB::raw('COALESCE(al.total_allowances, 0) as total_allowances'),
            DB::raw('COALESCE(d.total_deducts, 0) as total_deducts'),
            DB::raw('COALESCE(r.total_rewards, 0) as total_rewards'),


            DB::raw('(hs.basic + COALESCE(al.total_allowances, 0) + COALESCE(r.total_rewards, 0)) - (COALESCE(p.total_penalties, 0) + COALESCE(a.total_advances, 0) + COALESCE(d.total_deducts, 0)) as net_wage'))
            ->join('employees as e', 'he.employee_id', '=', 'e.id')
            ->where('e.deleted_at',null)
            ->join('hr_departments as hd', 'he.department_id', '=', 'hd.id')
            ->leftJoin('hr_department_translations as hdt', function ($join) {
                $join->on('hd.id', '=', 'hdt.hr_department_id')->where('hdt.locale', '=', 'ar');
            })
            ->join('hr_jobs as hj', 'he.job_id', '=', 'hj.id')
            ->leftJoin('hr_job_translations as hjt', function ($join) {
                $join->on('hj.id', '=', 'hjt.hr_job_id')->where('hjt.locale', '=', 'ar');
            })
            ->join('hr_salaries as hs', 'he.id', '=', 'hs.employee_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_penalties FROM hr_penalties where MONTH(due_date) ='.$month.' and status =2 and payroll_id is null  GROUP BY employee_id) as p'), 'he.id', '=', 'p.employee_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_advances FROM hr_monthly_payments where MONTH(due_at) ='.$month.' and status =2 and payroll_id is null  GROUP BY employee_id) as a'), 'he.id', '=', 'a.employee_id')
            ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_allowances FROM hr_salary_allowances  GROUP BY salary_id) as al'), 'hs.id', '=', 'al.salary_id')
            ->leftJoin(DB::raw('(SELECT salary_id, SUM(amount) as total_deducts FROM hr_salary_deducts  GROUP BY salary_id) as d'), 'hs.id', '=', 'd.salary_id')
            ->leftJoin(DB::raw('(SELECT employee_id, SUM(amount) as total_rewards FROM hr_rewards where MONTH(due_date) ='.$month.' and status =2  and payroll_id is null GROUP BY employee_id) as r'), 'he.id', '=', 'r.employee_id')
            ->groupBy('he.id', 'hs.id', 'hs.basic', 'hdt.name', 'hjt.name', 'e.username', 'p.total_penalties', 'a.total_advances', 'al.total_allowances', 'd.total_deducts', 'r.total_rewards')
            ->orderBy('net_wage', 'desc')
            ->get();
    }
    // ->where('created_at',">=",$month)
    public function currency(): string
    {
        return HrSetting::first()->currency;
    }

    public function create_approvals($payroll, $approval_users = []): void
    {
        foreach ($approval_users as $data) {
            $payroll->payroll_approvals()->create([
                'user_id' => $data['user_id'],
                'sort' => $data['sort'] ?? 1,
                'is_current' => isset($data['is_current']),
            ]);
        }
    }





    public function destroy(HrPayroll $hrPayroll)
    {
        DB::beginTransaction();
        try {
            $id = $hrPayroll->id;
            $payrollEmployees = \Modules\HR\App\Models\HrPayrollEmployee::where('payroll_id', $id)->get();

            foreach ($payrollEmployees as $payrollEmployee) {
                // 2. حذف جميع المعاملات المرتبطة بكل موظف
                \Modules\HR\App\Models\HrPayrollTransaction::where('payroll_employee_id', $payrollEmployee->id)->delete();
            }

            // 3. حذف جميع سجلات الموظفين
            \Modules\HR\App\Models\HrPayrollEmployee::where('payroll_id', $id)->delete();

            // 4. إعادة تعيين payroll_id في الجداول المرتبطة
            // Allowances
            \Modules\HR\App\Models\HrSalaryAllowance::where('payroll_id', $id)->update(['payroll_id' => null]);

            // Deducts
            \Modules\HR\App\Models\HrSalaryDeduct::where('payroll_id', $id)->update(['payroll_id' => null]);

            // Penalties
            \Modules\HR\App\Models\HrPenalty::where('payroll_id', $id)->update(['payroll_id' => null]);

            // Advances
            \Modules\HR\App\Models\HrMonthlyPayment::where('payroll_id', $id)->update(['payroll_id' => null ,'type' => HrMonthlyPayment::TYPE_PENDING]);

            // Rewards
            \Modules\HR\App\Models\HrReward::where('payroll_id', $id)->update(['payroll_id' => null]);

            // 5. تحديث إعدادات HR إذا كانت هذه الرواتب هي الحالية
            $setting = \Modules\HR\App\Models\HrSetting::first();
            if ($setting && $setting->payroll_id == $id) {
                $setting->update([
                    'payroll_id' => null,
                    'due_payroll_at' => null,
                    'preparing_payroll' => false,
                    'next_payroll_date' => null,
                    'payroll_status' => \Modules\HR\App\Models\HrSetting::PAYROLL_STATUS_OPEN,
                ]);
            }

            // 6. حذف الموافقات المرتبطة (إن وجدت)
            \Modules\HR\App\Models\HrPayrollApproval::where('payroll_id', $id)->delete();

            // 7. حذف سجل الرواتب نفسه
            $hrPayroll->delete();

             activity()
                ->causedBy(auth()->user())
                ->on($hrPayroll)
                ->event(__('hr::models/hr_payrolls.fields.deleted_payroll'))
                ->log(__('hr::models/hr_payrolls.fields.deleted_payroll'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
