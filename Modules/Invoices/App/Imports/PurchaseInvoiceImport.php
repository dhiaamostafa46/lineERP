<?php

namespace Modules\Invoices\App\Imports;

use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\ProductSize;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\StoreApp\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\invApp\InvSupplier;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Models\PurchaseInvoiceItem;

class PurchaseInvoiceImport implements ToCollection
{
    private $orgId;
    private $userId;
    private $suppliers = [];
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
        $this->suppliers = InvSupplier::where('org_id', $this->orgId)->get()->keyBy(function ($item) {
            return trim($item->name);
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

        // تجميع الصفوف حسب (المورد + رقم فاتورة المورد) لإنشاء فاتورة واحدة لكل مجموعة
        $groupedInvoices = [];
        foreach ($dataRows as $index => $row) {
            if (empty($row[1]) || empty($row[2]) || empty($row[6])) continue; // اسم المنتج والمورد والمستودع مطلوبين

            $supplierName = trim($row[2]);
            $supplierInvoiceNo = trim($row[8] ?? 'IMPORT-' . now()->format('Ymd')); // رقم فاتورة المورد في العمود 8
            
            $key = $supplierName . '_' . $supplierInvoiceNo;
            
            if (!isset($groupedInvoices[$key])) {
                $groupedInvoices[$key] = [
                    'supplier_name' => $supplierName,
                    'supplier_invoice_number' => $supplierInvoiceNo,
                    'store_name' => trim($row[6]),
                    'items' => []
                ];
            }
            $groupedInvoices[$key]['items'][] = ['row' => $row->toArray(), 'index' => $index + 3];
        }

        foreach ($groupedInvoices as $invoiceData) {
            $supplierId = $this->getSupplierId($invoiceData['supplier_name']);
            $storeId = $this->getStoreId($invoiceData['store_name']);
            
            if (!$supplierId || !$storeId) {
                $errorMsg = !$supplierId ? "المورد '{$invoiceData['supplier_name']}' غير موجود." : "المستودع '{$invoiceData['store_name']}' غير موجود.";
                foreach ($invoiceData['items'] as $itemData) {
                    $this->errorCount++;
                    $this->errors[] = ['row' => $itemData['row'], 'error' => $errorMsg];
                }
                continue;
            }

            DB::beginTransaction();
            try {
                $invoice = PurchaseInvoice::create([
                    'org_id' => $this->orgId,
                    'user_id' => $this->userId,
                    'branch_id' => Store::find($storeId)->branch_id,
                    'invoice_number' => 'PINV-' . now()->format('YmdHis') . rand(10, 99),
                    'supplier_invoice_number' => $invoiceData['supplier_invoice_number'],
                    'issue_date' => now(),
                    'supplier_id' => $supplierId,
                    'store_id' => $storeId,
                    'status' => PurchaseInvoice::STATUS_DRAFT,
                    'type_inv' => PurchaseInvoice::TYPE_INVOICE,
                    'total_exclusive_vat' => 0,
                    'total_vat' => 0,
                    'total_inclusive_vat' => 0,
                ]);

                $totalExcl = 0;
                $totalVat = 0;

                foreach ($invoiceData['items'] as $itemData) {
                    $row = $itemData['row'];
                    try {
                        $productInfo = $this->getOrCreateProduct($row);
                        
                        $quantity = $this->sanitizeNumeric($row[4]);
                        $unitPrice = $this->sanitizeNumeric($row[5]);
                        $vatRate = $this->sanitizeNumeric($row[7] ?? 15); // نسبة الضريبة الافتراضية 15% إذا لم تتوفر
                        
                        $lineTotalExcl = $quantity * $unitPrice;
                        $lineVat = $lineTotalExcl * ($vatRate / 100);
                        $lineTotalIncl = $lineTotalExcl + $lineVat;

                        PurchaseInvoiceItem::create([
                            'purchase_invoice_id' => $invoice->id,
                            'product_id' => $productInfo['product_id'],
                            'unit_id' => $productInfo['unit_id'],
                            'have_sizes' => $productInfo['have_sizes'],
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'vat_rate' => $vatRate,
                            'vat_amount' => $lineVat,
                            'total_amount' => $lineTotalIncl,
                        ]);

                        $totalExcl += $lineTotalExcl;
                        $totalVat += $lineVat;
                        $this->successCount++;
                    } catch (\Exception $e) {
                        $this->errorCount++;
                        $this->errors[] = ['row' => $row, 'error' => "الصف {$itemData['index']}: " . $e->getMessage()];
                    }
                }

                $invoice->update([
                    'total_exclusive_vat' => $totalExcl,
                    'total_vat' => $totalVat,
                    'total_inclusive_vat' => $totalExcl + $totalVat,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                foreach ($invoiceData['items'] as $itemData) {
                    $this->errorCount++;
                    $this->errors[] = ['row' => $itemData['row'], 'error' => "خطأ في الفاتورة: " . $e->getMessage()];
                }
            }
        }
    }

    private function getOrCreateProduct($row)
    {
        $barcode = !empty($row[0]) ? trim($row[0]) : null;
        $productName = trim($row[1]);
        $typeStr = trim($row[9] ?? 'Product'); // العمود 9 للنوع
        $haveSizes = (strtolower($typeStr) === 'size' || strtolower($typeStr) === 'مقاس');

        // البحث عن المنتج أو استخدامه من الكاش
        $product = null;
        if (isset($this->productsByName[$productName])) {
            $product = Product::find($this->productsByName[$productName]['id']);
        }

        if (!$product) {
            throw new \Exception("المنتج '{$productName}' غير موجود في النظام. يرجى إضافته أولاً.");
        }

        $finalProductId = $product->id;
        $finalUnitId = $product->base_unit_id; // استخدام الوحدة الأساسية كافتراضي

        // إذا كان مقاساً
        if ($haveSizes && str_contains($productName, ' - ')) {
            $parts = explode(' - ', $productName, 2);
            $sizeName = trim($parts[1]);
            $size = ProductSize::where('product_id', $product->id)
                ->whereHas('translations', function($q) use ($sizeName) {
                    $q->where('name', $sizeName);
                })->first();
            
            if ($size) {
                $finalProductId = $size->id;
            }
        }

        return [
            'product_id' => $finalProductId,
            'unit_id' => $finalUnitId,
            'have_sizes' => $haveSizes ? 1 : 0,
        ];
    }

    private function getSupplierId($name)
    {
        if (isset($this->suppliers[$name])) return $this->suppliers[$name]['id'];
        return null;
    }

    private function getStoreId($name)
    {
        if (isset($this->stores[$name])) return $this->stores[$name]['id'];
        return null;
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
