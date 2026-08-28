<?php

namespace Modules\Store\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Http\Requests\CreateStStoreRequest;
use Modules\Store\App\Http\Requests\UpdateStStoreRequest;
use Modules\Store\App\Repositories\StSettingRepository;
use Maatwebsite\Excel\Facades\Excel;
// Assuming a generic export class exists or you will create one for the Store module.
use App\Exports\GenericExport; // Example, adjust if you have a specific export class
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\StockMovement;
use Modules\Store\App\Models\InventorySettings;

class StSettingController extends AppBaseController
{
    /** @var StSettingRepository $stSettingRepository*/
    private $stSettingRepository;

    public function __construct(StSettingRepository $stSettingRepo)
    {
        $this->stSettingRepository = $stSettingRepo;
    }

    /**
     * Show the form for editing the specified Store.
     */
    public function edit($id)
    {
        $setting = $this->stSettingRepository->find($id);
        $data['setting'] = $setting;
        $data['CostingMethods'] = $this->stSettingRepository->getCostingMethods();

        if (empty($setting)) {
            flash()->error(__('store::models/st_setting.singular') . ' ' . __('messages.not_found'));
            return redirect()->back();
        }
        return view('store::settings.edit', $data);
    }

    public function inventorysettings()
    {
        $setting = InventorySettings::first();
        return response()->json([
            'allow_negative_stock' => $setting->allow_negative_stock ?? false,
            'costing_method' => $setting->costing_method ?? 'weighted_average',
            'auto_calculate_cost' => $setting->auto_calculate_cost ?? true,
        ]);
    }
































     //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################





























     //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################










































     //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################





























     //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################

































     //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################











