<?php

namespace Modules\Invoices\App\Services;

use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use App\Models\invApp\SalesInvoice;
use App\Models\invApp\SalesInvoicePayment;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Models\PurchaseInvoicePayment;
use Modules\AccuSoft\App\Models\AccountingSettings;
use Modules\Invoices\App\Helpers\InvoiceHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceAccountingService
{
    /**
     * Check if auto-posting is enabled for Sales
     */
    public function isSalesAutoPost(): bool
    {
        $accSettings = AccountingSettings::getInstance();
        if ($accSettings && isset($accSettings->sales_auto_post_journal_entries)) {
            return (bool) $accSettings->sales_auto_post_journal_entries;
        }

        $invSettings = InvoiceHelper::getSettings();
        return (bool) ($invSettings->sales_auto_post ?? false);
    }

    /**
     * Check if auto-posting is enabled for Purchases
     */
    public function isPurchaseAutoPost(): bool
    {
        $accSettings = AccountingSettings::getInstance();
        if ($accSettings && isset($accSettings->purchase_auto_post_journal_entries)) {
            return (bool) $accSettings->purchase_auto_post_journal_entries;
        }

        $invSettings = InvoiceHelper::getSettings();
        return (bool) ($invSettings->purchase_auto_post ?? false);
    }

    /**
     * Core builder & updater for Journal Entries
     * Follows the exact pattern from InventoryAccountingService
     */
    public function buildOrUpdateJournalEntry(
        $model,
        array $details,
        string $description,
        ?int $existingEntryId,
        int $entryType,
        string $referenceType,
        int $referenceId,
        bool $isAutoPost,
        $entryDate = null,
        $branchId = null,
        ?string $source = null
    ): ?JournalEntry {
        if (empty($details)) {
            return null;
        }

        // Filter valid details
        $validDetails = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $d) {
            $debit = round((float) ($d['debit'] ?? 0), 4);
            $credit = round((float) ($d['credit'] ?? 0), 4);

            if ($debit == 0 && $credit == 0) {
                continue;
            }

            if (empty($d['tree_account_id'])) {
                Log::warning("InvoiceAccountingService: Missing account in journal detail for '{$description}'. Detail: " . json_encode($d));
                return null;
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $validDetails[] = [
                'tree_account_id' => $d['tree_account_id'],
                'debit' => $debit,
                'credit' => $credit,
                'description' => $d['description'] ?? $description,
                'cost_center_id' => $d['cost_center_id'] ?? null,
            ];
        }

        $totalDebit = round($totalDebit, 4);
        $totalCredit = round($totalCredit, 4);

        if (empty($validDetails) || ($totalDebit <= 0 && $totalCredit <= 0)) {
            return null;
        }

        // Active Fiscal Year
        $date = $entryDate ? Carbon::parse($entryDate) : Carbon::now();
        $fiscalYear = FiscalYear::where('is_closed', false)
            ->where('start_date', '<=', $date->format('Y-m-d'))
            ->where('end_date', '>=', $date->format('Y-m-d'))
            ->first();

        if (!$fiscalYear) {
            $fiscalYear = FiscalYear::where('is_current', true)->first();
        }

        if (!$fiscalYear) {
            Log::warning("InvoiceAccountingService: No active fiscal year found for entry date {$date->format('Y-m-d')}");
            return null;
        }

        $status = $isAutoPost ? JournalEntry::STATUS_POSTED : JournalEntry::STATUS_PENDING;

        DB::beginTransaction();
        try {
            $entry = null;
            if ($existingEntryId) {
                $entry = JournalEntry::find($existingEntryId);
            }

            if (!$entry) {
                $entry = JournalEntry::where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->first();
            }

            if (!$entry) {
                $entry = new JournalEntry();
                $entry->entry_number = JournalEntry::generateEntryNumber();
                $entry->created_by = auth()->id() ?? 1;
            }

            $entry->entry_date = $date->format('Y-m-d');
            $entry->description = $description;
            $entry->fiscal_year_id = $fiscalYear->id;
            $entry->branch_id = $branchId ?? ($model->branch_id ?? (auth()->user()->branch_id ?? 1));
            $entry->entry_type = $entryType;
            $entry->status = $status;
            $entry->total_debit = $totalDebit;
            $entry->total_credit = $totalCredit;
            $entry->reference_type = $referenceType;
            $entry->reference_id = $referenceId;
            $entry->source = $source ?? JournalEntry::determineSource($referenceType, $description);

            if ($isAutoPost) {
                $entry->posted_by = auth()->id() ?? 1;
                $entry->posted_at = Carbon::now();
            } else {
                $entry->posted_by = null;
                $entry->posted_at = null;
            }

            $entry->save();

            // Clear old details and insert new ones
            $entry->details()->forceDelete();

            foreach ($validDetails as $d) {
                $entry->details()->create([
                    'tree_account_id' => $d['tree_account_id'],
                    'debit' => $d['debit'],
                    'credit' => $d['credit'],
                    'description' => $d['description'],
                    'cost_center_id' => $d['cost_center_id'],
                ]);
            }

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("InvoiceAccountingService error: " . $e->getMessage());
            throw $e;
        }
    }

    // =========================================================================
    // SALES SECTION
    // =========================================================================

    /**
     * Generate all accounting entries for a sales document (Sales Invoice, Return, Debit Note)
     */
    public function generateEntries(SalesInvoice $model): void
    {
        $this->generateSalesEntries($model);
    }

    /**
     * Alias for generateEntries for Sales
     */
    public function generateSalesEntries(SalesInvoice $model): void
    {
        if ($model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        // 1. Main Document Entry (Revenue/VAT/COGS/Inventory)
        $this->generateMainSalesEntry($model);

        // 2. Payment/Collection Entries
        $this->generateSalesPaymentEntries($model);
    }

    /**
     * Generate the main journal entry for the sales document
     */
    protected function generateMainSalesEntry(SalesInvoice $model): ?JournalEntry
    {
        $customerAccount = $model->customer->tree_account_id ?? AccountMapping::getAccountId('customer');
        $vatAccount = AccountMapping::getAccountId('sales_tax') ?? AccountMapping::getAccountId('tax');
        $inventoryAccount = $model->store->tree_account_id ?? AccountMapping::getAccountId('sales_inventory') ?? AccountMapping::getAccountId('inventory');
        $cogsAccount = AccountMapping::getAccountId('cogs');

        // Account determination based on document type
        if ($model->type_inv == SalesInvoice::TYPE_RETURN || $model->type_inv == SalesInvoice::TYPE_RETURN_POS) {
            $mainAccount = AccountMapping::getAccountId('sales_return') ?? AccountMapping::getAccountId('sales');
            $descPrefix = 'مرتجع مبيعات #';
        } elseif ($model->type_inv == SalesInvoice::TYPE_DEBIT_NOTE) {
            $mainAccount = AccountMapping::getAccountId('sales');
            $descPrefix = 'إشعار مدين مبيعات #';
        } else {
            $mainAccount = AccountMapping::getAccountId('sales');
            $descPrefix = 'فاتورة مبيعات #';
        }

        // --- POS DEVICE ACCOUNT OVERRIDES ---
        if ($model->pos_session_id) {
            $posSession = \Modules\Pos\App\Models\PosSession::find($model->pos_session_id);
            if ($posSession && $posSession->device) {
                $device = $posSession->device;
                if ($model->type_inv == SalesInvoice::TYPE_RETURN || $model->type_inv == SalesInvoice::TYPE_RETURN_POS) {
                    $mainAccount = $device->sales_account_id ?? $mainAccount;
                } else {
                    $mainAccount = $device->sales_account_id ?? $mainAccount;
                }

                $vatAccount = $device->vat_account_id ?? $vatAccount;
                $cogsAccount = $device->cogs_account_id ?? $cogsAccount;
                $inventoryAccount = $device->inventory_account_id ?? $inventoryAccount;
            }
        }

        if (!$customerAccount || !$mainAccount) {
            Log::warning("InvoiceAccountingService: Customer or Sales account mapping missing for Sales Invoice #{$model->invoice_number}");
            return null;
        }

        $entryDate = $model->issue_date ? $model->issue_date->format('Y-m-d') : now()->format('Y-m-d');
        $totalAmount = round((float) $model->total_inclusive_vat, 4);
        $vatAmount = ($model->total_vat > 0 && $vatAccount) ? round((float) $model->total_vat, 4) : 0;
        $shippingCost = round((float) ($model->shipping_cost ?? 0), 4);
        $netAmount = round($totalAmount - $vatAmount - $shippingCost, 4);

        $details = [];

        // Logic for Debit/Credit based on Type
        if ($model->type_inv == SalesInvoice::TYPE_RETURN || $model->type_inv == SalesInvoice::TYPE_RETURN_POS) {
            // Return: Sales/VAT/Shipping DEBIT, Customer CREDIT
            $details[] = ['tree_account_id' => $mainAccount, 'debit' => $netAmount, 'credit' => 0, 'description' => $descPrefix . $model->invoice_number];
            if ($vatAmount > 0) {
                $details[] = ['tree_account_id' => $vatAccount, 'debit' => $vatAmount, 'credit' => 0, 'description' => $descPrefix . ' - ضريبة مستردة'];
            }
            if ($shippingCost > 0) {
                $details[] = ['tree_account_id' => AccountMapping::getAccountId('shipping_revenue') ?? $mainAccount, 'debit' => $shippingCost, 'credit' => 0, 'description' => $descPrefix . ' - عكس شحن'];
            }
            $details[] = ['tree_account_id' => $customerAccount, 'debit' => 0, 'credit' => $totalAmount, 'description' => $descPrefix . $model->invoice_number];

            $inventoryDirection = 'in';
        } else {
            // Invoice/Debit: Customer DEBIT, Sales/VAT/Shipping CREDIT
            $details[] = ['tree_account_id' => $customerAccount, 'debit' => $totalAmount, 'credit' => 0, 'description' => $descPrefix . $model->invoice_number . ' - ' . ($model->customer->name ?? '')];
            $details[] = ['tree_account_id' => $mainAccount, 'debit' => 0, 'credit' => $netAmount, 'description' => $descPrefix . 'صافي - #' . $model->invoice_number];
            if ($shippingCost > 0) {
                $details[] = ['tree_account_id' => AccountMapping::getAccountId('shipping_revenue') ?? $mainAccount, 'debit' => 0, 'credit' => $shippingCost, 'description' => $descPrefix . ' - إيراد شحن'];
            }
            if ($vatAmount > 0) {
                $details[] = ['tree_account_id' => $vatAccount, 'debit' => 0, 'credit' => $vatAmount, 'description' => $descPrefix . ' - ضريبة مخرجات'];
            }

            $inventoryDirection = 'out';
        }

        // COGS & Inventory Adjustment
        if ($inventoryAccount && $cogsAccount) {
            $totalCost = 0;
            foreach ($model->items as $item) {
                if ($item->product && $item->product->type == 2) {
                    continue; // Skip services
                }

                $stockRecord = \App\Models\StoreApp\Stock::where('product_id', $item->product_id)
                    ->where('is_size', (bool) ($item->have_sizes ?? false))
                    ->when($model->store_id, fn ($q) => $q->where('store_id', $model->store_id))
                    ->orderBy('id', 'asc')
                    ->first();

                $unitAvgCost = ($stockRecord && $stockRecord->average_cost > 0)
                    ? (float) $stockRecord->average_cost
                    : (float) ($item->product->cost_price ?? 0);

                $totalCost += ((float) $item->quantity * $unitAvgCost);
            }

            if ($totalCost > 0) {
                if ($inventoryDirection == 'in') {
                    $details[] = ['tree_account_id' => $inventoryAccount, 'debit' => round($totalCost, 4), 'credit' => 0, 'description' => 'إعادة بضاعة للمخزن'];
                    $details[] = ['tree_account_id' => $cogsAccount, 'debit' => 0, 'credit' => round($totalCost, 4), 'description' => 'عكس تكلفة مبيعات مرتجعة'];
                } else {
                    $details[] = ['tree_account_id' => $cogsAccount, 'debit' => round($totalCost, 4), 'credit' => 0, 'description' => 'تكلفة بضاعة مباعة #' . $model->invoice_number];
                    $details[] = ['tree_account_id' => $inventoryAccount, 'debit' => 0, 'credit' => round($totalCost, 4), 'description' => 'نقص مخزون - #' . $model->invoice_number];
                }
            }
        }

        $isAutoPost = $this->isSalesAutoPost();

        $entry = $this->buildOrUpdateJournalEntry(
            $model,
            $details,
            $descPrefix . $model->invoice_number,
            $model->journal_entry_id,
            JournalEntry::ENTRY_TYPE_AUTO,
            SalesInvoice::class,
            $model->id,
            $isAutoPost,
            $entryDate,
            $model->branch_id
        );

        if ($entry && $model->journal_entry_id != $entry->id) {
            $model->updateQuietly(['journal_entry_id' => $entry->id]);
        }

        return $entry;
    }

    /**
     * Generate journal entries for sales payments (Collection/Refund)
     */
    public function generateSalesPaymentEntries(SalesInvoice $model): void
    {
        $customerAccount = $model->customer->tree_account_id ?? AccountMapping::getAccountId('customer');
        if (!$customerAccount) {
            return;
        }

        $entryDate = $model->issue_date ? $model->issue_date->format('Y-m-d') : now()->format('Y-m-d');
        $isAutoPost = $this->isSalesAutoPost();

        $validPaymentIds = [];

        foreach ($model->payments as $payment) {
            if ($payment->account_id && $payment->amount > 0) {
                $paymentAmount = round((float) $payment->amount, 4);
                $validPaymentIds[] = $payment->id;

                if ($model->type_inv == SalesInvoice::TYPE_RETURN || $model->type_inv == SalesInvoice::TYPE_RETURN_POS) {
                    $description = 'استرداد دفعة - مرتجع #' . $model->invoice_number;
                    $details = [
                        ['tree_account_id' => $payment->account_id, 'debit' => 0, 'credit' => $paymentAmount, 'description' => 'استرداد وسيلة تحصيل'],
                        ['tree_account_id' => $customerAccount, 'debit' => $paymentAmount, 'credit' => 0, 'description' => 'استرجاع مديونية العميل'],
                    ];
                } else {
                    $description = 'تحصيل للفاتورة/الإشعار #' . $model->invoice_number;
                    $details = [
                        ['tree_account_id' => $payment->account_id, 'debit' => $paymentAmount, 'credit' => 0, 'description' => 'وسيلة تحصيل'],
                        ['tree_account_id' => $customerAccount, 'debit' => 0, 'credit' => $paymentAmount, 'description' => 'تحصيل من العميل'],
                    ];
                }

                $this->buildOrUpdateJournalEntry(
                    $model,
                    $details,
                    $description,
                    null,
                    JournalEntry::ENTRY_TYPE_AUTO,
                    SalesInvoicePayment::class,
                    $payment->id,
                    $isAutoPost,
                    $entryDate,
                    $model->branch_id
                );
            }
        }

        // Clean up orphaned payment entries for this sales invoice
        $allInvoicePaymentIds = $model->payments()->pluck('id')->toArray();
        $orphans = JournalEntry::where('reference_type', SalesInvoicePayment::class)
            ->whereIn('reference_id', $allInvoicePaymentIds)
            ->whereNotIn('reference_id', $validPaymentIds)
            ->get();

        foreach ($orphans as $orphan) {
            $orphan->details()->forceDelete();
            $orphan->forceDelete();
        }
    }

    /**
     * Generate entries for Sales Returns
     */
    public function generateSalesReturnEntries(SalesInvoice $model): void
    {
        $this->generateSalesEntries($model);
    }

    /**
     * Generate entries for Sales Debit Notes
     */
    public function generateDebitNoteEntries(SalesInvoice $model): void
    {
        $this->generateSalesEntries($model);
    }

    // =========================================================================
    // PURCHASES SECTION
    // =========================================================================

    /**
     * Generate all accounting entries for a purchase document
     */
    public function generatePurchaseEntries(PurchaseInvoice $model): void
    {
        if ($model->status == PurchaseInvoice::STATUS_DRAFT) {
            return;
        }

        if ($model->type_inv == PurchaseInvoice::TYPE_RETURN) {
            $this->generatePurchaseReturnEntries($model);
            return;
        }

        // 1. Main Purchase Invoice Entry
        $this->generateMainPurchaseEntry($model);

        // 2. Purchase Payment Entries
        $this->generatePurchasePaymentEntries($model);
    }

    /**
     * Generate the main journal entry for a purchase invoice
     */
    protected function generateMainPurchaseEntry(PurchaseInvoice $model): ?JournalEntry
    {
        $supplierAccount = $model->supplier->tree_account_id ?? AccountMapping::getAccountId('supplier');
        $inventoryAccount = $model->store->tree_account_id
            ?? AccountMapping::getAccountId('purchase_inventory')
            ?? AccountMapping::getAccountId('purchase')
            ?? AccountMapping::getAccountId('inventory');
        $discountAccount = AccountMapping::getAccountId('purchase_discount');
        $vatAccount = AccountMapping::getAccountId('purchase_tax') ?? AccountMapping::getAccountId('tax');

        if (!$supplierAccount || !$inventoryAccount) {
            Log::warning("InvoiceAccountingService: Supplier or Inventory account missing for Purchase Invoice #{$model->invoice_number}");
            return null;
        }

        $entryDate = $model->issue_date ? $model->issue_date->format('Y-m-d') : now()->format('Y-m-d');
        $supplierAmount = round((float) $model->total_inclusive_vat, 2);
        $vatAmount = ($model->total_vat > 0 && $vatAccount) ? round((float) $model->total_vat, 2) : 0;
        $discountAmount = ($model->total_discount > 0 && $discountAccount) ? round((float) $model->total_discount, 2) : 0;
        $shippingVatAmount = round((float) ($model->shipping_vat_amount ?? 0), 2);

        $inventoryAmount = round($supplierAmount + $discountAmount - ($vatAmount + $shippingVatAmount), 2);

        $details = [];

        // من حـ/ المخزون (مدين)
        $details[] = [
            'tree_account_id' => $inventoryAccount,
            'debit' => $inventoryAmount,
            'credit' => 0,
            'description' => 'فاتورة شراء #' . $model->invoice_number . ' - مخزون ' . ($model->store->name ?? 'المختار'),
        ];

        // من حـ/ ضريبة القيمة المضافة - مدخلات (مدين)
        if ($vatAmount > 0 || $shippingVatAmount > 0) {
            $details[] = [
                'tree_account_id' => $vatAccount,
                'debit' => round($vatAmount + $shippingVatAmount, 2),
                'credit' => 0,
                'description' => 'فاتورة شراء #' . $model->invoice_number . ' - ضريبة مدخلات',
            ];
        }

        // إلى حـ/ المورد (دائن)
        $details[] = [
            'tree_account_id' => $supplierAccount,
            'debit' => 0,
            'credit' => $supplierAmount,
            'description' => 'فاتورة شراء #' . $model->invoice_number . ' - ' . ($model->supplier->name ?? 'مورد عام'),
        ];

        // إلى حـ/ الخصم المكتسب (دائن)
        if ($discountAmount > 0) {
            $details[] = [
                'tree_account_id' => $discountAccount,
                'debit' => 0,
                'credit' => $discountAmount,
                'description' => 'فاتورة شراء #' . $model->invoice_number . ' - خصم مكتسب',
            ];
        }

        $isAutoPost = $this->isPurchaseAutoPost();

        $entry = $this->buildOrUpdateJournalEntry(
            $model,
            $details,
            'فاتورة شراء #' . $model->invoice_number,
            $model->journal_entry_id,
            JournalEntry::ENTRY_TYPE_AUTO,
            PurchaseInvoice::class,
            $model->id,
            $isAutoPost,
            $entryDate,
            $model->branch_id
        );

        if ($entry && $model->journal_entry_id != $entry->id) {
            $model->updateQuietly(['journal_entry_id' => $entry->id]);
        }

        return $entry;
    }

    /**
     * Generate entries for Purchase Returns
     */
    public function generatePurchaseReturnEntries(PurchaseInvoice $model): void
    {
        if ($model->status == PurchaseInvoice::STATUS_DRAFT) {
            return;
        }

        // 1. Main Return Document Entry
        $this->generateMainPurchaseReturnEntry($model);

        // 2. Refund Payment Entries
        $this->generatePurchasePaymentEntries($model);
    }

    /**
     * Generate the main journal entry for a purchase return invoice
     */
    protected function generateMainPurchaseReturnEntry(PurchaseInvoice $model): ?JournalEntry
    {
        $supplierAccount = $model->supplier->tree_account_id ?? AccountMapping::getAccountId('supplier');
        $inventoryAccount = $model->store->tree_account_id
            ?? AccountMapping::getAccountId('purchase_inventory')
            ?? AccountMapping::getAccountId('purchase')
            ?? AccountMapping::getAccountId('inventory');
        $discountAccount = AccountMapping::getAccountId('purchase_discount');
        $vatAccount = AccountMapping::getAccountId('purchase_tax') ?? AccountMapping::getAccountId('tax');

        if (!$supplierAccount || !$inventoryAccount) {
            Log::warning("InvoiceAccountingService: Supplier or Inventory account missing for Purchase Return #{$model->invoice_number}");
            return null;
        }

        $entryDate = $model->issue_date ? $model->issue_date->format('Y-m-d') : now()->format('Y-m-d');
        $supplierAmount = round((float) $model->total_inclusive_vat, 2);
        $vatAmount = ($model->total_vat > 0 && $vatAccount) ? round((float) $model->total_vat, 2) : 0;
        $discountAmount = ($model->total_discount > 0 && $discountAccount) ? round((float) $model->total_discount, 2) : 0;
        $shippingVatAmount = round((float) ($model->shipping_vat_amount ?? 0), 2);

        $inventoryAmount = round(($supplierAmount + $discountAmount) - ($vatAmount + $shippingVatAmount), 2);

        $details = [];

        // دائن حـ/ المخزون (نقص المخزن)
        $details[] = [
            'tree_account_id' => $inventoryAccount,
            'debit' => 0,
            'credit' => $inventoryAmount,
            'description' => 'مرتجع مشتريات #' . $model->invoice_number . ' - مخزون ' . ($model->store->name ?? 'المختار'),
        ];

        // دائن حـ/ ضريبة القيمة المضافة (مدخلات مرتجعة)
        if ($vatAmount > 0 || $shippingVatAmount > 0) {
            $details[] = [
                'tree_account_id' => $vatAccount,
                'debit' => 0,
                'credit' => round($vatAmount + $shippingVatAmount, 2),
                'description' => 'مرتجع مشتريات #' . $model->invoice_number . ' - ضريبة مدخلات مرتجعة',
            ];
        }

        // مدين حـ/ المورد (نقص المديونية)
        $details[] = [
            'tree_account_id' => $supplierAccount,
            'debit' => $supplierAmount,
            'credit' => 0,
            'description' => 'مرتجع مشتريات #' . $model->invoice_number . ' - ' . ($model->supplier->name ?? 'مورد عام'),
        ];

        // مدين حـ/ الخصم المكتسب (عكس إيراد الخصم إن وجد)
        if ($discountAmount > 0) {
            $details[] = [
                'tree_account_id' => $discountAccount,
                'debit' => $discountAmount,
                'credit' => 0,
                'description' => 'مرتجع مشتريات #' . $model->invoice_number . ' - عكس خصم مكتسب',
            ];
        }

        $isAutoPost = $this->isPurchaseAutoPost();

        $entry = $this->buildOrUpdateJournalEntry(
            $model,
            $details,
            'مرتجع مشتريات #' . $model->invoice_number,
            $model->journal_entry_id,
            JournalEntry::ENTRY_TYPE_AUTO,
            PurchaseInvoice::class,
            $model->id,
            $isAutoPost,
            $entryDate,
            $model->branch_id
        );

        if ($entry && $model->journal_entry_id != $entry->id) {
            $model->updateQuietly(['journal_entry_id' => $entry->id]);
        }

        return $entry;
    }

    /**
     * Generate journal entries for purchase payments
     */
    public function generatePurchasePaymentEntries(PurchaseInvoice $model): void
    {
        $supplierAccount = $model->supplier->tree_account_id ?? AccountMapping::getAccountId('supplier');
        if (!$supplierAccount) {
            return;
        }

        $entryDate = $model->issue_date ? $model->issue_date->format('Y-m-d') : now()->format('Y-m-d');
        $isAutoPost = $this->isPurchaseAutoPost();

        $validPaymentIds = [];

        foreach ($model->payments as $payment) {
            if ($payment->account_id && $payment->amount > 0) {
                $paymentAmount = round((float) $payment->amount, 4);
                $validPaymentIds[] = $payment->id;

                if ($model->type_inv == PurchaseInvoice::TYPE_RETURN) {
                    $description = 'استرداد دفعة - مرتجع مشتريات #' . $model->invoice_number;
                    $details = [
                        ['tree_account_id' => $payment->account_id, 'debit' => $paymentAmount, 'credit' => 0, 'description' => 'استرداد وسيلة دفع'],
                        ['tree_account_id' => $supplierAccount, 'debit' => 0, 'credit' => $paymentAmount, 'description' => 'استرجاع مديونية مورد'],
                    ];
                } else {
                    $description = 'سداد فاتورة مشتريات #' . $model->invoice_number;
                    $details = [
                        ['tree_account_id' => $supplierAccount, 'debit' => $paymentAmount, 'credit' => 0, 'description' => 'سداد للمورد'],
                        ['tree_account_id' => $payment->account_id, 'debit' => 0, 'credit' => $paymentAmount, 'description' => 'وسيلة الدفع'],
                    ];
                }

                $this->buildOrUpdateJournalEntry(
                    $model,
                    $details,
                    $description,
                    null,
                    JournalEntry::ENTRY_TYPE_AUTO,
                    PurchaseInvoicePayment::class,
                    $payment->id,
                    $isAutoPost,
                    $entryDate,
                    $model->branch_id
                );
            }
        }

        // Clean up orphaned payment entries for this purchase invoice
        $allInvoicePaymentIds = $model->payments()->pluck('id')->toArray();
        $orphans = JournalEntry::where('reference_type', PurchaseInvoicePayment::class)
            ->whereIn('reference_id', $allInvoicePaymentIds)
            ->whereNotIn('reference_id', $validPaymentIds)
            ->get();

        foreach ($orphans as $orphan) {
            $orphan->details()->forceDelete();
            $orphan->forceDelete();
        }
    }

    // =========================================================================
    // POS CONSOLIDATED SESSIONS
    // =========================================================================

    /**
     * Generate a consolidated journal entry for a POS Session.
     */
    public function generateConsolidatedSessionEntry(\Modules\Pos\App\Models\PosSession $session, float $actualCash, string $notes = '', bool $transferCash = true): ?JournalEntry
    {
        $device = $session->device;
        if (!$device) {
            return null;
        }

        $salesAccount = $device->sales_account_id ?? AccountMapping::getAccountId('sales');
        $vatAccount = $device->vat_account_id ?? AccountMapping::getAccountId('sales_tax') ?? AccountMapping::getAccountId('tax');
        $inventoryAccount = $device->inventory_account_id ?? AccountMapping::getAccountId('sales_inventory') ?? AccountMapping::getAccountId('inventory');
        $cogsAccount = $device->cogs_account_id ?? AccountMapping::getAccountId('cogs');
        $mainSafeAccount = $device->main_safe_account_id;
        $expenseAccount = $device->expense_account_id;
        $shortageAccount = $device->shortage_account_id ?? AccountMapping::getAccountId('settlement_loss');
        $overageAccount = $device->overage_account_id ?? AccountMapping::getAccountId('settlement_profit');

        $cashMethod = \Modules\Pos\App\Models\PosPaymentMethod::where('device_id', $session->device_id)->where('type', 'cash')->first();
        $posDrawerAccount = $cashMethod ? $cashMethod->account_id : null;

        if (!$salesAccount || !$posDrawerAccount) {
            return null;
        }

        $details = [];

        $invoices = \App\Models\invApp\SalesInvoice::where('pos_session_id', $session->id)->with('items.product')->get();

        $totalSalesInclusive = 0;
        $totalSalesVat = 0;
        $totalSalesCogs = 0;

        $totalReturnsInclusive = 0;
        $totalReturnsVat = 0;
        $totalReturnsCogs = 0;

        foreach ($invoices as $invoice) {
            $cost = 0;
            foreach ($invoice->items as $item) {
                if ($item->product && $item->product->type != 2) {
                    $cost += ($item->quantity * ($item->product->cost_price ?? 0));
                }
            }

            if ($invoice->type_inv == SalesInvoice::TYPE_RETURN_POS || $invoice->type_inv == SalesInvoice::TYPE_RETURN) {
                $totalReturnsInclusive += $invoice->total_inclusive_vat;
                $totalReturnsVat += $invoice->total_vat;
                $totalReturnsCogs += $cost;
            } else {
                $totalSalesInclusive += $invoice->total_inclusive_vat;
                $totalSalesVat += $invoice->total_vat;
                $totalSalesCogs += $cost;
            }
        }

        $totalSalesExclusive = round($totalSalesInclusive - $totalSalesVat, 4);
        $totalReturnsExclusive = round($totalReturnsInclusive - $totalReturnsVat, 4);

        // Add Sales Revenue & VAT
        if ($totalSalesExclusive > 0) {
            $details[] = ['tree_account_id' => $salesAccount, 'debit' => 0, 'credit' => round($totalSalesExclusive, 4), 'description' => 'إجمالي مبيعات الوردية #' . $session->id];
        }
        if ($totalSalesVat > 0 && $vatAccount) {
            $details[] = ['tree_account_id' => $vatAccount, 'debit' => 0, 'credit' => round($totalSalesVat, 4), 'description' => 'إجمالي ضريبة المبيعات'];
        }
        // Add Return Revenue & VAT
        if ($totalReturnsExclusive > 0) {
            $details[] = ['tree_account_id' => $salesAccount, 'debit' => round($totalReturnsExclusive, 4), 'credit' => 0, 'description' => 'إجمالي مرتجعات المبيعات #' . $session->id];
        }
        if ($totalReturnsVat > 0 && $vatAccount) {
            $details[] = ['tree_account_id' => $vatAccount, 'debit' => round($totalReturnsVat, 4), 'credit' => 0, 'description' => 'إجمالي ضريبة مستردة'];
        }

        // Add COGS & Inventory
        if ($cogsAccount && $inventoryAccount) {
            if ($totalSalesCogs > 0) {
                $details[] = ['tree_account_id' => $cogsAccount, 'debit' => round($totalSalesCogs, 4), 'credit' => 0, 'description' => 'إجمالي تكلفة المبيعات'];
                $details[] = ['tree_account_id' => $inventoryAccount, 'debit' => 0, 'credit' => round($totalSalesCogs, 4), 'description' => 'إجمالي سحب المخزون'];
            }
            if ($totalReturnsCogs > 0) {
                $details[] = ['tree_account_id' => $inventoryAccount, 'debit' => round($totalReturnsCogs, 4), 'credit' => 0, 'description' => 'إجمالي إرجاع المخزون'];
                $details[] = ['tree_account_id' => $cogsAccount, 'debit' => 0, 'credit' => round($totalReturnsCogs, 4), 'description' => 'عكس تكلفة مبيعات مرتجعة'];
            }
        }

        // Credit Sales (Unpaid Amounts)
        foreach ($invoices as $invoice) {
            $customerAccount = $invoice->customer->tree_account_id ?? AccountMapping::getAccountId('customer');

            if ($invoice->type_inv == SalesInvoice::TYPE_RETURN_POS || $invoice->type_inv == SalesInvoice::TYPE_RETURN) {
                $refundedAmount = \Modules\Pos\App\Models\PosSessionTransaction::where('reference_id', $invoice->id)
                    ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)
                    ->sum('amount');
                $unpaidReturn = round($invoice->total_inclusive_vat - abs($refundedAmount), 4);

                if ($unpaidReturn > 0 && $customerAccount) {
                    $details[] = ['tree_account_id' => $customerAccount, 'debit' => 0, 'credit' => $unpaidReturn, 'description' => 'مرتجع آجل - فاتورة #' . $invoice->invoice_number];
                }
            } else {
                $paidAmount = \Modules\Pos\App\Models\PosSessionTransaction::where('reference_id', $invoice->id)
                    ->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)
                    ->sum('amount');
                $unpaidSale = round($invoice->total_inclusive_vat - $paidAmount, 4);

                if ($unpaidSale > 0 && $customerAccount) {
                    $details[] = ['tree_account_id' => $customerAccount, 'debit' => $unpaidSale, 'credit' => 0, 'description' => 'مبيعات آجلة - فاتورة #' . $invoice->invoice_number];
                }
            }
        }

        // Non-Cash Payment Methods Net Amounts
        $nonCashMethods = \Modules\Pos\App\Models\PosPaymentMethod::where('device_id', $session->device_id)->where('type', '!=', 'cash')->get();
        foreach ($nonCashMethods as $method) {
            if (!$method->account_id) {
                continue;
            }

            $salesAmt = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->where('pos_payment_method_id', $method->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)->sum('amount');
            $returnAmt = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
                ->where('pos_payment_method_id', $method->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)->sum('amount'));

            $netAmount = $salesAmt - $returnAmt;

            if ($netAmount > 0) {
                $details[] = ['tree_account_id' => $method->account_id, 'debit' => round($netAmount, 4), 'credit' => 0, 'description' => 'صافي مقبوضات ' . $method->name];
            } elseif ($netAmount < 0) {
                $details[] = ['tree_account_id' => $method->account_id, 'debit' => 0, 'credit' => round(abs($netAmount), 4), 'description' => 'صافي مدفوعات ' . $method->name];
            }
        }

        // Cash Drawer Operations
        $cashSales = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->where('pos_payment_method_id', $cashMethod->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)->sum('amount');
        $cashReturns = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->where('pos_payment_method_id', $cashMethod->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)->sum('amount'));
        $cashDeposits = \Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->where('pos_payment_method_id', $cashMethod->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT)->sum('amount');
        $cashWithdrawals = abs(\Modules\Pos\App\Models\PosSessionTransaction::where('pos_session_id', $session->id)
            ->where('pos_payment_method_id', $cashMethod->id)->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL)->sum('amount'));

        $netCashSales = $cashSales - $cashReturns;
        if ($netCashSales > 0) {
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => round($netCashSales, 4), 'credit' => 0, 'description' => 'صافي المبيعات النقدية'];
        } elseif ($netCashSales < 0) {
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => 0, 'credit' => round(abs($netCashSales), 4), 'description' => 'صافي المرتجعات النقدية'];
        }

        // Deposits and Withdrawals
        if ($cashDeposits > 0 && $mainSafeAccount && $mainSafeAccount != $posDrawerAccount) {
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => round($cashDeposits, 4), 'credit' => 0, 'description' => 'إيداعات نقدية للوردية'];
            $details[] = ['tree_account_id' => $mainSafeAccount, 'debit' => 0, 'credit' => round($cashDeposits, 4), 'description' => 'إيداعات نقدية للوردية'];
        }

        if ($cashWithdrawals > 0 && $expenseAccount && $expenseAccount != $posDrawerAccount) {
            $desc = 'سحوبات نقدية من الوردية';
            $details[] = ['tree_account_id' => $expenseAccount, 'debit' => round($cashWithdrawals, 4), 'credit' => 0, 'description' => $desc];
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => 0, 'credit' => round($cashWithdrawals, 4), 'description' => $desc];
        }

        // Variances
        $expectedCash = $session->opening_balance + $cashSales - $cashReturns + $cashDeposits - $cashWithdrawals;
        $variance = round($actualCash - $expectedCash, 4);

        if ($variance < 0 && $shortageAccount) {
            $shortageAmt = abs($variance);
            $details[] = ['tree_account_id' => $shortageAccount, 'debit' => $shortageAmt, 'credit' => 0, 'description' => 'عجز الصندوق - إغلاق الوردية'];
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => 0, 'credit' => $shortageAmt, 'description' => 'عجز الصندوق - إغلاق الوردية'];
        } elseif ($variance > 0 && $overageAccount) {
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => $variance, 'credit' => 0, 'description' => 'زيادة الصندوق - إغلاق الوردية'];
            $details[] = ['tree_account_id' => $overageAccount, 'debit' => 0, 'credit' => $variance, 'description' => 'زيادة الصندوق - إغلاق الوردية'];
        }

        // Final Cash Transfer to Main Safe
        if ($transferCash && $actualCash > 0 && $mainSafeAccount && $mainSafeAccount != $posDrawerAccount) {
            $desc = 'تصفية نقدية الوردية #' . $session->id;
            $details[] = ['tree_account_id' => $mainSafeAccount, 'debit' => round($actualCash, 4), 'credit' => 0, 'description' => $desc];
            $details[] = ['tree_account_id' => $posDrawerAccount, 'debit' => 0, 'credit' => round($actualCash, 4), 'description' => $desc];
        }

        $isAutoPost = $this->isSalesAutoPost();

        return $this->buildOrUpdateJournalEntry(
            $session,
            $details,
            'قيد إغلاق وتسوية وردية POS #' . $session->id . ($notes ? ' - ' . $notes : ''),
            null,
            JournalEntry::ENTRY_TYPE_AUTO,
            \Modules\Pos\App\Models\PosSession::class,
            $session->id,
            $isAutoPost,
            now()->format('Y-m-d'),
            $device->branch_id
        );
    }

    // =========================================================================
    // DELETION & CLEANUP
    // =========================================================================

    /**
     * Delete all journal entries associated with a document (Sales or Purchase)
     * Identical to InventoryAccountingService::deleteJournalEntries
     */
    public function deleteJournalEntries($model): bool
    {
        try {
            DB::beginTransaction();

            $modelClass = get_class($model);
            $entries = JournalEntry::where(function ($q) use ($model, $modelClass) {
                $q->where('reference_type', $modelClass)->where('reference_id', $model->id);
                if (!empty($model->journal_entry_id)) {
                    $q->orWhere('id', $model->journal_entry_id);
                }
            })->get();

            foreach ($entries as $entry) {
                $entry->details()->forceDelete();
                $entry->forceDelete();
            }

            // Also delete any payment journal entries
            if (method_exists($model, 'payments')) {
                $paymentClass = ($model instanceof PurchaseInvoice)
                    ? PurchaseInvoicePayment::class
                    : SalesInvoicePayment::class;

                $paymentIds = $model->payments()->pluck('id')->toArray();
                if (!empty($paymentIds)) {
                    $paymentEntries = JournalEntry::where('reference_type', $paymentClass)
                        ->whereIn('reference_id', $paymentIds)
                        ->get();

                    foreach ($paymentEntries as $pe) {
                        $pe->details()->forceDelete();
                        $pe->forceDelete();
                    }
                }
            }

            if (!empty($model->journal_entry_id)) {
                $model->updateQuietly(['journal_entry_id' => null]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete journal entries for " . get_class($model) . " #{$model->id}: " . $e->getMessage());
            return false;
        }
    }
}
