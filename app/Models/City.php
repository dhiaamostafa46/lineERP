<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class City extends Model
{
    use SoftDeletes, Translatable;

    protected $table = 'cities';

    protected $fillable = [
        'area_id',
        'code',
        'status',
    ];

    public $translatedAttributes = ['name'];

    public const STATUS_INACTIVE = 1;

    public const STATUS_ACTIVE = 2;

    public static function statuses(): array
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(?int $id = null): array
    {
        $rules = [];
        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['status'] = 'required|integer|in:'.self::STATUS_INACTIVE.','.self::STATUS_ACTIVE;
        $rules['area_id'] = 'required|integer|exists:areas,id';
        $rules['code'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('cities', 'code')->ignore($id),
        ];

        return $rules;
    }
}
