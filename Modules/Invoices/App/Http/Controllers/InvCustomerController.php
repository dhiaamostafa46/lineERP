<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\AccuSoft\AccountMapping; // ✅ إضافة الاستيراد
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\InvCustomerRepository;
use App\Models\Branch;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Modules\Invoices\App\Http\Requests\CreateInvCustomerRequest;
use Modules\Invoices\App\Http\Requests\UpdateInvCustomerRequest;
use Modules\Invoices\App\Imports\CustomersImport;
use Modules\Invoices\App\Exports\CustomerTemplateExport;
use Modules\Invoices\App\Exports\CustomerImportErrorsExport;

class InvCustomerController extends AppBaseController
{
    private $customerRepository;
    private $asTreeAccountsRepository;

    public function __construct(InvCustomerRepository $customerRepo, AsTreeAccountsRepository $asTreeAccountsRepo)
    {
        $this->customerRepository = $customerRepo;
        $this->asTreeAccountsRepository = $asTreeAccountsRepo;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->customerRepository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']));

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['customers'] = $query->paginate($perPage)->appends($request->all());
        return view('invoices::customers.index', $data);
    }

    public function create()
    {
        return view('invoices::customers.create');
    }

    public function store(CreateInvCustomerRequest $request)
    {
        try {
            $input = $request->all();

            DB::beginTransaction();

            // 1. تحديد الحساب الأب للعملاء من الربط المحاسبي (مثلاً: حساب المدينون)
            $parentId = AccountMapping::getAccountId('customer');

            if (!$parentId) {
                throw new \Exception('يرجى ضبط الحساب الأب للعملاء في إعدادات الربط المحاسبي أولاً.');
            }

            // 2. إنشاء سجل الحساب المالي في شجرة الحسابات
            $parentAccount = TreeAccounts::find($parentId);
            $accountData = [
                'parent_id' => $parentId,
                'account_type' => TreeAccounts::ACCOUNT_TYPE_CUSTOMERS, // Assets - أصول
                'type' => 1, // Debit - مدين
                'is_leaf' => true,
                'level' => $parentAccount ? $parentAccount->level + 1 : 1,
                'status' => 1, // Active
                'code' => TreeAccounts::generateCode($parentId),
            ];

            // إضافة ترجمة اسم الحساب (يأخذ نفس اسم العميل المدخل)
            foreach (config('langs') as $locale => $language) {
                $accountData[$locale]['name'] = $request->input($locale . '.name');
            }

            $treeAccount = TreeAccounts::create($accountData);

            // 3. ربط العميل بالحساب المالي الجديد وحفظ سجل العميل
            $input['tree_account_id'] = $treeAccount->id;
            $this->customerRepository->create($input);

            DB::commit();

            flash()->success(__('messages.saved', ['model' => __('invoices::models/customers.singular')]));
            return redirect()->route('invoices.customers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/customers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $customer = $this->customerRepository->find($id);

        if (empty($customer)) {
            flash()->error(__('invoices::models/customers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.customers.index'));
        }

        return view('invoices::customers.show')->with('customer', $customer);
    }

    public function edit($id)
    {
        $data['customer'] = $this->customerRepository->find($id);

        if (empty($data['customer'])) {
            flash()->error(__('invoices::models/customers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.customers.index'));
        }
        return view('invoices::customers.edit', $data);
    }

    public function update(UpdateInvCustomerRequest $request, $id)
    {
        try {
            $customer = $this->customerRepository->find($id);

            if (empty($customer)) {
                flash()->error(__('invoices::models/customers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.customers.index'));
            }

            $input = $request->all();

            DB::beginTransaction();

            // تحديث اسم الحساب المالي في حال تغير اسم العميل
            if ($customer->treeAccount) {
                $accountUpdateData = [];
                foreach (config('langs') as $locale => $language) {
                    $accountUpdateData[$locale]['name'] = $request->input($locale . '.name');
                }
                $customer->treeAccount->update($accountUpdateData);
            }

            $this->customerRepository->update($input, $id);

            DB::commit();

            flash()->success(__('messages.updated', ['model' => __('invoices::models/customers.singular')]));
            return redirect()->route('invoices.customers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/customers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $customer = $this->customerRepository->find($id);

            if (empty($customer)) {
                flash()->error(__('invoices::models/customers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.customers.index'));
            }
            $customer->treeAccount->delete();

            if ($customer->treeAccount) {
                $customer->treeAccount->delete();
            }

            $this->customerRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/customers.singular')]));
            return redirect()->route('invoices.customers.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/customers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->customerRepository->getHeaders();
        $dataExcel = $this->customerRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'customers.xlsx');
    }

    public function csv()
    {
        $headers = $this->customerRepository->getHeaders();
        $dataExcel = $this->customerRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'customers.csv');
    }

    public function pdf()
    {
        $headers = $this->customerRepository->getHeaders();
        $dataExcel = $this->customerRepository->dataExcel();
        $name = __('invoices::models/customers.plural');

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
        return view('invoices::customers.import');
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new CustomersImport();
            Excel::import($import, $request->file('file'));
            
            $summary = $import->getImportSummary();
            
            if ($summary['error_count'] > 0) {
                return Excel::download(
                    new CustomerImportErrorsExport($summary['errors']), 
                    'Customer_Import_Errors_' . now()->format('Y-m-d_H-i') . '.xlsx'
                );
            }

            flash()->success(__('messages.imported', ['model' => __('invoices::models/inv_customers.plural')]));
            return redirect()->route('invoices.customers.index');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            flash()->error(__('crud.import_errors_message'));
            return redirect()->back()->with('failures', $failures);
        } catch (\Exception $e) {
            flash()->error(__('messages.error_importing', ['model' => __('invoices::models/inv_customers.plural')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function importTemplate()
    {
        return Excel::download(new CustomerTemplateExport(), 'Customer_Import_Template.xlsx');
    }
}
