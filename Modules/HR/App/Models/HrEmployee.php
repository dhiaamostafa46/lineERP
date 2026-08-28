<?php

namespace Modules\HR\App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrEmployee extends Model
{
    use SoftDeletes;

    public $table = 'hr_employees';

    public $fillable = [
        'user_id',
        'employee_id',
        'job_id',
        'department_id',
        'shift_id',
        'username',
        'max_off_days',
        'max_advance',
        'job_level',
        'specialty',
        'start_at',
        'license_expired_at',
        'Direct_manager',
        'job_number', // الرقم الوظيفي
        'vacation_balance', // الرصيد السابق للإجازات
        'fingerprint_exempt', // إخفاء الموظف من البصمة
        'attendance_type',
    ];

    protected $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'job_id' => 'integer',
        'department_id' => 'integer',
        'shift_id' => 'integer',
        'max_off_days' => 'integer',
        'max_advance' => 'decimal:2',
        'job_level' => 'string',
        'specialty' => 'string',
        'start_at' => 'string',
        'license_expired_at' => 'string',
        'job_number' => 'string',
        'vacation_balance' => 'decimal:2',
        'fingerprint_exempt' => 'boolean',
        'attendance_type' => 'integer',
    ];

    public function setFingerprintExemptAttribute($value)
    {
        $this->attributes['fingerprint_exempt'] = $value ? 1 : 0;
    }

    public static array $rules = [
        'employee_id' => 'required',
        'job_id' => 'required',
        'department_id' => 'required',
        'shift_id' => 'required',
        'max_off_days' => 'required',
        'max_advance' => 'required',
        'start_at' => 'required',
        // 'license_expired_at' => 'required',
    ];

    public static function boot()
    {
        parent::boot();

        // The pre-generation of HrHolidayBalance records has been removed.
        // Balances are now calculated dynamically in HrHolidayBalanceRepository->FindBalance.

    }

    public function totalAdvances()
    {
        return $this->approved_advances->sum('amount');
    }

    const ATTENDANCE_All = 0;
    const ATTENDANCE_FINGERPRINT = 1;
    const ATTENDANCE_GEOGRAPHIC = 2;

    public static function attendanceTypes()
    {
        return [
            self::ATTENDANCE_All => __('lang.all'),
            self::ATTENDANCE_FINGERPRINT => __('lang.fingerprint'),
            self::ATTENDANCE_GEOGRAPHIC => __('lang.geographic'),
        ];
    }

    public function getAttendanceTypeTextAttribute()
    {
        return self::attendanceTypes()[$this->attendance_type] ?? __('lang.not_defined');
    }

    const FINGERPRINT_EXEMPT_TRUE = 1; // معفى من البصمة
    const FINGERPRINT_EXEMPT_FALSE = 0; // غير معفى

    public static function fingerprintExempts()
    {
        return [
            self::FINGERPRINT_EXEMPT_TRUE => __('lang.yes'),
            self::FINGERPRINT_EXEMPT_FALSE => __('lang.no'),
        ];
    }

    public function getFingerprintExemptTextAttribute()
    {
        return self::fingerprintExempts()[$this->fingerprint_exempt] ?? __('lang.not_defined');
    }

    public function totalPenalties()
    {
        return $this->penalties->sum('amount');
    }

    public function getNameAttribute(): ?string
    {
        return $this->main_employee?->full_name 
            ?? $this->main_employee?->name 
            ?? $this->user?->name 
            ?? $this->username 
            ?? null;
    }

    /**
     * Get the employee that owns the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function main_employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function main_employeewithTrashed(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withTrashed();
    }

    /**
     * Get the job that owns the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(HrJob::class, 'job_id');
    }

    /**
     * Get the department that owns the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    /**
     * Get the shift that owns the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(HrShiftType::class, 'shift_id');
    }

    /**
     * Get the salary associated with the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salary(): HasOne
    {
        return $this->hasOne(HrSalary::class, 'employee_id', 'id');
    }

    /**
     * Get all of the penalty for the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penalties(): HasMany
    {
        $now = Carbon::now();

        return $this->hasMany(HrPenalty::class, 'employee_id')->whereMonth('due_date', $now->month);
    }

    /**
     * Get all of the advances for the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function advances(): HasMany
    {
        return $this->hasMany(HrAdvance::class, 'employee_id')->latest();
    }

    public function approved_advances(): HasMany
    {
        return $this->hasMany(HrAdvance::class, 'employee_id')->where('status', HrAdvance::STATUS_APPROVED);
    }

    /**
     * Get all of the rewards for the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(HrReward::class, 'employee_id');
    }

    public function DirectManager(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'Direct_manager');
    }

    /**
     * Get the user that owns the HrEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function holidays()
    {
        return $this->hasMany(HrHoliday::class, 'employee_id')->latest();
    }
    public function HolidayBalance()
    {
        return $this->hasMany(HrHolidayBalance::class, 'employee_id');
    }

    public function Contract()
    {
        return $this->hasOne(HrContract::class, 'employee_id');
    }

    public function EndServer()
    {
        return $this->hasOne(HrEndService::class, 'employee_id');
    }

    public function Custodies()
    {
        return $this->hasMany(HrCustody::class, 'employee_id');
    }

    public function Task()
    {
        return $this->hasMany(HrTask::class, 'employee_id');
    }

    public function Place()
    {
        return $this->hasMany(HrPlace::class, 'employee_id');
    }

    public function AbsentRequests()
    {
        return $this->hasMany(HrAbsentRequests::class, 'employee_id');
    }

    public function Document()
    {
        return $this->hasMany(HrDocument::class, 'employee_id');
    }
}
