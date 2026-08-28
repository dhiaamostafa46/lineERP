<?php

namespace App\Models;

use App\Models\StoreApp\Store;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Company extends Model
{
    use SoftDeletes, Translatable;

    protected $table = 'companies';

    protected $fillable = [
        'code',
        'phone',
        'email',
        'contact_person',
        'address',
        'city_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
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

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function companyContracts(): HasMany
    {
        return $this->hasMany(CompanyContract::class, 'company_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'company_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'company_stores')
            ->withPivot('is_active')
            ->wherePivot('is_active', true)
            ->wherePivotNull('deleted_at');
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(?int $id = null): array
    {
        $rules = [];
        foreach (config('langs') as $locale => $_language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['status'] = 'required|integer|in:'.self::STATUS_INACTIVE.','.self::STATUS_ACTIVE;
        $rules['city_id'] = 'nullable|integer|exists:cities,id';
        $rules['code'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('companies', 'code')->ignore($id),
        ];
        $rules['phone'] = 'nullable|string|max:255';
        $rules['email'] = 'nullable|email|max:255';
        $rules['contact_person'] = 'nullable|string|max:255';
        $rules['address'] = 'nullable|string';

        return $rules;
    }
}
