<?php

namespace Modules\Invoices\App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\App\Helpers\HasInvoiceSharedLogic;
use Modules\Invoices\App\Models\PurchaseOrder;

class PurchaseOrderRepository extends BaseRepository
{
    use HasInvoiceSharedLogic;

    protected $fieldSearchable = ['uuid', 'invoice_number', 'supplier_invoice_number', 'supplier_id', 'issue_date', 'status', 'store_id'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return PurchaseOrder::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (isset($search['created_by']) && !empty($search['created_by'])) {
            $userId = $search['created_by'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('user_id', $userId);
            });
        }

        // Scoped Access Logic
        if (auth()->check() && ! auth()->user()->can('invoices.purchase_orders.scopedaccess')) {
            $query->where('created_by', auth()->id());
        }

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.purchase_orders';


        }

        return $query;
    }

    public function CreatePurchase($input): PurchaseOrder
    {
        return DB::transaction(function () use ($input) {
            $items = $input['items'] ?? [];
            $payments = $input['payments'] ?? [];
            unset($input['items'], $input['payments']);

            // Prepare default data (using type 'purchase' to match current settings structure or specifically 'purchase_order')
            // The trait handles prefixing based on the type passed to generateNumber
            $input = $this->prepareInvoiceData($input, 'purchase_order');

            /** @var PurchaseOrder $model */
            $model = $this->create($input);

            if (! empty($items)) {
                $this->saveItems($model, $items);
            }

            if (! empty($payments)) {
                $this->savePayments($model, $payments);
            }

            // لا يوجد ربط محاسبي أو مخزني مع أمر الشراء مبدئياً
            $this->processPurchaseLinking($model);

            return $model;
        });
    }

    /**
     * Prepare shared invoice data defaults.
     */
    protected function prepareInvoiceData(array $input, string $type): array
    {
        if (empty($input['invoice_number'])) {
            $input['invoice_number'] = $this->generateNumber($type);
        }

        $input['type_inv'] = $input['type_inv'] ?? PurchaseOrder::TYPE_INVOICE;
        $input['issue_date'] = $input['issue_date'] ?? now();
        $input['branch_id'] = $input['branch_id'] ?? (auth()->user()->branch_id ?? 1);
        $input['created_by'] = $input['created_by'] ?? (auth()->id() ?? 1);

        if (empty($input['status'])) {
            $input['status'] = PurchaseOrder::STATUS_NEW;
        }

        return $input;
    }

    public function updatePurchase($input, $id)
    {
        return DB::transaction(function () use ($input, $id) {
            $items = $input['items'] ?? null;
            $payments = $input['payments'] ?? null;

            // Remove items and payments from input before updating invoice
            unset($input['items'], $input['payments']);

            /** @var PurchaseOrder $model */
            $model = $this->find($id);

            // الحفظ كـ "جديد" تلقائياً
            if (empty($input['status'])) {
                $input['status'] = PurchaseOrder::STATUS_NEW;
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

            // لا يوجد إعادة معالجة للمخزون لأوامر الشراء
            // $this->processPurchaseLinking($model);

            return $model;
        });
    }

    protected function saveItems(PurchaseOrder $model, array $items)
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

            // التعامل مع الوحدة بناءً على نوع المنتج
            if ($product && $product->type != \App\Models\BasicDataApp\Product::TYPE_SERVICE) {
                // إذا كان منتج عادي ولم نجد له وحدة، نوقف العملية ونظهر رسالة
                if (empty($item['unit_id'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "items.{$index}.unit_id" => __('invoices::models/sales_invoices.validation.product_no_unit'),
                    ]);
                }
            } else {
                // إذا كانت خدمة، نسمح بأن تكون null
                $item['unit_id'] = $item['unit_id'] ?: null;
            }
        }
        $model->items()->createMany($items);
    }

    protected function savePayments(PurchaseOrder $model, array $payments)
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

            $model->items()->delete();
            $model->payments()->delete();

            return parent::delete($id);
        });
    }

    /**
     * معالجة الربط المحاسبي والمخزني لأمر الشراء (معطّل حالياً بناء على القواعد)
     */
    protected function processPurchaseLinking(PurchaseOrder $model)
    {
        // أوامر الشراء مبدئياً لا تسجل قيد محاسبي مباشر ولا تحركات مخزون
        // هذا مجرد وظيفة فارغة للحفاظ على التوافقية لو تم استدعاؤها
    }

    // Common data methods are now handled by HasInvoiceSharedLogic trait
    public function getHeaders(): array
    {
        return [
            __('invoices::models/purchase_orders.fields.invoice_number'),
            __('invoices::models/purchase_orders.fields.supplier_invoice_number'),
            __('invoices::models/purchase_orders.fields.supplier_id'),
            __('invoices::models/purchase_orders.fields.branch_id'),
            __('invoices::models/purchase_orders.fields.issue_date'),
            __('invoices::models/purchase_orders.fields.total_exclusive_vat'),
            __('invoices::models/purchase_orders.fields.total_vat'),
            __('invoices::models/purchase_orders.fields.total_discount'),
            __('invoices::models/purchase_orders.fields.total_inclusive_vat'),
            __('invoices::models/purchase_orders.fields.status'),
            __('invoices::models/purchase_orders.fields.created_by'),
        ];
    }

    public function dataExcel(): array
    {
        return PurchaseOrder::with(['supplier', 'store', 'createdBy'])
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
        return __('invoices::models/purchase_orders.plural');
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
