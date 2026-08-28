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
use Modules\HR\App\Repositories\HrHolidayRepository;
use Modules\HR\App\Models\HrHoliday;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
class ApiHolidaysController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var HrHolidayRepository $holidayRepository */
    private $holidayRepository;
     /** @var HrHolidayBalanceRepository $hrHolidayBalanceRepository*/
     private $holdayBalanceRepository;
     public function __construct(HrHolidayBalanceRepository $hrholidayBalanceRepository,HrEmployeeRepository $HREmployeeRepository,HrHolidayRepository $HRholyRepository)
    {
        $this->holdayBalanceRepository = $hrholidayBalanceRepository;
        $this->hrEmployeeRepository = $HREmployeeRepository;
         $this->holidayRepository = $HRholyRepository;
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
           $records =  $employee->hrEmployee->holidays()->get();
        //    $blances =  $employee->hrEmployee->HolidayBalance()->get();
                 $result =null;
              

                      $records =HrHoliday::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=',  $from)
                      ->whereDate('created_at','<=', $to)->get();
                   
                
           $requests=[];
            foreach($records as $record)
             {
                  $requests[]=
                 [
                     "request_id"=>$record->id,
                     "request_no"=>"LEV-".Carbon::now()->format('y')."-".$record->id,
                     "type"=>$record->type->translateOrNew($lang)->name,
                     "from"=>$record->from_at->format('d-m-Y'),
                     "to"=>$record->end_at->format('d-m-Y'),
                     "status"=>$record->status,//	1 = Pending, 2 = Approved, 3 = Rejected	
                      "reason"=>$record->reason,
                     "request_date"=>$record->created_at->format('d-m-Y')
                 ];
             }
            // $items=[];
            // foreach( $blances as $it)
            //  {
            //     $valid = ($it->annual_balance + $it->previous_balance) -$it->balance;
            //       if($it->annual_balance == 0)
            //         { $valid = $it->allowed  -$it->balance;}
            //      $items[]= 
            //      [
            //          "type"=>$it->holidayType->translateOrNew("ar")->name,
            //          "annual_balance"=>$it->annual_balance,
            //          "consumed_days"=>$it->balance,
            //          "available"=>$valid,
            //          "previous_balance"=>$it->previous_balance
            //      ];
            //  }
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            'requests'      =>$requests
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

    public function getBalance()
    {
         $employee= auth()->user()->employee()->first();
          $blances =  $employee->hrEmployee->HolidayBalance()->get();
            

          
          $leaveBalance =[];
              foreach( $blances as $it)
              {
                   if(($it->holidayType->translateOrNew("en")->name == "annual") || ($it->holidayType->id ==1))
                   {
                     $valid = ($it->annual_balance + $it->previous_balance) -$it->balance;
                      if($it->annual_balance == 0)
                        { $valid = $it->allowed  -$it->balance;}
                    $leaveBalance[]= 
                          [
                          "annual_balance"=>$it->annual_balance,
                          "consumed_days"=>$it->balance,
                           "available"=>$valid,
                           "previous_balance"=>$it->previous_balance
                          ];
                   }
              }
              if(count($leaveBalance) == 0)
                {
                    return response()->json([
                       'status_code'   => "500",
                       'message' => $leaveBalance         
        ], 500, [], JSON_UNESCAPED_UNICODE);
                }
               return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
             "annual_balance"=> $leaveBalance[0]["annual_balance"],
             "consumed_days"=> $leaveBalance[0]["consumed_days"],
             "available"=> $leaveBalance[0]["available"],
             "previous_balance"=>$leaveBalance[0]["previous_balance"]
                         
        ], 200, [], JSON_UNESCAPED_UNICODE);
           
    }
    
     public function getTypes($lang) 
    {
            
           $records =  HrHolidayType::where('status',2)->get();
                $items=[];
            foreach($records as $it)
             {
                 $items[]=
                 [
                    "id"=>$it->id, 
                    "name"=>$it->translateOrNew($lang)->name,
                 ];
             }
        return response()->json([
            'status_code'   => "00",
            'types'      => $items
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(CreateHrHolidayRequest $request) 
    {
        //employee_id type_id from_at end_at  comments attachment
        $input = $request->all();
           $employee= auth()->user()->employee()->first();

          // Store file if exists
    // $attachmentPath = null;
    // if ($request->hasFile('attachment')) {
    //     $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
    // }
            $input['employee_id'] =$employee->hrEmployee->id;
            try {
         $holiday = $this->holidayRepository->create($input);
        $this->holidayRepository->checkTracking($holiday);

        return response()->json([
            'status_code'   => "00" ,
            'request_id'   => $holiday->id,
            "request_no"=>"LEV-".Carbon::now()->format('y')."-".$holiday->id,
            'message'   => "request submited" 
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