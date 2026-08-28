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
         $request->validate([
        'from' => 'nullable|date',
        'to'   => 'nullable|date|after_or_equal:from'
          ]);
        
           app()->setLocale($lang);
           $employee= auth()->user()->employee()->first();
           $records =  HrPenalty::where('employee_id',$employee->HrEmployee->id)->get();
         
           $result =null;
                if (($request->filled('from')) && ($request->filled('to'))) {
                      $records =HrPenalty::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=', $request->from)
                      ->whereDate('created_at','<=', $request->to)->get();
                   
                }

                
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
                         "due_date"=>$record->due_date->format('m-Y'),
                         "status"=>$record->status//1 = pending, 2 = approved, 3 = rejected
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
                     "status"=>$record->status//1 = pending, 2 = approved, 3 = rejected
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    

    

   
  
}