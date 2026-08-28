<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderPayment extends Model
{
    use HasFactory;

    // Payment Methods
    const METHOD_CASH = '10';
    const METHOD_CREDIT = '30';
    const METHOD_BANK_ACCOUNT = '42';
    const METHOD_BANK_CARD = '48';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_order_id',
        'payment_method_code',
        'account_id',
        'amount',
        'transaction_reference'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    /**
     * Localized payment method names
     */
    public static function paymentMethods()
    {
        return [
            self::METHOD_CASH => __('invoices::models/purchase_orders.payment_methods.10'),
            self::METHOD_CREDIT => __('invoices::models/purchase_orders.payment_methods.30'),
            self::METHOD_BANK_ACCOUNT => __('invoices::models/purchase_orders.payment_methods.42'),
            self::METHOD_BANK_CARD => __('invoices::models/purchase_orders.payment_methods.48'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getMethodTextAttribute()
    {
        return self::paymentMethods()[$this->payment_method_code] ?? $this->payment_method_code;
    }

    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\AccuSoft\TreeAccounts::class, 'account_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\PurchaseOrderPaymentFactory::new();
    }
}
