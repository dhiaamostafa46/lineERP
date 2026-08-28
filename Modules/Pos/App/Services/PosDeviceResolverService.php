<?php

namespace Modules\Pos\App\Services;

use App\Models\User;
use Modules\Pos\App\Models\PosDevice;
use Illuminate\Support\Collection;

class PosDeviceResolverService
{
    /**
     * Resolves the device for a user.
     * Returns a PosDevice if directly resolved,
     * Returns a Collection of PosDevice if the user needs to select one,
     * Returns null if no devices available or unauthorized.
     */
    public function resolve(User $user)
    {
        $branchId = $user->branch_id;
        $devices = PosDevice::where('is_active', true)->where('branch_id', $branchId)->get();

        if ($devices->isEmpty()) {
            return null;
        }

        $linkedDevice = $devices->first(function ($device) use ($user) {
            if ($device->is_users_linked) {
                $linkedUsers = is_array($device->linked_users) ? $device->linked_users : (json_decode($device->linked_users, true) ?? []);
                return in_array($user->id, $linkedUsers);
            }
            return false;
        });

        if ($linkedDevice) {
            return $linkedDevice;
        }

        $unrestrictedDevices = $devices->filter(function ($device) {
            return !$device->is_users_linked;
        });

        if ($unrestrictedDevices->count() === 1) {
            return $unrestrictedDevices->first();
        }

        if ($unrestrictedDevices->count() > 1) {
            return $unrestrictedDevices;
        }

        return null;
    }
}
