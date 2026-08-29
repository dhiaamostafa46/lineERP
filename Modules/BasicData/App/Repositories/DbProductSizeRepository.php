<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\ProductSize;

class DbProductSizeRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'status', 'product_id'];

    public function model(): string
    {
        return ProductSize::class;
    }
}
