<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\AccountMapping;

use App\Repositories\BaseRepository;

class AsAccountMappingRepository extends BaseRepository
{
    protected $fieldSearchable = [
     'mapping_key', 'account_id', 'entity_type', 'entity_id', 'status', 'settings'
    ];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.AccountMapping';

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
        return AccountMapping::class;
    }

    public function getHeaders(): array
    {
        return [
            __('accusoft::models/as_account_mappings.fields.id'),
            __('accusoft::models/as_account_mappings.fields.name'),
            __('accusoft::models/as_account_mappings.fields.mapping_key'),
            __('accusoft::models/as_account_mappings.fields.account_id'),
            __('accusoft::models/as_account_mappings.fields.created_at'),
        ];
    }

    public function dataExcel(): array
    {
        return AccountMapping::with('account')
            ->get()
            ->map(function ($accountMapping) {
                return [
                    'id' => $accountMapping->id,
                    'name' => $accountMapping->name,
                    'key' => $accountMapping->mapping_key,
                    'account' => $accountMapping->account ? $accountMapping->account->name : '-',
                    'created_at' => $accountMapping->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('accusoft::models/as_account_mappings.singular');
    }
}
