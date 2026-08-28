<?php

namespace App\Models;

use App\Models\Vehicles\Vehicle;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Project extends Model
{
    use SoftDeletes, Translatable;

    protected $table = 'projects';

    protected $fillable = [
        'company_id',
        'code',
        'status',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'project_id');
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(?int $id = null, ?int $companyId = null): array
    {
        $rules = [];
        foreach (config('langs') as $locale => $_language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['company_id'] = 'required|integer|exists:companies,id';
        $rules['status'] = 'required|integer|in:'.self::STATUS_INACTIVE.','.self::STATUS_ACTIVE;
        $rules['code'] = [
            'nullable',
            'string',
            'max:255',
            Rule::unique('projects', 'code')
                ->where(fn ($query) => $query->where('company_id', $companyId))
                ->ignore($id),
        ];
        $rules['start_date'] = 'nullable|date';
        $rules['end_date'] = 'nullable|date|after_or_equal:start_date';

        return $rules;
    }

    public static function generateCode(int $companyId): string
    {
        $prefix = 'PRJ-';

        $last = static::withTrashed()
            ->where('company_id', $companyId)
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $sequence = 1;

        if (is_string($last) && preg_match('/-(\d{4})$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        do {
            $code = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $exists = static::withTrashed()
                ->where('company_id', $companyId)
                ->where('code', $code)
                ->exists();

            if ($exists) {
                $sequence++;
            }
        } while ($exists);

        return $code;
    }
}
