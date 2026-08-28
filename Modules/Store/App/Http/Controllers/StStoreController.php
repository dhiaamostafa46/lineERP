<?php

namespace Modules\Store\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Http\Requests\CreateStStoreRequest;
use Modules\Store\App\Http\Requests\UpdateStStoreRequest;
use Modules\Store\App\Repositories\StStoreRepository;

// Assuming a generic export class exists or you will create one for the Store module.
// Example, adjust if you have a specific export class

class StStoreController extends AppBaseController
{
    /** @var StStoreRepository */
    private $stStoreRepository;

    public function __construct(StStoreRepository $stStoreRepo)
    {
        $this->stStoreRepository = $stStoreRepo;
    }

    /**
     * Display a listing of the Store.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['stores'] = $this->stStoreRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stStoreRepository->statuses();
        $data['types'] = $this->stStoreRepository->types();

        return view('store::stores.index', $data);
    }

    /**
     * Show the form for creating a new Store.
     */
    public function create()
    {
        $data['statuses'] = $this->stStoreRepository->statuses();
        $data['branches'] = $this->stStoreRepository->branches();
        $data['managers'] = $this->stStoreRepository->managers();
        $data['types'] = $this->stStoreRepository->types();

        return view('store::stores.create', $data);
    }

    /**
     * Store a newly created Store in storage.
     */
    public function store(CreateStStoreRequest $request)
    {
        try {
            $input = $request->all();

            DB::beginTransaction();

            // 1. تحديد الحساب الأب للمخازن من الربط المحاسبي (مثلاً: حساب المخزون)
            $parentId = AccountMapping::getAccountId('inventory');

            if (! $parentId) {
                throw new \Exception(__('يرجى ضبط الحساب الأب للمخازن (المخزون) في إعدادات الربط المحاسبي أولاً.'));
            }

            // 2. إنشاء سجل الحساب المالي في شجرة الحسابات
            $accountData = [
                'parent_id' => $parentId,
                'account_type' => TreeAccounts::ACCOUNT_TYPE_INVENTORY, // Inventory - مخزون
                'type' => TreeAccounts::TYPE_DEBIT, // Debit - مدين
                'is_leaf' => true,
                'status' => TreeAccounts::STATUS_ACTIVE,
                'code' => TreeAccounts::generateCode($parentId),
            ];

            // إضافة ترجمة اسم الحساب (يأخذ نفس اسم المستودع المدخل)
            foreach (config('langs') as $locale => $language) {
                $accountData[$locale]['name'] = $request->input($locale.'.name');
            }

            $treeAccount = TreeAccounts::create($accountData);

            // 3. ربط المستودع بالحساب المالي الجديد وحفظ سجل المستودع
            $input['tree_account_id'] = $treeAccount->id;
            $this->stStoreRepository->create($input);

            DB::commit();

            flash()->success(__('messages.saved', ['model' => __('store::models/St_stores.singular')]));

            return redirect()->route('store.stores.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_creating', ['model' => __('store::models/St_stores.singular')]).': '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Store.
     */
    public function show($id)
    {
        $store = $this->stStoreRepository->find($id);

        if (empty($store)) {
            flash()->error(__('store::models/St_stores.singular').' '.__('messages.not_found'));

            return redirect(route('store.stores.index'));
        }

        return view('store::stores.show')->with('store', $store);
    }

    /**
     * Show the form for editing the specified Store.
     */
    public function edit($id)
    {
        $store = $this->stStoreRepository->find($id);
        $data['statuses'] = $this->stStoreRepository->statuses();
        $data['store'] = $store;
        $data['branches'] = $this->stStoreRepository->branches();
        $data['managers'] = $this->stStoreRepository->managers();
        $data['types'] = $this->stStoreRepository->types();

        if (empty($store)) {
            flash()->error(__('store::models/St_stores.singular').' '.__('messages.not_found'));

            return redirect(route('store.stores.index'));
        }

        return view('store::stores.edit', $data);
    }

    /**
     * Update the specified Store in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateStStoreRequest $request, $id)
    {
        try {
            $store = $this->stStoreRepository->find($id);

            if (empty($store)) {
                flash()->error(__('store::models/St_stores.singular').' '.__('messages.not_found'));

                return redirect(route('store.stores.index'));
            }

            $input = $request->all();

            DB::beginTransaction();

            // تحديث اسم الحساب المالي في حال تغير اسم المستودع
            if ($store->treeAccount) {
                $accountUpdateData = [];
                foreach (config('langs') as $locale => $language) {
                    $accountUpdateData[$locale]['name'] = $request->input($locale.'.name');
                }
                $store->treeAccount->update($accountUpdateData);
            }

            $this->stStoreRepository->update($input, $id);

            DB::commit();

            flash()->success(__('messages.updated', ['model' => __('store::models/St_stores.singular')]));

            return redirect()->route('store.stores.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_updating', ['model' => __('store::models/St_stores.singular')]).': '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Store from storage.
     */
    public function destroy($id)
    {
        try {
            $store = $this->stStoreRepository->find($id);

            if (empty($store)) {
                flash()->error(__('store::models/St_stores.singular').' '.__('messages.not_found'));

                return redirect(route('store.stores.index'));
            }

            DB::beginTransaction();

            // حذف الحساب المالي المرتبط بالمستودع
            if ($store->treeAccount) {
                $store->treeAccount->delete();
            }

            $this->stStoreRepository->delete($id);

            DB::commit();

            flash()->success(__('messages.deleted', ['model' => __('store::models/St_stores.singular')]));

            return redirect()->route('store.stores.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_deleting', ['model' => __('store::models/St_stores.singular')]).': '.$e->getMessage());

            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stStoreRepository->header();
        $dataExcel = $this->stStoreRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'stores.xlsx');
    }

    public function csv()
    {
        $headers = $this->stStoreRepository->header();
        $dataExcel = $this->stStoreRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'stores.csv');
    }

    public function pdf()
    {
        $headers = $this->stStoreRepository->header();
        $dataExcel = $this->stStoreRepository->dataExel();
        $name = $this->stStoreRepository->name();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;

        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;

        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->keep_table_proportions = true;

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->list_indent_first_level = 0;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(view('exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
