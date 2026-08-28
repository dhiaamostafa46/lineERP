<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BasicDataApp\Product;

class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_invoice_id',
        'product_id',
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
        return ($this->quantity * $this->unit_price) - $this->discount_amount;
    }

    public function getDiscountAmountAttribute()
    {
        if (isset($this->attributes['total_discount']) && (float)$this->attributes['total_discount'] > 0) {
            return (float) $this->attributes['total_discount'];
        }

        $gross = (float) ($this->quantity * $this->unit_price);
        $discountVal = (float) ($this->number_discount ?? 0);
        $type = (int) ($this->type_discount ?? 1);

        if ($type === 1) { // %
            return round($gross * ($discountVal / 100), 2);
        }

        return $discountVal;
    }

    public function getTaxableAmountAttribute()
    {
        return ($this->quantity * $this->unit_price) - $this->discount_amount;
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
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\BasicDataApp\Unit::class, 'unit_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\PurchaseInvoiceItemFactory::new();
    }
}
