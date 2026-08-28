<?php

namespace App\Models\StoreApp;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes;

    protected $table = 'stock_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'branch_id',
        'user_id',
        'product_id',
        'movement_number',
        'movement_date',
        'movement_type',
        'stock_type',
        'store_id',
        'unit_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'is_size',
        'to_store_id',
        'related_movement_id',
        'reason',
        'reference_type',
        'reference_id',
        'reference_number',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'movement_date' => 'date',
        'approved_at' => 'datetime',
        'is_size' => 'boolean',
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
    ];

    // Constants for movement_type
    const DOC_TYPE_OPENING_BALANCE = 1;

    const DOC_TYPE_DAMAGE = 2;

    const DOC_TYPE_TRANSFER_OUT = 3;

    const DOC_TYPE_TRANSFER_IN = 4;

    const DOC_TYPE_ADJUSTMENT = 5;

    const DOC_TYPE_PURCHASE = 6;

    const DOC_TYPE_SALE = 7;

    const DOC_TYPE_PRODUCTION = 9;

    const DOC_TYPE_RETURN = 10;

    const DOC_TYPE_RECEIVING = 11;

    const DOC_TYPE_ISSUING = 12;

    const DOC_TYPE_DIRECT_TRANSFER = 13;

    const DOC_TYPE_RESERVATION = 14;

    // Constants for reason
    const REASON_COUNT_VARIANCE = 1;

    const REASON_DAMAGE = 2;

    const REASON_EXPIRED = 3;

    const REASON_THEFT = 4;

    const REASON_OTHER = 5;

    // Constants for status
    const STATUS_DRAFT = 1;

    const STATUS_APPROVED = 2;

    const STATUS_CANCELLED = 3;

    const STATUS_PENDING = 4;

    const STATUS_COMPLETED = 5;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productSize(): BelongsTo
    {
        return $this->belongsTo(\App\Models\BasicDataApp\ProductSize::class, 'product_id', 'id');
    }

    public function getProductNameAttribute(): string
    {
        if ($this->is_size) {
            return $this->productSize?->product?->name.' - '.$this->productSize?->name;
        } else {
            return $this->product?->name ?? '';
        }
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function relatedMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'related_movement_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getMovementTypeNameAttribute(): ?string
    {
        return self::documentTypes()[$this->movement_type] ?? null;
    }

    public function getReasonNameAttribute(): ?string
    {
        return self::adjustmentReasons()[$this->reason] ?? null;
    }

    public function getStatusNameAttribute(): ?string
    {
        return self::statuses()[$this->status] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByType($query, int $type)
    {
        return $query->where('movement_type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_PENDING => __('lang.pending'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_COMPLETED => __('lang.completed'),
            self::STATUS_CANCELLED => __('lang.cancelled'),
        ];
    }

    public static function documentTypes()
    {
        return [
            self::DOC_TYPE_OPENING_BALANCE => __('lang.opening_balance'),
            self::DOC_TYPE_PURCHASE => __('lang.purchase'),
            self::DOC_TYPE_SALE => __('lang.sale'),
            self::DOC_TYPE_TRANSFER_OUT => __('lang.transfer_out'),
            self::DOC_TYPE_TRANSFER_IN => __('lang.transfer_in'),
            self::DOC_TYPE_ADJUSTMENT => __('lang.adjustment'),
            self::DOC_TYPE_DAMAGE => __('lang.damage'),
            self::DOC_TYPE_RETURN => __('lang.return'),
            self::DOC_TYPE_PRODUCTION => __('lang.production'),
            self::DOC_TYPE_RECEIVING => __('store::models/st_receivings.singular'),
            self::DOC_TYPE_ISSUING => __('store::models/st_issuings.singular'),
            self::DOC_TYPE_DIRECT_TRANSFER => __('store::models/st_direct_transfers.singular'),
            self::DOC_TYPE_RESERVATION => __('store::models/st_reservations.plural'),
        ];
    }

    public static function adjustmentReasons()
    {
        return [
            self::REASON_COUNT_VARIANCE => __('lang.count_variance'),
            self::REASON_DAMAGE => __('lang.damage'),
            self::REASON_THEFT => __('lang.theft'),
            self::REASON_EXPIRED => __('lang.expired'),
            self::REASON_OTHER => __('lang.other'),
        ];
    }
}
