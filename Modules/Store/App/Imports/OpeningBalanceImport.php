<?php

namespace Modules\Store\App\Imports;

use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\BasicDataApp\Unit;
use App\Models\StoreApp\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Store\App\Models\StOpeningBalance;
use Modules\Store\App\Models\StOpeningBalanceItem;
use Modules\Store\App\Repositories\StOpeningBalanceRepository;

class OpeningBalanceImport implements ToCollection
{
    private $orgId;
    private $userId;
    private $categories = [];
    private $units = [];
    private $stores = [];
    private $productsByName = [];
    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];

    public function __construct()
    {
        $this->orgId = auth()->user()->org_id ?? null;
        $this->userId = auth()->id();
        $this->loadLookups();
    }

    private function loadLookups()
    {
        $this->categories = Category::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();

        $this->units = Unit::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();

        $this->stores = Store::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->name);
        })->toArray();

        $this->productsByName = Product::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();
    }

    public function collection(Collection $rows)
    {
        // تخطي أول صفين (العناوين)
        $dataRows = $rows->slice(2);

        // تجميع الصفوف حسب المستودع لإنشاء مستند واحد لكل مستودع
        $groupedByStore = [];
        foreach ($dataRows as $index => $row) {
            if (empty($row[1]) || empty($row[6])) continue; // اسم المنتج واسم المستودع مطلوبين

            $storeName = trim($row[6]);
            if (!isset($groupedByStore[$storeName])) {
                $groupedByStore[$storeName] = [];
            }
            // تخزين الصف الأصلي كـ array لسهولة استخدامه في التصدير لاحقاً
            $groupedByStore[$storeName][] = ['row' => $row->toArray(), 'index' => $index + 3];
        }

        foreach ($groupedByStore as $storeName => $items) {
            $storeId = $this->getStoreId($storeName);
            if (!$storeId) {
                foreach ($items as $itemData) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $itemData['row'],
                        'error' => "المستودع '{$storeName}' غير موجود في النظام.",
                    ];
                }
                continue;
            }

            DB::beginTransaction();
            try {
                // إنشاء رأس مستند الرصيد الافتتاحي
                $openingBalance = StOpeningBalance::create([
                    'org_id' => $this->orgId,
                    'user_id' => $this->userId,
                    'branch_id' => Store::find($storeId)->branch_id,
                    'document_number' => StOpeningBalance::generateDocumentNumber(),
                    'document_date' => now(),
                    'store_id' => $storeId,
                    'status' => StOpeningBalance::STATUS_DRAFT,
                    'type' => StOpeningBalance::TYPE_OPENING_PLANCE,
                    'total_items' => 0,
                    'total_quantity' => 0,
                    'total_value' => 0,
                ]);

                $totalItems = 0;
                $totalQty = 0;
                $totalVal = 0;

                foreach ($items as $itemData) {
                    $row = $itemData['row'];
                    try {
                        $productInfo = $this->getOrCreateProduct($row);

                        $quantity = $this->sanitizeNumeric($row[4]);
                        $unitCost = $this->sanitizeNumeric($row[5]);
                        $totalCost = $quantity * $unitCost;

                        // الحصول على وحدات المنتج وتنسيقها كما يتوقعها النظام (مطلوب في قاعدة البيانات)
                        $productUnits = ProductUnit::where('product_id', $productInfo['real_product_id'])->with('unit')->get();

                        // حساب تكلفة الوحدة الأساسية أولاً لتوزيعها على باقي الوحدات بشكل صحيح
                        $selectedPUnit = $productUnits->where('id', $productInfo['unit_id'])->first();
                        $rowConversionFactor = ($selectedPUnit && $selectedPUnit->conversion_factor > 0) ? $selectedPUnit->conversion_factor : 1;
                        $baseUnitCost = $unitCost / $rowConversionFactor;

                        $formattedUnits = $productUnits->map(function ($pUnit) use ($baseUnitCost) {
                            return [
                                'id' => $pUnit->id,
                                'name' => $pUnit->unit->name ?? '---',
                                'conversion_factor' => $pUnit->conversion_factor ?: 1,
                                'cost_price' => round($baseUnitCost * ($pUnit->conversion_factor ?: 1), 2),
                                'available_quantity' => 0,
                                'barcode' => $pUnit->barcode ?? null,
                            ];
                        });

                        StOpeningBalanceItem::create([
                            'opening_balance_id' => $openingBalance->id,
                            'product_id' => $productInfo['product_id'],
                            'unit_id' => $productInfo['unit_id'],
                            'have_sizes' => $productInfo['have_sizes'],
                            'quantity' => $quantity,
                            'unit_cost' => $unitCost,
                            'total_cost' => $totalCost,
                            'status' => StOpeningBalance::STATUS_DRAFT,
                            'unit' => json_encode($formattedUnits),
                        ]);

                        $totalItems++;
                        $totalQty += $quantity;
                        $totalVal += $totalCost;
                        $this->successCount++;
                    } catch (\Exception $e) {
                        $this->errorCount++;
                        $this->errors[] = [
                            'row' => $row,
                            'error' => "الصف {$itemData['index']}: " . $e->getMessage(),
                        ];
                    }
                }

                $openingBalance->update([
                    'total_items' => $totalItems,
                    'total_quantity' => $totalQty,
                    'total_value' => $totalVal,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                foreach ($items as $itemData) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $itemData['row'],
                        'error' => "خطأ في معالجة المستودع: " . $e->getMessage(),
                    ];
                }
            }
        }
    }

    private function getOrCreateProduct($row)
    {
        $barcode = !empty($row[0]) ? trim($row[0]) : null;
        $fullName = trim($row[1]);
        $categoryName = trim($row[2]);
        $unitName = trim($row[3]);
        $typeStr = trim($row[7] ?? 'Product');
        $haveSizes = (strtolower($typeStr) === 'size');

        $category = $this->findOrCreateCategory($categoryName);
        $unit = $this->findOrCreateUnit($unitName);

        $productName = $fullName;
        $sizeName = null;

        if ($haveSizes && str_contains($fullName, ' - ')) {
            $parts = explode(' - ', $fullName, 2);
            $productName = trim($parts[0]);
            $sizeName = trim($parts[1]);
        }

        $names = $this->parseProductName($productName);

        // البحث عن المنتج (الأب)
        $product = null;
        if (!$haveSizes && $barcode) {
            $product = Product::where('barcode', $barcode)->where('org_id', $this->orgId)->first();
        }

        if (!$product && isset($this->productsByName[$productName])) {
            $product = Product::find($this->productsByName[$productName]['id']);
        }

        if (!$product) {
            // إنشاء منتج جديد
            $product = Product::create(array_merge([
                'org_id' => $this->orgId,
                'user_id' => $this->userId,
                'barcode' => ($haveSizes ? null : $barcode) ?: Product::generateUniqueBarcode(),
                'category_id' => $category->id,
                'base_unit_id' => $unit->id,
                'type' => $haveSizes ? 3 : 1, // 3: المنتج ذو المقاسات
                'have_sizes' => $haveSizes,
                'status' => 1,
                'cost_price' => $this->sanitizeNumeric($row[5]),
                'prod_price' => $this->sanitizeNumeric($row[5]) * 1.2,
            ], $this->formatTranslations($names)));

            // إضافة الوحدة الأساسية
            ProductUnit::updateOrCreate(
                ['product_id' => $product->id, 'unit_id' => $unit->id],
                ['conversion_factor' => 1, 'is_base' => true]
            );

            $this->productsByName[$productName] = $product->toArray();
        }

        $finalProductId = $product->id;
        $finalUnitId = null;

        // الحصول على سجل الوحدة في جدول product_units
        $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $unit->id)->first();
        if (!$productUnit) {
            $productUnit = ProductUnit::create([
                'product_id' => $product->id,
                'unit_id' => $unit->id,
                'conversion_factor' => 1,
                'is_base' => false,
            ]);
        }
        $finalUnitId = $productUnit->id;

        // إذا كان مقاساً، يجب التعامل مع الربط بـ ProductSize
        if ($haveSizes && $sizeName) {
            $size = ProductSize::updateOrCreate(
                ['product_id' => $product->id, 'barcode' => $barcode ?: Product::generateUniqueBarcode()],
                array_merge([
                    'status' => 1,
                    'consumption_factor' => 1,
                    'cost_price' => $this->sanitizeNumeric($row[5]),
                    'sale_price' => $this->sanitizeNumeric($row[5]) * 1.2,
                ], $this->formatTranslations(['ar' => $sizeName, 'en' => $sizeName]))
            );
            $finalProductId = $size->id;
        }

        return [
            'product_id' => $finalProductId,
            'real_product_id' => $product->id,
            'unit_id' => $finalUnitId,
            'have_sizes' => $haveSizes ? 1 : 0,
        ];
    }

    private function findOrCreateCategory($name)
    {
        $name = trim($name);
        if (isset($this->categories[$name])) return (object)$this->categories[$name];

        $category = Category::create(array_merge([
            'org_id' => $this->orgId,
            'user_id' => $this->userId,
            'status' => 1,
        ], $this->formatTranslations(['ar' => $name, 'en' => $name])));

        $this->categories[$name] = $category->toArray();
        return $category;
    }

    private function findOrCreateUnit($name)
    {
        $name = trim($name);
        if (isset($this->units[$name])) return (object)$this->units[$name];

        $unit = Unit::create(array_merge([
            'org_id' => $this->orgId,
            'user_id' => $this->userId,
            'status' => 1,
            'conversion_factor' => 1,
        ], $this->formatTranslations(['ar' => $name, 'en' => $name])));

        $this->units[$name] = $unit->toArray();
        return $unit;
    }

    private function getStoreId($name)
    {
        if (isset($this->stores[$name])) return $this->stores[$name]['id'];
        return null;
    }

    private function parseProductName($name): array
    {
        $name = (string)$name;
        if (str_contains($name, ' - ')) {
            $parts = explode(' - ', $name, 2);
            return ['ar' => trim($parts[0]), 'en' => trim($parts[1])];
        }
        return ['ar' => $name, 'en' => $name];
    }

    private function formatTranslations($names): array
    {
        $formatted = [];
        foreach ($names as $locale => $name) {
            $formatted[$locale] = ['name' => $name];
        }
        return $formatted;
    }

    private function sanitizeNumeric($value): float
    {
        if (empty($value)) return 0.0;
        if (is_numeric($value)) return (float) $value;
        return (float) preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
    }

    public function getSummary()
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
        ];
    }
}
