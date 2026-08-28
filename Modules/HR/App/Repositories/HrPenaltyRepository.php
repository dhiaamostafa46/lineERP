<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Helpers\TrackerTrait;

class HrPenaltyRepository extends BaseRepository
{
    use TrackerTrait;

    protected $fieldSearchable = [
        'employee_id',
        'description',
        'amount',
        'due_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }
    public function model(): string
    {
        return HrPenalty::class;
    }

    public function checkTracking($reward): void
    {
        $this->setTracker($reward, $reward->employee_id, HrTracker::TYPE_PENALTIES);
    }
}
