<?php

namespace Modules\Store\App\Repositories;

use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Models\User;
use App\Repositories\BaseRepository;

class StStoreRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'org_id',
        'branch_id',
        'type',
        'location',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Store::class;
    }

    public function statuses(): array
    {
        return Store::statuses();
    }

    public function types(): array
    {
        return Store::types();
    }

    public function branches(): array
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function managers(): array
    {
        return User::query()
            ->where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function header()
    {
        return [
            __('store::models/st_stores.fields.id') ?? 'ID',
            __('store::models/st_stores.fields.name') ?? 'Name',
            __('store::models/st_stores.fields.branch_id') ?? 'Branch',
            __('store::models/st_stores.fields.address') ?? 'Address',
            __('store::models/st_stores.fields.type') ?? 'Type',
            __('store::models/st_stores.fields.status') ?? 'Status',
            __('store::models/st_stores.fields.created_at') ?? 'Created At',
        ];
    }

    public function dataExel(): array
    {
        return Store::with(['translations', 'branch'])
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'branch_id' => $store->branch->name ?? '',
                    'address' => $store->address,
                    'type' => $store->type_text,
                    'status' => $store->status_text ?? $store->status,
                    'created_at' => $store->created_at ? $store->created_at->format('Y-m-d') : null,
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('store::models/st_stores.singular') ?? 'Store';
    }
}
