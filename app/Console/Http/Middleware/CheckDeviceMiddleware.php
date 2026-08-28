<?php

namespace App\Http\Middleware;

use App\Models\DeviceSession;
use App\Services\DeviceIdentificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDeviceMiddleware
{
    protected $deviceService;

    public function __construct(DeviceIdentificationService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $deviceSerial = $this->deviceService->getDeviceSerialNumber($request);

        // البحث عن الجهاز المسجل
        $userDevice = DeviceSession::where('user_id', $user->id)->where('device_serial', $deviceSerial)->first();

        if ($userDevice) {
            // التحقق من حالة التفعيل
            if (!$userDevice->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                flash()->error('هذا الجهاز غير مفعل. يرجى التواصل مع الإدارة لتفعيل الجهاز.');
                return redirect()->route('login');
            }

            // تحديث آخر نشاط
            $userDevice->update([
                'last_activity_at' => now(),
                'device_ip' => $request->ip(),
            ]);
        } else {
            // جهاز جديد - جمع بيانات كاملة من الخدمة
            $browserInfo = $this->deviceService->getBrowserInfo($request);

               $device_name = data_get($browserInfo, 'device_family') && $browserInfo['device_family'] !== 'Unknown'
                 ? $browserInfo['device_family']
                 : null;

            DeviceSession::create([
                'user_id' => $user->id,
                'device_serial' => $deviceSerial,
                'device_info' => json_encode($browserInfo),
                'user_agent' => $browserInfo['user_agent'] ?? $request->userAgent(),
                'device_ip' => $request->ip(),
                'ip' => $request->ip(),
                'device_type' => ucfirst($browserInfo['device_type'] ?? ''),
                'device_name' =>      $device_name,
                'browser' => $browserInfo['browser_family'] ?? '',
                'browser_ver' => $browserInfo['browser_ver'] ?? null,
                'os' => $browserInfo['platform_family'] ?? '',
                'os_ver' => $browserInfo['os_ver'] ?? null,
                'brand' => $browserInfo['brand'] ?? null,
                'login_time' => now(),
                'last_activity_at' => now(),
                'is_active' => false,
            ]);


 $deviceInfo = implode('|', [$browserInfo['browser_family'] ?? '', $browserInfo['platform_family'] ?? '', $browserInfo['device_type'] ?? '']);


            // تسجيل خروج المستخدم
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            flash()->error('تم تسجيل جهاز جديد. يرجى انتظار موافقة الإدارة على تفعيل هذا الجهاز.');
            return redirect()->route('login');
        }

        // تمرير معلومات الجهاز للطلب
        $request->attributes->add([
            'device_serial' => $deviceSerial,
            'device_info' => $this->deviceService->getBrowserInfo($request),
        ]);

        return $next($request);
    }
}
