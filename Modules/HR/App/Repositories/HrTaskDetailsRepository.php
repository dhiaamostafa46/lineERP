<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrTask;
use Modules\HR\App\Models\HrTaskDetail;

class HrTaskDetailsRepository extends BaseRepository
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
        return HrTaskDetail::class;
    }


    public function statuses(): array
    {
        return HrTask::statuses();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
}
