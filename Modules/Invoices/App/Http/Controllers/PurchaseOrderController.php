<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\PurchaseOrderRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Modules\Invoices\App\Models\PurchaseInvoice;
use Modules\Invoices\App\Http\Requests\CreatePurchaseOrderRequest;
use Modules\Invoices\App\Http\Requests\UpdatePurchaseOrderRequest;

class PurchaseOrderController extends AppBaseController
{
    private $purchaseOrderRepository;


    public function __construct(PurchaseOrderRepository $purchaseOrderRepo)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepo;

    }



    /**
     * Display a listing of the purchase invoices.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->purchaseOrderRepository
            ->allQuery($request->except(['pagination', 'sort_by', 'sort_order']))
            ->isInvoice();

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['purchaseOrders'] = $query->paginate($perPage)->appends($request->all());
        return view('invoices::purchase_orders.index', $data);
    }

    /**
     * Show the form for creating a new purchase invoice.
     */
    public function create()
    {
        $data = $this->purchaseOrderRepository->getFormData();
        return view('invoices::purchase_orders.create', $data);
    }

    /**
     * Store a newly created purchase invoice in storage.
     */
    public function store(CreatePurchaseOrderRequest $request)
    {
        // try {

            $this->purchaseOrderRepository->CreatePurchase($request->all());

            flash()->success(__('messages.saved', ['model' => __('invoices::models/purchase_orders.singular')]));
            return redirect()->route('invoices.purchase_orders.index');
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     return redirect()->back()->withErrors($e->errors())->withInput();
        // } catch (\Exception $e) {
        //     flash()->error(__('messages.error_creating', ['model' => __('invoices::models/purchase_orders.singular')]) . ': ' . $e->getMessage());
        //     return redirect()->back()->withInput();
        // }
    }

    /**
     * Display the specified purchase invoice.
     */
    public function show($id)
    {
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            flash()->error(__('invoices::models/purchase_orders.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.purchase_orders.index'));
        }

        $purchaseOrder->load(['items.product.units', 'payments', 'supplier']);

        return view('invoices::purchase_orders.show')->with('purchaseOrder', $purchaseOrder);
    }

    /**
     * Show the form for editing the specified purchase invoice.
     */
    public function edit($id)
    {
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            flash()->error(__('invoices::models/purchase_orders.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.purchase_orders.index'));
        }

        $data = $this->purchaseOrderRepository->getFormData();
        $data['purchaseOrder'] = $purchaseOrder;
        $data['purchaseOrder']->load(['items.product.units', 'payments']);

        return view('invoices::purchase_orders.edit', $data);
    }

    /**
     * Update the specified purchase invoice in storage.
     */
    public function update(UpdatePurchaseOrderRequest $request, $id)
    {
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);

            if (empty($purchaseOrder)) {
                flash()->error(__('invoices::models/purchase_orders.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase_orders.index'));
            }

            $this->purchaseOrderRepository->updatePurchase($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/purchase_orders.singular')]));
            return redirect()->route('invoices.purchase_orders.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/purchase_orders.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified purchase invoice from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);

            if (empty($purchaseOrder)) {
                flash()->error(__('invoices::models/purchase_orders.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.purchase_orders.index'));
            }

            $this->purchaseOrderRepository->deletePurchase($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/purchase_orders.singular')]));
            return redirect()->route('invoices.purchase_orders.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/purchase_orders.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function convertToInvoice($id)
    {
        $purchaseOrder = $this->purchaseOrderRepository->find($id);

        if (empty($purchaseOrder)) {
            flash()->error(__('invoices::models/purchase_orders.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.purchase_orders.index'));
        }

        // تحويل البيانات لفتح صفحة إنشاء فاتورة مشتريات ببيانات هذا الأمر
        return redirect()->route('invoices.purchase.create', ['from_po' => $id]);
    }

    public function excel()
    {
        $headers = $this->purchaseOrderRepository->getHeaders();
        $dataExcel = $this->purchaseOrderRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_orders.xlsx');
    }

    public function csv()
    {
        $headers = $this->purchaseOrderRepository->getHeaders();
        $dataExcel = $this->purchaseOrderRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'purchase_orders.csv');
    }

    public function pdf()
    {
        $headers = $this->purchaseOrderRepository->getHeaders();
        $dataExcel = $this->purchaseOrderRepository->dataExcel();
        $name = __('invoices::models/purchase_orders.plural');

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
