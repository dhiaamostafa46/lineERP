<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\Database\Factories\HrCalendarEventsFactory;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrCalendarEvents extends Model
{
    use HasFactory, Translatable ,SoftDeletes;

    public $table = 'hr_calendar_events';

    public $translatedAttributes = ['name'];
    protected $translationForeignKey = 'hr_calendar_event_id';
 public $translationModel = HrCalendarEventTranslation::class;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'description',
        'rules',
        'is_recurring',
        'status',
        'type',
        'color'
    ];


    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rules' => 'array',
        'is_recurring' => 'boolean',
        'status' => 'integer',
        'type' => 'integer',
    ];

    // Constants
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    const TYPE_HOLIDAY = 1;
    const TYPE_EVENT = 2;

    public static function rules()
    {
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|integer',
            'type' => 'required|integer',
            'rules' => 'nullable|array',
            'is_recurring' => 'boolean',
            'color' => 'nullable|string',
            'description' => 'nullable|string',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_HOLIDAY => __('hr::models/hr_calendar_events.types.holiday'),
            self::TYPE_EVENT => __('hr::models/hr_calendar_events.types.event'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? '';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge badge-success',
            self::STATUS_INACTIVE => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get a specific rule value, useful for retrieving settings like delays.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getRule($key, $default = null)
    {
        if (!is_array($this->rules)) {
            return $default;
        }

        return $this->rules[$key] ?? $default;
    }

    /**
     * Check if the given date exists in the rules array.
     *
     * @param string $date
     * @return bool
     */
    public function isDateInRules($date)
    {
        if (empty($this->rules) || !is_array($this->rules)) {
            return false;
        }

        if (isset($this->rules['dates']) && is_array($this->rules['dates'])) {
            return in_array($date, $this->rules['dates']);
        }

        return in_array($date, $this->rules);
    }

}
