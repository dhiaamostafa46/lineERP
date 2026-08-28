<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\AccuSoft\AccountMapping;
use App\Services\AccuSoft\JournalEntryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Invoices\App\Helpers\InvoiceHelper;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Models\PurchaseInvoiceItem;
use Modules\Invoices\App\Models\PurchaseInvoicePayment;

/**
 * مستودع مرتجع المشتريات
 * يعتمد بشكل كامل على PurchaseInvoiceRepository مع تخصيص عمليات الربط والمحاسبة للمرتجع
 */
class PurchaseReturnInvoiceRepository extends PurchaseInvoiceRepository
{
    public function model(): string
    {
        return PurchaseInvoice::class;
    }

    /**
     * استعلام مخصص لمرتجعات المشتريات فقط
     */
    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit)
            ->where('type_inv', PurchaseInvoice::TYPE_RETURN);

        // التحقق من الصلاحية: إذا لم يكن لدى المستخدم صلاحية الوصول الكامل، يتم تقييد الاستعلام بالسجلات التي أنشأها فقط
        if (auth()->check() && ! auth()->user()->can('invoices.purchase_return.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.purchase_return';


        }

        return $query;
    }

    /**  scopedaccess
     * إنشاء مرتجع مشتريات جديد
     */
    public function createReturn(array $input): PurchaseInvoice
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            // 1. التحقق من كميات الإرجاع
            $this->validateReturnQuantities($items, $input['parent_id'] ?? null);

            // 2. التحضير الأساسي للبيانات باستخدام المنطق المشترك
            $input = $this->prepareInvoiceData($input, 'purchase_return');

            /** @var PurchaseInvoice $model */
            $model = $this->create($input);

            // 3. حفظ الأصناف والدفعات
            if (! empty($items)) {
                $this->saveItems($model, $items);
            }
            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            // 4. معالجة الربط (المخزن والمحاسبة) - تتجاهل المسودة تلقائياً
            $this->processReturnLinking($model);

            // 5. تحديث حالة الفاتورة الأصلية (إذا لم تكن مسودة)
            if ($model->status != PurchaseInvoice::STATUS_DRAFT && ! empty($model->parent_id)) {
                $this->updateParentInvoiceStatus($model->parent_id);
            }

            return $model;
        });
    }

    /**
     * Prepare shared invoice data defaults (overriding to ensure type is RETURN).
     */
    protected function prepareInvoiceData(array $input, string $type): array
    {
        $input = parent::prepareInvoiceData($input, $type);
        $input['status'] = $input['status'] ?? PurchaseInvoice::STATUS_RETURNED;
        $isDraft = ((int) $input['status'] === PurchaseInvoice::STATUS_DRAFT);

        if (empty($input['invoice_number'])) {
            if ($isDraft) {
                $input['invoice_number'] = 'DRAFT-' . now()->format('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
            } else {
                $input['invoice_number'] = $this->generateNumber($type);
            }
        }

        $input['type_inv'] = PurchaseInvoice::TYPE_RETURN;
        $input['created_by'] = $input['created_by'] ?? (auth()->id() ?? 1);

        return $input;
    }

    /**
     * تحديث مرتجع مشتريات
     */
    public function updateReturn(array $input, int $id): PurchaseInvoice
    {
        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;
            unset($input['items'], $input['payments']);

            /** @var PurchaseInvoice $model */
            $model = $this->find($id);
            if (! $model) {
                throw new \Exception(__('messages.not_found'));
            }

            // منع تعديل مرتجع المشتريات إذا كان معتمداً
            if ($model->status != PurchaseInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن تعديل مرتجع المشتريات بعد اعتماده.');
            }

            $input['type_inv'] = PurchaseInvoice::TYPE_RETURN;

            // إذا تمت ترقية المسودة إلى معتمدة → توليد رقم مرتجع رسمي
            $newStatus = (int) ($input['status'] ?? $model->status);
            if ($newStatus != PurchaseInvoice::STATUS_DRAFT &&
                (empty($model->invoice_number) || str_starts_with($model->invoice_number, 'DRAFT-'))) {
                $input['invoice_number'] = $this->generateNumber('purchase_return', false);
            }

            // 1. التحقق من الكميات (مع استثناء المرتجع الحالي)
            if ($items !== null) {
                $this->validateReturnQuantities($items, $model->parent_id, $id);
            }

            // 2. عكس العمليات القديمة فقط إذا كان المرتجع معتمداً سابقاً
            if ($model->status != PurchaseInvoice::STATUS_DRAFT) {
                $this->reverseReturnOperations($model, false);
            }

            // 3. التحديث الأساسي
            $model = $this->update($input, $id);

            // 4. تحديث الأصناف والدفعات
            if ($items !== null) {
                $model->items()->delete();
                $this->saveItems($model, $items);
            }
            if ($payments !== null) {
                $model->payments()->delete();
                $this->savePayments($model, $payments);
            }

            // 5. إعادة بناء الربط (تتجاهل المسودات تلقائياً)
            $this->processReturnLinking($model);

            // 6. تحديث حالة الفاتورة الأصلية (إذا لم تكن مسودة)
            if ($model->status != PurchaseInvoice::STATUS_DRAFT && ! empty($model->parent_id)) {
                $this->updateParentInvoiceStatus($model->parent_id);
            }

            return $model;
        });
    }

    /**
     * حذف مرتجع
     */
    public function deleteReturn(int $id): ?bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->find($id);
            if (! $model) {
                return false;
            }

            $parentId = $model->parent_id;

            // 1. عكس كل العمليات (محاسبة ومخزن)
            $this->reverseReturnOperations($model, true);

            // 2. الحذف الفعلي
            $model->items()->delete();
            $model->payments()->delete();
            $result = parent::delete($id);

            // 3. تحديث حالة الفاتورة الأصلية
            if ($parentId) {
                $this->updateParentInvoiceStatus($parentId);
            }

            return $result;
        });
    }

    /**
     * التحقق من كميات الإرجاع
     */
    protected function validateReturnQuantities(array $items, $parentId, $excludeId = null)
    {
        if (empty($parentId)) {
            return;
        }

        $parent = PurchaseInvoice::with('items')->find($parentId);
        if (! $parent) {
            return;
        }

        $errors = [];

        foreach ($items as $index => $item) {
            $parentItem = $parent->items()->where('product_id', $item['product_id'])->first();

            // تحقق من وجود الصنف في الفاتورة الأصلية
            if (! $parentItem) {
                $errors["items.{$index}.product_id"] = __('invoices::models/purchase_return_invoices.validation.product_not_in_invoice', [
                    'product' => $item['product_name'] ?? ('#'.$item['product_id']),
                ]);
                continue;
            }

            // تحقق من الكمية المتاحة للإرجاع
            $alreadyReturned = PurchaseInvoiceItem::whereHas('invoice', function ($q) use ($parent, $excludeId) {
                $q->where('parent_id', $parent->id)
                    ->where('type_inv', PurchaseInvoice::TYPE_RETURN);
                if ($excludeId) {
                    $q->where('id', '!=', $excludeId);
                }
            })
                ->where('product_id', $item['product_id'])
                ->sum('quantity');

            $available = $parentItem->quantity - $alreadyReturned;

            if ($item['quantity'] > ($available + 0.0001)) { // هامش خطأ بسيط للكسور
                $errors["items.{$index}.quantity"] = __('invoices::models/purchase_return_invoices.validation.quantity_exceeded', [
                    'product' => $item['product_name'] ?? ('#'.$item['product_id']),
                    'available' => $available,
                ]);
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Common numbering logic is now handled by the HasInvoiceSharedLogic trait.
     */

    /**
     * عكس العمليات (القيود والمخزون)
     */
    protected function reverseReturnOperations(PurchaseInvoice $model, bool $isDeleting = false)
    {
        // 1. حذف القيود المحاسبية
        app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->deleteJournalEntries($model);

        // 2. عكس المخزون (trait StockManagementTrait)
        $this->revertAllStockMovements($model);
    }

    /**
     * معالجة الربط (المخزن والمحاسبة) - منطق المرتجع
     */
    protected function processReturnLinking(PurchaseInvoice $model)
    {
        // إذا كان المرتجع مسودة، لا يطّبق أي أثر على المخزون أو القيود المحاسبية
        if ($model->status == PurchaseInvoice::STATUS_DRAFT) {
            return;
        }

        // Ensure items and their products are loaded from the database
        // before processing stock movements.
        $model->load('items.product');

        // 1. حركات المخزون (إخراج)
        foreach ($model->items as $item) {
            $this->handleStockMovement($model, $item);
        }

        // 2. القيود المحاسبية عبر خدمة محاسبة الفواتير
        app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->generatePurchaseReturnEntries($model);
    }

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
        return PurchaseInvoice::isReturn()
            ->with(['supplier', 'store', 'createdBy'])
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'supplier_invoice_number' => $invoice->supplier_invoice_number ?? '---',
                    'supplier_id' => $invoice->supplier ? $invoice->supplier->name : '---',
                    'branch_id' => $invoice->store ? $invoice->store->name : '---',
                    'issue_date' => $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '---',
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

    /**
     * تحديث حالة الفاتورة الأصلية
     */
    protected function updateParentInvoiceStatus(int $parentId): void
    {
        $parent = PurchaseInvoice::find($parentId);
        if (! $parent) {
            return;
        }

        $totalReturned = PurchaseInvoice::where('parent_id', $parentId)
            ->where('type_inv', PurchaseInvoice::TYPE_RETURN)
            ->where('status', '!=', PurchaseInvoice::STATUS_REJECTED)
            ->sum('total_inclusive_vat');

        if ($totalReturned >= ($parent->total_inclusive_vat - 0.01)) {
            $parent->updateQuietly(['status' => PurchaseInvoice::STATUS_RETURNED]);
        } elseif ($totalReturned > 0) {
            $parent->updateQuietly(['status' => PurchaseInvoice::STATUS_PARTIALLY_RETURNED]);
        } else {
            $parent->updateQuietly(['status' => PurchaseInvoice::STATUS_SUBMITTED]);
        }
    }

    /**
     * قائمة الفواتير الأصلية
     */
    public function purchaseInvoices(): array
    {
        return PurchaseInvoice::isInvoice()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn ($inv) => [$inv->id => '#'.$inv->invoice_number.' - '.($inv->supplier->name ?? '---')])
            ->toArray();
    }

    public function name(): string
    {
        return __('invoices::models/purchase_return_invoices.plural') ?: 'مرتجعات المشتريات';
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
}
