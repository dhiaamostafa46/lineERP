<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\ApiChangePasswordRequest;
use App\Models\RefreshToken;
use App\Models\User;
use App\Rules\SaudiPhoneExists;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\HR\App\Traits\ApiResponses;

class ApiAuthController extends Controller
{
    use ApiResponses;

    /**
     * POST /api/login
     * Returns access + refresh tokens
     */
    public function login($lang, Request $request)
    {
        app()->setLocale($lang);
        $phoneRule = new SaudiPhoneExists('users', 'phone');
        $request->validate([
            'phone' => ['required', $phoneRule],
            'password' => 'required',
        ]);
        $actualPhone = $phoneRule->getFoundPhone();
        $credentials = ['phone' => $actualPhone, 'password' => $request->get('password')];

        if (! Auth::attempt($credentials)) {
            return response()->json(
                [
                    'status_code' => '01',
                    'message' => __('messages.login_failed'),
                ],
                401,
            );
        }

        $user = Auth::user();

        if ($user->status != User::STATUS_ACTIVE) {
            Auth::logout();

            return response()->json(
                [
                    'status_code' => '05',
                    'message' => __('auth.account_inactive'),
                ],
                403,
            );
        }

        $roles = $user->getRoleNames();
        $isAdmin = null;
        if ($roles->count() === 1 && $roles->first() === 'موظف') {
            $isAdmin = false;
        } else {
            $isAdmin = true;
        }

        $code = '00';
        $httpCode = 200;
        $error = null;
        $employee = $user->employee()->first();

        // ////////////////////By saeed For aApple test mobile app////////////////////////

        if (($actualPhone == '966535331597') || ($actualPhone == '0535331597')) {
            $tokens = self::createToken($user, 'login');
            $accessToken = $tokens['access'];
            $error = __('messages.login_success');

            return response()->json(
                [
                    'status_code' => '00',
                    'message' => $error,
                    'access_token' => $accessToken,
                    'expires_in' => 3600, // seconds (client should treat access token as ~1h TTL)
                    'full_name' => $employee->full_name,
                    'is_admin' => $isAdmin,
                    'app' => self::getVersion(),
                ],
                $httpCode,
            );
        }

        // /////////////////////////////////////////////////////////////////////////
        // $device =$user->deviceSessions->where('user_id',5000)->orWhere('device_serial',$request->get('device_refrence'))->get();
        $device = $user->deviceSessions()->get();
        $getdevce = $device->where('device_serial', $request->get('device_id'))->sortByDesc('created_at')->first() ?? false;
        $accessToken = null;
        if (! $getdevce) {
            $code = '02';
            $httpCode = 403;
            $tokens = self::createToken($user, 'device');
            $accessToken = $tokens['access'];
            $error = __('models/DeviceSessions.fields.not_registered');

            // throw new \Exception("device not registered", 02);
            return response()->json(
                [
                    'status_code' => $code,
                    'message' => $error,
                    'token' => $accessToken,
                ],
                $httpCode,
            );
        } else {
            $active = $getdevce->is_active ?? 0;
            if ($active) {
                // if device exist and active update FCM_Token
                $getdevce->device_token = $request->get('fcm_token');
                $getdevce->save();
                $tokens = self::createToken($user, 'login');
                $accessToken = $tokens['access'];
            } else {
                // if device exist but not active update FCM_Token
                $code = '04';
                $httpCode = 403;
                $error = __('models/DeviceSessions.fields.not_approved');

                return response()->json(
                    [
                        'status_code' => $code,
                        'message' => $error,
                    ],
                    $httpCode,
                );
            }
        }

        // /Check Old tokens with name "mobile"
        // $existingToken = $user->tokens()->where('name', 'mobile-app')->where('expires_at', '>', now())->first();

        // $refreshToken =$tokens["refresh"];
        $error = __('messages.login_success');

        return response()->json(
            [
                'status_code' => $code,
                'message' => $error,
                'access_token' => $accessToken,
                'expires_in' => 3600, // seconds (client should treat access token as ~1h TTL)
                'full_name' => $employee->full_name,
                'is_admin' => $isAdmin,
                'app' => self::getVersion(),
            ],
            $httpCode,
        );
    }

