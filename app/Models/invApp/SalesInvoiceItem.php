<?php

namespace App\Models\invApp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BasicDataApp\Product;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'serial',
        'product_name',
        'description',
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
        'subtotal_with_vat'
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get subtotal before VAT
     */
    public function getSubtotalExclusiveVatAttribute()
    {
        return ($this->quantity * $this->unit_price) - $this->total_discount;
    }

    public function getUnitPriceFormattedAttribute()
    {
        return number_format($this->unit_price, 2);
    }

    public function getVatAmountFormattedAttribute()
    {
        return number_format($this->vat_amount, 2);
    }

    public function getSubtotalWithVatFormattedAttribute()
    {
        return number_format($this->subtotal_with_vat, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unitname()
    {
        return $this->belongsTo(\App\Models\BasicDataApp\Unit::class, 'unit_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\SalesInvoiceItemFactory::new();
    }
}
