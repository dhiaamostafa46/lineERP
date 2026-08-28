<?php

namespace Modules\HR\App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_attendances'; // Explicitly define the table name

    protected $fillable = ['employee_id', 'day', 'name', 'lat', 'lon', 'address', 'status', 'distance', 'checkTime', 'date', 'type', 'delay', 'early_leave', 'Active', 'overtime', 'shift_from', 'shift_to', 'early_arrival', 'kind'];

    /**Active
     * Relationship to HrEmployee.
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class , 'employee_id');
    }


    public function HrEmployee()
    {
        return $this->belongsTo(HrEmployee::class)->withTrashed();
    }

    /**
     * Validation rules for the model.
     */
    public static function rules()
    {
        return [
            'employee_id' => 'required',
            'day' => 'required',
            'lat' => 'nullable',
            'lon' => 'nullable',
        ];
    }

    // Status constants
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    /**
     * Retrieve the list of statuses.
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    /**
     * Accessor to get the status text.
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    /**
     * Accessor to get the status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === self::STATUS_ACTIVE ? 'badge badge-success' : 'badge badge-danger';
    }

    // Day constants
    const DAY_SUNDAY = 1;
    const DAY_MONDAY = 2;
    const DAY_TUESDAY = 3;
    const DAY_WEDNESDAY = 4;
    const DAY_THURSDAY = 5;
    const DAY_FRIDAY = 6;
    const DAY_SATURDAY = 7;

    /**
     * Retrieve the list of weekdays.
     */
    public static function weekdays()
    {
        return [
            self::DAY_SUNDAY => __('hr::models/hr_attendances.weekdays.sunday'),
            self::DAY_MONDAY => __('hr::models/hr_attendances.weekdays.monday'),
            self::DAY_TUESDAY => __('hr::models/hr_attendances.weekdays.tuesday'),
            self::DAY_WEDNESDAY => __('hr::models/hr_attendances.weekdays.wednesday'),
            self::DAY_THURSDAY => __('hr::models/hr_attendances.weekdays.thursday'),
            self::DAY_FRIDAY => __('hr::models/hr_attendances.weekdays.friday'),
            self::DAY_SATURDAY => __('hr::models/hr_attendances.weekdays.saturday'),
        ];
    }

    /**
     * Accessor to get the weekday text.
     */
    public function getWeekdaysTextAttribute()
    {
        return self::weekdays()[$this->day] ?? 'Unknown';
    }

    // Type constants
    const TYPE_PRESENCE = 1;
    const TYPE_CHECKOUT = 2;

    /**
     * Retrieve the list of types.
     */
    public static function types()
    {
        return [
            self::TYPE_PRESENCE => __('hr::models/hr_attendances.type.presence'),
            self::TYPE_CHECKOUT => __('hr::models/hr_attendances.type.checkout'),
        ];
    }

    /**
     * Accessor to get the type text.
     */
    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? 'Unknown';
    }

    /**
     * Scope a query to only include presence records.
     */
    public function scopePresenceOnly($query)
    {
        return $query->where('type', self::TYPE_PRESENCE);
    }

    /**
     * Scope a query to only include checkout records.
     */
    public function scopeCheckoutOnly($query)
    {
        return $query->where('type', self::TYPE_CHECKOUT);
    }
}
