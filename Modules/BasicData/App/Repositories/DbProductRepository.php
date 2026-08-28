<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\AccuSoft\TaxAccount;
use App\Models\BasicDataApp\Category;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;
use Modules\BasicData\App\Models\DbKitchen;

class DbProductRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $modelName = class_basename($this->model());
        $permissionPrefix = 'basicdata.' . str_replace('db_', '', \Illuminate\Support\Str::snake(\Illuminate\Support\Str::plural($modelName)));

        if (auth()->check()) {

        }

        return $query;
    }

    public function model(): string
    {
        return Product::class;
    }

    public function statuses(): array
    {
        return Product::statuses();
    }

    public function listItems($id)
    {
        return  Product::findOrFail($id);
    }

    public function vats()
    {
        return  TaxAccount::Active()->get()->pluck('name', 'id')->toArray();
    }

     public function types()
    {
        return  Product::types();
    }





     public function Categories()
    {
        return Category::get()->pluck('name', 'id')->toArray();
    }
     public function kitchens()
    {
        return DbKitchen::get()->pluck('name', 'id')->toArray();
    }

       public function units()
    {
        return Unit::get()->pluck('name', 'id')->toArray();
    }




    public function header(): array
    {
        return [
            __('basicdata::models/db_products.fields.name'),
            __('basicdata::models/db_products.fields.category_id'),
            __('basicdata::models/db_products.fields.type'),
            __('basicdata::models/db_products.fields.barcode'),

            __('basicdata::models/db_products.fields.cost_price'),
            __('basicdata::models/db_products.fields.prod_price'),
            __('basicdata::models/db_products.fields.vat'),
            __('basicdata::models/db_products.fields.have_sizes'),
        ];
    }

    public function dataExel(): array
    {
        return Product::with(['translations', 'category'])
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'category' => $product->category ? $product->category->name : '',
                    'type' => $product->type_text,
                    'barcode' => $product->barcode,

                    'cost_price' => $product->cost_price,
                    'prod_price' => $product->prod_price,
                    'vat' => $product->vat,
                    'have_sizes' => $product->have_sizes_text,

                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('basicdata::models/db_products.singular');
    }

}
