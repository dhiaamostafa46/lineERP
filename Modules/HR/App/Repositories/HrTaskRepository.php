<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrGroup;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrTask;

class HrTaskRepository extends BaseRepository
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
        return HrTask::class;
    }


    public function statuses(): array
    {
        return HrTask::statuses();
    }

    public function flages(): array
    {
        return HrTask::flages();
    }

    public function Done($task)
    {
        $task->done =date('Y-m-d H:i:s');
        $task->save();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }

    public function Department(): array
    {
        return HrDepartment::get()->pluck('name', 'id')->toArray();
    }


    public function Group()
    {
        return HrGroup::get()->pluck('name', 'id')->toArray();
    }
}