    public function sendOTP($lang, Request $request)
    {
        app()->setLocale($lang);
        // Validate the phone number exists in users table

        $phoneRule = new SaudiPhoneExists('users', 'phone');
        $request->validate([
            'phone' => ['required', $phoneRule],
        ]);
        $actualPhone = $phoneRule->getFoundPhone();
        $request->merge(['phone' => $actualPhone]); // Replace with actual phone for further processing
        $user = User::where('phone', $actualPhone)->first();
        // $request->validate([
        //     'phone' => 'required|exists:users,phone', // تحقق من أن الرقم موجود
        // ]);

        // $user =User::where('phone' ,$request->phone)->first();

        // Retrieve and format the phone number
        $phone = $request->phone;

        // Check if phone number starts with '05'
        if (strpos($phone, '05') === 0 && strpos($phone, '966') !== 0) {
            $phone = '966'.substr($phone, 1); // حذف '0' وإضافة '966' في حال لم يكن الرقم يبدأ بها
        }

        // Generate a unique token
        $token = Str::random(60);

        // Generate a random OTP (6 digits)
        $OTP = mt_rand(1000, 9999); // Generate a random OTP

        // Store the token and OTP in a custom password_resets table
        \DB::table('password_resets')->insert([
            'phone' => $phone, // استخدم الرقم الجديد
            'email' => $user->email,
            'OTP' => $OTP,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(4),
            'created_at' => now(),
        ]);

        // Prepare to send the OTP via SMS
        $curl = curl_init();

        $req = json_encode([
            'src' => 'Evix',
            'dests' => [$phone],
            'body' => __('lang.your_code')." $OTP",
            'priority' => 0,
            'delay' => 0,
            'validity' => 0,
            'maxParts' => 0,
            'dlr' => 0,
            'prevDups' => 0,
            'msgClass' => 'transactional',
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.oursms.com/msgs/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer xZX_3oI0jaf-2Q3gWF3N'],
        ]);

        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            return response()->json(['error' => 'cURL error: '.curl_error($curl)], 500);
        }

        curl_close($curl); // Close the cURL session

        $obj = json_decode($response);

        // Check if the message was accepted
        if (isset($obj->accepted) && $obj->accepted == 1) {
            // return view('auth.OTP')->with('token', $token)->with('phone', $phone); // Show OTP input form

            return response()->json([
                'status_code' => '00',
                'message' => __('auth.otp_sent'),
                'expires_in' => 120, // seconds (client should treat OTP token as ~2M TTL)
            ]);
        }

        // return back()->with('error', 'فشل إرسال OTP.'); // Return with an error message
        $code = '07';
        $error = __('auth.otp_fail');

