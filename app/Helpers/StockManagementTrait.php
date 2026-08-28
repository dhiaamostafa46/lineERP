<?php

namespace App\Helpers;

use App\Models\invApp\SalesInvoice;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Store\App\Models\StDamaged;
use Modules\Store\App\Models\StDirectTransfer;
use Modules\Store\App\Models\StIssuing;
use Modules\Store\App\Models\StOpeningBalance;
use Modules\Store\App\Models\StReceiving;

trait StockManagementTrait
{
    /**
     * Check if negative stock is allowed for a specific document type and organization.
     */
    protected function canAllowNegativeStock(Model $document): bool
    {
        $strictDocs = [
            \Modules\Store\App\Models\StDamaged::class,
            \Modules\Store\App\Models\StReservation::class,
        ];

        // Never allow negative stock for strict operations (Damage, Transfer out)
        if (in_array(get_class($document), $strictDocs)) {
            return false;
        }

        // 1. Check Invoices Module Settings (Sales/Purchase/Returns)
        if ($document instanceof \App\Models\invApp\SalesInvoice ||
            $document instanceof \Modules\Invoices\App\Models\PurchaseInvoice) {

            // Check if POS specific setting was injected
            if (app()->bound('pos_allow_negative_stock')) {
                return app('pos_allow_negative_stock');
            }

            if (class_exists(\Modules\Invoices\App\Helpers\InvoiceHelper::class)) {
                $invSettings = \Modules\Invoices\App\Helpers\InvoiceHelper::getSettings();
                if ($invSettings && (bool) $invSettings->allow_negative_stock) {
                    return true;
                }
            }
        }

        // 2. Fallback to Inventory Settings (Store Module)
        if (class_exists(\Modules\Store\App\Models\InventorySettings::class)) {
            $settings = \Modules\Store\App\Models\InventorySettings::where('org_id', $document->org_id ?? 1)->first();

            return $settings ? (bool) $settings->allow_negative_stock : false;
        }

        return false;
    }

