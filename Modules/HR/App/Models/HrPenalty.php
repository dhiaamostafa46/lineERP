<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrPenalty extends Model
{
    use SoftDeletes;
    public $table = 'hr_penalties';

    public $fillable = [
        'employee_id',
        'approver_id',
        'payroll_id',
        'description',
        'amount',
        'due_date',
        'status' ,
        'type'
    ];



    const TYPE_PENALTY = 1;
    const TYPE_ABSENCE = 2;
    const TYPE_LATE    = 3;
    const TYPE_MISSING_FINGERPRINT =4;

    protected $casts = [
        'id'          => 'integer',
        'employee_id' => 'integer',
        'description' => 'string',
        'amount'      => 'integer',
        'status'      => 'integer',
        'due_date'    => 'date',
        'type'        => 'integer'
    ];

    public static array $rules = [
        'employee_id' => 'required',
        'description' => 'required',
        'amount'      => 'required',
        'due_date'    => 'required'
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }

    public function getDueDateTextAttribute()
    {
        if ($this->due_date) {
            return $this->due_date->format('Y-m-d');
        }
        return null;
    }
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id')
            ->where('status', '<', HrPayroll::STATUS_ACCREDITED);
    }
    // Status
    const STATUS_PENDING  = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    // Status
    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected')
        ];
    }

    // get status text
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING  => 'badge badge-warning',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_APPROVED => 'badge badge-success',
        ];
        return $badges[$this->status];
    }

    // Type
    public static function types()
    {
        return [
            self::TYPE_PENALTY  => __('lang.penalty'),
            self::TYPE_ABSENCE => __('lang.absence'),
            self::TYPE_LATE => __('lang.late'),
            self::TYPE_MISSING_FINGERPRINT => __('lang.fingerprint')
        ];
    }

    // get type text
    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? null;
    }

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPayroll($query)
    {
        return $query->whereNotNull('payroll_id');
    }

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutPayroll($query)
    {
        return $query->whereNull('payroll_id');
    }


    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
