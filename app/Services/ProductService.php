<?php

namespace App\Services;

use App\Models\BasicDataApp\Product;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AccuSoft\App\Models\AccountingSettings;
use Modules\Store\App\Models\InventorySettings;

class ProductService
{
    const TYPE_PRODUCT = 1; // منتج

    const TYPE_SERVICE = 2; // خدمة

    /**
     * 1 - دالة تقوم بجلب الخدمات فقط
     */
    public function searchServices(Request $request)
    {
        return $this->performSearch($request, [self::TYPE_SERVICE]);
    }

    /**
     * 2 - دالة تقوم بجلب المنتجات والخدمات معاً
     * في حال وجود مقاسات للمنتج، يتم جلب المقاسات
     */
    public function searchProductsAndServices(Request $request)
    {
        return $this->performSearch($request, [self::TYPE_PRODUCT, self::TYPE_SERVICE]);
    }

    /**
     * 3 - دالة تقوم بجلب المنتجات فقط بدون الخدمات
     * مع تطبيق منطق المقاسات
     */
    public function searchProductsOnly(Request $request)
    {
        return $this->performSearch($request, [self::TYPE_PRODUCT]);
    }

    /**
     * 4 - دالة تقوم بجلب المنتجات بناءً على الفرع أو المستودع
     * تضمن أن المنتجات المعروضة مرتبطة بالمكان المختار فقط
     */
    public function searchByBranchOrStore(Request $request)
    {
        $types = [self::TYPE_PRODUCT];
        
        // Include services only for sales operations
        if ($request->input('is_sale')) {
            $types[] = self::TYPE_SERVICE;
        }

        return $this->performSearch($request, $types, true);
    }

