<?php

namespace Modules\HR\App\Repositories;
use App\Models\Branch;
use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPlace;

class HrPlaceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'employee_id',
        'day',
        'name',
        'lat',
        'lon',
        'address',
        'status',
        'distance',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPlace::class;
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }

    public function statuses(): array
    {
        return HrPlace::statuses();
    }

    public function weekdays(): array
    {
        return HrPlace::weekdays();
    }

    public function Department(): array
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }


    public function Branches(): array
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }


    public function flages(): array
    {
        return HrPlace::flages();
    }

}

