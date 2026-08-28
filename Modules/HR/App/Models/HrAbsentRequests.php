<?php

namespace Modules\HR\App\Models;

use App\Models\User;
use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\App\Models\HrHolidayType;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrAbsentRequests extends Model
{
    use SoftDeletes;
    public $table = 'hr_absentrequests';

    // Status
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;


    public $fillable = [
        'employee_id',
        'approver_id',
        'details',
        'status',
        'from_at',
        'end_at',
        'request_date',
        
    ];

    protected $casts = [
        'id' => 'integer',
        'employee_id' => 'integer',
        'status' => 'integer',
        'from_at' => 'string',
        'end_at' => 'string',
        'request_date'=> 'date',
    ];

    public static array $rules = [
        'employee_id' => 'required|exists:employees,id',
        'from_at'     => 'required|string',
        'end_at'      => 'required|string',
        'status'      => 'nullable'
    ];

    // Status
    public static function statuses()
    {
        return [
            self::STATUS_PENDING  => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_REJECTED => __('lang.rejected')
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING  => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
        ];
        return $badges[$this->status];
    }

    ////////////////////////// Relations //////////////////////////

    public function type()
    {
        return $this->belongsTo(HrHolidayType::class, 'type_id');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
