<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\App\Http\Requests\CreateHrHolidayRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrHolidayBalanceRepository;
use App\Models\Employee;
use Modules\HR\App\Models\HrHolidayType;
use Modules\HR\App\Repositories\HrAbsentRequestRepository;
use Modules\HR\App\Models\HrHoliday;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Modules\HR\App\Models\HrAbsentRequests;
class ApiAbsentController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var HrAbsentRequestRepository $absentRepository */
    private $absentRepository;
     /** @var HrHolidayBalanceRepository $hrHolidayBalanceRepository*/
     private $holdayBalanceRepository;
     public function __construct(HrEmployeeRepository $HREmployeeRepository,HrAbsentRequestRepository $HRabsentRepository)
    {
        $this->hrEmployeeRepository = $HREmployeeRepository;
         $this->absentRepository = $HRabsentRepository;
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
                      $records =HrAbsentRequests::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=',  $from)
                      ->whereDate('created_at','<=', $to)->latest()->get();
                   
                
           $requests=[];
            foreach($records as $record)
             {
                  $requests[]=
                 [
                     "request_id"=>$record->id,
                     "request_no"=>"ABS-".Carbon::now()->format('y')."-".$record->id,
                     "reason"=>$record->details,
                     "from"=>$record->from_at,
                     "to"=>$record->end_at,
                     "status"=>$record->status,//	1 = Pending, 2 = Approved, 3 = Rejected	
                     "status_text"=>$record->status_text ?? null,
                     "request_date"=>$record->created_at->format('d-m-Y')
                 ];
             }
           
        return response()->json([
            'status_code'   => "00",
            'employee_name' =>  $employee->full_name,
            'requests'      => $requests
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    
    public function getDetails($lang,$id) 
    {
        
        
           app()->setLocale($lang);
           $employee= auth()->user()->employee()->first();
          // $records =  HrPenalty::where('employee_id',$employee->HrEmployee->id)->get();
          $record =HrHoliday::find($id);

           if(!$record)
            {
                 return response()->json([
                  'status_code'   => "103",
                  'message' => "no record found for provided id"
                  ], 200, [], JSON_UNESCAPED_UNICODE);
            }
                  
                   
             
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
                     "request_id"=>$record->id,
                     "request_no"=>"LEV-".Carbon::now()->format('y')."-".$record->id,
                     "type"=>$record->type->translateOrNew($lang)->name,
                     "from"=>$record->from_at->format('d-m-Y'),
                     "to"=>$record->end_at->format('d-m-Y'),
                     "status"=>$record->status,//	1 = Pending, 2 = Approved, 3 = Rejected	
                     "description"=>$record->comments,
                     "attachment"=>$record->getAttachmentUrlAttribute(),
                     "request_date"=>$record->created_at->format('d-m-Y')
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store($lang,Request $request) 
    {
         app()->setLocale($lang);
        //$succ_msg =  __('messages.request_add');
        //employee_id type_id from_at end_at  comments attachment
        $request->validate([
              'from' => 'required|string', // Assuming time is in H:i format
              'to' => 'required|string', // Assuming time is
              'request_date' => 'required|date',
              'reason' => 'required|string',
        ]);
        
        $input =[];
         $employee= auth()->user()->employee()->first();
        $input['employee_id'] = $employee->hrEmployee->id;
        $input['from_at'] = $request->from;
        $input['end_at'] = $request->to;
        $input['details'] = $request->reason;
        $input['request_date'] = $request->request_date;  
          // Store file if exists
    // $attachmentPath = null;
    // if ($request->hasFile('attachment')) {
    //     $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
    // }
           
            try {
                
         $absent = $this->absentRepository->create($input);
              
        return response()->json([
            'status_code'   => "00" ,
            'request_id'   => $absent->id,
            "request_no"=>"ABS-".Carbon::now()->format('y')."-".$absent->id,
            'message'   => __('messages.request_add') 
        ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {
           
            // MySQL duplicate entry error code
    if ($e->errorInfo[1] == 1062) {
        return response()->json([
            'status_code'   => "14",
             'message'   => "repeated request" 
            ], 409, [], JSON_UNESCAPED_UNICODE);
        
    }

        }
    }

    

   
  
}