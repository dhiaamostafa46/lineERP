<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;



use Astrotomic\Translatable\Translatable;
use App\Models\Branch;

class Asset extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, Translatable;

    public $translatedAttributes = ['name', 'description'];

    protected $table = 'assets';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'code',
        'asset_category_id',
        'branch_id',
        'asset_account_id',
        'cost_center_id',
        'purchase_date',
        'purchase_value',
        'useful_life',
        'useful_life_type',
        'salvage_value',
        'depreciation_expense_account_id',
        'accumulated_depreciation_account_id',
        'depreciation_method',
        'calculation_type',
        'declining_rate',
        'status',
        'disposal_date',
        'disposal_value',
        'disposal_type',
        'disposal_journal_entry_id',
        'disposal_gain_loss',
        'total_depreciation',
        'current_book_value',
        'last_depreciation_date',
        'next_depreciation_date',
        'assetable_type',
        'assetable_id',
        'depreciation_status',
        'tax_amount',
        'tax_type',
        'payment_account_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'disposal_date' => 'date',
        'last_depreciation_date' => 'date',
        'next_depreciation_date' => 'date',
    ];

    // Constants for depreciation methods
    public const METHOD_NONE = 0;
    public const METHOD_STRAIGHT_LINE = 1;
    public const METHOD_DECLINING_BALANCE = 2;
    public const METHOD_SUM_OF_YEARS = 3;
    public const METHOD_UNITS_OF_PRODUCTION = 4;

    // Constants for status
    public const STATUS_ACTIVE = 1;
    public const STATUS_DISPOSED = 2;
    public const STATUS_FULLY_DEPRECIATED = 3;
    public const STATUS_UNDER_MAINTENANCE = 4;

    // Constants for depreciation status
    public const DEPRECIATION_STATUS_NONE = 'none';
    public const DEPRECIATION_STATUS_CATEGORY = 'category';
    public const DEPRECIATION_STATUS_CUSTOM = 'custom';

    // Constants for disposal type
    public const DISPOSAL_SALE = 1;
    public const DISPOSAL_SCRAP = 2;
    public const DISPOSAL_EXCHANGE = 3;
    public const DISPOSAL_DONATION = 4;
    public const DISPOSAL_LOST = 5;

    public static function getDepreciationMethods()
    {
        return [
            self::METHOD_NONE => __('accusoft::models/as_asset_categories.methods.none') ?? 'لا يوجد',
            self::METHOD_STRAIGHT_LINE => __('accusoft::models/as_asset_categories.methods.straight_line'),
            self::METHOD_DECLINING_BALANCE => __('accusoft::models/as_asset_categories.methods.declining_balance'),
         //   self::METHOD_SUM_OF_YEARS => __('accusoft::models/as_asset_categories.methods.sum_of_years', [], 'ar') ?? 'مجموع سنوات الاستخدام',
           // self::METHOD_UNITS_OF_PRODUCTION => __('accusoft::models/as_asset_categories.methods.units_of_production', [], 'ar') ?? 'وحدات الإنتاج',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => __('accusoft::models/as_asset.asset_statuses.active'),
            self::STATUS_DISPOSED => __('accusoft::models/as_asset.asset_statuses.disposed'),
            self::STATUS_FULLY_DEPRECIATED => __('accusoft::models/as_asset.asset_statuses.fully_depreciated'),
            self::STATUS_UNDER_MAINTENANCE => __('accusoft::models/as_asset.asset_statuses.under_maintenance'),
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_DISPOSED => 'danger',
            self::STATUS_FULLY_DEPRECIATED => 'warning',
            self::STATUS_UNDER_MAINTENANCE => 'info',
        ];
    }

    public function getStatusColorAttribute()
    {
        return self::getStatusColors()[$this->status] ?? 'secondary';
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public static function getDepreciationStatuses()
    {
        return [
            self::DEPRECIATION_STATUS_NONE => __('accusoft::models/as_asset.depreciation_statuses.none') ?? 'أصل تعريفي',
            self::DEPRECIATION_STATUS_CATEGORY => __('accusoft::models/as_asset.depreciation_statuses.category') ?? 'تابع لفئة',
            self::DEPRECIATION_STATUS_CUSTOM => __('accusoft::models/as_asset.depreciation_statuses.custom') ?? 'أصل مخصص',
        ];
    }

    public static function getDepreciationStatusColors()
    {
        return [
            self::DEPRECIATION_STATUS_NONE => 'secondary',
            self::DEPRECIATION_STATUS_CATEGORY => 'info',
            self::DEPRECIATION_STATUS_CUSTOM => 'primary',
        ];
    }

    public static function getDisposalTypes()
    {
        return [
            self::DISPOSAL_SALE => __('accusoft::models/as_asset.disposal_types.sale'),
            self::DISPOSAL_SCRAP => __('accusoft::models/as_asset.disposal_types.scrap'),
            self::DISPOSAL_EXCHANGE => __('accusoft::models/as_asset.disposal_types.exchange'),
            self::DISPOSAL_DONATION => __('accusoft::models/as_asset.disposal_types.donation'),
            self::DISPOSAL_LOST => __('accusoft::models/as_asset.disposal_types.lost'),
        ];
    }


    /**
     * Relationship with translations.
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\AccuSoft\TreeAccounts::class, 'asset_account_id', 'id');
    }

    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assetable()
    {
        return $this->morphTo();
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\AccuSoft\CostCenters::class, 'cost_center_id');
    }

    public function impairments()
    {
        return $this->hasMany(AssetImpairment::class, 'asset_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transactions()
    {
        return $this->hasMany(AssetTransaction::class, 'asset_id');
    }

    /**
     * Check if the asset has been used in accounting (purchased or depreciated)
     */
    public function isUsedInAccounting(): bool
    {
        return $this->hasPostedDepreciations() || $this->transactions()->exists();
    }

    /**
     * Check if the asset has any posted depreciation journal entries.
     */
    public function hasPostedDepreciations(): bool
    {
        return $this->depreciations()->where('is_posted', true)->exists();
    }

    /**
     * Relationship with depreciation history.
     */
    public function depreciations()
    {
        return $this->hasMany(Depreciation::class);
    }

    public function getAssetAccountIdAttribute($value)
    {
        return $value ?? ($this->assetCategory ? $this->assetCategory->asset_account_id : null);
    }

    public function getAccumulatedDepreciationAccountIdAttribute($value)
    {
        return $value ?? ($this->assetCategory ? $this->assetCategory->accumulated_depreciation_account_id : null);
    }

    public function getDepreciationExpenseAccountIdAttribute($value)
    {
        return $value ?? ($this->assetCategory ? $this->assetCategory->depreciation_expense_account_id : null);
    }

    public function getParentAccountIdAttribute()
    {
        if ($this->depreciation_status == self::DEPRECIATION_STATUS_CUSTOM && $this->asset_account_id) {
            return $this->account ? $this->account->parent_id : null;
        }
        return null;
    }

    /**
     * Get the translated name of the asset.
     */
    public function getNameAttribute()
    {
        // Logic to get name from translations based on current locale
        // Placeholder:
        return $this->translations()->where('locale', app()->getLocale())->first()->name ?? 'N/A';
    }

    /**
     * Get Current Book Value Dynamically (Calculated)
     */
    public function getCurrentBookValueAttribute()
    {
        // This makes current_book_value completely dynamic
        // purchase_value - accumulated_depreciation - impairment_losses
        $impairments = $this->impairments()->sum('impairment_loss');
        $value = $this->purchase_value - $this->total_depreciation - $impairments;
        return max($value, $this->salvage_value);
    }


    /**
     * Calculate the monthly depreciation amount.
     * @return float
     */
    public function calculateDepreciationAmount(): float
    {
        $depreciableValue = $this->purchase_value - $this->salvage_value;
        if ($depreciableValue <= 0 || $this->useful_life <= 0) {
            return 0.0;
        }

        $isYearly = $this->useful_life_type === 'yearly';
        $totalPeriods = $isYearly ? $this->useful_life : ($this->useful_life * 12);

        switch ($this->depreciation_method) {
            case self::METHOD_NONE:
                return 0.0;

            case self::METHOD_STRAIGHT_LINE:
                return $depreciableValue / $totalPeriods;

            case self::METHOD_DECLINING_BALANCE:
                // Formula: (Book Value at start of period * Declining Rate)
                $bookValue = $this->current_book_value ?? $this->purchase_value;
                if (!$this->declining_rate) {
                    // default to double declining if no rate is specified
                    $rate = (1 / $totalPeriods) * 2;
                } else {
                    $rate = $this->declining_rate / 100;
                }
                $depreciation = $bookValue * $rate;
                return $depreciation;

            case self::METHOD_SUM_OF_YEARS:
                // Formula: (Remaining Life / SYD) * Depreciable Base
                // This is more complex for monthly calculation.
                // Placeholder for monthly calculation
                $years = $this->useful_life / 12;
                $syd = ($years * ($years + 1)) / 2;
                if ($syd == 0) return 0;
                $monthlyDepreciationRate = $depreciableValue / $this->useful_life;
                $monthsDepreciated = $this->total_depreciation / $monthlyDepreciationRate;
                $remainingLifeMonths = $this->useful_life - $monthsDepreciated;
                $remainingLifeYears = $remainingLifeMonths / 12;

                return ($depreciableValue * ($remainingLifeYears / $syd)) / 12;

            case self::METHOD_UNITS_OF_PRODUCTION:
                // This method requires usage data, which is not in the DB schema.
                // This would need another table/column to track units produced.
                // Returning 0 as a placeholder.
                return 0.0;

            default:
                return 0.0;
        }
    }

    /**
     * Run depreciation for a specific date using AssetService.
     * @param Carbon $date
     * @return bool
     */
    public function runDepreciation(Carbon $date): bool
    {
        return app(\App\Services\AccuSoft\AssetService::class)->depreciateAsset($this, $date);
    }

    /**
     * Dispose of the asset using AssetService.
     * @param Carbon $date
     * @param float $disposalValue
     * @param int $disposalType
     * @param int $cashAccountId
     * @param int $gainLossAccountId
     * @return bool
     */
    public function dispose(Carbon $date, float $disposalValue, int $disposalType, int $cashAccountId = 0, int $gainLossAccountId = 0): bool
    {
        return app(\App\Services\AccuSoft\AssetService::class)->disposeAsset($this, $date, $disposalValue, $disposalType, $cashAccountId, $gainLossAccountId);
    }

}
