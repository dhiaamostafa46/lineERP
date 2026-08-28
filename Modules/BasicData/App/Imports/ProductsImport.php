<?php

namespace Modules\BasicData\App\Imports;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Category;

use App\Models\BasicDataApp\Unit;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\BasicData\App\Models\DbKitchen;
use Throwable;

class ProductsImport implements ToModel, WithChunkReading, WithBatchInserts, WithValidation, SkipsOnError, SkipsEmptyRows, WithStartRow
{
    private $errors = [];
    private $successCount = 0;
    private $errorCount = 0;
    private $categories = [];
    private $units = [];
    private $kitchens = [];
    private $productsByName = [];
    private $orgId;
    private $userId;

    public function __construct()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }
        $this->orgId = $user->org_id;
        $this->userId = $user->id;

        // التحميل المسبق للبيانات لتقليل الاستعلامات في حال كانت البيانات كبيرة
        $this->loadLookups();
    }

    private function loadLookups()
    {
        // تحميل البيانات الحالية وتخزينها في المصفوفات للبحث السريع
        $this->categories = Category::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();

        $this->units = Unit::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();

        $this->kitchens = DbKitchen::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();

        // تحميل المنتجات الحالية بالاسم (للربط مع المقاسات)
        $this->productsByName = Product::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->translate('ar')?->name ?? $item->name);
        })->toArray();
    }

    public function startRow(): int
    {
        return 3;
    }

    /**
     * معالجة كل صف من ملف Excel
     */
    public function model(array $row)
    {
        try {
            if (!$this->orgId) {
                throw new \Exception('Organization not found or User not authenticated');
            }

            // فصل الاسم العربي والإنجليزي
            $names = $this->parseProductName($row[1]);

            // توليد باركود تلقائي إذا لم يتم تقديمه (استخدام دالة الموديل)
            $barcode = !empty($row[0]) ? trim($row[0]) : Product::generateUniqueBarcode();

            // البحث عن الفئة أو إنشائها (باستخدام الكاش)
            $category = $this->findOrCreateCategory($row[2]);

            // تحويل نوع المنتج ومعالجة المقاسات (الآن في العمود 8)
            $typeData = $this->convertProductType($row[8]);

            // البحث عن الوحدة أو إنشائها (باستخدام الكاش)
            $unit = !empty($row[5]) ? $this->findOrCreateUnit($row[5]) : null;

            if ($typeData['type'] != Product::TYPE_SERVICE && empty($unit)) {
                throw new \Exception(__('basicdata::models/db_products.messages.min_one_unit'));
            }

            if ($typeData['have_sizes']) {
                return $this->handleProductSize($row, $names, $category, $unit, $barcode);
            }

            // تحديث أو إنشاء المنتج العادي أو الخدمة
            $product = Product::updateOrCreate(
                [
                    'barcode' => $barcode,
                    'org_id' => $this->orgId,
                ],
                array_merge([
                    'user_id' => $this->userId,
                    'category_id' => $category->id,
                    'base_unit_id' => $unit ? $unit->id : null,
                    'prod_price' => $this->sanitizeNumeric($row[3]),
                    'cost_price' => $this->sanitizeNumeric($row[4]),
                    'vat' => $this->sanitizeNumeric($row[7]),
                    'min_quantity' => $this->sanitizeInteger($row[6] ?? 0),
                    'type' => $typeData['type'],
                    'have_sizes' => false,
                    'status' => 1,
                    'kitchen_id' => null,
                    'calories' => 0,
                ], $this->formatTranslations($names))
            );

            // إضافة الوحدة الأساسية لجدول product_units (ضروري للنظام)
            if ($unit) {
                ProductUnit::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'unit_id' => $unit->id,
                    ],
                    [
                        'conversion_factor' => 1,
                        'is_base' => true,
                    ]
                );
            }

            $this->successCount++;
            return $product;
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = [
                'row' => $row,
                'error' => $e->getMessage(),
            ];

            Log::error('Product import error at row', [
                'data' => $row,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * معالجة المنتجات ذات المقاسات
     */
    private function handleProductSize($row, $names, $category, $unit, $barcode)
    {
        $fullNameAr = $names['ar'];
        $fullNameEn = $names['en'];

        $parentNameAr = $fullNameAr;
        $parentNameEn = $fullNameEn;
        $sizeNameAr = $fullNameAr;
        $sizeNameEn = $fullNameEn;

        // البحث عن الفاصل - لفصل اسم المنتج عن المقاس
        if (str_contains($fullNameAr, ' - ')) {
            $partsAr = explode(' - ', $fullNameAr, 2);
            $parentNameAr = trim($partsAr[0]);
            $sizeNameAr = trim($partsAr[1]);
        }

        if (str_contains($fullNameEn, ' - ')) {
            $partsEn = explode(' - ', $fullNameEn, 2);
            $parentNameEn = trim($partsEn[0]);
            $sizeNameEn = trim($partsEn[1]);
        }

        // 1. البحث عن المنتج الأب أو إنشاؤه
        // نستخدم الاسم العربي كمفتاح للبحث في الكاش
        if (isset($this->productsByName[$parentNameAr])) {
            $product = Product::find($this->productsByName[$parentNameAr]['id']);
            // التأكد من تفعيل خاصية المقاسات وتحديث الوحدة إذا لزم الأمر
            $product->update([
                'have_sizes' => true,
                'base_unit_id' => $unit->id,
                'category_id' => $category->id,
            ]);
            $this->productsByName[$parentNameAr]['have_sizes'] = true;
            $this->productsByName[$parentNameAr]['base_unit_id'] = $unit->id;
        } else {
            $product = Product::create(array_merge([
                'org_id' => $this->orgId,
                'user_id' => $this->userId,
                'category_id' => $category->id,
                'base_unit_id' => $unit->id,
                'prod_price' => $this->sanitizeNumeric($row[3]),
                'cost_price' => $this->sanitizeNumeric($row[4]),
                'vat' => $this->sanitizeNumeric($row[7]),
                'type' => Product::TYPE_SALE,
                'have_sizes' => true,
                'status' => 1,
            ], $this->formatTranslations(['ar' => $parentNameAr, 'en' => $parentNameEn])));
            $this->productsByName[$parentNameAr] = $product->toArray();
        }

        // 2. تحديث أو إنشاء المقاس (Variant)
        // ملاحظة: نستخدم الباركود كمفتاح فريد للمقاس
        ProductSize::updateOrCreate(
            [
                'product_id' => $product->id,
                'barcode' => $barcode,
            ],
            array_merge([
                'sale_price' => $this->sanitizeNumeric($row[3]),
                'cost_price' => $this->sanitizeNumeric($row[4]),
                'consumption_factor' => 1,
                'status' => 1,
            ], $this->formatTranslations(['ar' => $sizeNameAr, 'en' => $sizeNameEn]))
        );

        // إضافة الوحدة للمقاس (في حال كان النظام يتطلب ذلك في جداول أخرى)
        // ملاحظة: المقاسات غالباً تتبع وحدة المنتج الأب الأساسية
        ProductUnit::updateOrCreate(
            [
                'product_id' => $product->id,
                'unit_id' => $unit->id,
            ],
            [
                'conversion_factor' => 1,
                'is_base' => true,
            ]
        );

        $this->successCount++;
        return $product;
    }

    /**
     * فصل الاسم العربي والإنجليزي من النص
     */
    private function parseProductName($name): array
    {
        $name = trim((string)$name);
        $separators = [' - ', ' / ', ' | ', '|', "\n", "\r\n"];

        foreach ($separators as $separator) {
            if (str_contains($name, $separator)) {
                $parts = explode($separator, $name, 2);
                return [
                    'ar' => trim($parts[0]),
                    'en' => trim($parts[1] ?? $parts[0]),
                ];
            }
        }

        return ['ar' => $name, 'en' => $name];
    }

    private function convertProductType($type): array
    {
        $type = trim((string)$type);
        $result = ['type' => Product::TYPE_SALE, 'have_sizes' => false];

        // دعم الكلمات الإنجليزية والعربية والأرقام
        if (in_array(strtolower($type), ['product', 'منتج', '1'])) {
            $result['type'] = Product::TYPE_SALE;
        } elseif (in_array(strtolower($type), ['service', 'خدمة', '2'])) {
            $result['type'] = Product::TYPE_SERVICE;
        } elseif (in_array(strtolower($type), ['size', 'sizes', 'مقاسات', '3'])) {
            $result['type'] = Product::TYPE_SALE;
            $result['have_sizes'] = true;
        }
        return $result;
    }

    private function findOrCreateCategory(string $name)
    {
        $name = trim($name);
        // التحقق من الكاش أولاً
        if (isset($this->categories[$name])) {
            return (object) $this->categories[$name];
        }

        $names = $this->parseProductName($name);

        // البحث باستخدام علاقة الترجمات (لتجنب خطأ Column not found 'name')
        $category = Category::where('org_id', $this->orgId)
            ->whereHas('translations', function ($q) use ($names) {
                $q->where('name', $names['ar'])->where('locale', 'ar');
            })->first();

        if (!$category) {
            $category = Category::create(array_merge([
                'org_id' => $this->orgId,
                'user_id' => $this->userId,
                'status' => 1,
            ], $this->formatTranslations($names)));
        }

        $this->categories[$name] = $category->toArray();
        return $category;
    }

    private function findOrCreateUnit(string $name)
    {
        $name = trim($name);
        if (isset($this->units[$name])) {
            return (object) $this->units[$name];
        }

        $names = $this->parseProductName($name);

        $unit = Unit::where('org_id', $this->orgId)
            ->whereHas('translations', function ($q) use ($names) {
                $q->where('name', $names['ar'])->where('locale', 'ar');
            })->first();

        if (!$unit) {
            $unit = Unit::create(array_merge([
                'org_id' => $this->orgId,
                'user_id' => $this->userId,
                'status' => 1,
                'conversion_factor' => 1,
            ], $this->formatTranslations($names)));
        }

        $this->units[$name] = $unit->toArray();
        return $unit;
    }

    private function sanitizeNumeric($value): float
    {
        if (empty($value)) return 0.0;
        if (is_numeric($value)) return (float) $value;
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
        return (float) ($cleaned ?: 0);
    }

    private function sanitizeInteger($value): int
    {
        return (int) $this->sanitizeNumeric($value);
    }

    /**
     * تحويل مصفوفة الأسماء إلى تنسيق متوافق مع حزمة الترجمة
     */
    private function formatTranslations(array $names, string $field = 'name'): array
    {
        $data = [];
        foreach ($names as $locale => $value) {
            $data[$locale] = [$field => $value];
        }
        return $data;
    }

    public function rules(): array
    {
        return [
            '0' => ['nullable', 'max:255'],
            '1' => ['required', 'string', 'max:500'],
            '2' => ['required', 'string', 'max:255'],
            '3' => ['required', 'numeric', 'min:0'],
            '4' => ['required', 'numeric', 'min:0'],
            '5' => ['nullable', 'string', 'max:255'],
            '7' => ['required', 'numeric', 'min:0', 'max:100'], // VAT انتقل للعمود 7
            '8' => ['required'], // Type انتقل للعمود 8
        ];
    }

    public function batchSize(): int
    {
        return 500; // زيادة حجم الدفعة للأداء
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function onError(Throwable $e)
    {
        Log::error('Import process error: ' . $e->getMessage());
    }

    public function getImportSummary(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return empty(array_filter($row, fn($v) => !is_null($v) && $v !== ''));
    }
}
