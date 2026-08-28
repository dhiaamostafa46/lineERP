<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\App\Http\Requests\api\ApiLoginRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrHolidayBalanceRepository;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Repositories\HrSalaryRepository;
use App\Models\Employee;
class ApiEmployeesController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var EmployeeRepository $employeeRepository*/
    private $employeeRepository;
     /** @var HrSalaryRepository $hrSalaryRepository*/
     private $hrSalaryRepository;
    
    public function getEmployee($lang) 
    {
         $employee= auth()->user()->employee()->first();
        //get salary data :
       
        return response()->json([
            'status_code'   => "00",
                'full_name'=>$employee->full_name,
                'phone' =>$employee->phone,
                'birthdate' =>$employee->dob??null,
                'address' =>$employee->address??null,
                'marital_status'=>$employee->getMaritalStatusTextAttribute(),
                'identity_no'=>$employee->identity->identity_no??null,
                'identity_expDate'=>$employee->identity->identity_expired_at??null,
                'bank_name'=>$employee->bank->bank_name??null,
                'bank_iban'=>$employee->bank->iban??null,
                'job'=>
                 [
                    'title'=>$employee->hrEmployee->job->translateOrNew($lang)->name,
                    'hiredate'=>$employee->hrEmployee->start_at??null,
                    'branch'=>$employee->branch->translateOrNew($lang)->name,
                    'department'=>$employee->hrEmployee->department->translateOrNew($lang)->name,
                    'Direct_Manager'=>$employee->hrEmployee->DirectManager??null,
                    'attendance_type'=>$employee->hrEmployee->attendance_type
                 ],
                 'contract'=>
                 [
                    'start_date'=>$employee->hrEmployee->Contract->start_at??null,
                    'end_date'=>$employee->hrEmployee->Contract->end_at??null,
                    'type'=>$employee->hrEmployee->Contract->type->translateOrNew($lang)->name??null,
                    'contract_no'=>$employee->hrEmployee->Contract->qiwa_no??0,
                 ]
                
            
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function getEmpSalary($lang)
    {
         $employee= auth()->user()->employee;
          $salary =$employee->hrEmployee->salary;
           //$salary =HrSalary::find(159);
          $fullSalary =$salary->basic  + $salary->totalAllowance()  - $salary->totalDeduct();
        $allowances =[];
        $alowns =$salary->salary_allowances ?? null;
        if($alowns != null)
        {
             foreach ( $alowns as $allowan)
            {
               $allowances[] = [
                    "name" => $allowan->allowance->translateOrNew($lang)->name ?? null,
                    "amount" => $allowan->amount ?? 0
                    ];

            }
        }
         $sal_deducts =$salary->salary_deducts ?? null;
         $deducts =[];
        if($sal_deducts != null)
        {
             foreach ( $sal_deducts as $deduct)
            {
               $deducts[] = [
                    "name" => $deduct->deduct->translateOrNew($lang)->name ?? null,
                    "amount" => $deduct->amount ?? 0
                    ];

            }
        }
        
          return response()->json([
            'status_code'   => "00",
            'employee_name' => $employee->full_name,
            'currency' => "SAR",
            'total_salary' => $fullSalary,
            'basic_salary' => $salary->basic,
            'alowances'=>$allowances,
            'deducts'=>$deducts
            
        ]);
    }
     public function update()
    {
        
    }
    

   
  
}