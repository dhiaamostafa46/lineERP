<?php

namespace App\Providers;

use App\Models\Vehicles\DriverCompanyReference;
use App\Observers\DriverCompanyReferenceObserver;
use App\Observers\GlobalNotificationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce English locale for Carbon dates across the system
        \Carbon\Carbon::setLocale('en');
        \Illuminate\Support\Carbon::setLocale('en');

        DriverCompanyReference::observe(DriverCompanyReferenceObserver::class);

        // Register Global Notification Observers for audited system events
        if (class_exists(\App\Models\invApp\SalesInvoice::class)) {
            \App\Models\invApp\SalesInvoice::observe(GlobalNotificationObserver::class);
        }
        if (class_exists(\Modules\Store\App\Models\StTransfer::class)) {
            \Modules\Store\App\Models\StTransfer::observe(GlobalNotificationObserver::class);
        }
        if (class_exists(\Modules\Pos\App\Models\PosDeviceSession::class)) {
            \Modules\Pos\App\Models\PosDeviceSession::observe(GlobalNotificationObserver::class);
        }
        if (class_exists(\App\Models\AccuSoft\JournalEntry::class)) {
            \App\Models\AccuSoft\JournalEntry::observe(GlobalNotificationObserver::class);
        }

        \Illuminate\Database\Query\Builder::macro('isolateBranch', function ($tableAlias = null) {
            if (auth()->hasUser() && !auth()->user()->can('global.viewBranches')) {
                $column = $tableAlias ? $tableAlias . '.branch_id' : 'branch_id';
                $realTable = explode(' as ', strtolower($this->from))[0];
                if (\Illuminate\Support\Facades\Schema::hasColumn($realTable, 'branch_id')) {
                    $this->where($column, auth()->user()->branch_id);
                }
            }
            return $this;
        });

        \Illuminate\Database\Eloquent\Builder::macro('isolateBranch', function ($tableAlias = null) {
            if (auth()->hasUser() && !auth()->user()->can('global.viewBranches')) {
                $column = $tableAlias ? $tableAlias . '.branch_id' : $this->getModel()->getTable() . '.branch_id';
                if (\Illuminate\Support\Facades\Schema::hasColumn($this->getModel()->getTable(), 'branch_id')) {
                    $this->where($column, auth()->user()->branch_id);
                }
            }
            return $this;
        });
    }
}
