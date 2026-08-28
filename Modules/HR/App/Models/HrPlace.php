<?php

namespace Modules\HR\App\Models;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\App\Models\HrDepartment;

class HrPlace extends Model
{
    

    use HasFactory, SoftDeletes;

    protected $table = 'hr_places'; // Specify the table if it's not the plural of the model name

    protected $fillable = ['employee_id', 'day', 'name', 'lat', 'lon', 'address', 'status', 'distance', 'flage', 'department_id', 'branch_id', 'start_date', 'end_date' ,'enable_daterange'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'day' => 'array',
        'employee_id' => 'array',
        'department_id' => 'array',
        'branch_id' => 'array',
    ];

    public static function rules()
    {
        return [
            'flage' => 'required',
            'day' => 'required',
            'name' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'distance' => 'required|numeric',
            'status' => 'required|integer',
        ];
    }

    /**
     * Define a relationship with the Employee model.
     */
    public function employees()
    {
        return HrEmployee::whereIn('id', $this->employee_id ?? []);
    }

    /**
     * Define a relationship with the HrDepartment model.
     */
    public function departments()
    {
        return HrDepartment::whereIn('id', $this->department_id ?? []);
    }

    /**
     * Define a relationship with the Branch model.
     */
    public function branches()
    {
        return Branch::whereIn('id', $this->branch_id ?? []);
    }

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status] ?? 'badge badge-secondary'; // Default badge if status is unknown
    }

    const DAY_ALL = 0;
    const DAY_SUNDAY = 1;
    const DAY_MONDAY = 2;
    const DAY_TUESDAY = 3;
    const DAY_WEDNESDAY = 4;
    const DAY_THURSDAY = 5;
    const DAY_FRIDAY = 6;
    const DAY_SATURDAY = 7;

    public static function weekdays()
    {
        return [
            self::DAY_ALL => __('hr::models/hr_places.weekdays.all'),
            self::DAY_SUNDAY => __('hr::models/hr_places.weekdays.sunday'),
            self::DAY_MONDAY => __('hr::models/hr_places.weekdays.monday'),
            self::DAY_TUESDAY => __('hr::models/hr_places.weekdays.tuesday'),
            self::DAY_WEDNESDAY => __('hr::models/hr_places.weekdays.wednesday'),
            self::DAY_THURSDAY => __('hr::models/hr_places.weekdays.thursday'),
            self::DAY_FRIDAY => __('hr::models/hr_places.weekdays.friday'),
            self::DAY_SATURDAY => __('hr::models/hr_places.weekdays.saturday'),
        ];
    }

    public function getWeekdaysTextAttribute()
    {
        if (is_array($this->day)) {
            $dayNames = [];
            $weekdays = self::weekdays();
            foreach ($this->day as $dayKey) {
                if (isset($weekdays[$dayKey])) {
                    $dayNames[] = $weekdays[$dayKey];
                }
            }
            return implode(', ', $dayNames);
        }
        return self::weekdays()[$this->day] ?? 'Unknown';
    }

    const FLAG_ALL = 1;
    const FLAG_DEPARTMENT = 3;
    const FLAG_EMPLOYEES = 2;
    const FLAG_BRANCHES = 4;

    public static function flages()
    {
        return [
            self::FLAG_ALL => __('hr::models/hr_places.flages.all'),
            self::FLAG_DEPARTMENT => __('hr::models/hr_places.flages.department'),
            self::FLAG_EMPLOYEES => __('hr::models/hr_places.flages.employees'),
            self::FLAG_BRANCHES => __('hr::models/hr_places.flages.branches'),
        ];
    }

    public function getFlagTextAttribute()
    {
        return self::flages()[$this->flage] ?? 'Unknown';
    }
}


