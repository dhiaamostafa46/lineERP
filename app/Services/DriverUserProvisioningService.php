<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Vehicles\Driver;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DriverUserProvisioningService
{
    public function createForDriver(Driver $driver): User
    {
        $phone = User::normalizeSaudiPhone((string) $driver->mobile);

        if ($phone === '') {
            throw new InvalidArgumentException(__('drivers::messages.mobile_required_for_user'));
        }

        if (User::query()->where('phone', $phone)->where('user_type', 'driver')->exists()) {
            throw new InvalidArgumentException(__('drivers::messages.driver_user_phone_exists'));
        }

        $email = $this->resolveEmail($driver);

        if (User::query()->where('email', $email)->exists()) {
            throw new InvalidArgumentException(__('drivers::messages.driver_user_email_exists'));
        }

        $this->ensureDriverRoleExists();

        $user = User::create([
            'name' => $driver->name,
            'phone' => $phone,
            'email' => $email,
            'user_type' => 'driver',
            'password' => config('drivers.default_password', 'Evix@26'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $user->assignRole('driver');

        $driver->update(['user_id' => $user->id]);

        return $user;
    }

    protected function ensureDriverRoleExists(): Role
    {
        return Role::firstOrCreate(
            ['name' => 'driver', 'guard_name' => 'web'],
            ['name' => 'driver', 'guard_name' => 'web']
        );
    }

    protected function resolveEmail(Driver $driver): string
    {
        if (filled($driver->email)) {
            return Str::lower(trim($driver->email));
        }

        $base = 'driver.'.preg_replace('/\D/', '', $driver->iqama).'@evix.local';
        $email = $base;
        $suffix = 1;

        while (User::query()->where('email', $email)->exists()) {
            $email = Str::before($base, '@').'+'.$suffix.'@'.Str::after($base, '@');
            $suffix++;
        }

        return $email;
    }
}
