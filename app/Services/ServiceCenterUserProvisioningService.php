<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ServiceCenterUserProvisioningService
{
    public function create(string $name, string $phoneInput): User
    {
        $phone = User::normalizeSaudiPhone($phoneInput);

        if ($phone === '') {
            throw new InvalidArgumentException(__('vehicles::models/maintenance_workshop.messages.contact_phone_required'));
        }

        if (User::query()->where('phone', $phone)->where('user_type', 'service_center')->exists()) {
            throw new InvalidArgumentException(__('vehicles::models/maintenance_workshop.messages.service_center_phone_exists'));
        }

        $email = $this->resolveEmail($phone);

        if (User::query()->where('email', $email)->exists()) {
            throw new InvalidArgumentException(__('vehicles::models/maintenance_workshop.messages.service_center_email_exists'));
        }

        $this->ensureServiceCenterRoleExists();

        $user = User::create([
            'name' => trim($name),
            'phone' => $phone,
            'email' => $email,
            'user_type' => 'service_center',
            'password' => config('vehicles.service_center_default_password', 'Evix@26'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $user->assignRole('service_center');
        $this->assignDefaultPortalPermissions();

        return $user;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function updateFromWorkshop(User $user, array $validated): User
    {
        $phone = User::normalizeSaudiPhone((string) ($validated['cont_pers_phone'] ?? ''));

        if ($phone === '') {
            throw new InvalidArgumentException(__('vehicles::models/maintenance_workshop.messages.contact_phone_required'));
        }

        $phoneTaken = User::query()
            ->where('phone', $phone)
            ->where('user_type', 'service_center')
            ->where('id', '!=', $user->id)
            ->exists();

        if ($phoneTaken) {
            throw ValidationException::withMessages([
                'cont_pers_phone' => [__('vehicles::models/maintenance_workshop.messages.service_center_phone_exists')],
            ]);
        }

        $user->name = trim((string) $validated['contact_person']);
        $user->phone = $phone;
        $user->save();

        return $user;
    }

    protected function ensureServiceCenterRoleExists(): Role
    {
        return Role::firstOrCreate(
            ['name' => 'service_center', 'guard_name' => 'web'],
            ['name' => 'service_center', 'guard_name' => 'web']
        );
    }

    public function assignDefaultPortalPermissions(): void
    {
        $role = $this->ensureServiceCenterRoleExists();

        $permissions = [
            'vc.workshop_portal.dashboard',
            'vc.workshop_portal.requests.index',
            'vc.workshop_portal.profile',
        ];

        foreach ($permissions as $permission) {
            if (\Spatie\Permission\Models\Permission::query()->where('name', $permission)->exists()) {
                $role->givePermissionTo($permission);
            }
        }
    }

    protected function resolveEmail(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $base = 'service_center.'.$digits.'@evix.local';
        $email = $base;
        $suffix = 1;

        while (User::query()->where('email', $email)->exists()) {
            $email = Str::before($base, '@').'+'.$suffix.'@'.Str::after($base, '@');
            $suffix++;
        }

        return $email;
    }
}
