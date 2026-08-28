<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'status',
        'city_id',
        'email',
        'phone',
        'contact_person',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Company::class;
    }

    public function statuses(): array
    {
        return Company::statuses();
    }
}
