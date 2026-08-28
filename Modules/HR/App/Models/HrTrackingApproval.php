<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HrTrackingApproval extends Model
{
    public $table = 'hr_tracking_approvals';

    public $fillable = [
        'trackable_id',
        'trackable_type',
        'user_id',
        'status', // 1 => pending, 2 => approved, 3 => rejected
        'sort',
        'note',
        'is_current'
    ];

    public $rules = [];

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    
    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('hr::models/hr_payroll_approvals.fields.pending'),
            self::STATUS_APPROVED => __('hr::models/hr_payroll_approvals.fields.approved'),
            self::STATUS_REJECTED => __('hr::models/hr_payroll_approvals.fields.rejected'),
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status_text', 'status_badge'];

    public function getStatusTextAttribute()
    {
        if ($this->is_current) {
            return __('hr::models/hr_payroll_approvals.fields.in_progress');
        }
        return $this->statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            self::STATUS_PENDING  => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-primary',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];

        if ($this->is_current) {
            return 'badge badge-info';
        }

        return $statuses[$this->status];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trackable()
    {
        return $this->morphTo();
    }
}
