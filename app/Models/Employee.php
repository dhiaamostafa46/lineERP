<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Imported related models used in relationships
use App\Models\User;
use App\Models\Branch;
use App\Models\EmployeeBank;
use App\Models\EmployeeIdentity;
use Modules\HR\App\Models\HrEmployee as HrEmployeeModule; // Aliased to prevent collision if a local HrEmployee existed
use Modules\HR\App\Models\HrHoliday;

class Employee extends Model
{
   

    use SoftDeletes, HasFactory;

    public $table = 'employees';

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value ? preg_replace('/[^\d\+]/', '', $value) : $value;
    }

    // --- Gender Constants ---
    const NOT_DEFINED = null;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    // --- Marital Status Constants ---
    const MARITAL_STATUS_SINGLE = 1;
    const MARITAL_STATUS_MARRIED = 2;
    const MARITAL_STATUS_DIVORCED = 3;
    const MARITAL_STATUS_WIDOWED = 4;
    const MARITAL_STATUS_ENGAGED = 5;

    // --- Tab Constants ---
    const TABS = [
        'main'            => 'main',
        'location'        => 'location',
        'settlement'      => 'settlement',
        'vacations'       => 'vacations',
        'advances'        => 'advances',
        // 'penalties'       => 'penalties',
        // 'permissions'     => 'permissions',
        // 'custody'          => 'custody',
        // 'tasks'           => 'tasks',
        'documents'       => 'documents',
    ];

    public $fillable = [
        'user_id',
        'branch_id',
        'org_id',
        'full_name',
        'username',
        'phone',
        'email',
        'dob',
        'address',
        'national_address',
        'religion',
        'gender',
        'marital_status',
        'number_of_children',
        'nationality',
        'tab' // Added to fillable
    ];

    public function getNameAttribute(): ?string
    {
        return $this->full_name ?? $this->attributes['name'] ?? null;
    }

    protected $casts = [
        'id'                 => 'integer',
        'full_name'          => 'string',
        'username'           => 'string',
        'phone'              => 'string',
        'email'              => 'string',
        'dob'                => 'string',
        'address'            => 'string',
        'national_address'   => 'string',
        'religion'           => 'string',
        'gender'             => 'integer',
        'marital_status'     => 'integer',
        'number_of_children' => 'integer',
        'nationality'        => 'string',
        'tab'                => 'string', // Tab cast added
    ];

    public static array $rules = [
        'full_name'          => 'required|string|max:255',
        'username'           => 'required|string|max:255|unique:employees,username',
        'phone'              => 'nullable|string|max:255',
        'email'              => 'nullable|string|max:255',
        'dob'                => 'nullable|string|max:255',
        'address'            => 'nullable|string|max:255',
        'national_address'   => 'nullable|string|max:255',
        'religion'           => 'nullable|string|max:255',
        'gender'             => 'required|integer',
        'marital_status'     => 'required|integer',
        'nationality'        => 'string|max:255',
        // 'tab' rule is omitted here as it's typically set internally
    ];

    /**
     * Helper to get all available tabs.
     */
    public static function availableTabs(): array
    {
        return self::TABS;
    }

    /**
     * Accessor for the tab attribute.
     */
    public function getTabAttribute(?string $value): ?string
    {
        // Ensures a default tab if none is set
        return $value ?? self::TABS['main'];
    }

    /**
     * Mutator for the tab attribute, ensures the value is a valid tab.
     */
    public function setTabAttribute(string $value): void
    {
        if (in_array($value, self::TABS)) {
            $this->attributes['tab'] = $value;
        } else {
            // Optionally, log an error or default to 'main' if an invalid tab is attempted
            $this->attributes['tab'] = self::TABS['main'];
        }
    }

    public static function genders(): array
    {
        return [
            self::NOT_DEFINED  => __('lang.not_defined'),
            self::GENDER_MALE  => __('lang.male'),
            self::GENDER_FEMALE => __('lang.female'),
        ];
    }

    public function getGenderTextAttribute(): ?string
    {
        return self::genders()[$this->gender] ?? null;
    }

    public static function maritalStatuses(): array
    {
        return [
            self::NOT_DEFINED         => __('lang.not_defined'),
            self::MARITAL_STATUS_SINGLE   => __('lang.single'),
            self::MARITAL_STATUS_MARRIED  => __('lang.married'),
            self::MARITAL_STATUS_DIVORCED => __('lang.divorced'),
            self::MARITAL_STATUS_WIDOWED  => __('lang.widowed'),
            self::MARITAL_STATUS_ENGAGED  => __('lang.engaged'),
        ];
    }

    public function getMaritalStatusTextAttribute(): ?string
    {
        return self::maritalStatuses()[$this->marital_status] ?? null;
    }


    // Relations: Renamed to follow Laravel camelCase convention (e.g., user() instead of User())

    public function bank()
    {
        return $this->hasOne(EmployeeBank::class, 'employee_id', 'id')->withTrashed();
    }

    public function identity()
    {
        return $this->hasOne(EmployeeIdentity::class, 'employee_id', 'id')->withTrashed();
    }

    public function holidays()
    {
        return $this->hasMany(HrHoliday::class, 'employee_id', 'id');
    }

    // Renamed from HrEmployee() to hrEmployee()
    public function hrEmployee()
    {
        return $this->hasOne(HrEmployeeModule::class, 'employee_id', 'id');
    }

    // Renamed from User() to user()
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Renamed from Branch() to branch()
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    // Renamed from UserTrashed() to userTrashed()
    public function userTrashed()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
