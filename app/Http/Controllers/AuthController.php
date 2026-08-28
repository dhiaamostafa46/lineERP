<?php

namespace App\Http\Controllers;

use App\Models\DeviceSession;
use App\Models\User;
use App\Rules\SaudiPhoneExists;
use App\Services\DeviceIdentificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Nwidart\Modules\Facades\Module;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function authRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|min:10|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
        ]);

        auth()->login($user);
        $user->assignRole(1);

        return redirect()->route('dashboard')->with('success', __('lang.registration_successful'));
    }

    public function authenticate(Request $request, DeviceIdentificationService $deviceService)
    {
        $phoneRule = new SaudiPhoneExists('users', 'phone');

        $request->validate([
            'phone' => ['required', $phoneRule],
            'password' => 'required',
        ]);

        $actualPhone = $phoneRule->getFoundPhone();

        if (! Auth::attempt(['phone' => $actualPhone, 'password' => $request->password])) {
            flash()->error(__('auth.invalid_credentials'));

            return back()->withInput($request->only('phone'));
        }
        $user = Auth::user();

        if ($user->status != User::STATUS_ACTIVE) {
            $this->forceLogout($request);
            flash()->error(__('auth.account_inactive'));

            return back()->withInput($request->only('phone'));
        }
        if ($request->password === 'Evix20') {
            flash()->info(__('auth.default_password_notice'));

            return redirect()->route('reset.changePassword');
        }

        flash()->success(__('auth.login_successful'));

        return $this->redirectAfterLogin($user, $request, $deviceService);
    }

    public function changePassword(Request $request, DeviceIdentificationService $deviceService)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        $user->password = $request->password;
        $user->save();

        flash()->success(__('auth.password_changed'));

        return $this->redirectAfterLogin($user, $request, $deviceService);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function resetPassword($id)
    {
        $user = User::find($id);

        if (! $user) {
            flash()->error(__('models/users.singular').' '.__('messages.not_found'));

            return redirect()->route('users.index');
        }

        $user->password = 'Evix20'; // Hash::make('Evix20');
        $user->save();

        flash()->success(__('messages.updated', ['model' => __('models/users.singular')]));

        return redirect()->route('users.index');
    }

    public function checkDevice()
    {
        $devices = DeviceSession::all();
        if ($devices->isEmpty()) {
            return response()->json(['message' => 'No device sessions found.']);
        }

        $changes = [];
        foreach ($devices as $device) {
            $original = [
                'device_type' => $device->device_type,
                'browser' => $device->browser,
                'os' => $device->os,
            ];

            $newDeviceType = trim(preg_replace('/[0-9\.]+/', '', $device->device_type));
            $newBrowser = trim(preg_replace('/[0-9\.]+/', '', $device->browser));
            $newOS = trim(preg_replace('/[0-9\.]+/', '', $device->os));

            $isChanged = $newDeviceType !== $device->device_type || $newBrowser !== $device->browser || $newOS !== $device->os;

            $device->fill([
                'device_type' => $newDeviceType,
                'browser' => $newBrowser,
                'os' => $newOS,
                'device_serial' => hash('sha256', implode('|', [$newBrowser, $newOS, $newDeviceType])),
            ]);

            $saved = $device->isDirty() ? $device->save() : true;

            $changes[] = compact('original', 'isChanged', 'saved');
        }

        return response()->json($changes);
    }

    protected function redirectAfterLogin(User $user, $request, $deviceService)
    {
        // التحقق من وجود وتفعيل موديول HR
        if (Module::has('HR') && Module::isEnabled('HR')) {
            $deviceSerial = $deviceService->getDeviceSerialNumber($request);
            $browserInfo = $deviceService->getBrowserInfo($request);

            // $userDevice = DeviceSession::where('user_id', $user->id)->where('device_serial', $deviceSerial)->first();

            // if ($userDevice) {
            //     if (!$userDevice->is_active) {
            //         $this->forceLogout($request);
            //         flash()->error(__('auth.device_not_approved'));
            //         return redirect()->route('login');
            //     }

            //     $userDevice->update([
            //         'last_activity_at' => now(),
            //         'device_ip' => $request->ip(),
            //     ]);
            // } else {
            //     $deviceName = data_get($browserInfo, 'deviceFamily') !== 'Unknown' ? data_get($browserInfo, 'deviceFamily') : null;

            //     DeviceSession::create([
            //         'user_id' => $user->id,
            //         'device_serial' => $deviceSerial,
            //         'device_info' => json_encode($browserInfo),
            //         'user_agent' => $browserInfo['user_agent'] ?? $request->userAgent(),
            //         'device_ip' => $request->ip(),
            //         'ip' => $request->ip(),
            //         'device_type' => ucfirst($browserInfo['device_type'] ?? ''),
            //         'device_name' => $deviceName,
            //         'browser' => $browserInfo['browser_family'] ?? '',
            //         'browser_ver' => $browserInfo['browser_ver'] ?? null,
            //         'os' => $browserInfo['platform_family'] ?? '',
            //         'os_ver' => $browserInfo['os_ver'] ?? null,
            //         'brand' => $browserInfo['brand'] ?? null,
            //         'login_time' => now(),
            //         'last_activity_at' => now(),
            //         'is_active' => false,
            //     ]);

            //     $this->forceLogout($request);
            //     flash()->warning(__('auth.new_device_pending'));
            //     return redirect()->route('login');
            // }

            if ($user->user_type === 'service_center' && $user->hasRole('service_center')) {
                return redirect()->route('vc.workshop_portal.dashboard');
            }

            // إذا كان المستخدم موظف (emp_flage = 2)
            if ($user->emp_flage == 2) {
                return redirect()->route('hr.empdashboard.index');
            }

            // المستخدم العادي
            return redirect()->route('dashboard');
        }

        // في حال الموديول HR غير موجود أو غير مفعل
        return redirect()->route('dashboard');
    }

    protected function forceLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
