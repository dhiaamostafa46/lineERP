<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrDepartment;
use App\Repositories\BaseRepository;

class HrDepartmentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status',
        'code',
        'type',
        'parent_id',
        'owner_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrDepartment::class;
    }

    public function types(): array
    {
        return HrDepartment::types();
    }

    public function statuses(): array
    {
        return HrDepartment::statuses();
    }

    public function parents(): array
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }
    public function owners(): array
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }
}
