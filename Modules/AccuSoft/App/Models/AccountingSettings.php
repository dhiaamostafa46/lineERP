<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AccountingSettings extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const CACHE_KEY = 'accounting_settings';

    const DEPRECIATION_STRAIGHT_LINE = 1;
    const DEPRECIATION_DECLINING_BALANCE = 2;

    const FREQUENCY_MONTHLY = 1;
    const FREQUENCY_QUARTERLY = 2;
    const FREQUENCY_YEARLY = 3;

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    protected $table = 'accounting_settings';

    protected $fillable = [
        'currency',
        'decimal_places',
        'journal_prefix',
        'journal_next_number',
        'allow_backdated_entries',
        'allow_future_dated_entries',
        'default_depreciation_method',
        'depreciation_frequency',
        'auto_post_depreciation_entries',
        'vat_enabled',
        'default_vat_rate',
        'custom_settings',
        'lock_period_pwd_enabled',
        'lock_period_pwd',
        'hr_auto_post_journal_entries',
        'vehicle_auto_post_journal_entries',
        'driver_auto_post_journal_entries',
        'store_auto_post_journal_entries',
        'sales_auto_post_journal_entries',
        'purchase_auto_post_journal_entries',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'journal_next_number' => 'integer',
        'allow_backdated_entries' => 'boolean',
        'allow_future_dated_entries' => 'boolean',
        'default_depreciation_method' => 'integer',
        'depreciation_frequency' => 'integer',
        'auto_post_depreciation_entries' => 'boolean',
        'vat_enabled' => 'boolean',
        'default_vat_rate' => 'decimal:2',
        'custom_settings' => 'array',
        'lock_period_pwd_enabled' => 'boolean',
        'lock_period_pwd' => 'string',
        'hr_auto_post_journal_entries' => 'boolean',
        'vehicle_auto_post_journal_entries' => 'boolean',
        'driver_auto_post_journal_entries' => 'boolean',
        'store_auto_post_journal_entries' => 'boolean',
        'sales_auto_post_journal_entries' => 'boolean',
        'purchase_auto_post_journal_entries' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
        });

        static::deleted(function () {
            Cache::forget(self::CACHE_KEY);
        });
    }


    public function setLockPeriodPwdAttribute($value)
    {
        if ($value) {
            $this->attributes['lock_period_pwd'] = bcrypt($value);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getInstance(): self
    {
        return Cache::remember(self::CACHE_KEY, now()->addDay(), function () {
            return self::firstOrCreate([], [
                'currency' => 'SAR',
                'decimal_places' => 2,
                'journal_prefix' => 'JE',
                'journal_next_number' => 1,
                'allow_backdated_entries' => false,
                'allow_future_dated_entries' => false,
                // 'default_depreciation_method' => self::DEPRECIATION_STRAIGHT_LINE,
                // 'depreciation_frequency' => self::FREQUENCY_YEARLY,
                // 'auto_post_depreciation_entries' => false,
                // 'vat_enabled' => true,
                // 'default_vat_rate' => 15.00,
                'lock_period_pwd_enabled' => false,
                'lock_period_pwd' => null,
                'hr_auto_post_journal_entries' => false,
                'vehicle_auto_post_journal_entries' => false,
                'driver_auto_post_journal_entries' => false,
                'store_auto_post_journal_entries' => false,
                'sales_auto_post_journal_entries' => false,
                'purchase_auto_post_journal_entries' => false,
            ]);
        });
    }

    public static function get(string $key, $default = null)
    {
        return static::getInstance()->$key ?? $default;
    }

    public static function set(string $key, $value): bool
    {
        $settings = static::getInstance();
        $settings->$key = $value;
        return $settings->save();
    }

    public static function rules(): array
    {
        return [
            'currency' => 'nullable|string|max:10',
            'decimal_places' => 'required|integer|min:0|max:4',
            'journal_prefix' => 'nullable|string|max:10',
            'journal_next_number' => 'required|integer|min:1',
            'allow_backdated_entries' => 'required|boolean',
            'allow_future_dated_entries' => 'required|boolean',
            'default_depreciation_method' => 'required|integer|in:' . implode(',', array_keys(self::depreciationMethods())),
            'depreciation_frequency' => 'required|integer|in:' . implode(',', array_keys(self::depreciationFrequencies())),
            'auto_post_depreciation_entries' => 'required|boolean',
            'vat_enabled' => 'required|boolean',
            'default_vat_rate' => 'required_if:vat_enabled,true|nullable|numeric|min:0|max:100',
            'lock_period_pwd_enabled' => 'required|boolean',
            'lock_period_pwd' => 'nullable|string',
            'hr_auto_post_journal_entries' => 'required|boolean',
            'vehicle_auto_post_journal_entries' => 'required|boolean',
            'driver_auto_post_journal_entries' => 'required|boolean',
            'store_auto_post_journal_entries' => 'nullable|boolean',
            'sales_auto_post_journal_entries' => 'nullable|boolean',
            'purchase_auto_post_journal_entries' => 'nullable|boolean',
        ];
    }

    public static function depreciationMethods(): array
    {
        return [
            self::DEPRECIATION_STRAIGHT_LINE => __('accusoft::models/as_accounting_settings.depreciation.straight_line'),
            self::DEPRECIATION_DECLINING_BALANCE => __('accusoft::models/as_accounting_settings.depreciation.declining_balance'),
        ];
    }




    public static function depreciationFrequencies(): array
    {
        return [
            self::FREQUENCY_MONTHLY => __('accusoft::models/as_accounting_settings.frequency.monthly'),
            self::FREQUENCY_QUARTERLY => __('accusoft::models/as_accounting_settings.frequency.quarterly'),
            self::FREQUENCY_YEARLY => __('accusoft:models/:as_accounting_settings.frequency.yearly'),
        ];
    }




    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDepreciationMethodNameAttribute(): string
    {
        return self::depreciationMethods()[$this->default_depreciation_method] ?? __('accusoft::as_accounting_settings.unknown');
    }

    public function getDepreciationFrequencyNameAttribute(): string
    {
        return self::depreciationFrequencies()[$this->depreciation_frequency] ?? __('accusoft::as_accounting_settings.unknown');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function generateJournalNumber(int $year = null): string
    {
        $year = $year ?? now()->year;
        $prefix = $this->journal_prefix;
        $number = str_pad($this->journal_next_number, 4, '0', STR_PAD_LEFT);

        $this->increment('journal_next_number');

        return "{$prefix}-{$year}-{$number}";
    }

    public function formatAmount(float $amount): string
    {
        return number_format($amount, $this->decimal_places, '.', '');
    }

    public function getCustomSetting(string $key, $default = null)
    {
        return data_get($this->custom_settings, $key, $default);
    }

    public function setCustomSetting(string $key, $value): void
    {
        $settings = $this->custom_settings ?? [];
        data_set($settings, $key, $value);
        $this->custom_settings = $settings;
    }

    public function isDateAllowed(string $date): bool
    {
        $entryDate = \Carbon\Carbon::parse($date);
        $today = \Carbon\Carbon::today();

        if ($entryDate->isFuture() && !$this->allow_future_dated_entries) {
            return false;
        }

        if ($entryDate->isPast() && !$this->allow_backdated_entries) {
            return false;
        }

        return true;
    }
}
