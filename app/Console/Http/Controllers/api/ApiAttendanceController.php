<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\ApiAttCheckInRequest;
use App\Http\Requests\api\ApiGetAttendanceRequest;
use Modules\HR\App\Traits\ApiResponses;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Repositories\HrAttendanceRepository;
use Modules\HR\App\Models\HrPlace;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
class ApiAttendanceController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var HrAttendanceRepository $attendRepository*/
    private $attendRepository;
   
     public function __construct(HrAttendanceRepository $HrAttendance,HrEmployeeRepository $HREmployeeRepository)
    {
        $this->attendRepository = $HrAttendance;
        $this->hrEmployeeRepository = $HREmployeeRepository;
    }

    public function getAttendance(Request $request)
    {
        
         $to   = $request->query('to', Carbon::today()->toDateString());
          $from = $request->query(
        'from',
        Carbon::parse($to)->subDays(6)->toDateString() // آخر 7 أيام
    );
    $request->merge([
        'from' => $from,
        'to'   => $to,
    ]);

     $request->validate([
        'from' => ['nullable', 'date_format:Y-m-d'],
        'to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from']
          ]);
                   
    
        $employee= auth()->user()->employee()->first();
        
        $Attendance = $this->attendRepository->EmployeePresenceSearch(
            (object) [
                'employee_id' => $employee->hrEmployee->id,
                'start_date' => $from ,
                'end_date' =>$to,
            ],
        );
        $records = [];
        foreach($Attendance as $item)
        {
            $records[] = 
            [
                "date"=>$item->date,
                "shift_st"=>$item->shift_from,
                "shift_end"=>$item->shift_to,
                "check_in"=>$item->first_check_in,
                "check_out"=>$item->last_check_out,
                "lately_check_in"=>$item->min_delay,//in seconds
                "early_Check_out"=>$item->min_early_leave//in seconds
            ];
        }
        return response()->json([
            'status_code'   => "00",
            'attendance_records' => $records
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function checkIn(ApiAttCheckInRequest $request)
    {
          $place = HrPlace::findOrFail($request->location_id);
           $result = $this->attendRepository->presence($place,$request);
          if($result["code"] == "00")
            {
                 return response()->json([
            'status_code'   => "00",
            'message' => $result["message"]
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
            }

             return response()->json([
            'status_code'   => "06",
            'error' => $result["message"]
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function checkOut(ApiAttCheckInRequest $request)
    {
          $place = HrPlace::findOrFail($request->place_id);
           $result = $this->attendRepository->checkout($place,$request);
           if($result["code"] == "00")
            {
                 return response()->json([
            'status_code'   => "00",
            'message' => $result["message"]
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
            }

             return response()->json([
            'status_code'   => "06",
            'error' => $result["message"]
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    
    public function getPlace($lang)
    {
          $employee= auth()->user()->employee()->first();
          $place =  $this->attendRepository->Place($employee->hrEmployee->id);
            //$place =  $this->attendRepository->Place(220);
          
        $records = [];
        foreach($place as $item)
        {
            $records[] = 
            [
                "id"=>$item->id,
                "name"=>$item->name,
                "address"=>$item->address
               
            ];
        }
        return response()->json([
            'status_code'   => "00",
            'employee_name' =>  $employee->full_name,
            'locations' => $records
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
     public function getShift()
    {
          $employee= auth()->user()->employee()->first();
           $records =  $employee->hrEmployee->shift()->first();
           $shifts=$this->hrEmployeeRepository->shifts();
          
      
        // foreach($place as $item)
        // {
            $shits=[];
            foreach($records->shifts as $it)
             {
                 $shits[]=
                 [
                     "from"=>$it->from,
                     "to"=>$it->to
                 ];
             }
            // $records[] = 
            // [
                
            //     "work_hours"=>$place->work_hours,
            //     "shifts"=>$shits
               
            // ];
        //}
        return response()->json([
            'status_code'   => "00",
            'employee_name' =>  $employee->full_name,
            'work_hours'   => $records->work_hours,
            "shifts"=>$shits,
            'work_days'   => $records->work_days,
            
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
   
  
}