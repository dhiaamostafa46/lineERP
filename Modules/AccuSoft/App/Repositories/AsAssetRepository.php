<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\TaxAccount;
use App\Models\AccuSoft\TreeAccounts;
use Modules\AccuSoft\App\Models\Asset;
use App\Repositories\BaseRepository;
use Modules\AccuSoft\App\Models\AssetCategory;

class AsAssetRepository extends BaseRepository
{
    protected $fieldSearchable = ['code', 'name', 'status', 'asset_category_id', 'branch_id'];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.assets';

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


    public function AssetCategory()
    {
        return AssetCategory::active()->get()->pluck('name', 'id')->prepend(__('lang.please_select'), '');
    }

    public function costcenters()
    {
        return CostCenters::active()->get()->pluck('name', 'id')->prepend(__('lang.please_select'), '');
    }   

    public function fixedassets()
    {
        return TreeAccounts::active()->where('account_type', TreeAccounts::ACCOUNT_TYPE_FIXED_ASSET)->get()->pluck('name', 'id')->prepend(__('lang.please_select'), '');
    }

    public function taxes(): array
    {
        return TaxAccount::active()->get()->pluck('name', 'id')->toArray();
    }

    public function paymentAccounts(): array
    {
        return TreeAccounts::whereIn('account_type', [
            TreeAccounts::ACCOUNT_TYPE_BANK,
            TreeAccounts::ACCOUNT_TYPE_TREASURY
        ])
            ->where('is_leaf', true)
            ->get()
            ->mapWithKeys(function ($account) {
                return [$account->id => $account->name];
            })
            ->toArray();
    }

    public function model(): string
    {
        return Asset::class;
    }

    public function getHeaders(): array
    {
        return [
            __('accusoft::models/as_asset.fields.code'),
            __('accusoft::models/as_asset.fields.name'),
            __('accusoft::models/as_asset.fields.purchase_date'),
            __('accusoft::models/as_asset.fields.purchase_value'),
            __('accusoft::models/as_asset.fields.current_book_value'),
            __('accusoft::models/as_asset.fields.status')
        ];
    }

    public function dataExcel(): array
    {
        $statuses = Asset::getStatuses();
        return Asset::with(['assetCategory'])->get()->map(function ($asset) use ($statuses) {
            return [
                'code' => $asset->code,
                'name' => $asset->name,
                'purchase_date' => $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '-',
                'purchase_value' => $asset->purchase_value,
                'current_book_value' => $asset->current_book_value ?? $asset->purchase_value,
                'status' => $statuses[$asset->status] ?? $asset->status,
            ];
        })->toArray();
    }

    public function name()
    {
        return __('accusoft::models/as_asset.plural');
    }
}
