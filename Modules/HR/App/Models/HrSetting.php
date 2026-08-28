<?php

namespace Modules\HR\App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrSetting extends Model
{
    use SoftDeletes;

    protected $table = 'hr_settings';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'delivery_payroll_at',
        'preparing_payroll_at',
        'min_salary',
        'max_off_days',
        'currency',
        'payroll_id',
        'due_payroll_at',
        'payroll_updated',
        'approval_payroll',
        'preparing_payroll',
        'next_payroll_date',
        'last_payroll_date',
        'payroll_status',


        // Attendance & Leave settings
        'calculate_missing_fingerprint',
        'missing_fingerprint_policy',
        'leave_include_weekend',
        'leave_include_holidays',
        'shift_text',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'id'                            => 'integer',
        'delivery_payroll_at'           => 'integer',
        'preparing_payroll_at'          => 'integer',
        'min_salary'                    => 'decimal:2',
        'max_off_days'                  => 'integer',
        'currency'                      => 'string',

        'payroll_updated'               => 'boolean',
        'preparing_payroll'             => 'boolean',

        'calculate_missing_fingerprint' => 'boolean',
        'missing_fingerprint_policy'    => 'integer',
        'leave_include_weekend'          => 'boolean',
        'leave_include_holidays'         => 'boolean',
        'shift_text'                     => 'string',

        'approval_payroll'              => 'array',
        'due_payroll_at'                => 'date',
        'next_payroll_date'             => 'date',
        'last_payroll_date'             => 'date',
    ];

    /**
     * Validation rules
     */
    public static array $rules = [
        'delivery_payroll_at'            => 'required|integer|min:1|max:31',
        'preparing_payroll_at'           => 'required|integer|min:1|max:31',
        'min_salary'                     => 'required|numeric|min:0',
        'max_off_days'                   => 'required|integer|min:0',
        'currency'                       => 'required|string|max:10',
        'payroll_id'                     => 'nullable|exists:hr_payrolls,id',
        'due_payroll_at'                 => 'nullable|date',
        'approval_payroll'               => 'nullable|array',

        'calculate_missing_fingerprint'  => 'boolean',
        'missing_fingerprint_policy'     => 'required|in:1,2,3',
        'leave_include_weekend'           => 'boolean',
        'leave_include_holidays'          => 'boolean',
        'shift_text'                     => 'nullable|string',
    ];

    /**
     * Payroll statuses
     */
    const PAYROLL_STATUS_OPEN        = 'open';
    const PAYROLL_STATUS_READY       = 'ready';
    const PAYROLL_STATUS_IN_PROGRESS = 'in_progress';
    const PAYROLL_STATUS_CLOSED      = 'closed';

    /**
     * Missing fingerprint policies
     */
    const MISSING_FP_HALF_DAY     = 1;
    const MISSING_FP_FULL_DAY     = 2;
    const MISSING_FP_FULL_SHIFT     = 3;
    const MISSING_FP_HALF_SHIFT   = 4;
    const MISSING_FP_IGNORE       = 5;

    /**
     * Get available missing fingerprint policies
     *
     * @return array
     */
    public static function missingFingerprintPolicies(): array
    {
        return [


            self::MISSING_FP_HALF_DAY => __('hr::models/hr_settings.missing_fp.half_day'),
            self::MISSING_FP_FULL_DAY => __('hr::models/hr_settings.missing_fp.full_day'),
            self::MISSING_FP_FULL_SHIFT => __('hr::models/hr_settings.missing_fp.full_shift'),
            self::MISSING_FP_HALF_SHIFT => __('hr::models/hr_settings.missing_fp.half_shift'),
            self::MISSING_FP_IGNORE   => __('hr::models/hr_settings.missing_fp.ignore'),
        ];
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id');
    }

    /* -----------------------------------------------------------------
     |  Attributes
     | -----------------------------------------------------------------
     */

    protected function approvalPayroll(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => json_encode($value),
        );
    }

    public function getDeliveryAtAttribute(): ?string
    {
        if (!$this->delivery_payroll_at) {
            return null;
        }

        return Carbon::now()
            ->addMonth()
            ->format('Y-m') . '-' . $this->delivery_payroll_at;
    }

    public function getPreparingAtAttribute(): ?string
    {
        if (!$this->preparing_payroll_at) {
            return null;
        }

        return Carbon::now()
            ->format('Y-m') . '-' . $this->preparing_payroll_at;
    }

    public function getMissingFingerprintPolicyTextAttribute(): ?string
    {
        return self::missingFingerprintPolicies()[$this->missing_fingerprint_policy] ?? null;
    }

    /* -----------------------------------------------------------------
     |  Business Logic
     | -----------------------------------------------------------------
     */

    /**
     * Should calculate missing fingerprint penalty
     */
    public function shouldCalculateMissingFingerprint(): bool
    {
        return $this->calculate_missing_fingerprint === true
            && $this->missing_fingerprint_policy !== self::MISSING_FP_IGNORE;
    }

    /**
     * Missing fingerprint penalty in days
     */
    public function missingFingerprintPenaltyDays(): float
    {
        return match ($this->missing_fingerprint_policy) {
            self::MISSING_FP_HALF_DAY => 0.5,
            self::MISSING_FP_FULL_DAY => 1,
            default => 0,
        };
    }

    /**
     * Check if leave counts this date
     */
    public function leaveCountsDate(Carbon $date, bool $isHoliday = false): bool
    {
        if ($date->isWeekend() && !$this->leave_include_weekend) {
            return false;
        }

        if ($isHoliday && !$this->leave_include_holidays) {
            return false;
        }

        return true;
    }
}
