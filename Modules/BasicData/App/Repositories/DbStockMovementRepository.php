<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\StockMovement;

class DbStockMovementRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'status'];

    public function model(): string
    {
        return StockMovement::class;
    }
}
