<?php

namespace Modules\Store\App\Models;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\BasicDataApp\Unit;
use Illuminate\Database\Eloquent\Model;

class StReservationItem extends Model
{
    protected $table = 'st_reservation_items';

    protected $fillable = [
        'reservation_id', 'product_id', 'unit_id', 'have_sizes', 
        'quantity', 'unit_cost', 'total_cost', 'unit', 'status', 'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'have_sizes' => 'boolean',
    ];

    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = (is_null($value) || $value === '' || (float)$value <= 0) ? 1 : $value;
    }

    public function setUnitCostAttribute($value)
    {
        $this->attributes['unit_cost'] = (is_null($value) || $value === '') ? 0 : $value;
    }

    public function reservation()
    {
        return $this->belongsTo(StReservation::class, 'reservation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ProductUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }

    public function getUnitNameAttribute()
    {
        return $this->ProductUnit?->unit?->name;
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_id');
    }
}
