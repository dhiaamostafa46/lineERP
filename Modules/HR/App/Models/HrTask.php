<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Database\Factories\HrTaskFactory;

class HrTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_tasks'; // Define the table name explicitly

    protected $fillable = [
        'title',
        'description',
        'done',
        'status',
        'flage',
        'group',
        'employee_id',
        'department_id',
        'group_id',
        'file',
    ];

    /**
     * Relationship to HrTaskDetail.
     */
    public function HrTaskdetails()
    {
        return $this->hasMany(HrTaskDetail::class, 'hr_task_id');
    }

    /**
     * Relationship to HrEmployee.
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }


    public function Department()
    {
        return $this->belongsTo(HrDepartment::class);
    }

    public function Group()
    {
        return $this->belongsTo(HrGroup::class);
    }

    public static function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
        ];
    }

    // Define status constants
    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_PROCESSED = 3;
    const STATUS_CLOSED = 4;

    // Status mapping for different statuses
    public static function statuses()
    {
        return [
            self::STATUS_PENDING => __('hr::models/hr_tasks.statuses.pending'),
            self::STATUS_IN_PROGRESS => __('hr::models/hr_tasks.statuses.in_progress'),
            self::STATUS_PROCESSED => __('hr::models/hr_tasks.statuses.processed'),
            self::STATUS_CLOSED => __('hr::models/hr_tasks.statuses.closed'),
        ];
    }

    // Accessor to get the status text
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    // Accessor to get the status badge class
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_IN_PROGRESS => 'badge badge-primary',
            self::STATUS_PROCESSED => 'badge badge-info',
            self::STATUS_CLOSED => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-secondary'; // Default badge if status is unknown
    }

    // Define flage constants
    const FLAGE_DEPARTMENT = 1;
    const FLAGE_EMPLOYEES  = 2;
    const FLAGE_GROUP      = 3;

    // Flage mapping for different flags
    public static function flages()
    {
        return [
            self::FLAGE_DEPARTMENT => __('hr::models/hr_tasks.flages.department'),
            self::FLAGE_EMPLOYEES  => __('hr::models/hr_tasks.flages.employees'),
            self::FLAGE_GROUP      => __('hr::models/hr_tasks.flages.Group'),
        ];
    }

    // Accessor to get the flage text
    public function getFlageTextAttribute()
    {
        return self::flages()[$this->flage] ?? 'Unknown';
    }

    // Accessor to get the flage badge class
    public function getFlageBadgeAttribute()
    {
        $badges = [
            self::FLAGE_DEPARTMENT => 'badge badge-info',
            self::FLAGE_EMPLOYEES => 'badge badge-secondary',
        ];

        return $badges[$this->flage] ?? 'badge badge-secondary'; // Default badge if flage is unknown
    }
}
