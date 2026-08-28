<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrHolidayBalance extends Model
{
    use SoftDeletes;

    protected $table = 'hr_holiday_balances';

    protected $fillable = [
        'employee_id',
        'type_id',
        'balance',
        'annual_balance',
        'previous_balance',
        'status',
        'allowed'
    ];

    protected $casts = [
        'balance' => 'float',
        'annual_balance' => 'float',
        'previous_balance' => 'float',
        'allowed' => 'float',
        'status' => 'integer',
    ];

    // 🟩 ثوابت الحالة
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    /**
     * العلاقة مع الموظف
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    /**
     * العلاقة مع نوع الإجازة
     */
    public function holidayType()
    {
        return $this->belongsTo(HrHolidayType::class, 'type_id');
    }

    /**
     * العلاقة مع سجلات الإجازات (HrHoliday)
     */
    public function holidays()
    {
        return $this->hasMany(HrHoliday::class, 'type_id', 'type_id')
            ->where('employee_id', $this->employee_id);
    }

    /**
     * الحصول على الإجازات المعتمدة فقط
     */
    public function approvedHolidays()
    {
        return $this->holidays()
            ->where('status', HrHoliday::STATUS_APPROVED);
    }

    /**
     * الحالات المتاحة
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE   => __('lang.active'),
        ];
    }

    /**
     * عرض نص الحالة
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }

    /**
     * عرض بادج الحالة (لون الحالة)
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE   => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    /**
     * احسب الرصيد الإجمالي (السنوي + السابق)
     */
    public function getTotalBalanceAttribute()
    {
        return (float) $this->annual_balance + (float) $this->previous_balance;
    }

    /**
     * حساب عدد أيام الإجازة المستخدمة
     */
    public function getUsedDaysAttribute()
    {
        return $this->approvedHolidays()->get()->sum(function ($holiday) {
            return $holiday->from_at->diffInDays($holiday->end_at) + 1;
        });
    }

    /**
     * حساب الرصيد المتبقي
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->balance - $this->used_days;
    }

    /**
     * التحقق من وجود رصيد كافي للإجازة الجديدة
     */
    public function hasEnoughBalance($daysNeeded)
    {
        return $this->remaining_balance >= $daysNeeded;
    }

    /**
     * تحديث الرصيد عند الموافقة على إجازة
     * يتم استدعاؤها من GetStatus عند الموافقة
     */
    public function updateBalanceOnApproval(HrHoliday $holiday)
    {
        $daysUsed = $holiday->from_at->diffInDays($holiday->end_at) + 1;

        if (!$this->hasEnoughBalance($daysUsed)) {
            throw new \Exception(__('lang.insufficient_balance'));
        }

        $this->balance -= $daysUsed;
        $this->save();

        return true;
    }

    /**
     * استرجاع الرصيد عند رفض الإجازة
     * يتم استدعاؤها من GetStatus عند الرفض
     */
    public function rollbackBalanceOnRejection(HrHoliday $holiday)
    {
        $daysUsed = $holiday->from_at->diffInDays($holiday->end_at) + 1;

        $this->balance += $daysUsed;
        $this->save();

        return true;
    }

    /**
     * الحصول على تفاصيل الرصيد
     */
    public function getBalanceDetails()
    {
        return [
            'annual_balance' => $this->annual_balance,
            'previous_balance' => $this->previous_balance,
            'total_balance' => $this->total_balance,
            'used_days' => $this->used_days,
            'remaining_balance' => $this->remaining_balance,
            'percentage_used' => ($this->used_days / $this->balance) * 100,
        ];
    }

    /**
     * Scope: عرض فقط السجلات النشطة
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: عرض فقط السجلات غير النشطة
     */
    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope: البحث عن رصيد موظف معين بنوع إجازة معين
     */
    public function scopeForEmployeeAndType($query, $employeeId, $typeId)
    {
        return $query->where('employee_id', $employeeId)
                     ->where('type_id', $typeId);
    }
}
