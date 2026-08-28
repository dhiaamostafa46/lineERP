<?php

namespace Modules\BasicData\App\Models;

use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbServicePoint extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'service_point_id';

    protected $table = 'db_service_points';

    protected $fillable = [
        'orgID',
        'branchID',
        'userID',
        'code',
        'type',
        'status',
    ];

    // الحالة
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // الأنواع
    const TYPE_TABLE = 1;
    const TYPE_KITCHEN = 2;
    const TYPE_DRIVE = 3;

    protected static function booted()
    {
        static::creating(function ($servicePoint) {
            $servicePoint->userID = $servicePoint->userID ?? auth()->id();
        });
    }

    /**
     * علاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID')->withDefault();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'orgID')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchID')->withDefault();
    }

    /**
     * Accessors & Mutators
     */
    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? __('lang.unknown');
    }

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

    /**
     * Static Methods
     */
    public static function rules()
    {
        $rules = [
            'branchID' => 'nullable|exists:branches,id',
            'code' => 'nullable|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(self::types())),
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
            self::TYPE_TABLE => __('basicdata::lang.table'),
          
            self::TYPE_DRIVE => __('basicdata::lang.drive'),
        ];
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
