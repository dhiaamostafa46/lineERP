<?php

namespace Modules\HR\App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrDepartment;

class HrTrackerRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'department_id',
        'type',
        'status',
        'name',
        'tracker_approvals'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTracker::class;
    }

    public function types()
    {
        return $this->model->types();
    }

    public function statuses()
    {
        return $this->model->statuses();
    }

    public function departments()
    {
        return HrDepartment::get()->pluck('name', 'id');
    }

    public function users()
    {
        return User::get()->pluck('name', 'id')->toArray();
    }
}
