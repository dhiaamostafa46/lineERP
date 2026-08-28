<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CompanyContract extends Model
{
    use SoftDeletes;

    protected $table = 'company_contracts';

    protected $fillable = [
        'company_id',
        'company_pricing_type',
        'company_pricing_value',
        'driver_payment_type',
        'driver_payment_value',
        'settlement_cycle',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'company_pricing_value' => 'decimal:2',
        'driver_payment_value' => 'decimal:2',
    ];

    public const STATUS_INACTIVE = 1;

    public const STATUS_ACTIVE = 2;

    public const COMPANY_PRICING_PER_ORDER = 'per_order';

    public const COMPANY_PRICING_PERCENTAGE = 'percentage';

    public const COMPANY_PRICING_MONTHLY = 'monthly';

    public const COMPANY_PRICING_CUSTOM = 'custom';

    public const DRIVER_PAYMENT_SALARY = 'salary';

    public const DRIVER_PAYMENT_PER_ORDER = 'per_order';

    public const DRIVER_PAYMENT_PERCENTAGE = 'percentage';

    public const SETTLEMENT_DAILY = 'daily';

    public const SETTLEMENT_WEEKLY = 'weekly';

    public const SETTLEMENT_MONTHLY = 'monthly';

    public static function statuses(): array
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function companyPricingTypes(): array
    {
        return [
            self::COMPANY_PRICING_PER_ORDER => __('models/CompanyContracts.company_pricing_types.per_order'),
            self::COMPANY_PRICING_PERCENTAGE => __('models/CompanyContracts.company_pricing_types.percentage'),
            self::COMPANY_PRICING_MONTHLY => __('models/CompanyContracts.company_pricing_types.monthly'),
            self::COMPANY_PRICING_CUSTOM => __('models/CompanyContracts.company_pricing_types.custom'),
        ];
    }

    public static function driverPaymentTypes(): array
    {
        return [
            self::DRIVER_PAYMENT_SALARY => __('models/CompanyContracts.driver_payment_types.salary'),
            self::DRIVER_PAYMENT_PER_ORDER => __('models/CompanyContracts.driver_payment_types.per_order'),
            self::DRIVER_PAYMENT_PERCENTAGE => __('models/CompanyContracts.driver_payment_types.percentage'),
        ];
    }

    public static function settlementCycles(): array
    {
        return [
            self::SETTLEMENT_DAILY => __('models/CompanyContracts.settlement_cycles.daily'),
            self::SETTLEMENT_WEEKLY => __('models/CompanyContracts.settlement_cycles.weekly'),
            self::SETTLEMENT_MONTHLY => __('models/CompanyContracts.settlement_cycles.monthly'),
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

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        $pricingTypes = [
            self::COMPANY_PRICING_PER_ORDER,
            self::COMPANY_PRICING_PERCENTAGE,
            self::COMPANY_PRICING_MONTHLY,
            self::COMPANY_PRICING_CUSTOM,
        ];
        $driverTypes = [
            self::DRIVER_PAYMENT_SALARY,
            self::DRIVER_PAYMENT_PER_ORDER,
            self::DRIVER_PAYMENT_PERCENTAGE,
        ];
        $settlements = [
            self::SETTLEMENT_DAILY,
            self::SETTLEMENT_WEEKLY,
            self::SETTLEMENT_MONTHLY,
        ];

        return [
            'company_id' => 'required|integer|exists:companies,id',
            'company_pricing_type' => ['required', 'string', Rule::in($pricingTypes)],
            'company_pricing_value' => 'required|numeric|min:0',
            'driver_payment_type' => ['required', 'string', Rule::in($driverTypes)],
            'driver_payment_value' => 'required|numeric|min:0',
            'settlement_cycle' => ['required', 'string', Rule::in($settlements)],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|integer|in:'.self::STATUS_INACTIVE.','.self::STATUS_ACTIVE,
            'notes' => 'nullable|string',
        ];
    }
}
