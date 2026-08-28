<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\App\Http\Requests\api\ApiLoginRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Repositories\HrSalaryRepository;
use App\Models\Employee;
use Modules\HR\App\Models\HrPayroll;
use Carbon\Carbon;
class ApiPayrollsController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var EmployeeRepository $employeeRepository*/
    private $employeeRepository;
    
      /** @var HrPayrollEmployeeRepository $hrPayrollEmployeeRepository*/
    private $hrPayrollEmployeeRepository;
     
     public function __construct(HrPayrollEmployeeRepository $hrPayrollEmployeeRepo)
    {
        $this->hrPayrollEmployeeRepository = $hrPayrollEmployeeRepo;
    }
    
    public function getAll($lang,Request $request) 
    {
         $employee= auth()->user()->employee()->first();

          $request->validate([
        'from' => 'required|date',
        'to'   => 'required|date|after_or_equal:from'
          ]);
          $startDate = Carbon::parse($request->input('from'))->startOfDay(); // e.g., 2023-01-01 00:00:00
$endDate = Carbon::parse($request->input('to'))->endOfDay();     // e.g., 2023-01-31 23:59:59
//   $payRoll = HrPayroll::where('payroll_date',">=",$request->from)
//   ->where('payroll_date',"<=",$request->to)->get();
  $payRolls =HrPayroll::whereBetween('payroll_date',[$startDate,$endDate])->where('status',3)->get();
      
       
            
            $records =[];
          foreach( $payRolls as $roll)
            {
                $rows = HrPayrollEmployee::where('payroll_id',$roll->id)->where('employee_id',$employee->hrEmployee->id)->get();
                foreach( $rows as $item){}
                  $records[]=
                  [
                    "payroll_id"=>$roll->id,
                    "payroll_date"=> $roll->payroll_date->format('d-m-Y'),
                     "basic_salary"=> $item->basic_wage ?? 0,
                     "total_allowances"=> $item->total_allowances ?? 0,
                     "total_deducts"=> $item->total_deducts ?? 0,
                     "total_penalties"=> $item->total_penalties ?? 0,
                     "total_advances"=> $item->total_advances ?? 0,
                     "total_rewards"=> $item->total_rewards ?? 0,
                     "net_wage"=> $item->net_wage ?? 0,
                  ];
            }

        //get salary data :
        // $requests=[];
               
        //           $requests[]=
        //          [
        //              "basic_salary"=>$record->basic_wage ?? 0,
        //              "total_allowances"=>$record->total_allowances ?? 0,
        //              "total_deducts"=>$record->total_deducts ?? 0,
        //              "total_penalties"=>$record->total_penalties ?? 0,
        //              "total_advances"=>$record->total_advances ?? 0,
        //              "total_rewards"=>$record->total_rewards ?? 0,
        //              "net_wage"=>$record->net_wage ?? 0,//1 = Pending, 2 = Approved, 3 = Rejected
               
        //          ];
            
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            'payrolls'      =>  $records,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    
     public function getDetails($lang,$id)
    {
          $employee= auth()->user()->employee()->first();
        $roll = HrPayrollEmployee::where('payroll_id',$id)
        ->where('employee_id',$employee->hrEmployee->id)
        ->first();
               
                   $records[]=
                   [
                     "payroll_id"=>$roll->payroll->id,
                     "payroll_date"=> $roll->payroll->payroll_date->format('d-m-Y'),
                     "basic_salary"=> $roll->basic_wage ?? 0,
                     "total_allowances"=> $roll->total_allowances ?? 0,
                     "total_deducts"=> $roll->total_deducts ?? 0,
                     "total_penalties"=> $roll->total_penalties ?? 0,
                     "total_advances"=> $roll->total_advances ?? 0,
                     "total_rewards"=> $roll->total_rewards ?? 0,
                     "net_wage"=> $roll->net_wage ?? 0,
                   ];
                    return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            'payrolls'      => $records,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    

   
  
}