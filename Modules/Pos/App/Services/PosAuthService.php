<?php

namespace Modules\Pos\App\Services;

use App\Models\User;
use Modules\Pos\App\Models\PosDevice;
use Modules\Pos\App\Models\PosDeviceUserPin;
use Modules\Pos\App\Models\PosSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PosAuthService
{
    public function authenticate(PosDevice $device, User $user, $credentials)
    {
        $mode = $device->auth_mode ?? 'system_user';

        if ($mode === 'pin') {
            $pin = PosDeviceUserPin::where('user_id', $user->id)
                ->where(function($query) use ($device) {
                    $query->where('device_id', $device->id)->orWhereNull('device_id');
                })
                ->where('is_active', true)
                ->first();
            
            if (!$pin || !Hash::check($credentials['pin'] ?? '', $pin->pin_hash)) {
                return ['success' => false, 'message' => __('pos::messages.invalid_pin')];
            }
        } elseif ($mode === 'password') {
            if (!Hash::check($credentials['password'] ?? '', $user->password)) {
                return ['success' => false, 'message' => __('pos::messages.invalid_password')];
            }
        }
        
        return ['success' => true];
    }
}
