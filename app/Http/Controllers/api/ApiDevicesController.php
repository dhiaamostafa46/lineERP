<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\HR\App\Http\Requests\api\ApiLoginRequest;
use Modules\HR\App\Traits\ApiResponses;
use App\Http\Requests\api\ApiDeviceRegRequest;
use App\Models\DeviceSession;
use App\Repositories\DeviceSessionRepository;

class ApiDevicesController extends Controller
{
   
    // private $DeviceSessionRepository;

    // public function __construct(DeviceSessionRepository $DeviceSessionRepository)
    // {
    //     $this->DeviceSessionRepository = $DeviceSessionRepository;
    // }

     public function store($lang,ApiDeviceRegRequest $request)
    {  
          app()->setLocale($lang);
       $device =DeviceSession::where('device_serial',$request->get('device_id'))->first();
       if($device)
        {
             return response()->json( [
               'status_code'   =>"05",
                'message' => __('models/DeviceSessions.fields.exist'),
            ], 201);
       
        }
            $item = new DeviceSession();
        $item->user_id=auth()->user()->id;
        $item->device_name=$request->name ?? "mobile"." ".$request->os;
        $item->device_serial=$request->device_id;
        $item->device_token=$request->fcm_token;
        $item->device_type ="Mobile";
        $item->os=$request->os;
         $item->is_active=0;
         $item->save();
         return response()->json( [
               'status_code'   =>"00",
                'message' => __('models/DeviceSessions.fields.created'),
            ], 201);
       
    }
   
}