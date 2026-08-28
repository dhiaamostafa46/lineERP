<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAbsentRequests;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Helpers\TrackerTrait;
use Modules\HR\App\Models\HrHolidayType;


class HrAbsentRequestRepository extends BaseRepository
{
    //use TrackerTrait;

    protected $fieldSearchable = [
        'employee_id',
        'status',
        'from_at',
        'end_at',
        'request_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrAbsentRequests::class;
    }

    // Status
    public function statuses()
    {
        return $this->model()::statuses();
    }

    // types
    // public function types()
    // {
    //     return HrHolidayType::get()->pluck('name', 'id')->toArray();
    // }

    // employees
    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    // public function checkTracking($reward): void
    // {
    //     $this->setTracker($reward, $reward->employee_id, HrTracker::TYPE_HOLIDAYS);
    // }
}
