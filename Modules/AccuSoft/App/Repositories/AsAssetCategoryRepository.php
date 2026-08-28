<?php

namespace Modules\AccuSoft\App\Repositories;

use Modules\AccuSoft\App\Models\AssetCategory;
use App\Repositories\BaseRepository;

class AsAssetCategoryRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'default_depreciation_method', 'has_accounting_effect'];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.assetcategories';

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
        return AssetCategory::class;
    }

    public function getHeaders(): array
    {
        return [
            __('accusoft::models/as_asset_categories.fields.name'),
            __('accusoft::models/as_asset_categories.fields.asset_account_id'),
            __('accusoft::models/as_asset_categories.fields.default_depreciation_method'),
            __('accusoft::models/as_asset_categories.fields.has_accounting_effect'),
            __('accusoft::models/as_asset_categories.fields.default_useful_life')
        ];
    }

    public function dataExcel(): array
    {
        return AssetCategory::with(['assetAccount'])->get()->map(function ($category) {
            return [
                'name' => $category->name,
                'asset_account' => $category->assetAccount ? $category->assetAccount->name : '-',
                'depreciation_method' => __('accusoft::models/as_asset_categories.methods.' . $category->default_depreciation_method),
                'has_accounting_effect' => $category->has_accounting_effect_text,
                'useful_life' => $category->default_useful_life,
            ];
        })->toArray();
    }

    public function depreciationMethods()
    {
        return AssetCategory::getDepreciationMethods();
    }

    public function name()
    {
        return __('accusoft::models/as_asset_categories.plural');
    }
}
