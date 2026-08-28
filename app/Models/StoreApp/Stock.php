<?php

namespace App\Models\StoreApp;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Models\Branch;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes;

    protected $table = 'stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'branch_id',
        'store_id',
        'product_id',
        'unit_id',
        'current_quantity',
        'reserved_quantity',
        'average_cost',
        'last_cost',
        'min_quantity',
        'reorder_point',
        'last_movement_at',
        'is_size',
    ];

    /**
     * Get the available quantity (Physical - Reserved).
     *
     * @return float
     */
    public function getAvailableQuantityAttribute(): float
    {
        return (float) ($this->current_quantity - ($this->reserved_quantity ?? 0));
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_quantity'  => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
        'average_cost'      => 'decimal:4',
        'last_cost'         => 'decimal:4',
        'min_quantity'      => 'decimal:4',
        'reorder_point'     => 'decimal:4',
        'last_movement_at'  => 'datetime',
        'is_size'           => 'boolean',
    ];

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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}