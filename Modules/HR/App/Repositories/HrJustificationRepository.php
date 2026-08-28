<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJustification;
use App\Repositories\BaseRepository;
use Modules\HR\App\Helpers\TrackerTrait;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrTracker;

class HrJustificationRepository extends BaseRepository
{
     use TrackerTrait;
    protected $fieldSearchable = [
        'reason',
        'request_date' ,
        'attendance_id',
        'employee_id',
        'type',
        'status',
        'approved_by',
        'approved_at',
        'approver_id',
    ];



    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrJustification::class;
    }

    public function statuses(): array
    {
        return HrJustification::statuses();
    }

    public function types(): array
    {
        return HrJustification::types();
    }

    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    public function attendances()
    {
        return HrAttendance::latest()->get()->pluck('date', 'id')->toArray();
    }

     public function checkTracking($Justification): void
    {
        $this->setTracker($Justification, $Justification->employee_id, HrTracker::TYPE_JUSTIFICATIONS);
    }
}
