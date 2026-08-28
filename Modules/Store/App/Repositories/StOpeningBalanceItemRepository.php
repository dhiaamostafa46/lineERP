<?php

namespace Modules\Store\App\Repositories;

use App\Models\BasicDataApp\Product;
use App\Models\Branch;
use App\Models\StoreApp\Store;
use App\Repositories\BaseRepository;
use Modules\Store\App\Models\InventoryAdjustment;
use Modules\Store\App\Models\StOpeningBalance;
use Modules\Store\App\Models\StOpeningBalanceItem;
use Mpdf\Tag\Br;

class StOpeningBalanceItemRepository extends BaseRepository
{

      protected $fieldSearchable = [
         'opening_balance_id',
        'product_id',
        'unit_id',
        'size_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'status',
        'notes',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return StOpeningBalanceItem::class;
    }

    public function statuses(): array
    {
        return StOpeningBalanceItem::statuses();
    }

    public function branches(): array
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }

    public function stores(): array
    {
        return Store::get()->pluck('name', 'id')->toArray();
    }

    public function products()
    {
        return Product::get()->pluck('name', 'id')->toArray();
    }


    public function deleteWhere($id)
    {
        // يقبل array مثل ['opening_balance_id' => 1] أو scalar مثل 1
        if (is_array($id)) {
            $query = $this->model->newQuery();
            foreach ($id as $column => $value) {
                $query->where($column, $value);
            }
            return $query->delete();
        }
        return $this->model->where('opening_balance_id', $id)->delete();
    }

    public function header()
    {
        return [
            __('store::models/st_opening_balance_items.fields.product_id') ?? 'Product',
            __('store::models/st_opening_balance_items.fields.unit_id') ?? 'Unit',
            __('store::models/st_opening_balance_items.fields.quantity') ?? 'Quantity',
            __('store::models/st_opening_balance_items.fields.unit_cost') ?? 'Unit Cost',
            __('store::models/st_opening_balance_items.fields.total_cost') ?? 'Total Cost',
        ];
    }

    public function dataExel(): array
    {
        return StOpeningBalanceItem::with(['product', 'unit'])->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product->name ?? '',
                    'unit' => $item->unit->name ?? '',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('store::models/st_opening_balance_items.singular') ?? 'Opening Balance Item';
    }
}
