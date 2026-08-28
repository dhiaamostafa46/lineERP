<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    // Show the form for requesting a password reset link
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Send the password reset link
    // public function sendResetLinkEmail(Request $request)
    // {

    //      $request->validate(['phone' => 'required|phone|exists:users,phone']);

    //     // Generate a unique token
    //     $token = Str::random(60);

    //     // Store the token in a custom password_resets table (create it in your database)
    //     \DB::table('password_resets')->insert([
    //         'phone' => $request->email,
    //         'OTP' => $OTP,
    //         'token' => $token,
    //         'created_at' => now(),
    //     ]);

    //     $phon = "966533166742";
    //     $curl = curl_init();

    //     $req =
    //         '{
    //         "src": "Eyein",
    //         "dests": ["'.$phon.'"],
    //         "body":"شكرا احمد الشكري لتسوقك لدى متجر عين رقم طلبك ",
    //         "priority": 0,
    //         "delay": 0,
    //         "validity": 0,
    //         "maxParts": 0,
    //         "dlr": 0,
    //         "prevDups": 0,
    //         "msgClass": "transactional"
    //         }';
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => 'https://api.oursms.com/msgs/sms',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => $req,
    //         CURLOPT_HTTPHEADER => ['Content-Type: application/json','Authorization:Bearer xZX_3oI0jaf-2Q3gWF3N'],
    //     ]);

    //     $response = curl_exec($curl);
    //      dd($response);
    //     $obj = json_decode($response);
    //     if($obj->accepted== 1)
    //     {
    //         dd($obj);
    //         return view('auth.OTP');
    //     }

    //     // $request->validate(['email' => 'required|email|exists:users,email']);

    //     // // Generate a unique token
    //     // $token = Str::random(60);

    //     // // Store the token in a custom password_resets table (create it in your database)
    //     // \DB::table('password_resets')->insert([
    //     //     'email' => $request->email,
    //     //     'token' => $token,
    //     //     'created_at' => now(),
    //     // ]);

    //     // Mail::to($request->email)->send(new \App\Mail\ResetPassword($request->email , $token));

    //    return back();
    // }

    public function sendResetLinkOTP(Request $request)
    {
        // Validate the phone number exists in users table
        $request->validate([
            'phone' => 'required|exists:users,phone', // تحقق من أن الرقم موجود
        ]);



        $user =User::where('phone' ,$request->phone)->first();

        // Retrieve and format the phone number
        $phone = $request->phone;

        // Check if phone number starts with '05'
        if (strpos($phone, '05') === 0 && strpos($phone, '966') !== 0) {
            $phone = '966' . substr($phone, 1); // حذف '0' وإضافة '966' في حال لم يكن الرقم يبدأ بها
        }

        
        // Generate a unique token
        $token = Str::random(60);

        // Generate a random OTP (6 digits)
        $OTP = mt_rand(100000, 999999); // Generate a random OTP

        // Store the token and OTP in a custom password_resets table
        \DB::table('password_resets')->insert([
            'phone' => $phone, // استخدم الرقم الجديد
            'email' =>   $user->email,
            'OTP' => $OTP,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Prepare to send the OTP via SMS
        $curl = curl_init();

        $req = json_encode([
            'src' => 'Evix',
            'dests' => [$phone],
            'body' => __('lang.your_code') . " $OTP",
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
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        curl_close($curl); // Close the cURL session

        $obj = json_decode($response);

        // Check if the message was accepted
        if (isset($obj->accepted) && $obj->accepted == 1) {
            return view('auth.OTP')->with('token', $token)->with('phone', $phone); // Show OTP input form
        }

        return back()->with('error', 'فشل إرسال OTP.'); // Return with an error message
    }

    public function OPTCheck(Request $request)
    {
        // جمع الرموز المكونة من 6 أرقام
         $otp = $request->input('code_1') . $request->input('code_2') . $request->input('code_3') . $request->input('code_4') . $request->input('code_5') . $request->input('code_6');

        // الحصول على التوكن
        $token = $request->input('token');

        // الحصول على تاريخ اليوم
        $today = \Carbon\Carbon::now()->startOfDay();

        // التحقق من وجود السجل مع التحقق من أن تاريخ الإنشاء هو اليوم
        $record = \DB::table('password_resets')
            ->where('OTP', $otp)
            ->where('token', $token)
            ->whereDate('created_at', $today) // التحقق من أن تاريخ الإنشاء هو اليوم
            ->first();



        // التأكد من وجود السجل والتحقق من الوقت
        if ($record) {
            $createdAt = \Carbon\Carbon::parse($record->created_at);
            $now = \Carbon\Carbon::now();

            // التحقق من أن الوقت لا يتجاوز 5 دقائق
            if ($now->diffInMinutes($createdAt) <= 5) {
                return redirect()->route('password.reset',$token);
                return response()->json(['message' => 'OTP is valid and within the time limit'], 200);
            } else {
                return redirect()->route('reset.password');
            }
        } else {
            return redirect()->route('reset.password');
        }
    }


    // Show the password reset form
    public function showResetForm($token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    // Handle the password reset
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required',
        ]);

        // تحقق من أن الرمز صحيح
        $resetRecord = \DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['token' => 'This password reset link is invalid.']);
        }

        // تحديث كلمة مرور المستخدم
        $user = User::where('email', $resetRecord->email)->first();


        $user->password = $request->password;
        $user->save();

        // التحقق من صحة كلمة المرور


        // حذف سجل إعادة تعيين كلمة المرور
        \DB::table('password_resets')
            ->where('email', $resetRecord->email)
            ->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }

}
