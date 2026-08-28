<?php

namespace App\Models\BasicDataApp;

use App\Models\Organization;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use  HasFactory, SoftDeletes, Translatable;

    protected $table = 'units';

    protected $fillable = ['org_id', 'branch_id', 'user_id', 'conversion_factor', 'is_base', 'status', 'is_virtual'];

    public $translatedAttributes = ['name'];

    // ثوابت الحالة
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    // ثوابت الوحدة الافتراضية
    const VIRTUAL_FALSE = 0;

    const VIRTUAL_TRUE = 1;

    /**
     * العلاقات
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    /**
     * Mutators
     */
    public function setOrgIDAttribute($value)
    {
        $this->attributes['org_id'] = $value ?? 0;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = $value ?? auth()->id();
    }

    public function setConversionFactorAttribute($value)
    {
        $this->attributes['conversion_factor'] = ($value === null || $value === '') ? 1 : $value;
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    public function getIsVirtualTextAttribute()
    {
        return self::virtualStatuses()[$this->is_virtual] ?? __('lang.unknown');
    }

    public function getIsVirtualBadgeAttribute()
    {
        $badges = [
            self::VIRTUAL_FALSE => 'badge badge-primary',
            self::VIRTUAL_TRUE => 'badge badge-warning',
        ];

        return $badges[$this->is_virtual] ?? 'badge badge-secondary';
    }

    /**
     * قواعد التحقق
     */
    public static function rules()
    {
        $rules = [];
        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['conversion_factor'] = 'nullable|numeric|min:0';

        return $rules;
    }

    /**
     * الحالات
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function virtualStatuses()
    {
        return [
            self::VIRTUAL_FALSE => __('lang.real'),
            self::VIRTUAL_TRUE => __('lang.virtual'),
        ];
    }

    /**
     * Scopes
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeVirtualOnly($query)
    {
        return $query->where('is_virtual', self::VIRTUAL_TRUE);
    }

    public function scopeRealOnly($query)
    {
        return $query->where('is_virtual', self::VIRTUAL_FALSE);
    }

    public function scopeByOrganization($query, $orgId)
    {
        return $query->where('org_id', $orgId);
    }

    protected static function booted()
    {
        // static::addGlobalScope('active', function ($query) {
        //     $query->where('status', self::STATUS_ACTIVE);
        // });
    }
}
