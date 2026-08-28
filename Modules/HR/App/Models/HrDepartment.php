<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrDepartment extends Model
{
    use SoftDeletes, Translatable;

    public $table = 'hr_departments';

    public $fillable = [
        'status',
        'code',
        'type',
        'parent_id',
        'owner_id'
    ];

    public $translatedAttributes = ['name'];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status']    = 'required';
        $rules['code']      = 'required';
        $rules['type']      = 'required';
        $rules['parent_id'] = 'nullable';
        $rules['owner_id']  = 'nullable';

        return $rules;
    }
    const TYPE_DEPARTMENT = 1;
    const TYPE_SECTION = 2;

    public static function types()
    {
        return [
            self::TYPE_DEPARTMENT => __('hr::models/hr_departments.fields.Department'),
            self::TYPE_SECTION    => __('hr::models/hr_departments.fields.Section'),
        ];
    }
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE   => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE   => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status];
    }
    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE   => 'badge badge-warning',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status];
    }

    /**
     * Scope a query to only include active Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }


    // Start Type text attribute
    public function getTypeTextAttribute()
    {
        return self::types()[$this->type];
    }
    // End Type text attribute


    // Relations

    public function parent()
    {
        return $this->belongsTo(HrDepartment::class, 'parent_id');
    }

    public function owner()
    {
        return $this->belongsTo(HrEmployee::class, 'owner_id' ,'id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(HrEmployee::class, 'department_id');
    }

    public function jobs()
    {
        return $this->hasManyThrough(
            HrJob::class,
            HrEmployee::class,
            'department_id',
            'id',
            'id',
            'job_id'
        );
    }
}
