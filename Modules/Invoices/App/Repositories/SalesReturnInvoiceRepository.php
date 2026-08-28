<?php

namespace Modules\Invoices\App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\invApp\SalesInvoice;
use App\Models\invApp\SalesInvoiceItem;

/**
 * مستودع مرتجع المبيعات
 * يعتمد على SalesInvoiceRepository مع تخصيص عمليات الربط والمحاسبة للمرتجع
 */
class SalesReturnInvoiceRepository extends SalesInvoiceRepository
{
    public function model(): string
    {
        return SalesInvoice::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (isset($search['type_inv']) && !empty($search['type_inv'])) {
            if (is_array($search['type_inv'])) {
                $query->whereIn('type_inv', $search['type_inv']);
            } else {
                $query->where('type_inv', $search['type_inv']);
            }
        } else {
            $query->whereIn('type_inv', [SalesInvoice::TYPE_RETURN, SalesInvoice::TYPE_RETURN_POS]);
        }

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.sales_return.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    // =========================================================================
    // SECTION: Core CRUD Operations
    // =========================================================================

    /**
     * Create a new sales return
     */
    public function createReturn(array $input): SalesInvoice
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            $this->validateReturnQuantities($items, $input['parent_id'] ?? null);

            $input = $this->prepareInvoiceData($input, 'sales_return');
            $input['status'] = $input['status'] ?? SalesInvoice::STATUS_RETURNED;

            /** @var SalesInvoice $model */
            $model = $this->create($input);

            if (! empty($items)) {
                $this->saveItems($model, $items);
            }
            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            $this->processReturnLinking($model);

            if (! empty($model->parent_id)) {
                $this->updateParentInvoiceStatus($model->parent_id);
            }

            return $model;
        });
    }

    /**
     * Update an existing sales return
     */
    public function updateReturn(array $input, int $id): SalesInvoice
    {
        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;
            unset($input['items'], $input['payments']);

            /** @var SalesInvoice $model */
            $model = $this->find($id);
            if (! $model) {
                throw new \Exception(__('messages.not_found'));
            }

            if ($model->status != SalesInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن تعديل المرتجع بعد ترحيله وفقاً لأنظمة هيئة الزكاة.');
            }

            if ($items !== null) {
                $this->validateReturnQuantities($items, $model->parent_id, $id);
            }

            // Handle transition from Draft to Official Number
            if (($input['status'] ?? $model->status) != SalesInvoice::STATUS_DRAFT &&
                (empty($model->invoice_number) || str_starts_with($model->invoice_number, 'DRAFT-'))) {
                $input['invoice_number'] = $this->generateNumber('sales_return', false);
            }

            if (isset($model) && $model->type_inv == SalesInvoice::TYPE_RETURN_POS) {
                $input['type_inv'] = SalesInvoice::TYPE_RETURN_POS;
            } else {
                $input['type_inv'] = SalesInvoice::TYPE_RETURN;
            }
            $model = $this->update($input, $id);

            if ($items !== null) {
                $model->items()->delete();
                $this->saveItems($model, $items);
            }
            if ($payments !== null) {
                $model->payments()->delete();
                $this->savePayments($model, $payments);
            }

            $this->processReturnLinking($model);
            if (! empty($model->parent_id)) {
                $this->updateParentInvoiceStatus($model->parent_id);
            }

            return $model;
        });
    }

    /**
     * Delete a sales return
     */
    public function deleteReturn(int $id): ?bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->find($id);
            if (! $model) {
                return false;
            }

            if ($model->status != SalesInvoice::STATUS_DRAFT) {
                throw new \Exception('لا يمكن حذف المرتجع بعد ترحيله وفقاً لأنظمة هيئة الزكاة.');
            }

            app(\Modules\Invoices\App\Services\InvoiceAccountingService::class)->deleteJournalEntries($model);

            $parentId = $model->parent_id;

            $model->items()->delete();
            $model->payments()->delete();
            $result = parent::delete($id);

            if ($parentId) {
                $this->updateParentInvoiceStatus($parentId);
            }

            return $result;
        });
    }

    // =========================================================================
    // SECTION: Data Preparation & Validation
    // =========================================================================

    protected function prepareInvoiceData(array $input, string $type): array
    {
        $input = parent::prepareInvoiceData($input, $type);

        $isPos = false;
        if (!empty($input['parent_id'])) {
            $parent = SalesInvoice::find($input['parent_id']);
            if ($parent && in_array($parent->type_inv, [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])) {
                $isPos = true;
            }
        }

        if ($isPos || (isset($input['type_inv']) && $input['type_inv'] == SalesInvoice::TYPE_RETURN_POS)) {
            $input['type_inv'] = SalesInvoice::TYPE_RETURN_POS;
        } else {
            $input['type_inv'] = SalesInvoice::TYPE_RETURN;
        }

        return $input;
    }

    protected function validateReturnQuantities(array &$items, $parentId, $excludeId = null)
    {
        if (empty($parentId) || empty($items)) {
            return;
        }

        $parentInvoice = SalesInvoice::with('items')->find($parentId);
        if (! $parentInvoice) {
            return;
        }

        $errors = [];

        foreach ($items as $index => &$item) {
            $productId = $item['product_id'] ?? null;
            $serial = $this->normalizeSerial($item['serial'] ?? null);

            $parentItems = $parentInvoice->items->where('product_id', $productId);
            if ($parentItems->isEmpty()) {
                $errors["items.{$index}.product_id"] = __('invoices::models/sales_return_invoices.validation.product_not_in_invoice', [
                    'product' => $item['product_name'] ?? ('#'.$productId),
                ]);
                continue;
            }

            try {
                $parentItem = $this->resolveParentItemBySerial($parentItems, $serial, $item, $index);
                $item['serial'] = $serial ?? $parentItem->serial;

                $alreadyReturned = $this->getAlreadyReturnedQuantity($parentInvoice->id, $productId, $item['serial'], $excludeId);
                $available = $parentItem->quantity - $alreadyReturned;

                if ($item['quantity'] > ($available + 0.0001)) {
                    $errors["items.{$index}.quantity"] = __('invoices::models/sales_return_invoices.validation.quantity_exceeded', [
                        'product' => $item['product_name'] ?? ('#'.$productId),
                        'available' => $available,
                    ]);
                }
            } catch (ValidationException $e) {
                $errors["items.{$index}.serial"] = $e->errors()['items'][0] ?? 'Invalid item configuration.';
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function resolveParentItemBySerial($parentItems, ?string $serial, array $item, int $index = 0)
    {
        if ($parentItems->count() === 1) {
            return $parentItems->first();
        }

        if (empty($serial)) {
            throw ValidationException::withMessages([
                'items' => [__('invoices::models/sales_return_invoices.validation.multiple_lines_require_serial', [
                    'product' => $item['product_name'] ?? ('#'.$item['product_id']),
                ])],
            ]);
        }

        $parentItem = $parentItems->firstWhere('serial', $serial);
        if (! $parentItem) {
            throw ValidationException::withMessages([
                'items' => [__('invoices::models/sales_return_invoices.validation.product_not_in_invoice', [
                    'product' => $item['product_name'] ?? ('#'.$item['product_id']),
                ])],
            ]);
        }

        return $parentItem;
    }

    protected function getAlreadyReturnedQuantity(int $parentId, ?int $productId, ?string $serial, $excludeId = null): float
    {
        $query = SalesInvoiceItem::whereHas('invoice', function ($q) use ($parentId, $excludeId) {
            $q->where('parent_id', $parentId)
              ->where('type_inv', SalesInvoice::TYPE_RETURN)
              ->where('status', '!=', SalesInvoice::STATUS_DRAFT);

            if ($excludeId) {
                $q->where('id', '!=', $excludeId);
            }
        })
        ->where('product_id', $productId);

        if (! empty($serial)) {
            $query->where('serial', $serial);
        }

        return (float) $query->sum('quantity');
    }

    protected function normalizeSerial($serial): ?string
    {
        if ($serial === null || $serial === '') {
            return null;
        }

        return str_pad((string) $serial, 6, '0', STR_PAD_LEFT);
    }

    protected function productNotInParentInvoiceException(array $item)
    {
        return ValidationException::withMessages([
            'items' => __('invoices::models/sales_return_invoices.validation.product_not_in_invoice', [
                'product' => $item['product_name'] ?? ('#'.$item['product_id']),
            ]),
        ]);
    }

    // =========================================================================
    // SECTION: Integration & Accounting Logic
    // =========================================================================

    protected function processReturnLinking(SalesInvoice $model)
    {
        if ($model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        $zatcaSuccess = $this->handleZatcaIntegration($model);
        if (! $zatcaSuccess && $model->status == SalesInvoice::STATUS_DRAFT) {
            return;
        }

        // Ensure items and their products are loaded from the database
        // before processing stock movements. This prevents the bug where
        // $model->items is cached as an empty collection after model creation.
        $model->load('items.product');

        foreach ($model->items as $item) {
            if ($item->product && $item->product->type == 2) {
                continue;
            }
            $this->handleStockMovement($model, $item);
        }

        $this->generateJournalEntries($model);
    }


    // =========================================================================
    // SECTION: Utility & Status Helpers
    // =========================================================================

    protected function updateParentInvoiceStatus(int $parentId): void
    {
        $parent = SalesInvoice::find($parentId);
        if (! $parent) {
            return;
        }

        $totalReturned = SalesInvoice::where('parent_id', $parentId)->where('type_inv', SalesInvoice::TYPE_RETURN)->where('status', '!=', SalesInvoice::STATUS_REJECTED)->where('status', '!=', SalesInvoice::STATUS_DRAFT)->sum('total_inclusive_vat');

        if ($totalReturned >= ($parent->total_inclusive_vat - 0.01)) {
            $parent->updateQuietly(['status' => SalesInvoice::STATUS_RETURNED]);
        } elseif ($totalReturned > 0) {
            $parent->updateQuietly(['status' => SalesInvoice::STATUS_PARTIALLY_RETURNED]);
        } else {
            $parent->updateQuietly(['status' => SalesInvoice::STATUS_SUBMITTED]);
        }
    }

    public function salesInvoices(): array
    {
        return SalesInvoice::isInvoice()->where('status', '!=', SalesInvoice::STATUS_DRAFT)->orderByDesc('id')->get()->mapWithKeys(fn ($inv) => [$inv->id => '#'.$inv->invoice_number.' - '.($inv->customer->name ?? '---')])->toArray();
    }

    public function name(): string
    {
        return __('invoices::models/sales_return_invoices.plural') ?: 'مرتجعات المبيعات';
    }

    public function getHeaders(): array
    {
        return [
            __('invoices::models/sales_return_invoices.fields.invoice_number') ?: __('invoices::models/sales_invoices.fields.invoice_number'),
            __('invoices::models/sales_return_invoices.fields.customer_id') ?: __('invoices::models/sales_invoices.fields.customer_id'),
            __('invoices::models/sales_return_invoices.fields.branch_id') ?: __('invoices::models/sales_invoices.fields.branch_id'),
            __('invoices::models/sales_return_invoices.fields.issue_date') ?: __('invoices::models/sales_invoices.fields.issue_date'),
            __('invoices::models/sales_return_invoices.fields.total_exclusive_vat') ?: __('invoices::models/sales_invoices.fields.total_exclusive_vat'),
            __('invoices::models/sales_return_invoices.fields.total_vat') ?: __('invoices::models/sales_invoices.fields.total_vat'),
            __('invoices::models/sales_return_invoices.fields.total_discount') ?: __('invoices::models/sales_invoices.fields.total_discount'),
            __('invoices::models/sales_return_invoices.fields.total_inclusive_vat') ?: __('invoices::models/sales_invoices.fields.total_inclusive_vat'),
            __('invoices::models/sales_return_invoices.fields.status') ?: __('invoices::models/sales_invoices.fields.status'),
            __('invoices::models/sales_return_invoices.fields.created_by') ?: __('invoices::models/sales_invoices.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return SalesInvoice::isReturn()
            ->with(['customer', 'store', 'createdBy'])
            ->get()
            ->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $invoice->customer->name ?? '---',
                    'branch_id' => $invoice->store ? $invoice->store->name : '---',
                    'issue_date' => $invoice->issue_date ? ($invoice->issue_date instanceof \Carbon\Carbon ? $invoice->issue_date->format('Y-m-d') : date('Y-m-d', strtotime($invoice->issue_date))) : '---',
                    'total_exclusive_vat' => number_format($invoice->total_exclusive_vat, 2),
                    'total_vat' => number_format($invoice->total_vat, 2),
                    'total_discount' => number_format($invoice->total_discount, 2),
                    'total_inclusive_vat' => number_format($invoice->total_inclusive_vat, 2),
                    'status' => $invoice->status_text,
                    'created_by' => $invoice->createdBy->name ?? '---',
                ];
            })
            ->toArray();
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

