<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\PermissionRegistrar;

class UpdatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates & sync the newly created views to have granted permissions upon users and roles.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $route_names = [];
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name === null || $name === '') {
                continue;
            }
            if (! in_array($name, config('permission.ignore_routes', []))) {
                $route_names[] = $name;
            }
        }

        $hide_permissions = array_map('strtolower', config('hidepermission.permissions', []));

        foreach ($route_names as $route) {
            $route_explodes = explode('.', $route);
            $group = '';
            $action = '';

            try {
                if (count($route_explodes) > 2) {
                    $action = $route_explodes[2];
                    $group = $route_explodes[0].'_'.$route_explodes[1];
                } elseif (count($route_explodes) == 2) {
                    $group = $route_explodes[0];
                    $action = $route_explodes[1];
                } else {
                    $group = $route_explodes[0];
                    $action = 'index';
                }

                // Skip specific actions by default
                if (str_contains($action, 'update') || str_contains($action, 'store') || str_contains($action, 'children')) {
                    continue;
                }

                // Check if the group or the full route should be hidden (Case-Insensitive Exact Match)
                if (in_array(strtolower($group), $hide_permissions) || in_array(strtolower($route), $hide_permissions)) {
                    continue;
                }

            } catch (\Throwable $th) {
                $this->error("Error processing route: $route");

                continue;
            }

            $exists = DB::table('permissions')->where('name', $route)->exists();
            if (! $exists) {
                DB::table('permissions')->insert([
                    'name' => $route,
                    'group' => $group,
                    'action' => $action,
                    'action_view' => $action,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Cache::forget(config('permission.cache.key'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Permissions updated successfully!');
    }
}
