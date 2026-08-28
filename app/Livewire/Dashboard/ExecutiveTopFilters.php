<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ExecutiveTopFilters extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month'; // today, yesterday, this_week, this_month, this_quarter, this_year, custom

    public $activeTab = 'all'; // 'all', 'inventory', 'accounting', 'pos', 'hr', 'fleet', 'activity'

    public $startDate;

    public $endDate;

    public $branches = [];

    public $stores = [];

    public function mount()
    {
        $this->branches = DB::table('branches')
            ->leftJoin('branch_translations', function ($join) {
                $join->on('branches.id', '=', 'branch_translations.branch_id')
                    ->where('branch_translations.locale', '=', app()->getLocale() ?? 'ar');
            })
            ->select('branches.id', DB::raw("COALESCE(branch_translations.name, CONCAT('فرع #', branches.id)) as name"))
            ->distinct()
            ->get()
            ->toArray();

        $this->stores = DB::table('stores')
            ->leftJoin('store_translations', function ($join) {
                $join->on('stores.id', '=', 'store_translations.store_id')
                    ->where('store_translations.locale', '=', app()->getLocale() ?? 'ar');
            })
            ->select('stores.id', DB::raw("COALESCE(store_translations.name, CONCAT('مستودع #', stores.id)) as name"))
            ->distinct()
            ->get()
            ->toArray();

        $this->updateDatesByPeriod();
    }

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('executiveTabChanged', $tab);
    }

    public function updatedPeriod()
    {
        $this->updateDatesByPeriod();
        $this->applyFilters();
    }

    public function updatedBranchId()
    {
        $this->applyFilters();
    }

    public function updatedStoreId()
    {
        $this->applyFilters();
    }

    public function updateDatesByPeriod()
    {
        $now = Carbon::now();
        switch ($this->period) {
            case 'today':
                $this->startDate = $now->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = $now->copy()->subDay()->format('Y-m-d');
                $this->endDate = $now->copy()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->startDate = $now->copy()->startOfQuarter()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
            default:
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function filterChanged($branchId = 'all', $storeId = 'all', $period = 'this_month')
    {
        $this->branchId = $branchId ?: 'all';
        $this->storeId = $storeId ?: 'all';
        $this->period = $period ?: 'this_month';
        $this->applyFilters();
    }

    public function applyFilters()
    {
        $this->updateDatesByPeriod();

        $this->dispatch('executiveFiltersUpdated',
            branchId: $this->branchId,
            storeId: $this->storeId,
            period: $this->period,
            startDate: $this->startDate,
            endDate: $this->endDate
        );
    }

    public function render()
    {
        return view('livewire.dashboard.executive-top-filters');
    }
}
