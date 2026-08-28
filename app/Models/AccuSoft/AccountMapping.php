<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Astrotomic\Translatable\Translatable;

class AccountMapping extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'account_mappings';

    public $translatedAttributes = ['name'];

    public $translationModel = AccountMappingTranslation::class;

    protected $fillable = ['mapping_key', 'account_id', 'entity_type', 'entity_id', 'status', 'settings'];

    protected $casts = [
        'settings' => 'array',
        'status' => 'integer',
    ];

    // Constants
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    /**
     * الحساب المالي المرتبط في الشجرة المحاسبية
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(TreeAccounts::class, 'account_id');
    }

    /**
     * الكيان المرتبط (اختياري)
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get available statuses.
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => __('lang.active'),
            self::STATUS_INACTIVE => __('lang.inactive'),
        ];
    }

    /**
     * Get status as text.
     */
    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    /**
     * Scope for active mappings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Helper to get account ID by key, with optional entity context.
     * It prioritizes entity-specific mapping and falls back to the default.
     *
     * @param string $key The mapping key (e.g., 'sales', 'tax').
     * @param Model|null $entity The entity to check for a specific mapping.
     * @param mixed|null $default The default value to return if no mapping is found.
     * @return int|null The account ID.
     */
    public static function getAccountId($key, $entity = null, $default = null)
    {
        $query = self::active()->where('mapping_key', $key);

        if ($entity instanceof Model) {
            $specificMapping = (clone $query)->where('entity_type', $entity->getMorphClass())->where('entity_id', $entity->id)->first();

            if ($specificMapping) {
                return $specificMapping->account_id;
            }
        }

        // Fallback to default mapping
        $defaultMapping = $query->whereNull('entity_type')->whereNull('entity_id')->first();

        return $defaultMapping?->account_id ?? $default;
    }

    /**
     * Validation rules for the model.
     */
    public static function rules($id = null)
    {
        $rules = [
            'account_id' => 'required|exists:tree_accounts,id',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
