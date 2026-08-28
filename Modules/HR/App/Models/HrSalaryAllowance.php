<?php

namespace Modules\HR\App\Models;


use Modules\HR\App\Models\HrPayroll;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrSalaryAllowance extends Model
{
    protected $table = 'hr_salary_allowances';

    public $fillable = [
        'id',
        'employee_id',
        'payroll_id',
        'salary_id',
        'allowance_id',
        'amount'
    ];
    public $timestamps = false;

    public function salary()
    {
        return $this->belongsTo(HrSalary::class, 'salary_id');
    }

    public function getNameAttribute()
    {
        return $this->allowance->name ?? '';
    }

    public function allowance()
    {
        return $this->belongsTo(HrAllowance::class, 'allowance_id');
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
}
