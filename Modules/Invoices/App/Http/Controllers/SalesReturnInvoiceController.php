<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Repositories\SalesReturnInvoiceRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Modules\Invoices\App\Http\Requests\CreateSalesReturnInvoiceRequest;
use Modules\Invoices\App\Http\Requests\UpdateSalesReturnInvoiceRequest;

class SalesReturnInvoiceController extends AppBaseController
{
    private SalesReturnInvoiceRepository $repository;

    public function __construct(SalesReturnInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * قائمة مرتجعات المبيعات
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

        $data['salesReturns'] = $query->paginate($perPage)->appends($request->all());
        return view('invoices::sales_return_invoices.index', $data);
    }

    /**
     * نموذج إنشاء مرتجع جديد
     */
    public function create(Request $request)
    {
        $data = $this->repository->getFormData();
        $data['salesInvoices'] = $this->repository->salesInvoices();

        // في حالة إنشاء المرتجع من فاتورة مبيعات محددة
        $data['selectedParentId'] = $request->query('parent_id');
        if ($data['selectedParentId']) {
            $parent = SalesInvoice::with(['items.product.units', 'customer', 'store'])
                ->find($data['selectedParentId']);

            if ($parent) {
                // تهيئة كائن المرتجع ببيانات الفاتورة الأصلية
                $parent->parent_id      = $parent->id;         // ربط المرتجع بالفاتورة الحالية
                $parent->invoice_number = null;                // تصفير الرقم لتوليد رقم جديد للمرتجع
                $parent->status         = SalesInvoice::STATUS_RETURNED;
                $data['salesReturn'] = $parent;
            }
        }

        return view('invoices::sales_return_invoices.create', $data);
    }

    /**
     * حفظ مرتجع جديد
     */
    public function store(CreateSalesReturnInvoiceRequest $request)
    {
        try {
            $input = $request->all();
            $input['type_inv'] = SalesInvoice::TYPE_RETURN;

            $this->repository->createReturn($input);

            flash()->success(__('messages.saved', ['model' => __('invoices::models/sales_return_invoices.singular')]));
            return redirect()->route('invoices.sales_return.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // التحقق من طلب تأكيد الفاتورة المبسطة
            if (str_starts_with($e->getMessage(), 'CONFIRM_SIMPLIFIED')) {
                $message = explode('|', $e->getMessage())[1] ?? 'بيانات العميل غير مكتملة';
                return redirect()->back()->with('confirm_simplified', $message)->withInput();
            }

            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/sales_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * عرض تفاصيل مرتجع
     */
    public function show($id)
    {
        $salesReturn = SalesInvoice::isReturn()->find($id);

        if (empty($salesReturn)) {
            flash()->error('مرتجع المبيعات غير موجود');
            return redirect(route('invoices.sales_return.index'));
        }

        $salesReturn->load(['items.product.units', 'payments', 'customer', 'parent', 'store', 'branch', 'createdBy']);

        $printFormat = in_array($salesReturn->type_inv, [
            SalesInvoice::TYPE_POS,
            SalesInvoice::TYPE_RETURN_POS
        ]) ? 'Thermal' : 'A4';

        $renderedTemplate = \App\Services\TemplateRenderingService::renderDocument($salesReturn, 'SalesInvoice', $printFormat);

        return view('invoices::sales_return_invoices.show', compact('salesReturn', 'renderedTemplate'));
    }

    /**
     * نموذج تعديل مرتجع
     */
    public function edit($id)
    {
        $salesReturn = SalesInvoice::isReturn()->find($id);

        if (empty($salesReturn)) {
            flash()->error('مرتجع المبيعات غير موجود');
            return redirect(route('invoices.sales_return.index'));
        }

        if ($salesReturn->status != \App\Models\invApp\SalesInvoice::STATUS_DRAFT) {
            flash()->error('لا يمكن تعديل المرتجع بعد ترحيله.');
            return redirect(route('invoices.sales_return.index'));
        }

        $data = $this->repository->getFormData();
        $data['salesInvoices'] = $this->repository->salesInvoices();
        $data['salesReturn'] = $salesReturn;
        $data['salesReturn']->load(['items.product.units', 'payments', 'parent']);

        return view('invoices::sales_return_invoices.edit', $data);
    }

    /**
     * تحديث مرتجع
     */
    public function update(UpdateSalesReturnInvoiceRequest $request, $id)
    {
        try {
            $salesReturn = SalesInvoice::isReturn()->find($id);

            if (empty($salesReturn)) {
                flash()->error(__('invoices::models/sales_return_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.sales_return.index'));
            }

            if ($salesReturn->status != \App\Models\invApp\SalesInvoice::STATUS_DRAFT) {
                flash()->error('لا يمكن تعديل المرتجع بعد ترحيله.');
                return redirect(route('invoices.sales_return.index'));
            }

            $input = $request->all();
            $input['type_inv'] = SalesInvoice::TYPE_RETURN;

            $this->repository->updateReturn($input, $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/sales_return_invoices.singular')]));
            return redirect()->route('invoices.sales_return.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // التحقق من طلب تأكيد الفاتورة المبسطة
            if (str_starts_with($e->getMessage(), 'CONFIRM_SIMPLIFIED')) {
                $message = explode('|', $e->getMessage())[1] ?? 'بيانات العميل غير مكتملة';
                return redirect()->back()->with('confirm_simplified', $message)->withInput();
            }

            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/sales_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * حذف مرتجع
     */
    public function destroy($id)
    {
        try {
            $salesReturn = SalesInvoice::isReturn()->find($id);

            if (empty($salesReturn)) {
                flash()->error(__('invoices::models/sales_return_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.sales_return.index'));
            }

            $this->repository->deleteReturn($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/sales_return_invoices.singular')]));
            return redirect()->route('invoices.sales_return.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/sales_return_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_returns.xlsx');
    }

    public function csv()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_returns.csv');
    }

    public function pdf()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        $name      = 'مرتجعات المبيعات';

        $mpdf = new Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;
        $mpdf->autoArabic       = true;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(
            view('basicdata::exports.pdf', [
                'headers' => $headers,
                'data'    => $dataExcel,
                'name'    => $name,
            ])
        );
        $mpdf->Output();
    }
}

