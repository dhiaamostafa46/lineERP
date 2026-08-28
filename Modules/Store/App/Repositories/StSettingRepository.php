<?php

namespace Modules\Store\App\Repositories;

use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\InventorySettings;
use Mpdf\Tag\Br;

class StSettingRepository extends BaseRepository
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
        return InventorySettings::class;
    }

    public function getCostingMethods(): array
    {
        return InventorySettings::getCostingMethods();
    }






    public function branches(): array
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }

    public function header()
    {
        return [
            __('store::models/store.fields.id') ?? 'ID',
            __('store::models/store.fields.name') ?? 'Name',
            __('store::models/store.fields.status') ?? 'Status',
            __('store::models/store.fields.created_at') ?? 'Created At',
        ];
    }

    public function dataExel(): array
    {
        return Store::with('translations')
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    // If your Store has stock-related fields, add them here, e.g. 'stock' => $store->stock
                    'status' => $store->status_text ?? $store->status,
                    'created_at' => $store->created_at ? $store->created_at->format('Y-m-d') : null,
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('store::models/store.singular') ?? 'Store';
    }
}
