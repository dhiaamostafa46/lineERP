<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Invoices\App\Models\Quotation;
use Modules\Invoices\App\Repositories\QuotationRepository;
use Modules\Invoices\App\Http\Requests\CreateQuotationRequest;
use Modules\Invoices\App\Http\Requests\UpdateQuotationRequest;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;

class QuotationController extends AppBaseController
{
    private QuotationRepository $repository;

    public function __construct(QuotationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $search = $request->except(['pagination', 'sort_by', 'sort_order']);
        $pagination = (int) $request->input('pagination', 15) ?: 15;

        $query = $this->repository->allQuery($search)
            ->with(['customer', 'createdBy']);

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $quotations = $query->paginate($pagination)->appends($request->all());

        $statuses = Quotation::statuses();

        return view('invoices::quotations.index', compact('quotations', 'statuses'));
    }

    public function create()
    {
        $data = $this->repository->getFormData();
        return view('invoices::quotations.create', $data);
    }

    public function store(CreateQuotationRequest $request)
    {
         try {
            $quotation = $this->repository->store($request->all());

            flash()->success(__('messages.saved', ['model' => __('invoices::models/quotations.singular')]));
            return redirect()->route('invoices.quotations.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/quotations.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $quotation = Quotation::with(['items.product.units', 'customer', 'branch', 'store', 'createdBy'])->findOrFail($id);

        $renderedTemplate = \App\Services\TemplateRenderingService::renderDocument($quotation, 'Quotation', 'A4');

        return view('invoices::quotations.show', compact('quotation', 'renderedTemplate'));
    }

    public function edit($id)
    {
        $quotation = Quotation::with(['items.product.units', 'customer'])->findOrFail($id);

        if (!in_array($quotation->status, [Quotation::STATUS_NEW, Quotation::STATUS_SENT])) {
            flash()->error(__('invoices::models/quotations.messages.cannot_edit'));
            return redirect()->route('invoices.quotations.index');
        }

        $data = $this->repository->getFormData();
        $data['quotation'] = $quotation;

        return view('invoices::quotations.edit', $data);
    }

    public function update(UpdateQuotationRequest $request, $id)
    {
        // try {
            $quotation = Quotation::findOrFail($id);

            if (!in_array($quotation->status, [Quotation::STATUS_NEW, Quotation::STATUS_SENT])) {
                flash()->error(__('invoices::models/quotations.messages.cannot_edit'));
                return redirect()->route('invoices.quotations.index');
            }

            $this->repository->update($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/quotations.singular')]));
            return redirect()->route('invoices.quotations.index');
        // } catch (\Exception $e) {
        //     flash()->error($e->getMessage());
        //     return redirect()->back()->withInput();
        // }
    }

    public function destroy($id)
    {
        try {
            $quotation = Quotation::findOrFail($id);

            if ($quotation->status === Quotation::STATUS_CONVERTED) {
                flash()->error(__('invoices::models/quotations.messages.cannot_delete_converted'));
                return redirect()->route('invoices.quotations.index');
            }

            $quotation->items()->delete();
            $quotation->delete();

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/quotations.singular')]));
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
        }

        return redirect()->route('invoices.quotations.index');
    }

    /**
     * تحويل عرض السعر إلى فاتورة مبيعات
     */
    public function convertToInvoice($id)
    {
        try {
            $quotation = Quotation::with(['items', 'customer'])->findOrFail($id);

            if ($quotation->status === Quotation::STATUS_CONVERTED) {
                flash()->error(__('invoices::models/quotations.messages.already_converted'));
                return redirect()->route('invoices.quotations.index');
            }

            // Mark as converted (Removed: status will be updated upon saving the invoice)
            // $quotation->update(['status' => Quotation::STATUS_CONVERTED]);

            // Redirect to create sales invoice with quotation data pre-filled
            $params = ['quotation_id' => $quotation->id];
            flash()->success(__('invoices::models/quotations.messages.converted_success'));
            return redirect()->route('invoices.sales.create', $params);
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * تغيير حالة عرض السعر
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:new,sent,accepted,rejected,expired,converted']);

        $quotation = Quotation::findOrFail($id);
        $quotation->update(['status' => $request->status]);

        flash()->success(__('messages.updated', ['model' => __('invoices::models/quotations.singular')]));
        return redirect()->back();
    }

     public function excel()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'quotations.xlsx');
    }

    public function csv()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'quotations.csv');
    }

    public function pdf()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        $name = __('invoices::models/quotations.plural');

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
