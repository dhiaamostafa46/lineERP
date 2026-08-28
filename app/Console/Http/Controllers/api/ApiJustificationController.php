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
use Modules\HR\App\Models\HrJustification;
use Modules\HR\App\Repositories\HrJustificationRepository;
use Illuminate\Support\Facades\DB; // Added DB facade
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiJustificationController extends Controller
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
           $records =  HrJustification::where('employee_id',$employee->HrEmployee->id)->get();
         
           $result =null;
                if (($request->filled('from')) && ($request->filled('to'))) {
                      $records =HrJustification::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=', $request->from)
                      ->whereDate('created_at','<=', $request->to)->get();
                   
                }

                
          //getAttachmentOriginalPathAttribute
          //getAttachmentUrlAttribute
          //getAttachmentInfoAttribute
           $requests=[];
            foreach($records as $record)
             {
                
                  $requests[]=
                 [
                     "request_id"=>$record->id,
                     "request_date"=>$record->created_at->format('d-m-Y'),
                     "reason"=>$record->reason,
                     "type"=>$record->type,//1 = late, 2 = EARLY_LEAVE, 3 = ABSENCE
                     "file_path"=>$record->getAttachmentUrlAttribute(),
                     "status"=>$record->status//1 = pending, 2 = approved, 3 = rejected
                 ];
             }
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            'requests'      =>$requests
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    
    public function store(ApiJutificationRequest $request) 
    {
        //  'shift_id' => 'required',
        //'reason' => 'required|string',
        //'type' => 'required|in:1,2,3,4', // 1 = late, 2 = early_leave, 3 = absence
        //'request_date' => 'required|date'

        $input = $request->all();
           $employee= auth()->user()->employee()->first();

            $input['employee_id'] =$employee->hrEmployee->id;
             DB::beginTransaction();
              try {
            $justif = $this->JustificationRepository->create($input);

           
              $this->JustificationRepository->checkTracking(   $justif);
        DB::commit();
        return response()->json([
            'status_code'   => "00" ,
            'request_id'   => $justif->id,
            'message'   => "request submited" 
        ], 200, [], JSON_UNESCAPED_UNICODE);

         } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                                   'status_code'   => "500" ,
                                    'message'   => "error" 
                                 ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    

    

   
  
}