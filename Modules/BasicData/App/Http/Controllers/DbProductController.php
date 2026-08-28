<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AccuSoft\TaxAccount;
use Modules\BasicData\App\Exports\ProductImportErrorsExport;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\BasicData\App\Http\Requests\CreateDbProductRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbProductRequest;
use Modules\BasicData\App\Imports\ProductsImport;
use Modules\BasicData\App\Exports\ProductTemplateExport;
use Modules\BasicData\App\Repositories\DbProductRepository;
use Modules\BasicData\App\Repositories\DbProductSizeRepository;
use Modules\BasicData\App\Repositories\DbProductUnitRepository;

class DbProductController extends AppBaseController
{
    /** @var DbProductRepository $dbProductRepository*/
    private $dbProductRepository;
    /** @var DbProductSizeRepository $dbProductSizeRepository */
    private $dbProductSizeRepository;
    /** @var DbProductUnitRepository $dbProductUnitRepository */
    private $dbProductUnitRepository;

    public function __construct(DbProductRepository $dbProductRepo, DbProductSizeRepository $dbProductSizeRepo, DbProductUnitRepository $dbProductUnitRepo)
    {
        $this->dbProductRepository = $dbProductRepo;
        $this->dbProductSizeRepository = $dbProductSizeRepo;
        $this->dbProductUnitRepository = $dbProductUnitRepo;
    }

    /**
     * Display a listing of the Product.
     */
    public function index(Request $request)
    {
        $data['products'] = $this->dbProductRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        // يمكنك إضافة بيانات أخرى هنا مثل الفئات
        $data['categories'] = $this->dbProductRepository->Categories();
        $data['vats'] = $this->dbProductRepository->vats();
        $data['statuses'] = $this->dbProductRepository->statuses();
        $data['units'] = $this->dbProductRepository->units();
        $data['types'] = $this->dbProductRepository->types();

        return view('basicdata::products.index', $data);
    }

    /**
     * Show the form for creating a new Product.
     */
    public function create(Request $request)
    {
        // يمكنك تمرير بيانات ضرورية لنموذج الإنشاء مثل الفئات
        $data['type'] = $request->type ?? 1;
        $data['categories'] = $this->dbProductRepository->Categories();
      
        $data['statuses'] = $this->dbProductRepository->statuses();
        $data['units'] = $this->dbProductRepository->units();
         $data['vats'] = $this->dbProductRepository->vats();
        $data['types'] = $this->dbProductRepository->types();

        return view('basicdata::products.create', $data);
    }

