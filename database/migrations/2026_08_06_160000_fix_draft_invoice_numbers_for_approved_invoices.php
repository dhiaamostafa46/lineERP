<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Helpers\InvoiceHelper;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix Sales Invoices with status != DRAFT (1) but invoice_number starting with DRAFT
        $salesInvoices = SalesInvoice::withTrashed()
            ->where('status', '!=', SalesInvoice::STATUS_DRAFT)
            ->where(function ($q) {
                $q->where('invoice_number', 'LIKE', 'DRAFT%')
                  ->orWhere('invoice_number', 'LIKE', 'draft%')
                  ->orWhereNull('invoice_number');
            })
            ->get();

        foreach ($salesInvoices as $invoice) {
            $type = match ((int)$invoice->type_inv) {
                SalesInvoice::TYPE_RETURN => 'sales_return',
                SalesInvoice::TYPE_DEBIT_NOTE => 'sales_debit',
                default => 'sales',
            };
            $number = $this->generateOfficialNumber($type);
            $invoice->updateQuietly(['invoice_number' => $number]);
        }

        // Fix Purchase Invoices with status != DRAFT (1) but invoice_number starting with DRAFT
        $purchaseInvoices = PurchaseInvoice::withTrashed()
            ->where('status', '!=', PurchaseInvoice::STATUS_DRAFT)
            ->where(function ($q) {
                $q->where('invoice_number', 'LIKE', 'DRAFT%')
                  ->orWhere('invoice_number', 'LIKE', 'draft%')
                  ->orWhereNull('invoice_number');
            })
            ->get();

        foreach ($purchaseInvoices as $pInvoice) {
            $type = match ((int)$pInvoice->type_inv) {
                PurchaseInvoice::TYPE_RETURN => 'purchase_return',
                default => 'purchase',
            };
            $number = $this->generateOfficialNumber($type);
            $pInvoice->updateQuietly(['invoice_number' => $number]);
        }
    }

    /**
     * Helper to generate official invoice numbers for migration
     */
    private function generateOfficialNumber(string $type): string
    {
        $settings = InvoiceHelper::getSettings();
        $prefixKey = "{$type}_prefix";
        $nextNumKey = "{$type}_next_number";

        $prefix = $settings->$prefixKey ?? strtoupper(substr($type, 0, 3));
        $nextNum = $settings->$nextNumKey ?? 1;

        $modelClass = match ($type) {
            'purchase', 'purchase_return' => PurchaseInvoice::class,
            default => SalesInvoice::class,
        };

        do {
            $number = $prefix . '-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            $exists = $modelClass::where('invoice_number', $number)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        if ($settings && $settings->id) {
            $settings->update([$nextNumKey => $nextNum + 1]);
        }

        return $number;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data fix
    }
};
