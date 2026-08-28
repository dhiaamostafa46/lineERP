<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\PurchaseInvoiceRepository;
use Modules\Invoices\App\Models\PurchaseInvoiceItem;
use Modules\Invoices\App\Models\PurchaseInvoicePayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use App\Services\ProductService;
use Modules\Invoices\App\Http\Requests\CreatePurchaseInvoiceRequest;
use Modules\Invoices\App\Http\Requests\UpdatePurchaseInvoiceRequest;
use Modules\Invoices\App\Imports\PurchaseInvoiceImport;
use Modules\Invoices\App\Exports\PurchaseInvoiceTemplateExport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurchaseInvoiceController extends AppBaseController
{
    private $purchaseInvoiceRepository;

    public function __construct(PurchaseInvoiceRepository $purchaseInvoiceRepo)
    {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepo;
    }

    /**
     * تحليل الفاتورة باستخدام الذكاء الاصطناعي (Gemini 1.5 Flash)
     */
    public function analyzeWithAI(Request $request)
    {
        try {
            $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']);
            $file = $request->file('file');
            $apiKey = trim(env('GEMINI_API_KEY'));

            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'يرجى ضبط GEMINI_API_KEY في ملف .env']);
            }

            // تحويل الملف إلى Base64
            $fileData = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType = $file->getMimeType();

            // تجربة الموديل الأساسي (للتأكد من صلاحية المفتاح والخدمة)
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Extract invoice items as JSON array: [{\"barcode\":\"...\",\"name\":\"...\",\"qty\":1,\"price\":0,\"vat_rate\":15}]"],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $fileData
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

                // تنظيف النص من أي علامات Markdown قد يضيفها الذكاء الاصطناعي
                $cleanJson = preg_replace('/^```json\s*|```$/m', '', trim($rawText));
                $items = json_decode($cleanJson, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('AI JSON Parse Error: ' . json_last_error_msg() . ' Raw: ' . $rawText);
                    return response()->json(['success' => false, 'message' => 'فشل في تحليل بيانات الذكاء الاصطناعي.']);
                }

                return response()->json(['success' => true, 'items' => $items]);
            }

            $errorDetail = $response->json()['error']['message'] ?? $response->body();
            Log::error('Gemini API Error: ' . $errorDetail);
            return response()->json(['success' => false, 'message' => 'خطأ من محرك الذكاء الاصطناعي: ' . $errorDetail]);
        } catch (\Exception $e) {
            Log::error('AI Analysis Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ تقني: ' . $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the purchase invoices.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->purchaseInvoiceRepository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']))->isInvoice();

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['purchaseInvoices'] = $query->paginate($perPage)->appends($request->all());
        return view('invoices::purchase_invoices.index', $data);
    }

    /**
     * Show the form for creating a new purchase invoice.
     */
    public function create(Request $request)
    {
        $data = $this->purchaseInvoiceRepository->getFormData();
        $data['purchaseInvoice'] = null;

        // التحقق من وجود بيانات مستوردة ذكياً في الجلسة (Session)
        if (session()->has('smart_import_data')) {
            $importData = session('smart_import_data');
            $data['purchaseInvoice'] = new \Modules\Invoices\App\Models\PurchaseInvoice();

            // تحويل البيانات المستوردة إلى تنسيق بنود الفاتورة
            $items = collect($importData['items'])->map(function ($item) {
                return new \Modules\Invoices\App\Models\PurchaseInvoiceItem([
                    'product_id' => $item['id'],
                    'unit_id' => $item['product_unit_id'], // ربط الوحدة الصحيحة
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'product_name' => $item['name'],

                ]);
            });
            $data['purchaseInvoice']->items = $items;

            // مسح البيانات من الجلسة بعد القراءة لضمان عدم تكرارها
            session()->forget('smart_import_data');
        }

        if ($request->has('from_po')) {
            $poId = $request->get('from_po');
            
            $purchaseOrder = \Modules\Invoices\App\Models\PurchaseOrder::with(['items', 'payments'])->find($poId);

            if ($purchaseOrder) {
                $data['purchaseInvoice'] = new \Modules\Invoices\App\Models\PurchaseInvoice($purchaseOrder->toArray());
                $data['purchaseInvoice']->items = $purchaseOrder->items;
                $data['purchaseInvoice']->payments = $purchaseOrder->payments;
                $data['purchaseInvoice']->from_po_id = $poId;
            }
        }

        return view('invoices::purchase_invoices.create', $data);
    }

    /**
     * معالجة البيانات المستوردة ذكياً: إنشاء المنتجات غير الموجودة وحفظها في الجلسة
     */
    public function processSmartImport(Request $request)
    {
        try {
            $items = $request->get('items');
            $processedItems = [];
            $orgId = auth()->user()->org_id ?? null;
            $userId = auth()->id();

            foreach ($items as $item) {
                $barcode = ($item['barcode'] == '---' || empty($item['barcode'])) ? null : $item['barcode'];
                $name = trim($item['name']);

                // 1. البحث عن المنتج بالباركود أو عبر الترجمات (لتجنب خطأ Unknown column 'name')
                $product = \App\Models\BasicDataApp\Product::where('org_id', $orgId)
                    ->where(function ($q) use ($barcode, $name) {
                        if ($barcode) {
                            $q->where('barcode', $barcode);
                        }
                        $q->orWhereHas('translations', function ($query) use ($name) {
                            $query->where('name', $name);
                        });
                    })->first();

                // 2. إذا لم يوجد المنتج، قم بإنشائه فوراً بنفس نمط ProductsImport.php
                if (!$product) {
                    // البحث عن تصنيف ووحدة افتراضية للمؤسسة (أو إنشاء عام)
                    $category = \App\Models\BasicDataApp\Category::where('org_id', $orgId)->first();
                    $unit = \App\Models\BasicDataApp\Unit::where('org_id', $orgId)->first();

                    $product = \App\Models\BasicDataApp\Product::create([
                        'org_id' => $orgId,
                        'user_id' => $userId,
                        'barcode' => $barcode ?: \App\Models\BasicDataApp\Product::generateUniqueBarcode(),
                        'category_id' => $category ? $category->id : 1,
                        'base_unit_id' => $unit ? $unit->id : 1,
                        'status' => 1,
                        'type' => 1, // منتج عادي
                        'have_sizes' => false,
                        'cost_price' => $item['price'],
                        'prod_price' => $item['price'] * 1.2,
                        'ar' => ['name' => $name],
                        'en' => ['name' => $name],
                    ]);

                    // إضافة الوحدة الأساسية لجدول product_units (ضروري جداً للنظام)
                    \App\Models\BasicDataApp\ProductUnit::updateOrCreate(
                        ['product_id' => $product->id, 'unit_id' => $product->base_unit_id],
                        ['conversion_factor' => 1, 'is_base' => true]
                    );
                }

                // الحصول على الـ product_unit_id (المعرف المطلوب في بنود الفاتورة)
                $pUnit = \App\Models\BasicDataApp\ProductUnit::where('product_id', $product->id)->first();

                $processedItems[] = [
                    'id' => $product->id,
                    'product_unit_id' => $pUnit ? $pUnit->id : null,
                    'name' => $product->name,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'vat_rate' => $item['vat_rate'] ?? 15
                ];
            }

            // 3. حفظ البيانات في الجلسة
            session(['smart_import_data' => ['items' => $processedItems]]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('invoices.purchase.create')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()
            ], 500);
        }
    }

    /**
     * Store a newly created purchase invoice in storage.
     */
    public function store(CreatePurchaseInvoiceRequest $request)
    {
        try {



            $this->purchaseInvoiceRepository->CreatePurchase($request->all());

            flash()->success(__('messages.saved', ['model' => __('invoices::models/purchase_invoices.singular')]));
            return redirect()->route('invoices.purchase.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/purchase_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified purchase invoice.
     */
    public function show($id)
    {
        $purchaseInvoice = $this->purchaseInvoiceRepository->find($id);

        if (empty($purchaseInvoice)) {
            flash()->error(__('invoices::models/purchase_invoices.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.purchase.index'));
        }

        $purchaseInvoice->load(['items.product.units', 'payments', 'supplier']);

        return view('invoices::purchase_invoices.show')->with('purchaseInvoice', $purchaseInvoice);
    }

    /**
     * Show the form for editing the specified purchase invoice.
     */
    public function edit($id)
    {
        $data = $this->purchaseInvoiceRepository->getFormData();
        $data['purchaseInvoice'] = $this->purchaseInvoiceRepository->find($id);

        if (empty($data['purchaseInvoice'])) {
            flash()->error(__('invoices::models/purchase_invoices.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.purchase.index'));
        }

        // منع تعديل الفاتورة إذا كانت معتمدة أو مسترجعة (تسمح بتعديل المسودة فقط)
        if ($data['purchaseInvoice']->status != \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT) {
            flash()->error('الفاتورة معتمدة ولا يمكن تعديلها، يمكنك إجراء ارجاع (مرتجع) للفاتورة.');
            return redirect(route('invoices.purchase.index'));
        }

        $data['purchaseInvoice']->load(['items.product.units', 'payments']);

        return view('invoices::purchase_invoices.edit', $data);
    }

    /**
     * Update the specified purchase invoice in storage.
     */
    public function update(UpdatePurchaseInvoiceRequest $request, $id)
    {
        try {
            $purchaseInvoice = $this->purchaseInvoiceRepository->find($id);

            if (empty($purchaseInvoice)) {
                flash()->error(__('invoices::models/purchase_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase.index'));
            }

            // منع تحديث الفاتورة إذا كانت معتمدة أو مسترجعة (تسمح بتعديل المسودة فقط)
            if ($purchaseInvoice->status != \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT) {
                flash()->error('الفاتورة معتمدة ولا يمكن تعديلها، يمكنك إجراء ارجاع (مرتجع) للفاتورة.');
                return redirect(route('invoices.purchase.index'));
            }

            $this->purchaseInvoiceRepository->updatePurchase($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/purchase_invoices.singular')]));
            return redirect()->route('invoices.purchase.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/purchase_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified purchase invoice from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseInvoice = $this->purchaseInvoiceRepository->find($id);

            if (empty($purchaseInvoice)) {
                flash()->error(__('invoices::models/purchase_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase.index'));
            }

            $this->purchaseInvoiceRepository->deletePurchase($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/purchase_invoices.singular')]));
            return redirect()->route('invoices.purchase.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/purchase_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->purchaseInvoiceRepository->getHeaders();
        $dataExcel = $this->purchaseInvoiceRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_invoices.xlsx');
    }

    public function csv()
    {
        $headers = $this->purchaseInvoiceRepository->getHeaders();
        $dataExcel = $this->purchaseInvoiceRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_invoices.csv');
    }

    public function pdf()
    {
        $headers = $this->purchaseInvoiceRepository->getHeaders();
        $dataExcel = $this->purchaseInvoiceRepository->dataExcel();
        $name = __('invoices::models/purchase_invoices.plural');

        $mpdf = new Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(
            view('basicdata::exports.pdf', [
                'headers' => $headers,
                'data' => $dataExcel,
                'name' => $name,
            ]),
        );
        $mpdf->Output();
    }

    public function import()
    {
        return view('invoices::purchase_invoices.import');
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new PurchaseInvoiceImport();
            Excel::import($import, $request->file('file'));

            $summary = $import->getSummary();

            if ($summary['error_count'] > 0) {
                flash()->warning(__('messages.imported_with_errors', [
                    'success' => $summary['success_count'],
                    'errors' => $summary['error_count']
                ]));
                return redirect()->route('invoices.purchase.index')->with('import_errors', $summary['errors']);
            }

            flash()->success(__('messages.imported', ['model' => __('invoices::models/purchase_invoices.plural')]));
            return redirect()->route('invoices.purchase.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_importing', ['model' => __('invoices::models/purchase_invoices.plural')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importTemplate()
    {
        return Excel::download(new PurchaseInvoiceTemplateExport(), 'Purchase_Invoice_Import_Template.xlsx');
    }

    /**
     * إعادة احتساب وتصحيح جميع فواتير المشتريات السابقة
     */
    public function recalculateAll(Request $request)
    {
        try {
            $result = $this->purchaseInvoiceRepository->recalculateAllInvoices();

            flash()->success("تم إعادة احتساب وتصحيح جميع فواتير المشتريات السابقة ومزامنة قيودها بنجاح (عدد الفواتير المعالجة: {$result['count']}).");
            return redirect()->route('invoices.purchase.index')->with('recalculate_result', $result);
        } catch (\Exception $e) {
            flash()->error('حدث خطأ أثناء إعادة احتساب الفواتير: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
