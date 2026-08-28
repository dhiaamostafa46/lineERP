<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrPayrollEmployee extends Model
{
    use SoftDeletes;

    public $table = 'hr_payroll_employees';

    public $fillable = [
        'employee_id',
        'payroll_id',
        'currency',
        'username',
        'job_name',
        'department_name',
        'basic_wage',
        'total_allowances',
        'total_deducts',
        'total_penalties',
        'total_advances',
        'total_rewards',
        'net_wage',
        'status', // default(1) = pending, 2 = approved, 3 = rejected
    ];

    protected $casts = [
        'id'               => 'integer',
        'employee_id'      => 'integer',
        'payroll_id'       => 'integer',
        'salary_id'        => 'integer',
        'total_allowances' => 'integer',
        'total_deducts'    => 'integer',
        'basic_salary'     => 'integer',
        'status'           => 'integer'
    ];

    public static array $rules = [];


    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('hr::models/hr_payroll_transactions.fields.pending'),
            self::STATUS_APPROVED => __('hr::models/hr_payroll_transactions.fields.approved'),
            self::STATUS_REJECTED => __('hr::models/hr_payroll_transactions.fields.rejected'),
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
        return $this->statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            self::STATUS_PENDING  => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-primary',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];

        return $statuses[$this->status];
    }

    /**
     * Get all of the transaction for the HrPayrollEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(HrPayrollTransaction::class, 'payroll_employee_id');
    }

    /**
     * Get the payroll that owns the HrPayrollEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id');
    }
}
