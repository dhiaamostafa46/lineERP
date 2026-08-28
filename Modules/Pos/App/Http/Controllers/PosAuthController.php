<?php

namespace Modules\Pos\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Services\PosAuthService;
use Modules\Pos\App\Services\PosDeviceResolverService;

class PosAuthController extends Controller
{
    protected $authService;
    protected $deviceResolver;

    public function __construct(PosAuthService $authService, PosDeviceResolverService $deviceResolver)
    {
        $this->authService = $authService;
        $this->deviceResolver = $deviceResolver;
    }

    public function apiLogin(Request $request)
    {
       
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required',
            'device_uuid' => 'required|exists:pos_devices,uuid',
        ]);

     
        $device = PosDevice::where('uuid', $request->device_uuid)->firstOrFail();
        
        $loginField = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!auth()->attempt([$loginField => $request->login_id, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        // 1. Verify User has access to this device (Implementation depends on your logic)
        // This is a placeholder for actual permission check
        // if (!$user->hasPermissionToDevice($device)) { ... }

        // 2. Issue Token
        $token = $user->createToken('pos-terminal-' . $device->uuid)->plainTextToken;
        $tokenId = explode('|', $token)[0];

        // 3. Register Session
        \Modules\Pos\App\Models\PosDeviceSession::create([
            'device_uuid' => $device->uuid,
            'user_id' => $user->id,
            'token_id' => $tokenId,
            'browser_fingerprint' => $request->header('X-Browser-Fingerprint'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $request->header('X-Device-Name', 'Unknown Device'),
            'browser' => $request->header('X-Browser-Name', 'Unknown Browser'),
            'operating_system' => $request->header('X-OS-Name', 'Unknown OS'),
            'status' => 'Active',
            'created_by' => $user->id
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'device_uuid' => $device->uuid
        ]);
    }

    public function apiLogout(Request $request)
    {
        $user = $request->user();
        $deviceUuid = $request->header('X-Device-UUID');

        if ($user && $deviceUuid) {
            // Find session and mark as logged out
            $session = \Modules\Pos\App\Models\PosDeviceSession::where('user_id', $user->id)
                ->where('device_uuid', $deviceUuid)
                ->where('status', 'Active')
                ->latest()
                ->first();

            if ($session) {
                $session->update([
                    'logout_time' => now(),
                    'status' => 'Inactive'
                ]);
            }
            
            // Delete the token
            $user->tokens()->where('name', 'pos-terminal-' . $deviceUuid)->delete();
        }

        return response()->json(['message' => 'Successfully logged out']);
    }
}
