<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrReward;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Helpers\TrackerTrait;

class HrRewardRepository extends BaseRepository
{
    use TrackerTrait;

    protected $fieldSearchable = [
        'employee_id',
        'type',
        'amount',
        'status',
        'over_time',
        'days_off',
        'start_at',
        'end_at',
        'due_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    // Types
    public function types()
    {
        return HrReward::types();
    }

    // Status
    public function statuses()
    {
        return HrReward::statuses();
    }

    // Employees
    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    public function model(): string
    {
        return HrReward::class;
    }
    
    public function checkTracking($reward): void
    {
        $this->setTracker($reward, $reward->employee_id, HrTracker::TYPE_REWARDS);
    }
}
