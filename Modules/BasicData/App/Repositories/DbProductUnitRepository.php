<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\ProductUnit;

class DbProductUnitRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'status', 'product_id'];

    public function model(): string
    {
        return ProductUnit::class;
    }
}