    public function getProduct(Request $request)
    {
        $locale = app()->getLocale();
        $searchTerm = $request->input('q', '');
        $storeId = $request->input('store');
        $typepage = $request->input('typepage', 'openingbalance');

        // جلب إعدادات المخزون
        $setting = InventorySettings::first();

        // إنشاء إعدادات افتراضية إذا لم تكن موجودة لمنع توقف AJAX
        if (!$setting) {
            $setting = new \stdClass();
            $setting->allow_negative_stock = false;
            $setting->costing_method = 'standard';
            $setting->auto_calculate_cost = true;
        }

        // جلب المنتجات الفعالة
        $query = Product::with(['units.unit', 'sizes'])->where('status', Product::STATUS_ACTIVE)->where('type', Product::TYPE_SALE);

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('barcode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('sizes', function($qSize) use ($searchTerm) {
                      $qSize->where('name', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('barcode', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $products = $query->take(30)->get();

        // تجميع كل المعرفات لجلب المخزون دفعة واحدة
        $productIds = $products->pluck('id')->toArray();
        $sizeIds = [];
        foreach ($products as $p) {
            if ($p->have_sizes) {
                $sizeIds = array_merge($sizeIds, $p->sizes->pluck('id')->toArray());
            }
        }

        // جلب المخزون دفعة واحدة
        $stocks = collect();
        if ($storeId) {
            $pStocks = Stock::where('store_id', $storeId)->whereIn('product_id', $productIds)->where('is_size', false)->get()->keyBy('product_id');
            $sStocks = Stock::where('store_id', $storeId)->whereIn('product_id', $sizeIds)->where('is_size', true)->get()->keyBy('product_id');
        } else {
            $pStocks = collect();
            $sStocks = collect();
        }

        $data = [];
        foreach ($products as $item) {
            if ($item->have_sizes) {
                foreach ($item->sizes as $size) {
                    $stock = $sStocks->get($size->id);
                    $productData = $this->prepareProductData($size, $item, $storeId, true, $setting, $locale, $stock);
                    if ($productData) $data[] = $productData;
                }
            } else {
                $stock = $pStocks->get($item->id);
                $productData = $this->prepareProductData($item, null, $storeId, false, $setting, $locale, $stock);
                if ($productData) $data[] = $productData;
            }
        }

        return response()->json($data);
    }

    /**
     * تحضير بيانات المنتج مع معالجة التكلفة والكمية حسب الإعدادات
     */
    private function prepareProductData($product, $parentProduct, $storeId, $isSize, $setting, $locale, $stock = null)
    {
        $baseQuantity = $stock ? $stock->current_quantity : 0;

        if (!$setting->allow_negative_stock && $baseQuantity < 0) {
            $baseQuantity = 0;
        }

        $baseCostPrice = $this->calculateBaseCost($product, $stock, $setting);
        $baseSalePrice = $isSize ? ($product->sale_price ?? 0) : ($product->prod_price ?? 0);

        $units = $this->prepareUnitsWithCostAndQuantity($isSize ? $parentProduct->units : $product->units, $baseCostPrice, $baseSalePrice, $baseQuantity, $locale, $setting);

        return [
            'id' => $product->id,
            'name' => $isSize ? $parentProduct->name . ' - ' . $product->name : $product->name,
            'type' => $isSize ? 1 : 0,
            'barcode' => $product->barcode ?? '',
            'cost_price' => round($baseCostPrice, 2),
            'sale_price' => round($baseSalePrice, 2),
            'product_id' => $isSize ? $parentProduct->id : null,
            'units' => $units,
            'quantity' => round($baseQuantity, 2),
            'allow_negative_stock' => $setting->allow_negative_stock,
            'costing_method' => $setting->costing_method,
            'auto_calculate_cost' => $setting->auto_calculate_cost,
        ];
    }

    /**
     * حساب التكلفة الأساسية حسب طريقة التكلفة المحددة في الإعدادات
     */
    private function calculateBaseCost($product, $stock, $setting)
    {
        switch ($setting->costing_method) {
            case InventorySettings::COSTING_METHOD_FIFO:
                return $this->calculateFIFOCost($product, $stock);

            case InventorySettings::COSTING_METHOD_LIFO:
                return $this->calculateLIFOCost($product, $stock);

            case InventorySettings::COSTING_METHOD_WEIGHTED_AVERAGE:
                return $this->calculateWeightedAverageCost($product, $stock);

            case InventorySettings::COSTING_METHOD_STANDARD:
                return $product->cost_price ?? 0;

            default:
                return $product->cost_price ?? 0;
        }
    }

    /**
     * حساب التكلفة بطريقة FIFO (First In, First Out)
     */
    private function calculateFIFOCost($product, $stock)
    {
        if (!$stock) {
            return $product->cost_price ?? 0;
        }

        // جلب أقدم دفعة متاحة في المخزون
        $oldestBatch = StockMovement::where('product_id', $product->id)->where('remaining_quantity', '>', 0)->orderBy('created_at', 'asc')->first();

        return $oldestBatch ? $oldestBatch->unit_cost : $product->cost_price ?? 0;
    }

    /**
     * حساب التكلفة بطريقة LIFO (Last In, First Out)
     */
    private function calculateLIFOCost($product, $stock)
    {
        if (!$stock) {
            return $product->cost_price ?? 0;
        }

        // جلب أحدث دفعة متاحة في المخزون
        $latestBatch = StockMovement::where('product_id', $product->id)->where('remaining_quantity', '>', 0)->orderBy('created_at', 'desc')->first();

        return $latestBatch ? $latestBatch->unit_cost : $product->cost_price ?? 0;
    }

    /**
     * حساب التكلفة بطريقة المتوسط المرجح
     */
    private function calculateWeightedAverageCost($product, $stock)
    {
        if (!$stock) {
            return $product->cost_price ?? 0;
        }

        // استخدام متوسط التكلفة المخزن أو حسابه
        if ($stock->average_cost && $stock->average_cost > 0) {
            return $stock->average_cost;
        }

        // حساب المتوسط المرجح من الدفعات المتاحة
        $batches = StockMovement::where('product_id', $product->id)->where('remaining_quantity', '>', 0)->get();

        if ($batches->isEmpty()) {
            return $product->cost_price ?? 0;
        }

        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($batches as $batch) {
            $totalCost += $batch->unit_cost * $batch->remaining_quantity;
            $totalQuantity += $batch->remaining_quantity;
        }

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : $product->cost_price ?? 0;
    }

    /**
     * تحضير الوحدات مع حساب التكلفة والكمية
     */
    private function prepareUnitsWithCostAndQuantity($productUnits, $baseCostPrice, $baseSalePrice, $baseQuantity, $locale, $setting)
    {
        $units = [];

        foreach ($productUnits as $unit) {
            $conversionFactor = $unit->conversion_factor ?? 1;

            // حساب التكلفة للوحدة
            if ($setting->auto_calculate_cost) {
                // حساب تلقائي للتكلفة
                $unitCostPrice = $baseCostPrice * $conversionFactor;
                $finalCostPrice = $unit->Average_Cost ?? $unitCostPrice;

                // سعر البيع للوحدة (إذا كان مخزناً نستخدمه، وإلا نحسبه)
                $unitSalePrice = ($unit->prod_price && $unit->prod_price > 0) ? $unit->prod_price : ($baseSalePrice * $conversionFactor);
            } else {
                // استخدام التكلفة المخزنة فقط
                $finalCostPrice = $unit->Average_Cost ?? ($unit->cost_price ?? $baseCostPrice * $conversionFactor);
                $unitSalePrice = ($unit->prod_price && $unit->prod_price > 0) ? $unit->prod_price : ($baseSalePrice * $conversionFactor);
            }

            // حساب الكمية المتاحة بهذه الوحدة
            $quantityInUnit = $conversionFactor > 0 ? $baseQuantity / $conversionFactor : 0;

            // معالجة الكمية السالبة
            if (!$setting->allow_negative_stock && $quantityInUnit < 0) {
                $quantityInUnit = 0;
            }

            $units[] = [
                'id' => $unit->id,
                'name' => $unit->unit?->translate($locale)->name ?? '',
                'barcode' => $unit->barcode ?? '',
                'conversion_factor' => $conversionFactor,
                'is_base' => $unit->is_base,
                'cost_price' => round($finalCostPrice, 2),
                'sale_price' => round($unitSalePrice, 2),
                'quantity' => round($quantityInUnit, 2),
                'base_quantity' => round($baseQuantity, 2),
            ];
        }

        return $units;
    }

    /**
     * ملاحظة: تحتاج إلى إنشاء جدول stock_movements لتخزين حركات المخزون
     * وتتبع الدفعات للطرق FIFO و LIFO
     *
     * Migration Example:
     *
     * Schema::create('stock_movements', function (Blueprint $table) {
     *     $table->id();
     *     $table->foreignId('product_id')->constrained()->onDelete('cascade');
     *     $table->foreignId('store_id')->constrained()->onDelete('cascade');
     *     $table->decimal('quantity', 15, 4);
     *     $table->decimal('remaining_quantity', 15, 4);
     *     $table->decimal('unit_cost', 15, 4);
     *     $table->string('movement_type'); // purchase, sale, transfer, etc.
     *     $table->timestamps();
     * });
     */

    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################
    //#############################################################################################

    //     public function getProduct(Request $request)
    // {
    //     $locale = app()->getLocale();
    //     $searchTerm = $request->input('q', ''); // للبحث عبر Select2
    //     $storeId = $request->input('store'); // معرف المخزن

    //     $setting =InventorySettings::first();
    //       return response()->json( $setting);

    //     // جلب المنتجات الفعالة فقط مع الوحدات
    //     $query = Product::with('units.unit', 'sizes')->where('status', Product::STATUS_ACTIVE)->where('type', Product::TYPE_SALE);
    //     $products = $query->get();

    //     // مصفوفة النتائج
    //     $data = [];

    //     // تعبئة البيانات من قاعدة البيانات
    //     foreach ($products as $item) {
    //         if ($item->have_sizes) {
    //             // المنتجات التي لها أحجام
    //             foreach ($item->sizes as $size) {
    //                 // جلب الكمية من جدول Stock للمنتج الذي له حجم
    //                 $stock = Stock::where('store_id', $storeId)
    //                     ->where('product_id', $size->id)
    //                     ->where('is_size', true)
    //                     ->first();

    //                 $baseQuantity = $stock ? $stock->current_quantity : 0;

    //                 // التكلفة الأساسية للحجم
    //                 $baseCostPrice = $size->cost_price ?? 0;

    //                 // تحضير الوحدات مع حساب التكلفة والكمية لكل وحدة
    //                 $units = $this->prepareUnitsWithCostAndQuantity($item->units, $baseCostPrice, $baseQuantity, $locale);

    //                 $data[] = [
    //                     'id' => $size->id,
    //                     'name' => $item->name . ' - ' . $size->name,
    //                     'type' => 1,
    //                     'barcode' => $size->barcode ?? '',
    //                     'cost_price' => $baseCostPrice,
    //                     'product_id' => $item->id,
    //                     'units' => $units,
    //                     'quantity' => $baseQuantity,
    //                     'allow_negative_stock' =>    $setting->allow_negative_stock,
    //                      'costing_method' =>    $setting->costing_method,
    //                     // الكمية بالوحدة الأساسية
    //                 ];
    //             }
    //         } else {
    //             // المنتجات العادية (بدون أحجام)
    //             $stock = Stock::where('store_id', $storeId)
    //                 ->where('product_id', $item->id)
    //                 ->where('is_size', false)
    //                 ->first();

    //             $baseQuantity = $stock ? $stock->current_quantity : 0;

    //             // التكلفة الأساسية للمنتج
    //             $baseCostPrice = $item->cost_price ?? 0;

    //             // تحضير الوحدات مع حساب التكلفة والكمية لكل وحدة
    //             $units = $this->prepareUnitsWithCostAndQuantity($item->units, $baseCostPrice, $baseQuantity, $locale);

    //             $data[] = [
    //                 'id' => $item->id,
    //                 'name' => $item->name,
    //                 'type' => 0,
    //                 'barcode' => $item->barcode ?? '',
    //                 'cost_price' => $baseCostPrice,
    //                 'units' => $units,
    //                 'quantity' => $baseQuantity,
    //                  'allow_negative_stock' =>    $setting->allow_negative_stock,
    //                 'costing_method' =>    $setting->costing_method,// الكمية بالوحدة الأساسية
    //             ];
    //         }
    //     }

    //     return response()->json($data);
    // }

    // /**
    //  * تحضير الوحدات مع حساب التكلفة والكمية لكل وحدة بناءً على معامل التحويل
    //  *
    //  * @param \Illuminate\Database\Eloquent\Collection $productUnits
    //  * @param float $baseCostPrice التكلفة الأساسية للمنتج
    //  * @param float $baseQuantity الكمية بالوحدة الأساسية
    //  * @param string $locale
    //  * @return array
    //  */
    // private function prepareUnitsWithCostAndQuantity($productUnits, $baseCostPrice, $baseQuantity, $locale)
    // {
    //     $units = [];

    //     // البحث عن الوحدة الأساسية أولاً
    //     $baseUnit = $productUnits->firstWhere('is_base', true);
    //     $baseConversionFactor = $baseUnit ? $baseUnit->conversion_factor : 1;

    //     foreach ($productUnits as $unit) {
    //         /**
    //          * معامل التحويل (Conversion Factor):
    //          * يحدد كم وحدة أساسية في هذه الوحدة
    //          *
    //          * مثال:
    //          * - الوحدة الأساسية: قطعة (conversion_factor = 1)
    //          * - كرتون = 12 قطعة (conversion_factor = 12)
    //          * - علبة = 6 قطع (conversion_factor = 6)
    //          */
    //         $conversionFactor = $unit->conversion_factor ?? 1;

    //         // ===== حساب التكلفة لهذه الوحدة =====
    //         // تكلفة الوحدة = التكلفة الأساسية × معامل التحويل
    //         $unitCostPrice = $baseCostPrice * $conversionFactor;

    //         // استخدام Average_Cost إذا كان موجوداً
    //         $finalCostPrice = $unit->Average_Cost ?? $unitCostPrice;

    //         // ===== حساب الكمية المتاحة بهذه الوحدة =====
    //         /**
    //          * الكمية بالوحدة = الكمية الأساسية ÷ معامل التحويل
    //          *
    //          * مثال:
    //          * لديك 100 قطعة في المخزن
    //          *
    //          * 1. بالقطعة (conversion_factor = 1):
    //          *    الكمية = 100 ÷ 1 = 100 قطعة
    //          *
    //          * 2. بالكرتون (conversion_factor = 12):
    //          *    الكمية = 100 ÷ 12 = 8.33 كرتون
    //          *    (أي 8 كراتين كاملة + 4 قطع)
    //          *
    //          * 3. بالعلبة (conversion_factor = 6):
    //          *    الكمية = 100 ÷ 6 = 16.67 علبة
    //          *    (أي 16 علبة كاملة + 4 قطع)
    //          */
    //         $quantityInUnit = $conversionFactor > 0 ? ($baseQuantity / $conversionFactor) : 0;

    //         $units[] = [
    //             'id' => $unit->id,
    //             'name' => $unit->unit?->translate($locale)->name ?? '',
    //             'barcode' => $unit->barcode ?? '',
    //             'conversion_factor' => $conversionFactor,
    //             'is_base' => $unit->is_base,
    //             'cost_price' => round($finalCostPrice, 4), // التكلفة للوحدة الواحدة
    //             'calculated_cost' => round($unitCostPrice, 4), // التكلفة المحسوبة
    //             'quantity' => round($quantityInUnit, 4), // الكمية المتاحة بهذه الوحدة
    //             'base_quantity' => $baseQuantity, // الكمية الأساسية للمرجع
    //         ];
    //     }

    //     return $units;
    // }

    // ```
    // المنتج: بسكويت
    // الكمية في المخزن: 144 قطعة
    // التكلفة الأساسية: 2 ريال/قطعة

    // الوحدات:
    // ┌─────────────┬──────────────┬─────────────┬──────────────┬─────────────────┐
    // │ الوحدة      │ معامل التحويل│ الكمية      │ التكلفة      │ الإجمالي        │
    // ├─────────────┼──────────────┼─────────────┼──────────────┼─────────────────┤
    // │ قطعة (أساسي)│      1       │ 144 قطعة    │ 2 ريال       │ 288 ريال        │
    // │ علبة        │      6       │ 24 علبة     │ 12 ريال      │ 288 ريال        │
    // │ كرتون       │     12       │ 12 كرتون    │ 24 ريال      │ 288 ريال        │
    // └─────────────┴──────────────┴─────────────┴──────────────┴─────────────────┘

    // الحسابات:
    // - قطعة: الكمية = 144 ÷ 1 = 144 قطعة
    // - علبة: الكمية = 144 ÷ 6 = 24 علبة
    // - كرتون: الكمية = 144 ÷ 12 = 12 كرتون
    // ```

    // ### مثال 2: سوائل
    // ```
    // المنتج: عصير
    // الكمية في المخزن: 50 لتر
    // التكلفة الأساسية: 10 ريال/لتر

    // الوحدات:
    // ┌─────────────┬──────────────┬─────────────┬──────────────┬─────────────────┐
    // │ الوحدة      │ معامل التحويل│ الكمية      │ التكلفة      │ الإجمالي        │
    // ├─────────────┼──────────────┼─────────────┼──────────────┼─────────────────┤
    // │ لتر (أساسي) │      1       │ 50 لتر      │ 10 ريال      │ 500 ريال        │
    // │ زجاجة نصف لتر│    0.5       │ 100 زجاجة   │ 5 ريال       │ 500 ريال        │
    // │ جالون       │    3.785     │ 13.21 جالون │ 37.85 ريال   │ 500 ريال        │
    // └─────────────┴──────────────┴─────────────┴──────────────┴─────────────────┘

    // الحسابات:
    // - لتر: الكمية = 50 ÷ 1 = 50 لتر
    // - زجاجة: الكمية = 50 ÷ 0.5 = 100 زجاجة
    // - جالون: الكمية = 50 ÷ 3.785 = 13.21 جالون
    // ```

    // ### مثال 3: كمية غير كاملة
    // ```
    // المنتج: أقلام
    // الكمية في المخزن: 50 قلم
    // التكلفة الأساسية: 1 ريال/قلم

    // الوحدات:
    // ┌─────────────┬──────────────┬─────────────┬──────────────┐
    // │ الوحدة      │ معامل التحويل│ الكمية      │ التكلفة      │
    // ├─────────────┼──────────────┼─────────────┼──────────────┤
    // │ قلم (أساسي) │      1       │ 50 قلم      │ 1 ريال       │
    // │ علبة        │     12       │ 4.17 علبة   │ 12 ريال      │
    // └─────────────┴──────────────┴─────────────┴──────────────┘

    // الحسابات:
    // - قلم: 50 ÷ 1 = 50 قلم
    // - علبة: 50 ÷ 12 = 4.17 علبة (4 علب كاملة + 2 قلم)

    // public function getProduct(Request $request)
    // {
    //     $locale = app()->getLocale();
    //     $searchTerm = $request->input('q', ''); // للبحث عبر Select2
    //     $storeId = $request->input('store'); // معرف المخزن

    //     // جلب المنتجات الفعالة فقط مع الوحدات
    //     $query = Product::with('units', 'sizes')->where('status', Product::STATUS_ACTIVE)->where('type', Product::TYPE_SALE);
    //     $products = $query->get();

    //     // مصفوفة النتائج
    //     $data = [];

    //     // تعبئة البيانات من قاعدة البيانات
    //     foreach ($products as $item) {
    //         // تحضير الوحدات (فقط id و name و barcode)

    //         if ($item->have_sizes) {
    //             // المنتجات التي لها أحجام
    //             foreach ($item->sizes as $size) {
    //                 $units = [];
    //                 foreach ($item->units as $unit) {
    //                     $units[] = [
    //                         'id' => $unit->id,
    //                         'name' => $unit->unit?->translate($locale)->name ?? '',
    //                         'barcode' => $unit->barcode ?? '',
    //                     ];
    //                 }
    //                 // جلب الكمية من جدول Stock للمنتج الذي له حجم
    //                 $stock = Stock::where('store_id', $storeId)->where('product_id', $size->id)->where('is_size', true)->first();

    //                 $quantity = $stock ? $stock->current_quantity : 0;

    //                 $data[] = [
    //                     'id' => $size->id,
    //                     'name' => $item->name . ' - ' . $size->name,
    //                     'type' => 1,
    //                     'barcode' => $size->barcode ?? '',
    //                     'cost_price' => $size->cost_price ?? 0,
    //                     'product_id' => $item->id,
    //                     'units' => $units,
    //                     'quantity' => $quantity,
    //                 ];
    //             }
    //         } else {
    //             $units = [];
    //             foreach ($item->units as $unit) {
    //                 $units[] = [
    //                     'id' => $unit->id,
    //                     'name' => $unit->unit?->translate($locale)->name ?? '',
    //                     'barcode' => $unit->barcode ?? '',
    //                 ];
    //             }
    //             // المنتجات العادية (بدون أحجام)
    //             $stock = Stock::where('store_id', $storeId)->where('product_id', $item->id)->where('is_size', false)->first();

    //             $quantity = $stock ? $stock->current_quantity : 0;

    //             $data[] = [
    //                 'id' => $item->id,
    //                 'name' => $item->name,
    //                 'type' => 0,
    //                 'barcode' => $item->barcode ?? '',
    //                 'cost_price' => $item->cost_price ?? 0,
    //                 'units' => $units,
    //                 'quantity' => $quantity,
    //             ];
    //         }
    //     }

    //     return response()->json($data);
    // }

    // public function getProduct(Request $request)
    // {
    //     $locale = app()->getLocale();
    //     $searchTerm = $request->input('q', ''); // للبحث عبر Select2

    //     // جلب المنتجات الفعالة فقط مع الوحدات
    //     $query = Product::with('units', 'sizes')->where('status', Product::STATUS_ACTIVE)->where('type', Product::TYPE_SALE);
    //     $products = $query->get();

    //     // مصفوفة النتائج

    //     $count = 0;
    //     $data = [];

    //     // تعبئة البيانات من قاعدة البيانات
    //     foreach ($products as $item) {
    //         // تحضير الوحدات (فقط id و name و barcode)
    //         $units = [];
    //         foreach ($item->units as $unit) {
    //             $units[] = [
    //                 'id' => $unit->id,
    //                 'name' => $unit->unit?->translate($locale)->name ?? '',
    //                 'barcode' => $unit->barcode ?? '',
    //             ];
    //         }

    //         if ($item->have_sizes) {
    //              $count = Stock::where('store_id' ,$request->store)->where('product_id' ,$size->id) ->where('is_size' , $item->have_sizes)->fist()->current_quantity;

    //             // المنتجات التي لها أحجام
    //             foreach ($item->sizes as $size) {
    //                 $data[] = [
    //                     'id' => $size->id,
    //                     'name' => $item->name . ' - ' . $size->name,
    //                     'type' => 1,
    //                     'barcode' => $size->barcode ?? '',
    //                     'cost_price' => $size->cost_price ?? 0,
    //                     'product_id' => $item->id,
    //                     'units' => $units,
    //                     'quantity' => $count,
    //                     // $request->store,
    //                     // $request->typepage,
    //                 ];
    //             }
    //         } else {
    //             //$count = Stock::where('store_id' ,$request->store)->where('product_id' ,$item->id) ->where('is_size' , $item->have_sizes)->()->current_quantity;

    //             // المنتجات العادية
    //             $data[] = [
    //                 'id' => $item->id,
    //                 'name' => $item->name,
    //                 'type' => 0,
    //                 'barcode' => $item->barcode ?? '',
    //                 'cost_price' => $item->cost_price ?? 0,
    //                 'units' => $units,
    //                 'quantity' => $count,
    //                 // $request->store,
    //                 // $request->typepage,
    //             ];
    //         }
    //     }

    //     return response()->json($data);
    // }

    /**
     * Update the specified Store in storage.
     *
     * @param int $id
     * @param UpdateStStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $store = $this->stSettingRepository->find($id);

            if (empty($store)) {
                flash()->error(__('store::models/st_setting.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.settings.index'));
            }

            $input = $request->all();
            $store = $this->stSettingRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('store::models/st_setting.singular')]));

            return redirect()->back();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('store::models/st_setting.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
