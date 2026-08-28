<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrHolidayType extends Model
{
    use SoftDeletes, Translatable;
    public $table = 'hr_holiday_types';

    public $fillable = ['status', 'off_days', 'type'];

    public $translatedAttributes = ['name'];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status'] = 'required';
        $rules['off_days'] = 'required';
        $rules['type'] = 'required';
        return $rules;
    }

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    const TYPE_WITH_DEDUCTION = 1; // مع الخصم
    const TYPE_WITHOUT_DEDUCTION = 2; // بدون الخصم
    const TYPE_SICK_LEAVE = 3; // إجازة مرضية

    public static function types()
    {
        return [
            self::TYPE_WITH_DEDUCTION => __('hr::models/hr_holiday_types.with_deduction'),
            self::TYPE_WITHOUT_DEDUCTION =>  __('hr::models/hr_holiday_types.without_deduction'),
            self::TYPE_SICK_LEAVE =>  __('hr::models/hr_holiday_types.sick_leave'),
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

     public function getTypeTextAttribute()
    {
        return self::types()[$this->type];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
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
     * Get all of the holidays for the HrHolidayType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function holidays(): HasMany
    {
        return $this->hasMany(HrHoliday::class, 'type_id');
    }

    public function HolidayBalance()
    {
        return $this->hasOne(HrHolidayBalance::class, 'type_id');
    }
}
