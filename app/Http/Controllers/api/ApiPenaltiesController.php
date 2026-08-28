<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\ApiJutificationRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrMonthlyPaymentRepository;
use App\Models\Employee;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Repositories\HrJustificationRepository;
use Illuminate\Support\Facades\DB; // Added DB facade
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

class ApiPenaltiesController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var HrJustificationRepository $JustificationRepository */
    private $JustificationRepository;
    /** @var HrMonthlyPaymentRepository $HrMonthlyPaymentRepository */
    private $HrMonthlyPaymentRepository;
  
  
     public function __construct(HrEmployeeRepository $HREmployeeRepository,HrJustificationRepository $JustificationRepository)
    {
      
        $this->hrEmployeeRepository = $HREmployeeRepository;
         $this->JustificationRepository = $JustificationRepository;
    }
    public function getRequests($lang,Request $request) 
    {    
         
         $to   = $request->query('to', Carbon::today()->toDateString());
          $from = $request->query(
        'from',
        Carbon::now()->startOfMonth()->toDateString() // from start of month
    );
    $request->merge([
        'from' => $from,
        'to'   => $to,
    ]);

     $request->validate([
        'from' => ['nullable', 'date_format:Y-m-d'],
        'to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from']
          ]);
           app()->setLocale($lang);
           $employee= auth()->user()->employee()->first();
        
         
           $result =null;
                
                      $records =HrPenalty::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=', $from)
                      ->whereDate('created_at','<=', $to)->get();
                   
                

                
          //getAttachmentOriginalPathAttribute
          //getAttachmentUrlAttribute
          //getAttachmentInfoAttribute
           $requests=[];
            foreach($records as $record)
             {
                if($record->status == 2)
                {
                
                      $requests[]=
                     [
                         "id"=>$record->id,
                         "amount"=>$record->amount,
                         "details"=>$record->description,
                         "due_date"=>$record->due_date->format('m-Y'),
                         
                     ];
                 }
             }
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            'penalties'      => $requests
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    
    public function getDetails($lang,$id) 
    {
        
        
           app()->setLocale($lang);
           $employee= auth()->user()->employee()->first();
          // $records =  HrPenalty::where('employee_id',$employee->HrEmployee->id)->get();
          $record =HrPenalty::find($id);

          if(!$record)
            {
                 return response()->json([
                  'status_code'   => "103",
                  'employee_name' =>  $employee->full_name,
                  "id"=>$record->id
                  ], 200, [], JSON_UNESCAPED_UNICODE);
            }
                  
                   
             
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
           "id"=>$record->id,
                     "date"=>$record->created_at->format('d-m-Y'),
                     "amount"=>$record->amount,
                     "deatils"=>$record->description,//1 = late, 2 = EARLY_LEAVE, 3 = ABSENCE
                     "due_date"=>$record->due_date->format('m-Y'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    

    

   
  
}