<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\ApiAdvanceRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrMonthlyPaymentRepository;
use App\Models\Employee;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Repositories\HrAdvanceRepository;
use Illuminate\Support\Facades\DB; // Added DB facade
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

class ApiAdvanceController extends Controller
{
    use ApiResponses;
    /** @var HrEmployeeRepository $hrEmployeeRepository*/
    private $hrEmployeeRepository;

    /** @var HrAdvanceRepository $advanceRepository */
    private $advanceRepository;
    /** @var HrMonthlyPaymentRepository $HrMonthlyPaymentRepository */
    private $HrMonthlyPaymentRepository;
  
  
     public function __construct(HrEmployeeRepository $HREmployeeRepository,HrAdvanceRepository $HRAdvanceRepository,HrMonthlyPaymentRepository $hrMonthlyPaymentRepository)
    {
      
        $this->hrEmployeeRepository = $HREmployeeRepository;
         $this->advanceRepository = $HRAdvanceRepository;
        $this->HrMonthlyPaymentRepository = $hrMonthlyPaymentRepository;
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
           $records =  $employee->hrEmployee->advances()->get();
           $result =null;
         
                
                      $records =HrAdvance::where('employee_id',$employee->hrEmployee->id)
                      ->whereDate('created_at', '>=', $from)
                      ->whereDate('created_at','<=', $to)->latest()->get();
                   
                

                
          
           $requests=[];
           $items=[];
            foreach($records as $record)
             {
                $insallt =$record->monthlyPayments()->get();
                
                foreach( $insallt as $it)
                {
                   $items[]=
                   [
                     "date"=>$it->due_at->format('m-Y'),
                     "amount"=>$it->amount,
                     "paid_status"=>$it->tpye,//	1 = Pending, 2 = Done
                    
                   ];
                 }
                  $requests[]=
                 [
                     "request_id"=>$record->id,
                     "request_no"=>"ADV-".Carbon::now()->format('y')."-".$record->id,
                     "request_date"=>$record->created_at->format('d-m-Y'),
                     "amount"=>$record->amount,
                     "reason"=>$record->reason,
                     "status"=>$record->status,//1 = Pending, 2 = Approved, 3 = Rejected
                  
                 ];
             }
            
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
           //$records =  $employee->hrEmployee->advances()->get();
            $record =HrAdvance::find($id);
         
              if(!$record)
            {
                 return response()->json([
                  'status_code'   => "103",
                  'message' => "no record found for provided id"
                  ], 200, [], JSON_UNESCAPED_UNICODE);
            }
                
          
           $requests=[];
           $items=[];
           
                $insallt =$record->monthlyPayments()->get();
                
                foreach( $insallt as $it)
                {
                   $items[]=
                   [
                     "date"=>$it->due_at->format('m-Y'),
                     "amount"=>$it->amount,
                     "paid_status"=>$it->type,//	1 = Pending, 2 = Done
                    
                   ];
                 }
                 
             
            
        return response()->json([
            'status_code'   => "00",
             'employee_name' =>  $employee->full_name,
            "request_id"=>$record->id,
                     "request_no"=>"ADV-".Carbon::now()->format('y')."-".$record->id,
                     "request_date"=>$record->created_at->format('d-m-Y'),
                     "amount"=>$record->amount,
                     "description"=>$record->description,
                     "reason"=>$record->reason,
                     "attachment"=>$record->getAttachmentUrlAttribute(),
                     "status"=>$record->status,//1 = Pending, 2 = Approved, 3 = Rejected
                     "insalltments"=>$items,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    
    public function store($lang,ApiAdvanceRequest $request) 
    {
         app()->setLocale($lang);
        $input = $request->all();
           $employee= auth()->user()->employee()->first();

            $input['employee_id'] =$employee->hrEmployee->id;
            $input['amount']=$request->amount;
            $input['due_at']=\Carbon\Carbon::now();
            $input['from_date']=$request->from_date;
            $input['to_date']=$request->end_at;
            $input['description']=$request->description;
            $input['reason']=$request->reason;
            $input['attachment']=$request->attachment;
  
            
             DB::beginTransaction();
              try {
            $advance = $this->advanceRepository->create($input);
           
            $lastdate="";
            $totalMonthlyAmount =0;
            foreach ($request->installments as $ins)
                 {
                    If($ins['date'] < $input['from_date'])
                    {
                              DB::rollBack();
                                 return response()->json([
                                   'status_code'   => "13" ,
                                    'message'   => "inalltment date should be after start date" 
                                 ], 200, [], JSON_UNESCAPED_UNICODE);
                    }
                     $totalMonthlyAmount += $ins['amount'];
                    $this->HrMonthlyPaymentRepository->create([
                        'hr_advance_id' => $advance->id,
                        'employee_id' => $advance->employee_id,
                        'due_at' => $ins['date'] . '-01',
                        'amount' => $ins['amount'],
                    ]);
                    
                       $lastdate = $ins['date'] . '-01';
                    
                 }
            
            $advance->to_date =$lastdate ?? $request->end_at;
            $advance->save();
            
                if($totalMonthlyAmount != $advance->amount)
                  {
                               //throw new HttpException(404, 'Item not found.');
                                DB::rollBack();
                                 return response()->json([
                                   'status_code'   => "12" ,
                                    'message'   => "total amount not equal installments amount" 
                                 ], 200, [], JSON_UNESCAPED_UNICODE);

                  }
              $this->advanceRepository->checkTracking(  $advance);
        DB::commit();
        return response()->json([
            'status_code'   => "00" ,
            'request_id'   => $advance->id,
            'request_no'=>"ADV-".Carbon::now()->format('y')."-".$advance->id,
            'message'   => __('messages.request_add') 
        ], 200, [], JSON_UNESCAPED_UNICODE);

         } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                                   'status_code'   => "500" ,
                                    'message'   =>$request->installments
                                 ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    

   
  
}