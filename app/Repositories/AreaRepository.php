<?php

namespace App\Repositories;

use App\Models\Area;

class AreaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Area::class;
    }

    public function statuses(): array
    {
        return Area::statuses();
    }
}
