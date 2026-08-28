<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Employee;
use App\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'full_name',
        'username',
        'phone',
        'email',
        'dob',
        'address',
        'national_address',
        'religion',
        'gender',
        'marital_status',
        'number_of_children',
        'nationality'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Employee::class;
    }


    public function branches(): array
    {
            return Branch::select('id')
            ->with('translations:branch_id,locale,name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

}
