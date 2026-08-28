<?php
namespace App\Services;

use App\Models\DeviceSession;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceLockService {
    protected $deviceService;

    public function __construct(DeviceIdentificationService $deviceService) {
        $this->deviceService = $deviceService;
    }

    public function authenticateDevice(User $user, Request $request): bool {

        dd('here');
        $deviceSerial = $this->deviceService->getDeviceSerialNumber($request);
        $deviceInfo = $this->deviceService->getBrowserInfo($request);

        // البحث عن جلسة نشطة لنفس المستخدم
        $activeSession = DeviceSession::where('user_id', $user->id)
            ->where('device_serial', '!=', $deviceSerial)
            ->first();

        if ($activeSession) {
            throw new \Exception('حسابك مسجل من جهاز آخر. يرجى تسجيل الخروج أولاً من الجهاز الآخر.');
        }

        // تحديث أو إنشاء جلسة الجهاز
        $session = DeviceSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_serial' => $deviceSerial
            ],
            [
                'device_name' => $this->getDeviceName(),
                'device_ip' => $request->ip(),
                'device_type' => $this->getDeviceType($request),
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'last_activity_at' => now()
            ]
        );

        session(['device_serial' => $deviceSerial]);

        return true;
    }

    public function verifyDevice(User $user, Request $request): bool {
        $deviceSerial = $this->deviceService->getDeviceSerialNumber($request);
        $sessionDevice = session('device_serial');

        if ($sessionDevice !== $deviceSerial) {
            throw new \Exception('الجهاز غير متطابق. يرجى تسجيل الدخول مرة أخرى.');
        }

        // تحديث آخر نشاط
        DeviceSession::where('user_id', $user->id)
            ->where('device_serial', $deviceSerial)
            ->update(['last_activity_at' => now()]);

        return true;
    }

    public function forceLogoutOtherDevices(User $user): void {
        $currentDeviceSerial = session('device_serial');

        DeviceSession::where('user_id', $user->id)
            ->where('device_serial', '!=', $currentDeviceSerial)
            ->delete();
    }

    public function getUserDevices(User $user) {
        return DeviceSession::where('user_id', $user->id)
            ->get(['id', 'device_name', 'device_ip', 'device_type', 'browser', 'os', 'last_activity_at']);
    }

    public function revokeDevice(User $user, $deviceId): void {
        DeviceSession::where('user_id', $user->id)
            ->where('id', $deviceId)
            ->delete();
    }

    private function getDeviceName(): string {
        return gethostname() ?: 'Unknown Device';
    }

    private function getDeviceType(Request $request): string {
        $userAgent = $request->header('User-Agent');

        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }
        return 'desktop';
    }
}
