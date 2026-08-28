<?php

use App\Http\Controllers\api\ApiAbsentController;
use App\Http\Controllers\api\ApiAdvanceController;
use App\Http\Controllers\api\ApiAttendanceController;
use App\Http\Controllers\api\ApiAuthController;
use App\Http\Controllers\api\ApiDevicesController;
use App\Http\Controllers\api\ApiEmployeesController;
use App\Http\Controllers\api\ApiHolidaysController;
use App\Http\Controllers\api\ApiJustificationController;
use App\Http\Controllers\api\ApiNotificationsController;
use App\Http\Controllers\api\ApiPayrollsController;
use App\Http\Controllers\api\ApiPenaltiesController;
use App\Services\Firebase\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('auth/login/{lang}', [ApiAuthController::class, 'login']);
Route::post('/refresh', [ApiAuthController::class, 'refresh']);
Route::post('auth/sendOTP/{lang}', [ApiAuthController::class, 'sendOTP']);
Route::post('auth/verifyOTP/{lang}', [ApiAuthController::class, 'verifyOTPCode']);
Route::post('auth/resetPassword/{lang}', [ApiAuthController::class, 'resetPass']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();getEmployee
// });

Route::post('/device/create/{lang}', [ApiDevicesController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:create_device']);

Route::middleware(['auth:sanctum', 'ability:user:read'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // /////////Authoraization
    Route::post('auth/logout/{lang}', [ApiAuthController::class, 'logout']);
    Route::put('auth/changePassword/{lang}', [ApiAuthController::class, 'passwordChange']);
    Route::get('/getEmployee/{lang}', [ApiEmployeesController::class, 'getEmployee']);
    Route::get('/getSalary/{lang}', [ApiEmployeesController::class, 'getEmpSalary']);

    // /////attendance
    Route::get('attendance/getAttendance/{lang}', [ApiAttendanceController::class, 'getAttendance']);
    Route::get('attendance/getLoations/{lang}', [ApiAttendanceController::class, 'getPlace']);
    Route::get('attendance/getShift/{lang}', [ApiAttendanceController::class, 'getShift']);
    Route::post('attendance/checkIn/{lang}', [ApiAttendanceController::class, 'checkIn']);
    Route::post('attendance/checkOut/{lang}', [ApiAttendanceController::class, 'checkOut']);

    // //Holidyas
    Route::get('/leaves/getTypes/{lang}', [ApiHolidaysController::class, 'getTypes']);
    Route::get('/leaves/getRequests/{lang}', [ApiHolidaysController::class, 'getRequests']);
    Route::get('/leaves/getDetails/{lang}/{id}', [ApiHolidaysController::class, 'getDetails']);
    Route::get('/leaves/getBalance/{lang}', [ApiHolidaysController::class, 'getBalance']);
    Route::post('/leaves/new/{lang}', [ApiHolidaysController::class, 'store']);
    // //Advances
    Route::post('/loan/new/{lang}', [ApiAdvanceController::class, 'store']);
    Route::get('/loan/getRequests/{lang}', [ApiAdvanceController::class, 'getRequests']);
    Route::get('/loan/getDetails/{lang}/{id}', [ApiAdvanceController::class, 'getDetails']);
    // ///Justification
    Route::post('/correction/new/{lang}', [ApiJustificationController::class, 'store']);
    Route::get('/correction/getRequests/{lang}', [ApiJustificationController::class, 'getRequests']);

    // Penalties
    Route::get('/penalties/getRequests/{lang}', [ApiPenaltiesController::class, 'getRequests']);
    Route::get('/penalties/getDetails/{lang}/{id}', [ApiPenaltiesController::class, 'getDetails']);

    // Payrolls
    Route::get('/payrolls/getPayrolls/{lang}', [ApiPayrollsController::class, 'getAll']);
    Route::get('/payrolls/getDetails/{lang}/{id}', [ApiPayrollsController::class, 'getDetails']);

    // Notifications
    Route::get('/notifications/getRequests/{lang}', [ApiNotificationsController::class, 'getRequests']);
    Route::post('/notifications/readMark/{lang}/{id}', [ApiNotificationsController::class, 'markAsRead']);

    // //Absent Requests
    Route::post('/absents/new/{lang}', [ApiAbsentController::class, 'store']);
    Route::get('/absents/getRequests/{lang}', [ApiAbsentController::class, 'getRequests']);
    Route::get('/absents/getDetails/{lang}/{id}', [ApiAbsentController::class, 'getDetails']);

    // //Last Requests
    Route::get('/lastRequests/get/{lang}', [ApiEmployeesController::class, 'getLastReq']);

});

Route::get('/test-fcm', function (FirebaseNotificationService $firebase) {
    $token = 'dLHTFLa6RNa_kKSyRWUGEd:APA91bFV6m4l6RBotZ0d0-ywOOXF8X215PwJebmfLHiZYWEOlCMymzeMTLMkfeIDw3Hx5FFMebCUoDD3tOKxnItUpilLAzfqwmq7tXaJ2ZJ9s6CThn-RC4E';

    $result = $firebase->sendToToken(
        token: $token,
        title: 'إشعار تجريبي',
        body: 'test',
        data: [
            'type' => 'test',
            'screen' => 'home',
        ]
    );

    return response()->json($result);
});
