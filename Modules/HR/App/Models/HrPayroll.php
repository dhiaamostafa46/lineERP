<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

class HrPayroll extends Model
{
    use SoftDeletes;
    public $table = 'hr_payrolls';

    public $fillable = [
        'total',
        'payroll_date',
        'currency',
        'delivery_at',
        'tab',
        'preparing_at', // default(now())
        'status', // 1 = draft, default(2) = preparing, 3 = accredited, 4 = delivered
        'approvals_is_ready'
    ];

    protected $casts = [
        'id'           => 'integer',
        'total'        => 'float',
        'payroll_date' => 'date',
        'delivery_at'  => 'date',
        'preparing_at' => 'date',
        'status'       => 'integer'
    ];

    const STATUS_DRAFT = 1;
    const STATUS_PREPARING = 2;
    const STATUS_ACCREDITED = 3;
    const STATUS_LATE = 4;
    const STATUS_DELIVERED = 5;

    public static array $rules = [];


    public static function statuses()
    {
        return [
            self::STATUS_DRAFT      => __('hr::models/hr_payrolls.fields.draft'),
            self::STATUS_PREPARING  => __('hr::models/hr_payrolls.fields.preparing'),
            self::STATUS_ACCREDITED => __('hr::models/hr_payrolls.fields.accredited'),
            self::STATUS_LATE       => __('hr::models/hr_payrolls.fields.late'),
            self::STATUS_DELIVERED  => __('hr::models/hr_payrolls.fields.delivered'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_DRAFT      => 'badge badge-light-danger',
            self::STATUS_PREPARING  => 'badge badge-light-warning',
            self::STATUS_ACCREDITED => 'badge badge-light-primary',
            self::STATUS_LATE       => 'badge badge-light-danger',
            self::STATUS_DELIVERED  => 'badge badge-light-success',
        ];
        return $badges[$this->status];
    }

    public function getPayrollDateTextAttribute()
    {
        return $this->payroll_date->format('M Y');
    }

    public function getPreparingAtTextAttribute()
    {
        return $this->preparing_at->diffForHumans();
    }

    public function getDeliveryAtTextAttribute()
    {
        return $this->delivery_at->format('d M Y');
    }
    public function getTotalTextAttribute()
    {
        return number_format($this->total, 2) . ' ' . $this->currency;
    }

    public function payroll_employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HrPayrollEmployee::class, 'payroll_id');
    }

    public function payroll_approval()
    {
        return $this->belongsToMany(HrEmployee::class, 'hr_payroll_approval', 'payroll_id', 'employee_id');
    }

    public function payroll_approvals()
    {
        return $this->hasMany(HrPayrollApproval::class, 'payroll_id');
    }
}
