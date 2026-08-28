<?php

namespace Modules\Store\App\Models;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductUnit;
use Illuminate\Database\Eloquent\Model;

class StReceivingItem extends Model
{
    protected $table = 'st_receiving_items';

    protected $fillable = [
        'receiving_id', 'product_id', 'unit_id', 'have_sizes',
        'quantity', 'unit_cost', 'total_cost', 'unit', 'status', 'notes',
    ];

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

    public function receiving()
    {
        return $this->belongsTo(StReceiving::class, 'receiving_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productSize()
    {
        return $this->belongsTo(\App\Models\BasicDataApp\ProductSize::class, 'product_id', 'id');
    }

    public function getProductNameAttribute()
    {
        if ($this->have_sizes == 1) {
            return $this->productSize?->product?->name.' - '.$this->productSize?->name;
        } else {
            return $this->product?->name;
        }
    }

    public function getUnitNameAttribute()
    {
        return $this->ProductUnit?->unit?->name;
    }

    public function ProductUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }
}
