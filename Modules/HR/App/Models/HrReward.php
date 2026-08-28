<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrReward extends Model
{
    use SoftDeletes;

    public $table = 'hr_rewards';

    // Types
     const TYPE_OVERTIME = 1;
    const TYPE_STATIC_AMOUNT = 2;
    const TYPE_COMPENSATORY_HOLIDAYS = 3;
    const TYPE_IN_KIND_REWARD   = 4;

    // Status
    const STATUS_PENDING  = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    public $fillable = [
        'employee_id',
        'approver_id',
        'payroll_id',
        'type',
        'amount',
        'status',
        'over_time',
        'days_off',
        'start_at',
        'end_at',
        'due_date',
        'note'
    ];

    protected $casts = [
        'id'          => 'integer',
        'employee_id' => 'integer',
        'type'        => 'integer',
        'amount'      => 'integer',
        'status'      => 'integer',
        'over_time'   => 'integer',
        'days_off'    => 'integer',
        'start_at'    => 'date',
        'end_at'      => 'date',
        'due_date'    => 'date'
    ];

    public static array $rules = [
        'employee_id' => 'required',
        'type'        => 'required',
        'amount'      => 'required',
        'status'      => 'required',
        'over_time'   => 'required',
        'days_off'    => 'required',
        'start_at'    => 'required',
        'end_at'      => 'required'

    ];

    // Types
    public static function types()
    {
        return [
            // self::TYPE_OVERTIME              => __('hr::models/hr_rewards.fields.overtime'),
            self::TYPE_STATIC_AMOUNT         => __('hr::models/hr_rewards.fields.static_amount'),
            self::TYPE_COMPENSATORY_HOLIDAYS => __('hr::models/hr_rewards.fields.compensatory_holidays'),
            self::TYPE_IN_KIND_REWARD        => __('hr::models/hr_rewards.fields.in_kind_reward'),
        ];
    }

    // get type text
    public function getTypeTextAttribute()
    {
        return self::types()[$this->type];
    }



    public static function getTypeText($value)
    {
        $types = self::types(); // جلب الأنواع
        return $types[$value] ?? '-'; // إرجاع النص المناسب أو '-' إذا كان النوع غير معروف
    }

    public function getValueTextAttribute()
    {
        switch ($this->type) {
            case self::TYPE_STATIC_AMOUNT:
                return $this->amount . ' ' . currency();
            case self::TYPE_COMPENSATORY_HOLIDAYS:
                return __('hr::models/hr_rewards.days', [
                    'days_off' => $this->days_off,
                    'start_at' => $this->start_at->format('d-m-Y'),
                    'end_at' => $this->end_at->format('d-m-Y'),
                ]);
            case self::TYPE_IN_KIND_REWARD:
                return $this->note;
            case self::TYPE_OVERTIME:
                return __('hr::models/hr_rewards.over_time_hours', [
                    'over_time' => $this->over_time,
                    'amount' => $this->amount  . ' ' . currency()
                ]);
            default:
                return '-';
        }
    }
    public function getDueDateTextAttribute()
    {
        if ($this->due_date) {
            return $this->due_date->format('Y-m-d');
        }
        return null;
    }

    // Status
    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected')
        ];
    }




    public static function getstatusesText($value)
    {
        $types = self::statuses(); // جلب الأنواع
        return $types[$value] ?? '-'; // إرجاع النص المناسب أو '-' إذا كان النوع غير معروف
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

    ////////////////////////// Relations //////////////////////////
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id')
            ->where('status', '<', HrPayroll::STATUS_ACCREDITED);
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
