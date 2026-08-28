<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\AccuSoft\AccountMapping;
use App\Repositories\BaseRepository;
use App\Services\AccuSoft\JournalEntryService;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\App\Helpers\HasInvoiceSharedLogic;
use Modules\Invoices\App\Helpers\InvoiceHelper;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Models\PurchaseInvoicePayment;

class PurchaseInvoiceRepository extends BaseRepository
{
    use \App\Helpers\StockManagementTrait;
    use HasInvoiceSharedLogic;

    protected $fieldSearchable = ['uuid', 'invoice_number', 'supplier_invoice_number', 'supplier_id', 'issue_date', 'status', 'store_id'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return PurchaseInvoice::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (static::class === PurchaseInvoiceRepository::class) {
            $query->where('type_inv', PurchaseInvoice::TYPE_INVOICE);
        }

        if (isset($search['created_by']) && !empty($search['created_by'])) {
            $userId = $search['created_by'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('user_id', $userId);
            });
        }

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.purchase.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.purchase';


        }

        return $query;
    }

    public function CreatePurchase($input): PurchaseInvoice
    {

        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            // Prepare default data
            $input = $this->prepareInvoiceData($input, 'purchase');

            /** @var PurchaseInvoice $model */
            $model = $this->create($input);

            if (! empty($items)) {
                $this->saveItems($model, $items);
            }

            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            // تحديث حالة أمر الشراء إذا كان محولاً
            if (! empty($input['from_po_id'])) {
                $purchaseOrder = \Modules\Invoices\App\Models\PurchaseOrder::find($input['from_po_id']);
                if ($purchaseOrder) {
                    $purchaseOrder->update(['status' => \Modules\Invoices\App\Models\PurchaseOrder::STATUS_COMPLETED]);
                }
            }

            // تنفيذ الربط مع المخزون والحسابات
            $this->processPurchaseLinking($model);

            // Ensure non-draft invoice has official invoice number
            $model->refresh();
            if ($model->status != PurchaseInvoice::STATUS_DRAFT && (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))) {
                $officialNumber = $this->generateNumber('purchase', false);
                $model->updateQuietly(['invoice_number' => $officialNumber]);
            }

            return $model;
        });
    }

    /**
     * Prepare shared invoice data defaults.
     */
    protected function prepareInvoiceData(array $input, string $type): array
    {
        // الحالة الافتراضية: معتمدة (مستلمة)
        $input['status'] = $input['status'] ?? PurchaseInvoice::STATUS_RECEIVED;
        $isDraft = ((int) $input['status'] === PurchaseInvoice::STATUS_DRAFT);

        if (empty($input['invoice_number'])) {
            if ($isDraft) {
                // رقم مؤقت للمسودة بنفس أسلوب فواتير المبيعات
                $input['invoice_number'] = 'DRAFT-' . now()->format('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
            } else {
                $input['invoice_number'] = $this->generateNumber($type);
            }
        }

        $input['type_inv'] = $input['type_inv'] ?? PurchaseInvoice::TYPE_INVOICE;
        $input['issue_date'] = $input['issue_date'] ?? now();
        $input['branch_id'] = $input['branch_id'] ?? (auth()->user()->branch_id ?? 1);
        $input['created_by'] = $input['created_by'] ?? (auth()->id() ?? 1);

        return $input;
    }

    public function updatePurchase($input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;

            // Remove items and payments from input before updating invoice
            unset($input['items'], $input['payments']);

            /** @var PurchaseInvoice $model */
            $model = $this->find($id);

            // منع تعديل الفاتورة إذا كانت مُعتمدة أو مسترجعة
            if ($model->status != PurchaseInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن تعديل الفاتورة بعد اعتمادها. يرجى استخدام المرتجع بدلاً من ذلك.');
            }

            // إذا تمت ترقية المسودة إلى معتمدة → توليد رقم فاتورة رسمي
            $newStatus = (int) ($input['status'] ?? $model->status);
            if ($newStatus != PurchaseInvoice::STATUS_DRAFT &&
                (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))) {
                $input['invoice_number'] = $this->generateNumber('purchase', false);
            }

            $model = $this->update($input, $id);

            if ($items !== null) {
                // إعادة المخزون فقط إذا كانت الفاتورة معتمدة سابقاً (لا تأثير للمسودة)
                if ($model->status != PurchaseInvoice::STATUS_DRAFT) {
                    $this->revertAllStockMovements($model);
                }
                $model->items()->delete();
                $this->saveItems($model, $items);
            }

            if ($payments !== null) {
                $this->syncPayments($model, $payments);
            }

            // إعادة معالجة الربط بعد التحديث (تتجاهل المسودة تلقائياً)
            $this->processPurchaseLinking($model);

            // Ensure non-draft invoice has official invoice number
            $model->refresh();
            if ($model->status != PurchaseInvoice::STATUS_DRAFT && (empty($model->invoice_number) || str_starts_with(strtoupper($model->invoice_number), 'DRAFT'))) {
                $officialNumber = $this->generateNumber('purchase', false);
                $model->updateQuietly(['invoice_number' => $officialNumber]);
            }

            return $model;
        });
    }

    protected function syncPayments(PurchaseInvoice $model, array $payments)
    {
        $journalService = app(JournalEntryService::class);
        $validPayments = array_values(array_filter($payments, function ($payment) {
            return !empty($payment['account_id']);
        }));

        $existingPayments = $model->payments()->get();
        $existingCount = $existingPayments->count();
        $newCount = count($validPayments);

        for ($i = 0; $i < max($existingCount, $newCount); $i++) {
            if ($i < $newCount && $i < $existingCount) {
                // Update existing payment record in-place
                $existingPayments[$i]->update([
                    'account_id' => $validPayments[$i]['account_id'],
                    'amount' => $validPayments[$i]['amount'],
                    'payment_method_code' => $validPayments[$i]['payment_method_code'] ?? '10',
                ]);
            } elseif ($i < $newCount) {
                // Create new payment record
                $model->payments()->create([
                    'account_id' => $validPayments[$i]['account_id'],
                    'amount' => $validPayments[$i]['amount'],
                    'payment_method_code' => $validPayments[$i]['payment_method_code'] ?? '10',
                ]);
            } elseif ($i < $existingCount) {
                // Payment was removed: delete its journal entry directly without reversal entries
                $oldPayment = $existingPayments[$i];
                $oldEntries = app(\App\Models\AccuSoft\JournalEntry::class)
                    ->where('reference_type', PurchaseInvoicePayment::class)
                    ->where('reference_id', $oldPayment->id)
                    ->get();

                foreach ($oldEntries as $entry) {
                    try {
                        $journalService->delete($entry);
                    } catch (\Exception $e) {
                        $entry->details()->delete();
                        $entry->delete();
                    }
                }
                $oldPayment->delete();
            }
        }
    }

    protected function saveItems(PurchaseInvoice $model, array $items)
    {
        foreach ($items as $index => &$item) {
            $item['have_sizes'] = isset($item['have_sizes']) ? filter_var($item['have_sizes'], FILTER_VALIDATE_BOOLEAN) : false;

            if ($item['have_sizes']) {
                $productSize = \App\Models\BasicDataApp\ProductSize::with('product')->find($item['product_id']);
                $product = $productSize ? $productSize->product : null;
            } else {
                $product = \App\Models\BasicDataApp\Product::find($item['product_id']);
            }
            $unitId = null;

            // 1. Check if unit name was sent
            $unitName = $item['unit'] ?? null;
            if (! empty($unitName)) {
                $unit = \App\Models\BasicDataApp\Unit::whereTranslation('name', $unitName)->first();
                if ($unit) {
                    $unitId = $unit->id;
                }
            }

            // 2. Check if unit_id was sent (ProductUnit ID or direct Unit ID)
            if (empty($unitId) && ! empty($item['unit_id']) && $product) {
                $prodUnit = \App\Models\BasicDataApp\ProductUnit::where('product_id', $product->id)
                    ->where(function ($q) use ($item) {
                        $q->where('unit_id', $item['unit_id'])
                            ->orWhere('id', $item['unit_id']);
                    })
                    ->first();
                if ($prodUnit) {
                    $unitId = $prodUnit->unit_id;
                } else {
                    $unit = \App\Models\BasicDataApp\Unit::find($item['unit_id']);
                    if ($unit) {
                        $unitId = $unit->id;
                    }
                }
            }

            // 3. Fallback to product's base unit
            if (empty($unitId) && $product) {
                $unitId = $product->base_unit_id;
            }

            $item['unit_id'] = $unitId;

            if (empty($item['unit_id']) && $product && $product->type != \App\Models\BasicDataApp\Product::TYPE_SERVICE) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "items.{$index}.unit_id" => __('invoices::models/sales_invoices.validation.product_no_unit'),
                ]);
            }
        }
        $model->items()->createMany($items);
    }

    protected function savePayments(PurchaseInvoice $model, array $payments)
    {
        $validPayments = array_filter($payments, function ($payment) {
            return ! empty($payment['account_id']);
        });

        if (! empty($validPayments)) {
            $model->payments()->createMany($validPayments);
        }
    }

    public function deletePurchase($id): ?bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->find($id);
            if (! $model) {
                return false;
            }

            // منع حذف الفاتورة إذا كان يوجد لها مرتجعات مشتريات
            $hasReturns = PurchaseInvoice::where('parent_id', $model->id)
                ->where('type_inv', PurchaseInvoice::TYPE_RETURN)
                ->exists();
            if ($hasReturns) {
                throw new \Exception('لا يمكن حذف فاتورة مشتريات يوجد لها مرتجعات مرتبطة.');
            }

            // حذف القيود المحاسبية المرتبطة بالفاتورة والدفعات
            app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->deleteJournalEntries($model);

            // التراجع عن حركات وكميات المخزون
            $this->revertAllStockMovements($model);

            $model->items()->delete();
            $model->payments()->delete();

            return parent::delete($id);
        });
    }

    /**
     * معالجة الربط المحاسبي والمخزني لفاتورة المشتريات
     * المسودات لا تؤثر على المخزون ولا القيود المحاسبية
     */
    protected function processPurchaseLinking(PurchaseInvoice $model)
    {
        // المسودة لا تؤثر على المخزون ولا القيود المحاسبية
        if ($model->status == PurchaseInvoice::STATUS_DRAFT) {
            return;
        }

        // Ensure items and their products are loaded from the database
        // before processing stock movements.
        $model->load('items.product');

        // 1. معالجة المخزون (Inventory) باستخدام StockManagementTrait
        foreach ($model->items as $item) {
            $this->handleStockMovement($model, $item);
        }

        // 2. معالجة القيود المحاسبية (General Ledger) عبر خدمة محاسبة الفواتير
        app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->generatePurchaseEntries($model);
    }

    // Common data methods are now handled by HasInvoiceSharedLogic trait

    public function getHeaders(): array
    {
        return [
            __('invoices::models/purchase_invoices.fields.invoice_number'),
            __('invoices::models/purchase_invoices.fields.supplier_invoice_number'),
            __('invoices::models/purchase_invoices.fields.supplier_id'),
            __('invoices::models/purchase_invoices.fields.branch_id'),
            __('invoices::models/purchase_invoices.fields.issue_date'),
            __('invoices::models/purchase_invoices.fields.total_exclusive_vat'),
            __('invoices::models/purchase_invoices.fields.total_vat'),
            __('invoices::models/purchase_invoices.fields.total_discount'),
            __('invoices::models/purchase_invoices.fields.total_inclusive_vat'),
            __('invoices::models/purchase_invoices.fields.status'),
            __('invoices::models/purchase_invoices.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return PurchaseInvoice::isInvoice()
            ->with(['supplier', 'store', 'createdBy'])
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'supplier_invoice_number' => $invoice->supplier_invoice_number ?? '---',
                    'supplier_id' => $invoice->supplier ? $invoice->supplier->name : '---',
                    'branch_id' => $invoice->store ? $invoice->store->name : '---',
                    'issue_date' => $invoice->issue_date ? ($invoice->issue_date instanceof \Carbon\Carbon ? $invoice->issue_date->format('Y-m-d') : date('Y-m-d', strtotime($invoice->issue_date))) : '---',
                    'total_exclusive_vat' => number_format($invoice->total_exclusive_vat, 2),
                    'total_vat' => number_format($invoice->total_vat, 2),
                    'total_discount' => number_format($invoice->total_discount, 2),
                    'total_inclusive_vat' => number_format($invoice->total_inclusive_vat, 2),
                    'status' => $invoice->status_text,
                    'created_by' => $invoice->createdBy ? $invoice->createdBy->name : '---',
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('invoices::models/purchase_invoices.plural');
    }

    public function create(array $input, bool $withLog = true): \Illuminate\Database\Eloquent\Model
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::create($input, $withLog);
    }

    public function update(array $input, int $id, bool $withLog = true)
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::update($input, $id, $withLog);
    }

    public function recalculateAllInvoices(): array
    {
        return DB::transaction(function () {
            $calculator = new \Modules\Invoices\App\Services\InvoiceCalculatorService();
            $journalService = app(JournalEntryService::class);
            $invoices = PurchaseInvoice::with(['items', 'payments'])->get();
            $processedCount = 0;
            $details = [];

            // 1. تنظيف القيود المحاسبية اليتيمة والمعكوسة وقيود العكس القديمة التابعة للمشتريات
            $validInvoiceIds = PurchaseInvoice::pluck('id')->toArray();
            $validPaymentIds = PurchaseInvoicePayment::pluck('id')->toArray();

            $orphanedInvoiceEntries = app(\App\Models\AccuSoft\JournalEntry::class)
                ->where('reference_type', PurchaseInvoice::class)
                ->whereNotIn('reference_id', $validInvoiceIds)
                ->get();
            foreach ($orphanedInvoiceEntries as $e) {
                $e->details()->delete();
                $e->delete();
            }

            $orphanedPaymentEntries = app(\App\Models\AccuSoft\JournalEntry::class)
                ->where('reference_type', PurchaseInvoicePayment::class)
                ->whereNotIn('reference_id', $validPaymentIds)
                ->get();
            foreach ($orphanedPaymentEntries as $e) {
                $e->details()->delete();
                $e->delete();
            }

            // حذف أي قيود سابقة مكتوب فيها عكس أو حالتها معكوس ومتعلقة بالمشتريات
            $allReversedEntries = app(\App\Models\AccuSoft\JournalEntry::class)
                ->where('status', \App\Models\AccuSoft\JournalEntry::STATUS_REVERSED)
                ->orWhere('description', 'like', '%عكس%سداد%')
                ->orWhere('description', 'like', '%عكس آلي%')
                ->get();
            foreach ($allReversedEntries as $e) {
                $e->details()->delete();
                $e->delete();
            }

            foreach ($invoices as $inv) {
                if ($inv->items->isEmpty()) {
                    continue;
                }

                // 2. إعادة حساب المبالغ والخصوم والضرائب
                $itemsArr = $inv->items->toArray();
                $res = $calculator->calculate(
                    $itemsArr,
                    (float) $inv->number_discount,
                    (int) $inv->type_discount,
                    false,
                    (float) $inv->shipping_cost,
                    (float) $inv->shipping_vat_rate
                );

                foreach ($res['items'] as $index => $calcItem) {
                    if (isset($inv->items[$index])) {
                        $inv->items[$index]->updateQuietly([
                            'vat_amount' => $calcItem['vat_amount'],
                            'subtotal_with_vat' => $calcItem['subtotal_with_vat'],
                            'total_discount' => $calcItem['total_discount'],
                        ]);
                    }
                }

                $inv->updateQuietly([
                    'total_exclusive_vat' => $res['total_exclusive_vat'],
                    'total_discount' => $res['total_discount'],
                    'total_vat' => $res['total_vat'],
                    'total_inclusive_vat' => $res['total_inclusive_vat'],
                ]);

                // 3. تنظيف وحذف أي دفعات مكررة أو زائدة
                $payments = $inv->payments()->get();
                if ($payments->count() > 1) {
                    $seenAccounts = [];
                    foreach ($payments as $payment) {
                        if (in_array($payment->account_id, $seenAccounts)) {
                            // حذف الدفعة المكررة
                            $payment->delete();
                        } else {
                            $seenAccounts[] = $payment->account_id;
                        }
                    }
                }

                // 4. حذف القيود السابقة المرتبطة بهذه الفاتورة نهائياً
                app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->deleteJournalEntries($inv);

                // 5. إعادة بناء وإنشاء القيود الصحيحة والموزونة ومزامنة حركات المخزون
                $this->processPurchaseLinking($inv);

                $processedCount++;
                $details[] = [
                    'invoice_number' => $inv->invoice_number,
                    'total_inclusive_vat' => $res['total_inclusive_vat'],
                    'total_vat' => $res['total_vat'],
                ];
            }

            return [
                'count' => $processedCount,
                'details' => $details,
            ];
        });
    }
}
