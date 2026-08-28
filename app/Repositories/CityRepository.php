<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'status',
        'area_id',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return City::class;
    }

    public function statuses(): array
    {
        return City::statuses();
    }
}
