<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPayrollTransaction extends Model
{
    use SoftDeletes;
    public $table = 'hr_payroll_transactions';

    public $fillable = [
        'payroll_employee_id',
        'forable_id',
        'forable_type',
        'amount',
        'currency', // default('SAR')
        'is_deduct',
        'note',
        'name',
        'type', // 1 => basic_wage, 2 => allowance, 3 => deduction, 4 => penalty, 5 => advance, 6 => reward
        'status' // default(1) => pending, 2 => approved, 3 => rejected
    ];

    protected $casts = [
        'id'                  => 'integer',
        'payroll_employee_id' => 'integer',
        'forable_id'          => 'integer',
        'forable_type'        => 'string',
        'amount'              => 'integer',
        'currency'            => 'string',
        'is_deduct'           => 'boolean',
        'type'                => 'integer',
        'status'              => 'integer',
        'note'                => 'string'
    ];

    public static array $rules = [];

    const TYPE_SALARY = 1;
    const TYPE_ALLOWANCE = 2;
    const TYPE_DEDUCT = 3;
    const TYPE_PENALTY = 4;
    const TYPE_ADVANCE = 5;
    const TYPE_REWARD = 6;

    public static function types()
    {
        return [
            self::TYPE_SALARY    => __('hr::models/hr_payroll_transactions.fields.basic_salary'),
            self::TYPE_ALLOWANCE => __('hr::models/hr_payroll_transactions.fields.allowance'),
            self::TYPE_DEDUCT    => __('hr::models/hr_payroll_transactions.fields.deduction'),
            self::TYPE_PENALTY   => __('hr::models/hr_payroll_transactions.fields.penalty'),
            self::TYPE_ADVANCE   => __('hr::models/hr_payroll_transactions.fields.advance'),
            self::TYPE_REWARD    => __('hr::models/hr_payroll_transactions.fields.reward'),
        ];
    }

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
    protected $appends = ['status_text', 'status_badge', 'type_text'];

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

    public function getTypeTextAttribute()
    {
        return $this->types()[$this->type];
    }



      public function NameTransactions()
    {
        return $this->belongsTo($this->forable_type::class, 'forable_id');
    }
}
