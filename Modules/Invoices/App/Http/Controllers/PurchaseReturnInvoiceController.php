<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Repositories\PurchaseReturnInvoiceRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Modules\Invoices\App\Http\Requests\CreatePurchaseReturnInvoiceRequest;
use Modules\Invoices\App\Http\Requests\UpdatePurchaseReturnInvoiceRequest;

class PurchaseReturnInvoiceController extends AppBaseController
{
    private PurchaseReturnInvoiceRepository $repository;

    public function __construct(PurchaseReturnInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * قائمة مرتجعات المشتريات
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->repository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']))->isReturn();

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['purchaseReturns'] = $query->paginate($perPage)->appends($request->all());

        return view('invoices::purchase_return_invoices.index', $data);
    }

    /**
     * نموذج إنشاء مرتجع جديد
     */
    public function create(Request $request)
    {
        $data = $this->repository->getFormData();
        $data['purchaseInvoices'] = $this->repository->purchaseInvoices();

        // في حالة إنشاء المرتجع من فاتورة مشتريات محددة
        $data['selectedParentId'] = $request->query('parent_id');
        if ($data['selectedParentId']) {
            $parent = PurchaseInvoice::with(['items.product.units', 'supplier', 'store'])->find($data['selectedParentId']);

            if ($parent) {
                // تهيئة كائن المرتجع ببيانات الفاتورة الأصلية
                $parent->parent_id = $parent->id; // ربط المرتجع بالفاتورة الحالية
                $parent->invoice_number = null; // تصفير الرقم لتوليد رقم جديد للمرتجع
                $parent->status = PurchaseInvoice::STATUS_RETURNED;

                $data['purchaseReturn'] = $parent;
            }
        }

        return view('invoices::purchase_return_invoices.create', $data);
    }

    /**
     * حفظ مرتجع جديد
     */
    public function store(CreatePurchaseReturnInvoiceRequest $request)
    {
        try {
            $input = $request->all();
                $input['type_inv'] = PurchaseInvoice::TYPE_RETURN;

            $this->repository->createReturn($input);

            flash()->success(__('messages.saved', ['model' => __('invoices::models/purchase_return_invoices.singular')]));
            return redirect()->route('invoices.purchase_return.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/purchase_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * عرض تفاصيل مرتجع
     */
    public function show($id)
    {
        $purchaseReturn = PurchaseInvoice::isReturn()->find($id);

        if (empty($purchaseReturn)) {
            flash()->error('مرتجع المشتريات غير موجود');
            return redirect(route('invoices.purchase_return.index'));
        }

        $purchaseReturn->load(['items.product.units', 'payments', 'supplier', 'parent', 'store', 'branch', 'createdBy']);

        return view('invoices::purchase_return_invoices.show')->with('purchaseReturn', $purchaseReturn);
    }

    /**
     * نموذج تعديل مرتجع
     */
    public function edit($id)
    {
        $purchaseReturn = PurchaseInvoice::isReturn()->find($id);

        if (empty($purchaseReturn)) {
            flash()->error('مرتجع المشتريات غير موجود');
            return redirect(route('invoices.purchase_return.index'));
        }

        if ($purchaseReturn->status != \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT) {
            flash()->error('مرتجع المشتريات معتمد ولا يمكن تعديله.');
            return redirect(route('invoices.purchase_return.index'));
        }

        $data = $this->repository->getFormData();
        $data['purchaseInvoices'] = $this->repository->purchaseInvoices();
        $data['purchaseReturn'] = $purchaseReturn;
        $data['purchaseReturn']->load(['items.product.units', 'payments', 'parent']);

        return view('invoices::purchase_return_invoices.edit', $data);
    }

    /**
     * تحديث مرتجع
     */
    public function update(UpdatePurchaseReturnInvoiceRequest $request, $id)
    {
        try {
            $purchaseReturn = PurchaseInvoice::isReturn()->find($id);

            if (empty($purchaseReturn)) {
                flash()->error(__('invoices::models/purchase_return_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase_return.index'));
            }

            if ($purchaseReturn->status != \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT) {
                flash()->error('مرتجع المشتريات معتمد ولا يمكن تعديله.');
                return redirect(route('invoices.purchase_return.index'));
            }

            $input = $request->all();
            $input['type_inv'] = PurchaseInvoice::TYPE_RETURN;

            $this->repository->updateReturn($input, $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/purchase_return_invoices.singular')]));
            return redirect()->route('invoices.purchase_return.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/purchase_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * حذف مرتجع
     */
    public function destroy($id)
    {
        try {
            $purchaseReturn = PurchaseInvoice::isReturn()->find($id);

            if (empty($purchaseReturn)) {
                flash()->error(__('invoices::models/purchase_return_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase_return.index'));
            }

            $this->repository->deleteReturn($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/purchase_return_invoices.singular')]));
            return redirect()->route('invoices.purchase_return.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/purchase_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_returns.xlsx');
    }

    public function csv()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_returns.csv');
    }

    public function pdf()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        $name = 'مرتجعات المشتريات';

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
}
