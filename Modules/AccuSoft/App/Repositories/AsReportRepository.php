<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\Branch;
use App\Repositories\BaseRepository;

class AsReportRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'code',
        'account_type', // 1=asset, 2=liability, 3=equity, 4=revenue, 5=expense, 6=cost_of_sales
        'parent_id',
        'level',
        'is_leaf',
        'status',
        'is_system',
        'attributes',
        'type', // 1=debit, 2=credit
    ];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.TreeAccounts';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        return $query;
    }

     public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }
    public function model(): string
    {
        return TreeAccounts::class;
    }

    public function statuses(): array
    {
        return TreeAccounts::statuses();
    }

  public function TreeAccounts()
    {
        return TreeAccounts::active()
            ->get()
            ->mapWithKeys(function ($account) {
                return [
                    $account->id => $account->name . ' (' . $account->code . ')',
                ];
            })
            ->toArray();
    }

    public function CostCenters()
    {
        return CostCenters::active()
            ->get()
            ->mapWithKeys(function ($account) {
                return [
                    $account->id => $account->name . ' (' . $account->code . ')',
                ];
            })
            ->toArray();
    }

    public function fiscalYears()
    {
        return FiscalYear::get()->pluck('name', 'id')->toArray();
    }


     public  function branchs()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function users()
    {
        return \App\Models\User::pluck('name', 'id')->toArray();
    }

    public function assetsReport(array $filters)
    {
        $query = \Modules\AccuSoft\App\Models\Asset::with(['assetCategory', 'branch', 'costCenter']);

        if (!empty($filters['branchId'])) {
            $query->where('branch_id', $filters['branchId']);
        }
        
        if (!empty($filters['costCenter'])) {
            $query->where('cost_center_id', $filters['costCenter']);
        }

        if (!empty($filters['categoryId'])) {
            $query->where('asset_category_id', $filters['categoryId']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['depreciation_status'])) {
            $query->where('depreciation_status', $filters['depreciation_status']);
        }

        if (isset($filters['depreciation_method']) && $filters['depreciation_method'] !== '') {
            $query->where(function ($q) use ($filters) {
                $q->where(function ($q2) use ($filters) {
                    $q2->where('depreciation_status', '!=', \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY)
                       ->where('depreciation_method', $filters['depreciation_method']);
                })->orWhere(function ($q2) use ($filters) {
                    $q2->where('depreciation_status', \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY)
                       ->whereHas('assetCategory', function ($q3) use ($filters) {
                           $q3->where('default_depreciation_method', $filters['depreciation_method']);
                       });
                });
            });
        }

        if (!empty($filters['useful_life_type'])) {
            $query->where(function ($q) use ($filters) {
                $q->where(function ($q2) use ($filters) {
                    $q2->where('depreciation_status', '!=', \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY)
                       ->where('useful_life_type', $filters['useful_life_type']);
                })->orWhere(function ($q2) use ($filters) {
                    $q2->where('depreciation_status', \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY)
                       ->whereHas('assetCategory', function ($q3) use ($filters) {
                           $q3->where('useful_life_type', $filters['useful_life_type']);
                       });
                });
            });
        }

        if (!empty($filters['fromDate'])) {
            $query->whereDate('purchase_date', '>=', $filters['fromDate']);
        }

        if (!empty($filters['toDate'])) {
            $query->whereDate('purchase_date', '<=', $filters['toDate']);
        }

        return $query->get();
    }
}
