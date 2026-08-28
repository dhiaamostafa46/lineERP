<?php

namespace Modules\HR\App\Models;

use Carbon\Carbon;
use Modules\HR\App\Models\HrShift;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrShiftType extends Model
{
    use SoftDeletes, Translatable;

    public $table = 'hr_shift_types';

    public $fillable = ['status', 'type', 'work_hours', 'work_days', 'early_entry', 'late_entry', 'early_exit', 'late_exit', 'entry_end', 'exit_start', 'exempt_days', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'work_days' => 'array',
        'exempt_days' => 'array',
    ];

    protected $timeFormat = 'H:i a';

    public $translatedAttributes = ['name'];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status'] = 'required';
        $rules['type'] = 'required';
        $rules['early_entry'] = 'required';
        $rules['late_entry'] = 'required';
        $rules['early_exit'] = 'required';
        $rules['late_exit'] = 'required';
        $rules['entry_end'] = 'required';
        $rules['exit_start'] = 'required';
        $rules['work_hours'] = 'required_if:type,' . self::TYPE_STATIC;
        $rules['shifts'] = 'nullable|array';
        $rules['shifts.*.from'] = 'required_if:type,' . self::TYPE_STATIC;
        $rules['shifts.*.to'] = 'required_if:type,' . self::TYPE_STATIC;

        return $rules;
    }

    public function getFromTextAttribute()
    {
        return Carbon::parse($this->from)->format('h:i a');
    }

    public function getToTextAttribute()
    {
        return Carbon::parse($this->to)->format('h:i a');
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
        return self::statuses()[$this->status];
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

    const TYPE_STATIC = 1;
    const TYPE_FLEX = 2;
    const TYPE_SPECIFIC = 3;
    public static function types()
    {
        return [
            self::TYPE_STATIC => __('hr::models/hr_shift_types.static'),
            self::TYPE_FLEX => __('hr::models/hr_shift_types.flexible'),
            self::TYPE_SPECIFIC => __('hr::models/hr_shift_types.fields.Specificperiod'),
        ];
    }

    public function scopeStaticTypeOnly($query)
    {
        return $query->where('type', self::TYPE_STATIC);
    }

    /**
     * Scope a query to only include inactive Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFlexibleOnly($query)
    {
        return $query->where('type', self::TYPE_FLEX);
    }

    // Start Type text attribute
    public function getTypeTextAttribute()
    {
        return $this->types()[$this->type];
    }
    // End Type text attribute

    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_STATIC => 'badge badge-warning',
            self::TYPE_FLEX => 'badge badge-success',
            self::TYPE_SPECIFIC => 'badge badge-info',
        ];
        return $badges[$this->type];
    }

    /**
     * Get all of the shifts for the HrShiftType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(HrShift::class, 'type_id');
    }

    /**
     * Get all of the employees for the HrShiftType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(HrEmployee::class, 'shift_id');
    }
}
