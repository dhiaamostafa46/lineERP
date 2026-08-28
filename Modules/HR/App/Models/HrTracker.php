<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrTrackerJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrTracker extends Model
{
    public $table = 'hr_trackers';

    public $fillable = [
        'department_id',
        'type', // 1 => Holidays, 2 => Penalties, 3 => Advances, 4 => Rewards
        'name',
        'status', // 1 => Inactive, default(2) => Active
        'tracker_approvals',
    ];

    protected $casts = [
        'id'                   => 'integer',
        'department_id'        => 'integer',
        'type'                 => 'integer',
        'name'                 => 'string',
        'status'               => 'integer',
        'tracker_approvals'    => 'array',
    ];


    public static array $rules = [
        'department_id'     => 'required|exists:hr_departments,id',
        'type'              => 'required',
        'status'            => 'required',
        'name'              => 'required|string|max:255',
        // 'tracker_approvals' => 'nullable|json|max:255'
    ];
    /**
     * Get the user's approvals payroll list.
     */
    protected function setTrackerApprovalsAttribute($value)
    {
        $this->attributes['tracker_approvals'] = json_encode($value);
    }

    const TYPE_HOLIDAYS = 1;
    const TYPE_PENALTIES = 2;
    const TYPE_ADVANCES = 3;
    const TYPE_REWARDS = 4;
    const TYPE_JUSTIFICATIONS = 5;

    public static function types()
    {
        return [
            self::TYPE_HOLIDAYS  => __('hr::models/hr_trackers.types.holidays'),
            self::TYPE_PENALTIES => __('hr::models/hr_trackers.types.penalties'),
            self::TYPE_ADVANCES => __('hr::models/hr_trackers.types.advances'),
            self::TYPE_REWARDS => __('hr::models/hr_trackers.types.rewards'),
            self::TYPE_JUSTIFICATIONS => __('hr::models/hr_trackers.types.justifications'),
        ];
    }

    public function getTypeTextAttribute()
    {
        return $this->types()[$this->type];
    }

    public function getTypeBadgeAttribute()
    {
        $types = [
            self::STATUS_INACTIVE  => 'badge badge-warning',
            self::STATUS_ACTIVE => 'badge badge-primary',
        ];

        return $types[$this->type];
    }
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE  => __('hr::models/hr_trackers.fields.inactive'),
            self::STATUS_ACTIVE => __('hr::models/hr_trackers.fields.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return $this->statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            self::STATUS_INACTIVE  => 'badge badge-warning',
            self::STATUS_ACTIVE => 'badge badge-primary',
        ];

        return $statuses[$this->status];
    }

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }


    public function jobs()
    {
        return $this->belongsToMany(HrJob::class, 'hr_tracker_jobs', 'tracker_id', 'job_id');
    }
}
