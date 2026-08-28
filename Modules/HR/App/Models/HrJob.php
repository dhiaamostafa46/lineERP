<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrEmployee;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrJob extends Model
{
    use SoftDeletes, Translatable;
    public $table = 'hr_jobs';

    public $fillable = [
        'status',
        'license_required'
    ];

    public $translatedAttributes = ['name'];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status'] = 'required';
        $rules['license_required'] = 'required';

        return $rules;
    }

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    const LICENSE_REQUIRED_NO = 1;
    const LICENSE_REQUIRED_YES = 2;

    public static function licenses()
    {
        return [
            self::LICENSE_REQUIRED_NO   => __('lang.no'),
            self::LICENSE_REQUIRED_YES => __('lang.yes'),
        ];
    }
    public function getLicenseTextAttribute()
    {
        return self::licenses()[$this->license_required];
    }

    public function getLicenseBadgeAttribute()
    {
        $badges = [
            self::LICENSE_REQUIRED_NO   => 'badge badge-danger',
            self::LICENSE_REQUIRED_YES => 'badge badge-success',
        ];
        return $badges[$this->license_required];
    }

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


    /**
     * Get all of the employees for the HrDepartment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(HrEmployee::class, 'job_id');
    }
}
