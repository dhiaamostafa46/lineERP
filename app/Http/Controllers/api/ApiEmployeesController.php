<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrSalaryRepository;
use Modules\HR\App\Traits\ApiResponses;

class ApiEmployeesController extends Controller
{
    use ApiResponses;

    /** @var HrEmployeeRepository */
    private $hrEmployeeRepository;

    /** @var EmployeeRepository */
    private $employeeRepository;

    /** @var HrSalaryRepository */
    private $hrSalaryRepository;

    public function getEmployee($lang)
    {
        $employee = auth()->user()->employee()->first();
        app()->setLocale($lang);
        // get salary data :

        return response()->json([
            'status_code' => '00',
            'full_name' => $employee->full_name,
            'phone' => $employee->phone,
            'birthdate' => $employee->dob ?? null,
            'address' => $employee->address ?? null,
            'marital_status' => $employee->getMaritalStatusTextAttribute(),
            'identity_no' => $employee->identity->identity_no ?? null,
            'identity_expDate' => $employee->identity->identity_expired_at ?? null,
            'bank_name' => $employee->bank->bank_name ?? null,
            'bank_iban' => $employee->bank->iban ?? null,
            'job' => [
                'title' => $employee->hrEmployee?->job->name,
                'hiredate' => $employee->hrEmployee?->start_at ?? null,
                'branch' => $employee->branch?->name,
                'department' => $employee->hrEmployee->department?->name,
                'Direct_Manager' => $employee->hrEmployee->DirectManager ?? null,
                'attendance_type' => $employee->hrEmployee->attendance_type,
            ],
            'contract' => [
                'start_date' => $employee->hrEmployee->Contract->start_at ?? null,
                'end_date' => $employee->hrEmployee->Contract->end_at ?? null,
                'type' => $employee->hrEmployee?->Contract?->type?->translateOrNew($lang)?->name,
                'contract_no' => $employee->hrEmployee->Contract->qiwa_no ?? 0,
            ],

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getEmpSalary($lang)
    {
        app()->setLocale($lang);
        $employee = auth()->user()->employee;
        $salary = $employee->hrEmployee->salary;
        // $salary =HrSalary::find(159);
        $fullSalary = $salary->basic + $salary->totalAllowance() - $salary->totalDeduct();
        $allowances = [];
        $alowns = $salary->salary_allowances ?? null;
        if ($alowns != null) {
            foreach ($alowns as $allowan) {
                $allowances[] = [
                    'name' => $allowan->allowance->name ?? null,
                    'amount' => $allowan->amount ?? 0,
                ];

            }
        }
        $sal_deducts = $salary->salary_deducts ?? null;
        $deducts = [];
        if ($sal_deducts != null) {
            foreach ($sal_deducts as $deduct) {
                $deducts[] = [
                    'name' => $deduct->deduct->name ?? null,
                    'amount' => $deduct->amount ?? 0,
                ];

            }
        }

        return response()->json([
            'status_code' => '00',
            'employee_name' => $employee->full_name,
            'currency' => 'SAR',
            'total_salary' => $fullSalary,
            'basic_salary' => $salary->basic,
            'alowances' => $allowances,
            'deducts' => $deducts,

        ]);
    }

    public function update() {}

    public function getLastReq($lang)
    {
        $employee = auth()->user()->employee()->first();

        $lve = '';
        $lang == 'ar' ? $lve = 'إجازة' : $lve = 'leave';
        $leave = DB::table('hr_holidays')
            ->select(
                'id',
                DB::raw("'$lve' as type"),
                'status',
                'created_at'
            )->where('employee_id', $employee->hrEmployee->id)->latest()
            ->limit(10);
        $adv = '';
        $lang == 'ar' ? $adv = 'سلفة' : $adv = 'advance';
        $advance = DB::table('hr_advances')
            ->select(
                'id',
                DB::raw("'$adv' as type"),
                'status',
                'created_at'
            )->where('employee_id', $employee->hrEmployee->id)->latest()
            ->limit(10);

        $corr = '';
        $lang == 'ar' ? $corr = 'تصحيح حضور' : $corr = 'attendance correction';
        $corrections = DB::table('hr_justifications')
            ->select(
                'id',
                DB::raw("'$corr' as type"),
                'status',
                'created_at'
            )->where('employee_id', $employee->hrEmployee->id)->latest()
            ->limit(10);

        $requests = $leave
            ->unionAll($advance)
            ->unionAll($corrections);

        $results = DB::query()
            ->fromSub($requests, 'all_requests')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status_code' => '00',
            'requests' => $results,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
