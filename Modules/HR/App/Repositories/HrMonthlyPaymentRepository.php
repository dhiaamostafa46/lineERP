<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJustification;
use App\Repositories\BaseRepository;
use Modules\HR\App\Helpers\TrackerTrait;

use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrMonthlyPayment;
use Modules\HR\App\Models\HrTracker;

class HrMonthlyPaymentRepository extends BaseRepository
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
        'attachment'
    ];



    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrMonthlyPayment::class;
    }

    public function statuses(): array
    {
        return HrMonthlyPayment::statuses();
    }

    public function types(): array
    {
        return HrMonthlyPayment::types();
    }

    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }


     public function updateOrCreate(array $attributes, array $values = []): HrMonthlyPayment
    {
        return HrMonthlyPayment::updateOrCreate($attributes, $values);
    }

}
