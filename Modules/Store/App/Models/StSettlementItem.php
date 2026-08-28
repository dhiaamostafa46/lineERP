<?php

namespace Modules\Store\App\Models;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\BasicDataApp\Unit;
use Illuminate\Database\Eloquent\Model;

class StSettlementItem extends Model
{
    const STATUS_DRAFT = 1;

    const STATUS_APPROVED = 2;

    const STATUS_PROCESSED = 3;

    const STATUS_CANCELLED = 4;

    protected $fillable = [
        'settlement_id', 'product_id', 'unit_id', 'unit', 'have_sizes',
        'system_quantity', 'actual_quantity', 'variance_quantity',
        'unit_cost', 'total_cost', 'variance_type', 'status', 'notes',
    ];

    // Relationships
    public function settlement()
    {
        return $this->belongsTo(StSettlement::class, 'settlement_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Status Methods
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_PROCESSED => __('lang.processed'),
            self::STATUS_CANCELLED => __('lang.cancelled'),
        ];
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? '';
    }

    public function productSize()
    {
        return $this->belongsTo(\App\Models\BasicDataApp\ProductSize::class, 'product_id', 'id');
    }

    // Accessors
    public function getProductNameAttribute(): string
    {
        if ($this->have_sizes == 1) {
            return $this->productSize?->product?->name.' - '.$this->productSize?->name;
        } else {
            return $this->product?->name ?? '';
        }
    }

    public function getUnitNameAttribute(): string
    {
        return $this->ProductUnit?->unit?->name ?? $this->unitRelation?->name ?? $this->unit ?? '';
    }

    //  public function getUnitNameAttribute()
    // {
    //     return $this->ProductUnit?->unit?->name;
    // }
    public function getBarcodeAttribute(): ?string
    {
        return $this->product?->barcode;
    }

    public function ProductUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }

    // Events
    protected static function booted(): void
    {
        // Calculate total cost automatically
        static::saving(function ($item) {
            $item->total_cost = ($item->variance_quantity ?? 0) * ($item->unit_cost ?? 0);
        });
    }
}
