<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrPayrollApproval extends Model
{
    use SoftDeletes;

    public $table = 'hr_payroll_approvals';

    public $fillable = [
        'payroll_id',
        'user_id',
        'status',
        'note',
        'sort',
        'is_current'
    ];

    protected $casts = [
        'id'         => 'integer',
        'payroll_id' => 'integer',
        'user_id'    => 'integer',
        'status'     => 'integer'
    ];

    public static array $rules = [];

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
        if($this->is_current) {
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

    /**
     * Get the user that owns the HrPayrollApproval
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
