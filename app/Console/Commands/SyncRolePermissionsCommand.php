<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SyncRolePermissionsCommand extends Command
{
    protected $signature = 'permissions:sync-role {role : Role name (e.g. owner)}';

    protected $description = 'Assigns all web guard permissions to a role (recovery when role_has_permissions was cleared).';

    public function handle(): int
    {
        $roleName = (string) $this->argument('role');

        $role = Role::query()
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->first();

        if ($role === null) {
            $this->error("Role \"{$roleName}\" (guard web) was not found.");

            return self::FAILURE;
        }

        $permissions = Permission::query()->where('guard_name', 'web')->get();

        $role->syncPermissions($permissions);

        Cache::forget(config('permission.cache.key'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("Synced {$permissions->count()} permissions to role \"{$roleName}\".");

        return self::SUCCESS;
    }
}
