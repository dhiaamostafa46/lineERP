<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\SalesInvoiceRepository;
use App\Models\invApp\SalesInvoiceItem;
use App\Models\invApp\SalesInvoicePayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use App\Services\ProductService;
use Modules\Invoices\App\Http\Requests\CreateSalesInvoiceRequest;
use Modules\Invoices\App\Http\Requests\UpdateSalesInvoiceRequest;

class SalesInvoiceController extends AppBaseController
{
    private $salesInvoiceRepository;

    public function __construct(SalesInvoiceRepository $salesInvoiceRepo)
    {
        $this->salesInvoiceRepository = $salesInvoiceRepo;
    }

    /**
     * Display a listing of the sales invoices.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->salesInvoiceRepository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']));

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $data['salesInvoices'] = $query->paginate($perPage)->appends($request->all());
        return view('invoices::sales_invoices.index', $data);
    }

    /**
     * Show the form for creating a new sales invoice.
     */
    public function create(Request $request)
    {
        $data = $this->salesInvoiceRepository->getFormData();

        

        if ($request->has('quotation_id')) {
            $quotation = \Modules\Invoices\App\Models\Quotation::with(['items.product.units', 'items.unitname'])->find($request->quotation_id);
            if ($quotation) {
                // Prepare a new SalesInvoice object pre-filled with quotation data
                $salesInvoice = new \App\Models\invApp\SalesInvoice();
                $salesInvoice->fill($quotation->toArray());
                $salesInvoice->id = null; // Ensure it's a new record
                $salesInvoice->invoice_number = null;
                $salesInvoice->status = \App\Models\invApp\SalesInvoice::STATUS_DRAFT;

                // Manually set the items relation since fill() doesn't handle relations
                $salesInvoice->setRelation('items', $quotation->items);

                $data['salesInvoice'] = $salesInvoice;
                $data['isFromQuotation'] = true;
            }
        }

        return view('invoices::sales_invoices.create', $data);
    }

    /**
     * Store a newly created sales invoice in storage.
     */
    public function store(CreateSalesInvoiceRequest $request)
    {
         try {
            $this->salesInvoiceRepository->CreateSales($request->all());
            flash()->success(__('messages.saved', ['model' => __('invoices::models/sales_invoices.singular')]));
            return redirect()->route('invoices.sales.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (str_starts_with($message, 'CONFIRM_SIMPLIFIED|')) {
                $actualMessage = substr($message, 19);
                return redirect()->back()->withInput()->with('confirm_simplified', $actualMessage);
            }
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/sales_invoices.singular')]) . ': ' . $message);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified sales invoice.
     */
    public function show($id)
    {
        $salesInvoice = $this->salesInvoiceRepository->find($id);

        if (empty($salesInvoice)) {
            flash()->error(__('invoices::models/sales_invoices.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.sales.index'));
        }

        $salesInvoice->load(['items.product.units', 'payments', 'customer', 'branch', 'createdBy']);

        $printFormat = in_array($salesInvoice->type_inv, [
            \App\Models\invApp\SalesInvoice::TYPE_POS,
            \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS
        ]) ? 'Thermal' : 'A4';

        $renderedTemplate = \App\Services\TemplateRenderingService::renderDocument($salesInvoice, 'SalesInvoice', $printFormat);

        return view('invoices::sales_invoices.show', compact('salesInvoice', 'renderedTemplate'));
    }

    /**
     * Show the form for editing the specified sales invoice.
     */
    public function edit($id)
    {
        $salesInvoice = $this->salesInvoiceRepository->find($id);

        if (empty($salesInvoice)) {
            flash()->error(__('invoices::models/sales_invoices.singular') . ' ' . __('messages.not_found'));
            return redirect(route('invoices.sales.index'));
        }

        if ($salesInvoice->status != \App\Models\invApp\SalesInvoice::STATUS_DRAFT) {
            flash()->error('لا يمكن تعديل الفاتورة بعد ترحيلها.');
            return redirect(route('invoices.sales.index'));
        }

        $data = $this->salesInvoiceRepository->getFormData();
        $data['salesInvoice'] = $salesInvoice;
        $data['salesInvoice']->load(['items.product.units', 'payments']);

        return view('invoices::sales_invoices.edit', $data);
    }

    /**
     * Update the specified sales invoice in storage.
     */
    public function update(UpdateSalesInvoiceRequest $request, $id)
    {
        try {
            $salesInvoice = $this->salesInvoiceRepository->find($id);

            if (empty($salesInvoice)) {
                flash()->error(__('invoices::models/sales_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.sales.index'));
            }

            if ($salesInvoice->status != \App\Models\invApp\SalesInvoice::STATUS_DRAFT) {
                flash()->error('لا يمكن تعديل الفاتورة بعد ترحيلها.');
                return redirect(route('invoices.sales.index'));
            }

            $this->salesInvoiceRepository->updateSales($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/sales_invoices.singular')]));
            return redirect()->route('invoices.sales.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (str_starts_with($message, 'CONFIRM_SIMPLIFIED|')) {
                $actualMessage = substr($message, 19);
                return redirect()->back()->withInput()->with('confirm_simplified', $actualMessage);
            }
            flash()->error(__('messages.error_updating', ['model' => __('invoices::models/sales_invoices.singular')]) . ': ' . $message);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified sales invoice from storage.
     */
    public function destroy($id)
    {
        try {
            $salesInvoice = $this->salesInvoiceRepository->find($id);

            if (empty($salesInvoice)) {
                flash()->error(__('invoices::models/sales_invoices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('invoices.sales.index'));
            }

            $this->salesInvoiceRepository->deleteSales($id);

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/sales_invoices.singular')]));
            return redirect()->route('invoices.sales.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/sales_invoices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->salesInvoiceRepository->getHeaders();
        $dataExcel = $this->salesInvoiceRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_invoices.xlsx');
    }

    public function csv()
    {
        $headers = $this->salesInvoiceRepository->getHeaders();
        $dataExcel = $this->salesInvoiceRepository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_invoices.csv');
    }

    public function pdf()
    {
        $headers = $this->salesInvoiceRepository->getHeaders();
        $dataExcel = $this->salesInvoiceRepository->dataExcel();
        $name = __('invoices::models/sales_invoices.plural');

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

