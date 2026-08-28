<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrSalaryDeduct extends Model
{
    protected $table = 'hr_salary_deducts';

    public $fillable = [
        'id',
        'employee_id',
        'payroll_id',
        'salary_id',
        'deduct_id',
        'amount'
    ];
    public $timestamps = false;

    public function salary()
    {
        return $this->belongsTo(HrSalary::class, 'salary_id');
    }

    public function getNameAttribute()
    {
        return $this->deduct->name;
    }

    public function deduct()
    {
        return $this->belongsTo(HrDeduct::class, 'deduct_id');
    }

    /**
     * Get the payroll that owns the HrSalaryDeduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
