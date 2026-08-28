<?php

namespace App\Models\invApp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesInvoicePayment extends Model
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
        'sales_invoice_id',
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
        $methods = config('payment_methods', []);
        return ($methods['cash'] ?? []) + ($methods['bank'] ?? []) + ($methods['other'] ?? []);
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
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\AccuSoft\TreeAccounts::class, 'account_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Invoices\Database\Factories\SalesInvoicePaymentFactory::new();
    }
}
