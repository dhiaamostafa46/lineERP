<?php

namespace Modules\Invoices\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Invoices\Database\Factories\SalesInvoiceZatcaFactory;
use App\Models\invApp\SalesInvoice;

class SalesInvoiceZatca extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_invoice_id',
        'uuid',
        'icv',
        'previous_invoice_hash',
        'xml_content',
        'request_payload',
        'response_payload',
        'request_id',
        'validation_results'
    ];

    protected static function booted()
    {
        static::creating(function ($zatca) {
            if (empty($zatca->uuid)) {
                $zatca->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $casts = [
        'validation_results' => 'json'
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }
}
