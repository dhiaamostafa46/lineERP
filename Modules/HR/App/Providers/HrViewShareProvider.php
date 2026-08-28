<?php

namespace Modules\HR\App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\HR\App\Models\HrSetting;

class HrViewShareProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once base_path().'/Modules/HR/App/Helpers/globalFunctions.php';
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('hr_settings')) {
                view()->share('hr_setting', HrSetting::first());
            }
        } catch (\Exception $e) {
            // Ignore errors during boot if DB is not ready
        }
    }
}
