<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HrSalary extends Model
{
    use SoftDeletes;
    public $table = 'hr_salaries';

    public $fillable = [
        'employee_id',
        'basic',
        'day_amount',
        'hour_amount'
    ];

    protected $casts = [
        'id'          => 'integer',
        'employee_id' => 'integer',
        'basic'       => 'string'
    ];

    public static array $rules = [];


    public function setDayAmountAttribute()
    {
        return $this->attributes['day_amount'] = $this->attributes['basic'] / 30;
    }

    public function setHourAmountAttribute()
    {
        return $this->attributes['hour_amount'] = $this->attributes['basic'] / 176;
    }

    public function totalAllowance()
    {
        return $this->salary_allowances->sum('amount');
    }

    public function totalDeduct()
    {
        return $this->salary_deducts->sum('amount');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    /**
     * The allowances that belong to the HrSalary
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function allowances(): BelongsToMany
    {
        return $this->belongsToMany(HrAllowance::class, 'hr_salary_allowances', 'salary_id', 'allowance_id')->withPivot('amount');
    }

    /**
     * The deducts that belong to the HrSalary
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deducts(): BelongsToMany
    {
        return $this->belongsToMany(HrDeduct::class, 'hr_salary_deducts', 'salary_id', 'deduct_id')->withPivot('amount');
    }

    /**
     * Get all of the salary_allowances for the HrSalary
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salary_allowances(): HasMany
    {
        return $this->hasMany(HrSalaryAllowance::class, 'salary_id');
    }
    /**
     * Get all of the salary_deducts for the HrSalary
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salary_deducts(): HasMany
    {
        return $this->hasMany(HrSalaryDeduct::class, 'salary_id');
    }
}
