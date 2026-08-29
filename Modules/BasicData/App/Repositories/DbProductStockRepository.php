<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\ProductStock;

class DbProductStockRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'status'];

    public function model(): string
    {
        return ProductStock::class;
    }
}
