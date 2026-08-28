<?php

namespace App\Models\BasicDataApp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $table = 'product_units';

    protected $fillable = ['product_id', 'unit_id', 'conversion_factor', 'is_base', 'Average_Cost'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'conversion_factor' => 'float',
        'is_base' => 'boolean',
    ];

    public function setConversionFactorAttribute($value)
    {
        $this->attributes['conversion_factor'] = ($value === null || $value === '') ? 1 : $value;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getIsBaseTextAttribute()
    {
        return $this->is_base ? __('lang.yes') : __('lang.no');
    }

    public function getIsBaseBadgeAttribute()
    {
        return $this->is_base ? 'badge-light-success' : 'badge-light-danger';
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Scope a query to only include base units.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }

    /**
     * Scope a query to only include non-base units.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotBase($query)
    {
        return $query->where('is_base', false);
    }
}
