<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\InvSupplierRepository;
use App\Models\Branch;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Modules\Invoices\App\Http\Requests\CreateInvSupplierRequest;
use Modules\Invoices\App\Http\Requests\UpdateInvSupplierRequest;
use Modules\Invoices\App\Imports\SuppliersImport;
use Modules\Invoices\App\Exports\SupplierTemplateExport;
use Modules\Invoices\App\Exports\SupplierImportErrorsExport;

class InvSupplierController extends AppBaseController
{
    private $supplierRepository;
    private $asTreeAccountsRepository;

    public function __construct(InvSupplierRepository $supplierRepo, AsTreeAccountsRepository $asTreeAccountsRepo)
    {
        $this->supplierRepository = $supplierRepo;
        $this->asTreeAccountsRepository = $asTreeAccountsRepo;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->supplierRepository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']));

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['suppliers'] = $query->paginate($perPage)->appends($request->all());
      
        return view('invoices::suppliers.index', $data);
    }

    public function create()
    {
        return view('invoices::suppliers.create');
    }

    public function store(CreateInvSupplierRequest $request)
    {
        try {
            $input = $request->all();

            DB::beginTransaction();

            // 1. تحديد الحساب الأب للموردين من الربط المحاسبي (مثلاً: حساب الدائنون/الموردون)
            $parentId = AccountMapping::getAccountId('supplier');

            if (!$parentId) {
                throw new \Exception('يرجى ضبط الحساب الأب للموردين في إعدادات الربط المحاسبي أولاً.');
            }

            // 2. إنشاء سجل الحساب المالي في شجرة الحسابات
            $parentAccount = TreeAccounts::find($parentId);
            $accountData = [
                'parent_id' => $parentId,
                'account_type' => TreeAccounts::ACCOUNT_TYPE_SUPPLIERS, // Liabilities - خصوم
                'type' => 2,         // Credit - دائن
                'is_leaf' => true,
                'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                'status' => 1, // Active
                'code' => TreeAccounts::generateCode($parentId),
            ];

            // إضافة ترجمة اسم الحساب (يأخذ نفس اسم المورد المدخل)
            foreach (config('langs') as $locale => $language) {
                $accountData[$locale]['name'] = $request->input($locale . '.name');
            }

            $treeAccount = TreeAccounts::create($accountData);

            // 3. ربط المورد بالحساب المالي الجديد وحفظ سجل المورد
            $input['tree_account_id'] = $treeAccount->id;
            $this->supplierRepository->create($input);

            DB::commit();

            flash()->success(__('messages.saved', ['model' => __('invoices::models/inv_suppliers.singular')]));
            return redirect()->route('invoices.suppliers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/inv_suppliers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            flash()->error(__('invoices::models/inv_suppliers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.suppliers.index'));
        }

        return view('invoices::suppliers.show')->with('supplier', $supplier);
    }

    public function edit($id)
    {
        $data['supplier'] = $this->supplierRepository->find($id);

        if (empty($data['supplier'])) {
            flash()->error(__('invoices::models/inv_suppliers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.suppliers.index'));
        }
        return view('invoices::suppliers.edit', $data);
    }

    public function update(UpdateInvSupplierRequest $request, $id)
    {
        try {
            $supplier = $this->supplierRepository->find($id);

            if (empty($supplier)) {
                flash()->error(__('invoices::models/inv_suppliers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.suppliers.index'));
            }

            $input = $request->all();

            DB::beginTransaction();

            // تحديث اسم الحساب المالي في حال تغير اسم المورد
            if ($supplier->treeAccount) {
                $accountUpdateData = [];
                foreach (config('langs') as $locale => $language) {
                    $accountUpdateData[$locale]['name'] = $request->input($locale . '.name');
                }
                $supplier->treeAccount->update($accountUpdateData);
            }

            $this->supplierRepository->update($input, $id);

            DB::commit();

            flash()->success(__('messages.updated', ['model' => __('invoices::models/inv_suppliers.singular')]));
            return redirect()->route('invoices.suppliers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/inv_suppliers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = $this->supplierRepository->find($id);

            if (empty($supplier)) {
                flash()->error(__('invoices::models/inv_suppliers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.suppliers.index'));
            }

            if ($supplier->treeAccount) {
                $supplier->treeAccount->delete();
            }

            $this->supplierRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/inv_suppliers.singular')]));
            return redirect()->route('invoices.suppliers.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/inv_suppliers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->supplierRepository->getHeaders();
        $dataExcel = $this->supplierRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'suppliers.xlsx');
    }

    public function csv()
    {
        $headers = $this->supplierRepository->getHeaders();
        $dataExcel = $this->supplierRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'suppliers.csv');
    }

    public function pdf()
    {
        $headers = $this->supplierRepository->getHeaders();
        $dataExcel = $this->supplierRepository->dataExcel();
        $name = __('invoices::models/inv_suppliers.plural');

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

    public function import(Request $request)
    {
        return view('invoices::suppliers.import');
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new SuppliersImport();
            Excel::import($import, $request->file('file'));
            
            $summary = $import->getImportSummary();
            
            if ($summary['error_count'] > 0) {
                return Excel::download(
                    new SupplierImportErrorsExport($summary['errors']), 
                    'Supplier_Import_Errors_' . now()->format('Y-m-d_H-i') . '.xlsx'
                );
            }

            flash()->success(__('messages.imported', ['model' => __('invoices::models/inv_suppliers.plural')]));
            return redirect()->route('invoices.suppliers.index');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            flash()->error(__('crud.import_errors_message'));
            return redirect()->back()->with('failures', $failures);
        } catch (\Exception $e) {
            flash()->error(__('messages.error_importing', ['model' => __('invoices::models/inv_suppliers.plural')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importTemplate()
    {
        return Excel::download(new SupplierTemplateExport(), 'Supplier_Import_Template.xlsx');
    }
}