    /**
     * منطق البحث المشترك لـ Select2 AJAX
     *
     * @param  array  $types  مصفوفة أنواع البيانات المطلوب جلبها
     * @param  bool  $enforceLocation  هل يجب الالتزام بفلتر الفرع/المستودع في الاستعلام؟
     * @return array
     */
    private function performSearch(Request $request, array $types, bool $enforceLocation = false)
    {
        $searchTerm = $request->input('q', '');
        $storeId = $request->input('store');
        $purchaseInvoiceId = $request->input('purchase_invoice_id');
        $lang = $request->input('lang') ?? app()->getLocale();

        // Get inventory settings
        $setting =
            InventorySettings::first() ?:
            (object) [
                'allow_negative_stock' => false,
                'costing_method' => 'standard',
                'auto_calculate_cost' => true,
            ];

        // Query active products of type sale
        $query = Product::with([
            'translations',
            'units.unit.translations',
            'sizes.translations',
            'stocks' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                }
            },
            'sizes.stocks' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                }
            },
        ])
            ->where('status', Product::STATUS_ACTIVE ?? 1)
            ->whereIn('type', $types);

        // فلترة بناءً على المستودع أو الفرع إذا تم طلب ذلك بشكل صريح
        if ($enforceLocation) {
            $query->where(function ($q) use ($request, $storeId) {
                // الخدمات تظهر دائماً بغض النظر عن الموقع أو المخزون
                $q->where('type', self::TYPE_SERVICE);

                // المنتجات تخضع لفلترة الموقع
                $q->orWhere(function ($qProd) use ($request, $storeId) {
                    $qProd->where('type', self::TYPE_PRODUCT);

                    if ($storeId) {
                        // جلب المنتجات التي لها سجل مخزون في هذا المستودع (سواء للمنتج نفسه أو لمقاساته)
                        $qProd->where(function ($qStockSub) use ($storeId) {
                            $qStockSub->whereHas('stocks', function ($qStock) use ($storeId) {
                                $qStock->where('store_id', $storeId);
                            })->orWhereHas('sizes.stocks', function ($qStock) use ($storeId) {
                                $qStock->where('store_id', $storeId);
                            });
                        });
                    } elseif ($request->filled('branch_id')) {
                        $branchId = $request->branch_id;
                        // جلب المنتجات المرتبطة بالمستودعات التابعة لهذا الفرع
                        $qProd->where(function ($qStockSub) use ($branchId) {
                            $qStockSub->whereHas('stocks.store', function ($qStore) use ($branchId) {
                                $qStore->where('branch_id', $branchId);
                            })->orWhereHas('sizes.stocks.store', function ($qStore) use ($branchId) {
                                $qStore->where('branch_id', $branchId);
                            });
                        });
                    }
                });
            });
        }

        // فلترة بناءً على الفاتورة (للمرتجعات المرتبطة)
        if ($purchaseInvoiceId) {
            $query->where(function ($q) use ($purchaseInvoiceId) {
                // الفلترة للأصناف العادية
                $q->whereHas('purchaseInvoiceItems', function ($qItem) use ($purchaseInvoiceId) {
                    $qItem->where('invoice_id', $purchaseInvoiceId)->where('have_sizes', false);
                })
                    // أو فلترة المقاسات
                    ->orWhereHas('sizes.purchaseInvoiceItems', function ($qSizeItem) use ($purchaseInvoiceId) {
                        $qSizeItem->where('invoice_id', $purchaseInvoiceId)->where('have_sizes', true);
                    });
            });
        }

        // Filter by specific product ID (useful for edit modes)
        if ($request->filled('product_id')) {
            $query->where('id', $request->product_id);
        }

        if (! empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                // البحث في الترجمة أو الباركود
                $q->whereTranslationLike('name', '%'.$searchTerm.'%')
                    ->orWhere('barcode', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhereHas('sizes', function ($qSize) use ($searchTerm) {
                        $qSize->whereTranslationLike('name', '%'.$searchTerm.'%')->orWhere('barcode', 'LIKE', '%'.$searchTerm.'%');
                    });
            });
        }

        // Limit results for performance
        $perPage = $request->input('per_page', 20);
        $paginated = $query->paginate($perPage);
        $products = $paginated->items();

        $results = [];

        foreach ($products as $product) {
            $hasSizes = $product->sizes->count() > 0;

            if ($product->type == self::TYPE_PRODUCT && $hasSizes) {
                // التعامل مع المقاسات كمنتجات منفصلة
                foreach ($product->sizes as $size) {
                    // إذا تم تفعيل فلترة الموقع، نتحقق أن هذا المقاس تحديداً مرتبط بهذا المستودع
                    if ($enforceLocation && $storeId) {
                        $existsInStore = $size->stocks->where('store_id', $storeId)->count() > 0;
                        if (! $existsInStore) {
                            continue;
                        }
                    }

                    // التحقق من مطابقة البحث (إذا وجد)
                    if (! empty($searchTerm)) {
                        $sizeName = $size->translate($lang)->name ?? '';
                        $parentName = $product->translate($lang)->name ?? '';
                        $sizeMatch = stripos($sizeName, $searchTerm) !== false ||
                            stripos($size->barcode ?? '', $searchTerm) !== false ||
                            stripos($parentName, $searchTerm) !== false;

                        if (! $sizeMatch) {
                            continue;
                        }
                    }

                    $results[] = $this->formatProductResult($size, $product, $storeId, true, $setting, $lang);
                }
            } else {
                // للمنتجات العادية، الاستعلام الأصلي قام بالفلترة، لذا نضيفها مباشرة
                $results[] = $this->formatProductResult($product, null, $storeId, false, $setting, $lang);
            }
        }

        return [
            'results' => $results,
            'more' => $paginated->hasMorePages(),
        ];
    }

    /**
     * Format product/size data for API response.
     */
    private function formatProductResult($item, $parent, $storeId, $isSize, $setting, $lang)
    {
        // Get stock info
        $stock = null;
        if ($storeId) {
            if ($item->relationLoaded('stocks')) {
                $stock = $item->stocks->where('store_id', $storeId)->first();
            }

            if (! $stock) {
                $stock = Stock::where('product_id', $item->id)
                    ->where('is_size', $isSize)
                    ->where('store_id', $storeId)
                    ->first();
            }

            $baseQuantity = $stock ? $stock->available_quantity : 0;
            $averageCost = $stock ? $stock->average_cost : null;
        } else {
            // Since sum() returns a scalar, we can't easily sum available_quantity without raw SQL.
            // But we can do sum(current_quantity) - sum(reserved_quantity).
            $baseQuantity = Stock::where('product_id', $item->id)
                ->where('is_size', $isSize)
                ->sum(DB::raw('IFNULL(current_quantity, 0) - IFNULL(reserved_quantity, 0)')) ?? 0;
            $averageCost = $item->cost_price;
        }

        // Logic for display text matching user example
        $name = $item->translate($lang)->name ?? ($item->name ?? '---');
        if ($isSize) {
            $parentName = $parent->translate($lang)->name ?? ($parent->name ?? '---');
            $displayName = "{$parentName} - {$name}";
        } else {
            $displayName = $name;
        }

        $displayName .= ($item->type == self::TYPE_SERVICE) ? '' : (($baseQuantity > 0) ? ' ( Avl: '.round($baseQuantity, 2).' )' : ' ( OOS )');

        // Cost and Sale prices
        $costPrice = (float) ($item->cost_price ?? 0);
        if ($setting && property_exists($setting, 'costing_method') && $setting->costing_method == 'weighted_average') {
            $costPrice = ($storeId && $averageCost !== null) ? (float) $averageCost : $costPrice;
        }
        $salePrice = (float) ($isSize ? ($item->sale_price ?? 0) : ($item->prod_price ?? 0));

        // Prepare units
        $units = [];
        $productUnits = $isSize ? $parent->units : $item->units;
        foreach ($productUnits as $pUnit) {
            $factor = (float) ($pUnit->conversion_factor ?: 1);
            $units[] = [
                'id' => $pUnit->id,
                'name' => $pUnit->unit ? ($pUnit->unit->translate($lang)->name ?? $pUnit->unit->name) : '---',
                'conversion_factor' => $factor,
                'cost_price' => round($costPrice * $factor, 2),
                'sale_price' => round($salePrice * $factor, 2),
                'available_quantity' => round($factor > 0 ? $baseQuantity / $factor : 0, 2),
                'barcode' => $pUnit->barcode,
                'is_base' => $pUnit->is_base,
            ];
        }

        // Image and VAT logic
        $imgPath = $isSize ? ($parent->img_path ?? asset('uploads/images/products/no_img.jpg')) : ($item->img_path ?? asset('uploads/images/products/no_img.jpg'));
        $vatPercentage = $isSize ? ($parent->vat ?? 0) : ($item->vat ?? 0);

        return [
            'id' => (int) $item->id,
            'text' => $displayName,
            'barcode' => $item->barcode,
            'tax_id' => $isSize ? $parent->tax_id : $item->tax_id,
            'is_size' => (bool) $isSize,
            'parent_id' => $isSize ? $parent->id : $item->id,
            'cost_price' => round($costPrice, 2),
            'sale_price' => round($salePrice, 2),
            'units' => $units,
            'quantity' => round($baseQuantity, 2),
            'image_url' => $imgPath,
            'tax_rate' => $vatPercentage,
        ];
    }

    /**
     * دالة البحث الافتراضية (للتوافق مع الكود القديم)
     */
    public function searchProducts(Request $request)
    {
        return $this->searchProductsOnly($request);
    }
}
