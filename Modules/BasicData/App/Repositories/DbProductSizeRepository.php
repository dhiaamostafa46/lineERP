<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class DbProductSizeRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'status', 'product_id'];

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
        return ProductSize::class;
    }


    public function statuses(): array
    {
        return ProductSize::statuses();
    }


    public function types(): array
    {
        return ProductSize::types();
    }


    public function listItems($id)
    {
        return ProductSize::findOrFail($id);
    }


}