        return response()->json([
            'status_code' => $code,
            'message' => $error,
        ]);
    }

    // Step 2: Verify Reset Code

    public function verifyOTPCode($lang, Request $request)
    {
        app()->setLocale($lang);
        $phoneRule = new SaudiPhoneExists('users', 'phone');
        $request->validate([
            'phone' => ['required', $phoneRule],
            'code' => 'required|digits:4',
        ]);
        // $phone = $request->phone;
        $phone = $phoneRule->getFoundPhone();
        // // Check if phone number starts with '05'
        // if (strpos($phone, '05') === 0 && strpos($phone, '966') !== 0) {
        //     $phone = '966' . substr($phone, 1); // حذف '0' وإضافة '966' في حال لم يكن الرقم يبدأ بها
        // }

        // //by saeed for test data/////////
        if ($phone == '966535331597' && $request->code == '6045') {
            return response()->json([
                'status_code' => '00',
                'message' => __('auth.otp_valid'),
            ]);
        }
        // //////////////////////////////
        $record = \DB::table('password_resets')->where('phone', $phone)->where('OTP', $request->code)->first();

        if (! $record) {
            return response()->json(
                [
                    'status_code' => '08',
                    'message' => __('auth.otp_Invalid'),
                ],
                400,
            );
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(
                [
                    'status_code' => '09',
                    'message' => __('auth.otp_expired'),
                ],
                400,
            );
        }

        return response()->json([
            'status_code' => '00',
            'message' => __('auth.otp_valid'),
        ]);
    }

    public function resetPass($lang, Request $request)
    {
        app()->setLocale($lang);
        $phoneRule = new SaudiPhoneExists('users', 'phone');
        $request->validate([
            'phone' => ['required', $phoneRule],
            'code' => 'required|digits:4',
            'password' => 'required|min:6|confirmed',
        ]);
        // $phone = $request->phone;
        $phone = $phoneRule->getFoundPhone();
        // Check if phone number starts with '05'
        // if (strpos($phone, '05') === 0 && strpos($phone, '966') !== 0) {
        //     $phone = '966'.substr($phone, 1); // حذف '0' وإضافة '966' في حال لم يكن الرقم يبدأ بها
        // }
        $record = \DB::table('password_resets')->where('phone', $phone)->where('OTP', $request->code)->first();

        if (! $record) {
            return response()->json(
                [
                    'status_code' => '08',
                    'message' => 'Invalid reset code',
                ],
                400,
            );
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(
                [
                    'status_code' => '09',
                    'message' => __('auth.otp_expired'),
                ],
                400,
            );
        }

        User::where('phone', $request->phone)->update([
            'password' => Hash::make($request->password),
        ]);

        // Remove old reset code
        \DB::table('password_resets')->where('phone', $phone)->delete();

        return response()->json([
            'status_code' => '00',
            'message' => __('auth.pass_reset'),
        ]);
    }

    public function passwordChange($lang, ApiChangePasswordRequest $request)
    {
        app()->setLocale($lang);
        // old_password new_password new_password_confirmation
        // pass length 6
        $user = auth()->user();

        // Check old password
        if (! Hash::check($request->get('old_password'), $user->password)) {
            return response()->json(
                [
                    'status_code' => '10',
                    'message' => __('auth.old_pass_fail'),
                ],
                400,
            );
        }

        // Prevent using same password
        if (Hash::check($request->get('new_password'), $user->password)) {
            return response()->json(
                [
                    'status_code' => '11',
                    'message' => __('auth.newpass_fail'),
                ],
                400,
            );
        }

        // Update password
        User::where('phone', $user->phone)->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Optional: logout from other devices
        $user
            ->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return response()->json([
            'status_code' => '00',
            'message' => __('auth.pass_changed'),
        ]);
    }

    /**
     * POST /api/refresh
     * Rotates refresh token and returns new access token
     */
    public function refresh($lang, Request $request)
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $hashed = hash('sha256', $data['refresh_token']);

        $record = RefreshToken::where('token', $hashed)->first();

        if (! $record || $record->revoked || $record->expires_at->isPast()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid or expired refresh token',
                ],
                401,
            );
        }

        $user = $record->user ?? \App\Models\User::find($record->user_id);

        // Revoke old refresh token (rotation)
        $record->revoked = true;
        $record->save();

        // Issue new refresh token
        $newRefreshRaw = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $newRefreshRaw),
            'revoked' => false,
            'expires_at' => Carbon::now()->addDays(14),
        ]);

        // Issue new access token
        $newAccess = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Token refreshed',
            'access_token' => $newAccess,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'refresh_token' => $newRefreshRaw,
        ]);
    }

    /**
     * POST /api/logout
     * Revokes all user tokens and provided refresh token (optional)
     */
    public function logout($lang, Request $request)
    {
        app()->setLocale($lang);
        // If called with Bearer access token, Sanctum will resolve user
        $user = $request->user();

        if ($user) {
            // Delete all access tokens for this user (or delete current token only)
            $user->tokens()->delete();
        }

        return response()->json([
            'status_code' => '00',
            'message' => __('messages.logout_success'),
        ]);
    }

    private function createToken($user, $type)
    {
        // 1) Create short-lived access token via Sanctum
        // Note: Sanctum tokens don't expire by default; we manage expiry app-side.
        if ($type == 'login') {
            $user->tokens()->delete(); // logout all devices
            $accessToken = $user->createToken('mobile', ['user:read', 'device:heartbeat', 'notification:read'], now()->addHours(1))->plainTextToken;
            // 2) Create refresh token (random string). Consider hashing before storing.
            // $refreshToken = Str::random(64);
            // RefreshToken::create([
            //     'user_id'  => $user->id,
            //     'token'    => hash('sha256', $refreshToken), // store hashed
            //     'revoked'  => false,
            //     'expires_at'=> Carbon::now()->addDays(14),     // 14 days TTL
            // ]);
        } else {
            $accessToken = $user->createToken('tempDevice', ['create_device'], now()->addHours(1))->plainTextToken;
        }

        return ['access' => $accessToken];
    }

    private function getVersion()
    {
        $x = \DB::table('hr_settings')->first();

        return [
            'latest_version' => $x->app_version,
            'min_version' => $x->app_min_version,
            'update_url' => url($x->app_url),
        ];
    }
}
