<?php

namespace Modules\Store\App\Models;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Store\Database\Factories\StDamagedItemFactory;

class StDamagedItem extends Model
{


      protected $fillable = ['damaged_id', 'product_id', 'unit_id', 'have_sizes', 'quantity', 'unit_cost', 'unit',  'total_cost', 'status', 'notes'];

    protected $table = 'st_damaged_items';
    const STATUS_DRAFT = 0;
    const STATUS_APPROVED = 1;
    const STATUS_PROCESSED = 2;
    const STATUS_CANCELLED = 3;
    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
    ];

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = (is_null($value) || $value === '' || (float)$value <= 0) ? 1 : $value;
    }

    public function setUnitCostAttribute($value)
    {
        $this->attributes['unit_cost'] = (is_null($value) || $value === '') ? 0 : $value;
    }

    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => __('lang.draft'),
            self::STATUS_APPROVED => __('lang.approved'),
            self::STATUS_PROCESSED => __('lang.processed'),
            self::STATUS_CANCELLED => __('lang.cancelled'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? '';
    }
    // العلاقات
    public function damaged()
    {
        return $this->belongsTo(StDamaged::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getProductNameAttribute()
    {
        if ($this->have_sizes == 1) {
            return $this->productSize?->product?->name . ' - ' . $this->productSize?->name;
        } else {
            return $this->product?->name;
        }
    }

    public function getUnitNameAttribute()
    {
        return $this->ProductUnit?->unit?->name;
    }

    public function getBarcodeAttribute()
    {
        return $this->product?->barcode;
    }

    public function getTypeAttribute()
    {
        return $this->have_sizes;
    }

    public function ProductUnit()
    {
      return $this->belongsTo(ProductUnit::class ,'unit_id' ,'id');
    }

    // Events
    protected static function booted()
    {
        // حساب التكلفة الإجمالية تلقائياً
        static::saving(function ($item) {
            $item->total_cost = $item->quantity * $item->unit_cost;
        });
    }
}