    /**
     * Handle stock movement creation and stock record updates for various document types.
     *
     * @param  Model  $document  The source document
     * @param  string  $quantityField  The field name for quantity (default is 'quantity')
     * @param  array|null  $manualConfig  Optional manual configuration override
     *
     * @throws \Exception
     */
    public function handleStockMovement(Model $document, $item, string $quantityField = 'quantity', $manualConfig = null): void
    {
        DB::beginTransaction();

        try {
            if (! $item->relationLoaded('product')) {
                $item->load('product');
            }

            // عدم معالجة حركات المخزون للخدمات (Service Products)
            if ($item->product && $item->product->type == \App\Models\BasicDataApp\Product::TYPE_SERVICE) {
                DB::commit();

                return;
            }

            // تحويل البيانات إلى الوحدة الأساسية
            $baseUnitData = $this->convertToBaseUnit($item, $quantityField);

            $config = $manualConfig ?? $this->getDocumentConfig($document, $item);

            // إنشاء حركة المخزون بالوحدة الأساسية
            $Movement = $this->createStockMovement($document, $baseUnitData, $config, $item);
            // dd(  $baseUnitData , $config   ,   $Movement ,$item);

            // تحديث المخزون بالوحدة الأساسية
            $this->updateStockQuantity($document, $baseUnitData, $config);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle stock reservation for Sales Orders or similar documents.
     *
     * @param  object  $item
     * @param  string  $action  'reserve' or 'unreserve'
     */
    public function handleStockReservation(Model $document, $item, string $action = 'reserve', string $quantityField = 'quantity'): void
    {
        DB::beginTransaction();
        try {
            $baseUnitData = $this->convertToBaseUnit($item, $quantityField);

            $this->updateReservedQuantity($document, $baseUnitData, $action);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحويل البيانات من أي وحدة إلى الوحدة الأساسية
     *
     * @param  object  $item
     *
     * @throws \Exception
     */
    protected function convertToBaseUnit($item, string $quantityField = 'quantity'): array
    {
        // استخراج الوحدات
        $decodedUnits = isset($item->unit) && is_string($item->unit) ? json_decode($item->unit, true) : null;
        if (is_array($decodedUnits) && ! empty($decodedUnits)) {
            $units = $decodedUnits;
        } else {
            $itemHaveSizes = isset($item->have_sizes) ? filter_var($item->have_sizes, FILTER_VALIDATE_BOOLEAN) : false;
            if ($itemHaveSizes) {
                // إذا كان الصنف بمقاس، نحضر الوحدات من المنتج الأب
                $productSize = \App\Models\BasicDataApp\ProductSize::with('product.units')->find($item->product_id);
                $units = $productSize && $productSize->product ? $productSize->product->units->toArray() : [];
            } else {
                // Ensure product relationship is loaded if needed
                if (! $item->relationLoaded('product')) {
                    $item->load('product.units');
                }
                $units = $item->product ? $item->product->units->toArray() : [];
            }
        }

        if (empty($units) || ! is_array($units)) {
            // fallback للمنتجات التي لا تحتوي على سجلات في جدول product_units
            $product = $item->product ?? (\App\Models\BasicDataApp\Product::find($item->product_id));
            $unitId = $item->unit_id ?? ($product->base_unit_id ?? 1);
            $units = [
                [
                    'id' => $unitId,
                    'unit_id' => $unitId,
                    'is_base' => 1,
                    'conversion_factor' => 1,
                ],
            ];
        }

        // البحث عن الوحدة الأساسية
        $baseUnit = collect($units)->first(function ($u) {
            return ! empty($u['is_base']) && ($u['is_base'] == 1 || $u['is_base'] == true);
        });

        if (! $baseUnit) {
            if (count($units) === 1) {
                $baseUnit = reset($units);
            } else {
                // الاعتماد على أصغر معامل تحويل كخيار، وإذا لم يوجد نأخذ الوحدة الأخيرة
                $baseUnit = collect($units)->sortBy('conversion_factor')->first() ?? collect($units)->last();
            }
        }

        if (! $baseUnit) {
            $unitId = $item->unit_id ?? 1;
            $baseUnit = [
                'id' => $unitId,
                'unit_id' => $unitId,
                'is_base' => 1,
                'conversion_factor' => 1,
            ];
        }

        // البحث عن الوحدة المدخلة
        $selectedUnit = collect($units)->first(function ($u) use ($item) {
            return (isset($u['unit_id']) && $u['unit_id'] == $item->unit_id) ||
                   (isset($u['id']) && $u['id'] == $item->unit_id);
        });

        if (! $selectedUnit) {
            // استخدام الوحدة الأساسية افتراضياً في حال عدم مطابقة رقم الوحدة بدلاً من إيقاف المعالجة
            $selectedUnit = $baseUnit;
        }

        /**
         * ========================================
         * حساب الكمية بالوحدة الأساسية
         * ========================================
         *
         * القاعدة:
         * الكمية الأساسية = الكمية المدخلة × (معامل الوحدة المدخلة ÷ معامل الوحدة الأساسية)
         *
         * مثال 1: إدخال 10 كراتين (كل كرتون = 12 قطعة)
         * - الوحدة المدخلة: كرتون (conversion_factor = 12)
         * - الوحدة الأساسية: قطعة (conversion_factor = 1)
         * - الكمية الأساسية = 10 × (12 ÷ 1) = 120 قطعة ✓
         *
         * مثال 2: إدخال 100 ملم من منتج أساسه لتر
         * - الوحدة المدخلة: ملم (conversion_factor = 0.001)
         * - الوحدة الأساسية: لتر (conversion_factor = 1)
         * - الكمية الأساسية = 100 × (0.001 ÷ 1) = 0.1 لتر ✓
         */
        $selectedFactor = $selectedUnit['conversion_factor'] ?? 1;
        $baseFactor = $baseUnit['conversion_factor'] ?? 1;

        if ($baseFactor == 0) {
            throw new \Exception('معامل تحويل الوحدة الأساسية لا يمكن أن يكون صفر');
        }

        $baseQuantity = $item->$quantityField * ($selectedFactor / $baseFactor);

        /**
         * ========================================
         * حساب التكلفة بالوحدة الأساسية
         * ========================================
         *
         * القاعدة:
         * تكلفة الوحدة الأساسية = تكلفة الوحدة المدخلة ÷ معامل التحويل
         *
         * مثال: شراء كرتون ب 120 ريال (12 قطعة)
         * - تكلفة الكرتون = 120 ريال
         * - معامل التحويل = 12
         * - تكلفة القطعة = 120 ÷ 12 = 10 ريال ✓
         *
         * التحقق: 120 قطعة × 10 ريال = 1200 ريال
         *         10 كراتين × 120 ريال = 1200 ريال ✓
         */
        $itemUnitCost = $item->unit_cost ?? $item->unit_price ?? 0;

        // إذا كان المورد هو فاتورة مبيعات، يجب استخدام متوسط التكلفة المرجح الحالي من المخزون
        // وليس cost_price من بطاقة المنتج (ثابتة وغير محدّثة تلقائياً)
        if ($item instanceof \App\Models\invApp\SalesInvoiceItem) {
            // جلب متوسط التكلفة المرجح الحالي من جدول المخزون للمستودع المحدد
            $stockRecord = \App\Models\StoreApp\Stock::where('product_id', $item->product_id)
                ->where('is_size', (bool) ($item->have_sizes ?? false))
                ->when(
                    isset($item->salesInvoice) && $item->salesInvoice?->store_id,
                    fn ($q) => $q->where('store_id', $item->salesInvoice->store_id)
                )
                ->orderBy('id', 'asc')
                ->first();

            $currentAverageCost = ($stockRecord && $stockRecord->average_cost > 0)
                ? (float) $stockRecord->average_cost   // تكلفة الوحدة الأساسية
                : (float) ($item->product->cost_price ?? 0); // fallback للقيمة القديمة

            // التكلفة الأساسية (per base unit) × معامل الوحدة المختارة = تكلفة الوحدة المختارة
            $itemUnitCost = $currentAverageCost * (float) $selectedFactor;
        }

        $baseUnitCost = ($selectedFactor > 0)
            ? ($itemUnitCost / $selectedFactor) * $baseFactor
            : $itemUnitCost;

        // التكلفة الإجمالية تبقى ثابتة
        $itemTotalCost = $item->total_cost ?? (($item->quantity * $itemUnitCost) - ($item->total_discount ?? 0));
        $totalCost = $itemTotalCost;

        return [
            'quantity' => $baseQuantity,
            'unit_id' => $baseUnit['unit_id'] ?? $baseUnit['id'],
            'unit_cost' => $baseUnitCost,
            'total_cost' => $totalCost,
            'product_id' => $item->product_id,
            'notes' => $item->notes ?? null,
            'have_sizes' => $item->have_sizes ?? false,
            'original_unit_id' => $item->unit_id,
            'original_quantity' => $item->quantity,
            'original_unit_cost' => $itemUnitCost,
        ];
    }

    /**
     * Get document configuration based on document type
     */
    protected function getDocumentConfig(Model $document, $item): array
    {
        $docClass = get_class($document);

        return match ($docClass) {
            PurchaseInvoice::class => $document->type_inv === PurchaseInvoice::TYPE_RETURN
                ? [
                    'prefix' => 'RTV',
                    'type' => StockMovement::DOC_TYPE_PURCHASE,
                    'direction' => 'out',   // مرتجع → إخراج من المخزون
                    'store_id' => $document->store_id,
                ]
                : [
                    'prefix' => 'PUV',
                    'type' => StockMovement::DOC_TYPE_PURCHASE,
                    'direction' => 'in',
                    'store_id' => $document->store_id,
                ],
            StOpeningBalance::class => [
                'prefix' => 'OB',
                'type' => StockMovement::DOC_TYPE_OPENING_BALANCE,
                'direction' => 'in',
                'store_id' => $document->store_id,
            ],
            StDamaged::class => [
                'prefix' => 'DMG',
                'type' => StockMovement::DOC_TYPE_DAMAGE,
                'direction' => 'out',
                'store_id' => $document->store_id,
            ],
            SalesInvoice::class => ($document->type_inv === SalesInvoice::TYPE_RETURN || $document->type_inv === SalesInvoice::TYPE_RETURN_POS)
                ? [
                    'prefix' => 'SRV',
                    'type' => StockMovement::DOC_TYPE_SALE,
                    'direction' => 'in',
                    'store_id' => $document->store_id,
                ]
                : [
                    'prefix' => 'SLV',
                    'type' => StockMovement::DOC_TYPE_SALE,
                    'direction' => 'out',
                    'store_id' => $document->store_id,
                ],
            StReceiving::class => [
                'prefix' => 'REC',
                'type' => StockMovement::DOC_TYPE_RECEIVING,
                'direction' => 'in',
                'store_id' => $document->store_id,
            ],
            StIssuing::class => [
                'prefix' => 'ISS',
                'type' => StockMovement::DOC_TYPE_ISSUING,
                'direction' => 'out',
                'store_id' => $document->store_id,
            ],
            StDirectTransfer::class => [
                'prefix' => 'TRF',
                'type' => StockMovement::DOC_TYPE_DIRECT_TRANSFER,
                'direction' => 'out',
                'store_id' => $document->from_store_id,
            ],
            \Modules\Store\App\Models\StSettlement::class => $item->variance_type === 'in'
                ? [
                    'prefix' => 'SET-IN',
                    'type' => StockMovement::DOC_TYPE_ADJUSTMENT ?? 3,
                    'direction' => 'in',
                    'store_id' => $document->store_id,
                ]
                : [
                    'prefix' => 'SET-OUT',
                    'type' => StockMovement::DOC_TYPE_ADJUSTMENT ?? 3,
                    'direction' => 'out',
                    'store_id' => $document->store_id,
                ],
            \Modules\Store\App\Models\StReservation::class => [
                'prefix' => 'RES',
                'type' => StockMovement::DOC_TYPE_RESERVATION,
                'direction' => $document->status == \Modules\Store\App\Models\StReservation::STATUS_RETURNED ? 'in' : 'out',
                'store_id' => $document->store_id,
            ],

            default => throw new \Exception("Unsupported document type: {$docClass}"),
        };
    }

    /**
     * Create stock movement record
     */
    protected function createStockMovement(Model $document, array $baseUnitData, array $config, $item = null)
    {
        // Create a unique suffix to prevent integrity constraint violation (e.g. PUV-3 vs PUV-3-1)
        $uniquePart = substr(md5(uniqid(rand(), true)), 0, 4);
        $suffix = isset($item->id) && $item->id ? "-{$item->id}-{$uniquePart}" : "-{$uniquePart}";

        return StockMovement::create([
            'org_id' => $document->org_id,
            'branch_id' => $document->branch_id,
            'user_id' => $document->user_id ?? auth()->id(),
            'product_id' => $baseUnitData['product_id'],
            'movement_number' => "{$config['prefix']}-{$document->id}{$suffix}",
            'movement_date' => $document->document_date ?? now(),
            'movement_type' => $config['type'],
            'stock_type' => $config['direction'],
            'store_id' => $config['store_id'],
            'unit_id' => $baseUnitData['unit_id'], // الوحدة الأساسية
            'quantity' => $baseUnitData['quantity'], // الكمية بالوحدة الأساسية
            'unit_cost' => $baseUnitData['unit_cost'], // التكلفة بالوحدة الأساسية
            'total_cost' => $baseUnitData['total_cost'],
            'is_size' => $baseUnitData['have_sizes'],
            'reference_type' => get_class($document),
            'reference_id' => $document->id,
            'reference_number' => $document->document_number ?? $document->invoice_number ?? $document->id,
            'status' => StockMovement::STATUS_APPROVED,
            'notes' => $baseUnitData['notes'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Get and consolidate stock records to prevent unit_id fragmentation
     */
    protected function getConsolidatedStock(Model $document, array $baseUnitData, array $config, string $action = 'update'): Stock
    {
        $storeId = $config['store_id'] ?? $document->store_id;

        $stocks = clone Stock::where('store_id', $storeId)
            ->where('product_id', $baseUnitData['product_id'])
            ->where('is_size', $baseUnitData['have_sizes']);

        // Lock for update if we are actively updating stock
        if ($action !== 'read') {
            $stocks = $stocks->lockForUpdate();
        }

        $stocks = $stocks->orderBy('id', 'asc')->get();

        if ($stocks->isEmpty()) {
            return Stock::create([
                'store_id' => $storeId,
                'product_id' => $baseUnitData['product_id'],
                'unit_id' => $baseUnitData['unit_id'],
                'is_size' => $baseUnitData['have_sizes'],
                'org_id' => $document->org_id ?? 1,
                'branch_id' => $document->branch_id ?? 1,
                'current_quantity' => 0,
                'reserved_quantity' => 0,
                'average_cost' => 0,
                'last_cost' => 0,
            ]);
        }

        $mainStock = $stocks->first();

        // If there are duplicates, consolidate them
        if ($stocks->count() > 1) {
            $totalCurrent = 0;
            $totalReserved = 0;
            $totalValue = 0;

            foreach ($stocks as $s) {
                $totalCurrent += $s->current_quantity;
                $totalReserved += $s->reserved_quantity;
                $totalValue += ($s->current_quantity * ($s->average_cost ?? 0));

                if ($s->id !== $mainStock->id) {
                    $s->forceDelete(); // Force delete to completely remove duplicate
                }
            }

            $mainStock->current_quantity = $totalCurrent;
            $mainStock->reserved_quantity = $totalReserved;
            $mainStock->average_cost = $totalCurrent > 0 ? round($totalValue / $totalCurrent, 4) : $mainStock->average_cost;
            $mainStock->unit_id = $baseUnitData['unit_id']; // Update to latest base unit
            $mainStock->save();
        }

        return $mainStock;
    }

    /**
     * Update or create stock record and adjust quantities
     * جميع العمليات تتم بالوحدة الأساسية
     */
    protected function updateStockQuantity(Model $document, array $baseUnitData, array $config): void
    {
        $stock = $this->getConsolidatedStock($document, $baseUnitData, $config);

        $oldQuantity = $stock->current_quantity;
        $oldAverageCost = $stock->average_cost ?? 0;

        if ($config['direction'] === 'in') {
            /**
             * ========================================
             * إضافة للمخزون + حساب متوسط التكلفة المرجح
             * ========================================
             *
             * الصيغة:
             * متوسط التكلفة الجديد = (القيمة القديمة + القيمة الجديدة) ÷ الكمية الكلية
             *
             * مثال:
             * المخزون الحالي: 100 قطعة × 10 ريال = 1000 ريال
             * الوارد الجديد: 50 قطعة × 12 ريال = 600 ريال
             *
             * الكمية الكلية = 100 + 50 = 150 قطعة
             * القيمة الكلية = 1000 + 600 = 1600 ريال
             * متوسط التكلفة = 1600 ÷ 150 = 10.67 ريال ✓
             */
            $newQuantity = $oldQuantity + $baseUnitData['quantity'];

            if ($baseUnitData['unit_cost'] > 0) {
                // حساب القيم
                $oldTotalValue = $oldQuantity * $oldAverageCost;
                $newItemValue = $baseUnitData['quantity'] * $baseUnitData['unit_cost'];
                $totalValue = $oldTotalValue + $newItemValue;

                // حساب متوسط التكلفة المرجح
                $stock->average_cost = $newQuantity > 0
                    ? round($totalValue / $newQuantity, 4)
                    : $baseUnitData['unit_cost'];

                $stock->last_cost = $baseUnitData['unit_cost'];

            }

            $stock->current_quantity = $newQuantity;

        } else {
            /**
             * ========================================
             * خصم من المخزون
             * ========================================
             *
             * ملاحظة: متوسط التكلفة لا يتغير عند الخصم
             */
            if (! $this->canAllowNegativeStock($document) && $stock->available_quantity < $baseUnitData['quantity']) {
                $requested = round($baseUnitData['quantity'], 2);
                $available = round($stock->available_quantity, 2);
                throw new \Exception(
                    "المخزون غير كافٍ للصنف رقم {$baseUnitData['product_id']}. ".
                    "المتاح فعلياً: {$available}، المطلوب: {$requested}"
                );
            }

            $stock->current_quantity = $oldQuantity - $baseUnitData['quantity'];
        }

        $stock->last_movement_at = now();
        $stock->save();
    }

    /**
     * Update reserved quantity in stock record
     */
    protected function updateReservedQuantity(Model $document, array $baseUnitData, string $action): void
    {
        $config = ['store_id' => $document->store_id];
        $stock = $this->getConsolidatedStock($document, $baseUnitData, $config);

        if ($action === 'reserve') {
            // Check if available stock is enough (optional based on settings)
            if (! $this->canAllowNegativeStock($document) && $stock->available_quantity < $baseUnitData['quantity']) {
                throw new \Exception("المخزون المتاح غير كافٍ للحجز للصنف {$baseUnitData['product_id']}. المتاح: {$stock->available_quantity}");
            }
            $stock->increment('reserved_quantity', $baseUnitData['quantity']);
        } else {
            // Unreserve (release)
            $stock->decrement('reserved_quantity', min($stock->reserved_quantity, $baseUnitData['quantity']));
        }

        $stock->save();
    }

    /**
     * تعديل حركة مخزون موجودة
     */
    public function revertStockMovements(Model $document, $item, string $quantityField = 'quantity'): void
    {
        DB::beginTransaction();

        try {
            // تحويل البيانات الجديدة إلى الوحدة الأساسية
            $newBaseUnitData = $this->convertToBaseUnit($item, $quantityField);

            // البحث عن الحركة القديمة (بالوحدة الأساسية)
            $movement = StockMovement::where('reference_type', get_class($document))
                ->where('reference_id', $document->id)
                ->where('product_id', $item->product_id)
                ->where('is_size', $item->have_sizes ?? false)
                ->first();

            if (! $movement) {

                DB::commit();

                return;
            }

            $oldBaseQuantity = $movement->quantity;
            $oldBaseCost = $movement->unit_cost;

            // الحصول على سجل المخزون باستخدام الطريقة الموحدة التي تمنع تكرار السجلات
            $config = ['store_id' => $movement->store_id];
            $stock = $this->getConsolidatedStock($document, $newBaseUnitData, $config);

            if (! $stock) {
                throw new \Exception("لا يوجد سجل مخزون للمنتج {$item->product_id}");
            }

            $config = $this->getDocumentConfig($document, $item);

            // حساب الفرق بالوحدة الأساسية
            $quantityDifference = $newBaseUnitData['quantity'] - $oldBaseQuantity;

            if ($config['direction'] === 'in') {
                /**
                 * ========================================
                 * تعديل وارد: إعادة حساب متوسط التكلفة
                 * ========================================
                 */

                // إزالة تأثير الحركة القديمة من متوسط التكلفة
                $currentTotal = $stock->current_quantity * $stock->average_cost;
                $oldValue = $oldBaseQuantity * $oldBaseCost;
                $newValue = $newBaseUnitData['quantity'] * $newBaseUnitData['unit_cost'];

                // إعادة الحساب
                $adjustedTotal = $currentTotal - $oldValue + $newValue;
                $newTotalQuantity = $stock->current_quantity - $oldBaseQuantity + $newBaseUnitData['quantity'];

                if ($newTotalQuantity > 0) {
                    $stock->average_cost = round($adjustedTotal / $newTotalQuantity, 4);
                }

                $stock->current_quantity = $newTotalQuantity;
                $stock->last_cost = $newBaseUnitData['unit_cost'];

            } else {
                /**
                 * ========================================
                 * تعديل صادر: التحقق من الكمية المتاحة
                 * ========================================
                 */

                // إعادة الكمية القديمة أولاً
                $stock->current_quantity += $oldBaseQuantity;

                // التحقق من كفاية المخزون للكمية الجديدة (باستخدام الرصيد المتاح فعلياً)
                $availableQuantityBeforeRevert = $stock->available_quantity;
                $projectedAvailable = $availableQuantityBeforeRevert + $oldBaseQuantity;

                if (! $this->canAllowNegativeStock($document) && $projectedAvailable < $newBaseUnitData['quantity']) {
                    $requested = round($newBaseUnitData['quantity'], 2);
                    $available = round($projectedAvailable, 2);
                    throw new \Exception(
                        "المخزون غير كافٍ للصنف رقم {$newBaseUnitData['product_id']} لتعديل العملية. ".
                        "المتاح فعلياً: {$available}، المطلوب بعد التعديل: {$requested}"
                    );
                }

                // خصم الكمية الجديدة
                $stock->current_quantity -= $newBaseUnitData['quantity'];

                //   dd( $oldBaseQuantity  ,  $oldBaseCost  , $newBaseUnitData['quantity'] , $stock->current_quantity , $stock->current_quantity);

            }

            $stock->last_movement_at = now();
            $stock->save();

            // تحديث حركة المخزون
            $movement->update([
                'quantity' => $newBaseUnitData['quantity'],
                'unit_cost' => $newBaseUnitData['unit_cost'],
                'total_cost' => $newBaseUnitData['total_cost'],
                'notes' => $newBaseUnitData['notes'],
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('فشل في تعديل حركة المخزون', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * حذف حركة مخزون
     */
    public function deleteStockMovement(Model $document, $item): void
    {
        DB::beginTransaction();

        try {
            $movement = StockMovement::where('reference_type', get_class($document))
                ->where('reference_id', $document->id)
                ->where('product_id', $item->product_id)
                ->where('is_size', $item->have_sizes ?? false)
                ->first();

            if (! $movement) {
                Log::warning('لم يتم العثور على حركة المخزون للحذف');
                DB::commit();

                return;
            }

            $stock = Stock::where('store_id', $movement->store_id)
                ->where('product_id', $movement->product_id)
                ->where('unit_id', $movement->unit_id)
                ->where('is_size', $movement->is_size)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                if ($movement->stock_type === 'in') {
                    $stock->current_quantity -= $movement->quantity;
                } else {
                    $stock->current_quantity += $movement->quantity;
                }

                $stock->last_movement_at = now();
                $stock->save();
            }

            $movement->forceDelete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * حذف جميع حركات المخزون لمستند
     */
    public function revertAllStockMovements(Model $document): void
    {
        DB::beginTransaction();

        try {
            $movements = StockMovement::where('reference_type', get_class($document))
                ->where('reference_id', $document->id)
                ->get();

            foreach ($movements as $movement) {
                $stock = Stock::where('store_id', $movement->store_id)
                    ->where('product_id', $movement->product_id)
                    ->where('unit_id', $movement->unit_id)
                    ->where('is_size', $movement->is_size)
                    ->first();

                if ($stock) {
                    if ($movement->stock_type === 'in') {
                        $stock->decrement('current_quantity', $movement->quantity);
                    } else {
                        $stock->increment('current_quantity', $movement->quantity);
                    }

                    $stock->last_movement_at = now();
                    $stock->save();
                }
            }

            StockMovement::where('reference_type', get_class($document))
                ->where('reference_id', $document->id)
                ->forceDelete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
