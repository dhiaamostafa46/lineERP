<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'description',
        'unit',
        'unit_id',
        'have_sizes',
        'quantity',
        'unit_price',
        'total_discount',
        'type_discount',
        'number_discount',
        'tax_id',
        'vat_rate',
        'vat_amount',
        'subtotal_with_vat',
        'notes'
    ];

    protected $casts = [
        'have_sizes' => 'boolean',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_discount' => 'decimal:4',
        'number_discount' => 'decimal:4',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:4',
        'subtotal_with_vat' => 'decimal:4',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function unitname()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