    /**
     * Store a newly created Product in storage.
     */
    public function store(CreateDbProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            if (!empty($input['tax_id'])) {
                $taxAccount = TaxAccount::find($input['tax_id']);
                $input['vat'] = $taxAccount ? $taxAccount->rate : 15;
            } else {
                $defaultTax = TaxAccount::Active()->first();
                if ($defaultTax) {
                    $input['tax_id'] = $defaultTax->id;
                    $input['vat'] = $defaultTax->rate;
                } else {
                    $input['vat'] = 15;
                }
            }
 
            // 1. إنشاء المنتج الأساسي
            $product = $this->dbProductRepository->create($input);

            //  dd($input);// 2. معالجة وحفظ وحدات المنتج
            if ($request->has('units') && is_array($input['units'])) {
                foreach ($input['units'] as $unitData) {
                    // التأكد من وجود البيانات الأساسية قبل الحفظ
                    if (!empty($unitData['unit_id'])) {
                        $unitData['conversion_factor'] = (isset($unitData['conversion_factor']) && $unitData['conversion_factor'] !== '') ? $unitData['conversion_factor'] : 1;
                        $unitData['product_id'] = $product->id;
                        $this->dbProductUnitRepository->create($unitData);
                    }
                }
            }

            // 3. معالجة وحفظ أحجام المنتج إذا كان الخيار مفعلاً
            if ($request->boolean('have_sizes') && $request->has('sizes') && is_array($input['sizes'])) {
                foreach ($input['sizes'] as $sizeData) {
                    // التأكد من وجود اسم للحجم قبل الحفظ
                    if (!empty($sizeData['ar']['name']) || !empty($sizeData['en']['name'])) {
                        $sizeData['product_id'] = $product->id;
                        $this->dbProductSizeRepository->create($sizeData);
                    }
                }
            }

            DB::commit();

            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_products.singular')]));

            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Product.
     */
    public function show($id)
    {
        try {
            $product = $this->dbProductRepository->find($id);

            if (empty($product)) {
                flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.products.index'));
            }

            return view('basicdata::products.show')->with('product', $product);
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect(route('basicdata.products.index'));
        }
    }

    /**
     * Show the form for editing the specified Product.
     */
    public function edit($id)
    {
        $data['product'] = $this->dbProductRepository->find($id);

        if (empty($data['product'])) {
            flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.products.index'));
        }

        // يمكنك تمرير بيانات ضرورية لنموذج التعديل مثل الفئات
        // $data['categories'] = $this->dbProductRepository->getCategories();
        $data['type'] = $data['product']->type ?? 1;
        $data['categories'] = $this->dbProductRepository->Categories();
        $data['kitchens'] = $this->dbProductRepository->kitchens();
        $data['statuses'] = $this->dbProductRepository->statuses();
        $data['units'] = $this->dbProductRepository->units();
        $data['vats'] = $this->dbProductRepository->vats();
        $data['types'] = $this->dbProductRepository->types();

        return view('basicdata::products.edit', $data);
    }

    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param UpdateDbProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDbProductRequest $request, $id)
    {

      
        DB::beginTransaction();
        try {
            $product = $this->dbProductRepository->find($id);

            if (empty($product)) {
                DB::rollBack();
                flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.products.index'));
            }

            $input = $request->all();
            if (!empty($input['tax_id'])) {
                $taxAccount = TaxAccount::find($input['tax_id']);
                $input['vat'] = $taxAccount ? $taxAccount->rate : 15;
            } else {
                $defaultTax = TaxAccount::Active()->first();
                if ($defaultTax) {
                    $input['tax_id'] = $defaultTax->id;
                    $input['vat'] = $defaultTax->rate;
                } else {
                    $input['vat'] = 15;
                }
            }
            // 1. تحديث بيانات المنتج الأساسية
            $product = $this->dbProductRepository->update($input, $id);

            // 2. حذف الوحدات والأحجام القديمة للبدء من جديد
            $product->units()->delete();
            $product->sizes()->delete();

            // 3. إعادة إنشاء وحدات المنتج
            if ($request->has('units') && is_array($input['units'])) {
                foreach ($input['units'] as $unitData) {
                    if (!empty($unitData['unit_id'])) {
                        $unitData['conversion_factor'] = (isset($unitData['conversion_factor']) && $unitData['conversion_factor'] !== '') ? $unitData['conversion_factor'] : 1;
                        $unitData['product_id'] = $product->id;
                        $this->dbProductUnitRepository->create($unitData);
                    }
                }
            }

            // 4. إعادة إنشاء أحجام المنتج إذا كان الخيار مفعلاً
            if ($request->boolean('have_sizes') && $request->has('sizes') && is_array($input['sizes'])) {
                foreach ($input['sizes'] as $sizeData) {
                    if (!empty($sizeData['ar']['name']) || !empty($sizeData['en']['name'])) {
                        $sizeData['product_id'] = $product->id;
                        $this->dbProductSizeRepository->create($sizeData);
                    }
                }
            }

            DB::commit();

            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_products.singular')]));

            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Product from storage.
     */
    public function destroy($id)
    {
        try {
            $product = $this->dbProductRepository->find($id);

            if (empty($product)) {
                flash()->error(__('basicdata::models/db_products.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.products.index'));
            }

            $this->dbProductRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_products.singular')]));

            return redirect()->route('basicdata.products.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_products.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import(Request $request)
    {
        return view('basicdata::products.import');
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new ProductsImport();
            Excel::import($import, $request->file('file'));
            
            $summary = $import->getImportSummary();
            
            if ($summary['error_count'] > 0) {
                // بدلاً من عرض الأخطاء في الصفحة، نقوم بتحميل ملف الإكسيل بالأخطاء
                return Excel::download(
                    new ProductImportErrorsExport($summary['errors']), 
                    'Product_Import_Errors_' . now()->format('Y-m-d_H-i') . '.xlsx'
                );
            }

            flash()->success(__('messages.imported', ['model' => __('basicdata::models/db_products.plural')]));
            return redirect()->route('basicdata.products.index');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            flash()->error(__('crud.import_errors_message'));
            return redirect()->back()->with('failures', $failures);
        } catch (\Exception $e) {
            flash()->error(__('messages.error_importing', ['model' => __('basicdata::models/db_products.plural')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importTemplate()
    {
        return Excel::download(new ProductTemplateExport(), 'Product_Import_Template.xlsx');
    }

    public function excel()
    {
        $headers = $this->dbProductRepository->header();
        $dataExcel = $this->dbProductRepository->dataExel(); // استخدم Unit بدلاً من dataExel

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'products.xlsx');
    }

    public function csv()
    {
        $headers = $this->dbProductRepository->header();
        $dataExcel = $this->dbProductRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'products.csv');
    }

    public function pdf()
    {
        $headers = $this->dbProductRepository->header();
        $dataExcel = $this->dbProductRepository->dataExel();
        $name = $this->dbProductRepository->name();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;
        $mpdf->baseScript = 1;
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');

        $html = view('basicdata::exports.pdf', [
            'headers' => $headers, 
            'data' => $dataExcel, 
            'name' => $name,
            'date' => now()->format('Y-m-d H:i')
        ])->render();

        $mpdf->WriteHTML($html);
        return response($mpdf->Output($name . '.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $name . '.pdf"'
        ]);
    }
}
