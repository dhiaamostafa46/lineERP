<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Modules\Invoices\App\Repositories\SalesDebitNoteRepository;
use App\Models\invApp\SalesInvoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;
use Modules\Invoices\App\Http\Requests\CreateSalesDebitNoteRequest;
use Modules\Invoices\App\Http\Requests\UpdateSalesDebitNoteRequest;
use Illuminate\Validation\ValidationException;
use Exception;

class SalesDebitNoteController extends AppBaseController
{
    protected SalesDebitNoteRepository $repository;

    public function __construct(SalesDebitNoteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the Debit Notes.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $query = $this->repository->allQuery($request->except(['pagination', 'sort_by', 'sort_order']));

        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $debitNotes = $query->paginate($perPage)->appends($request->all());

        return view('invoices::sales_debit_notes.index', compact('debitNotes'));
    }

    /**
     * Show the form for creating a new Debit Note.
     */
    public function create(Request $request)
    {
        $data = $this->repository->getFormData();
        $data['parentInvoice'] = null;
        
        if ($request->has('parent_id')) {
            $data['parentInvoice'] = SalesInvoice::with(['items.product.units', 'customer', 'store'])->find($request->parent_id);
            if ($data['parentInvoice']) {
                $data['debitNote'] = clone $data['parentInvoice'];
                $data['debitNote']->invoice_number = null;
                $data['debitNote']->status = SalesInvoice::STATUS_DRAFT;
            }
        }

        return view('invoices::sales_debit_notes.create', $data);
    }

    /**
     * Store a newly created Debit Note in storage.
     */
    public function store(CreateSalesDebitNoteRequest $request)
    {
        try {
            $this->repository->store($request->all());

            flash()->success(__('messages.saved', ['model' => __('invoices::models/sales_debit_notes.singular')]));
            return redirect(route('invoices.sales_debit.index'));

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {

          
            // التحقق من طلب تأكيد الفاتورة المبسطة
            if (str_starts_with($e->getMessage(), 'CONFIRM_SIMPLIFIED')) {
                $message = explode('|', $e->getMessage())[1] ?? 'بيانات العميل غير مكتملة';
                return redirect()->back()->with('confirm_simplified', $message)->withInput();
            }

            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/sales_debit_notes.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Debit Note.
     */
    public function show($id)
    {
        $debitNote = SalesInvoice::where('type_inv', SalesInvoice::TYPE_DEBIT_NOTE)->findOrFail($id);
        $debitNote->load(['items.product.units', 'payments', 'customer', 'parent', 'store', 'branch', 'createdBy']);

        $renderedTemplate = \App\Services\TemplateRenderingService::renderDocument($debitNote, 'SalesInvoice', 'A4');

        return view('invoices::sales_debit_notes.show', compact('debitNote', 'renderedTemplate'));
    }

    /**
     * Show the form for editing the specified Debit Note.
     */
    public function edit($id)
    {
        $debitNote = SalesInvoice::where('type_inv', SalesInvoice::TYPE_DEBIT_NOTE)->findOrFail($id);
        
        if ($debitNote->status != SalesInvoice::STATUS_DRAFT) {
            flash()->error('لا يمكن تعديل الإشعار المدين بعد اعتماده.');
            return redirect(route('invoices.sales_debit.index'));
        }

        $data = $this->repository->getFormData();
        $data['debitNote'] = $debitNote;
        $data['debitNote']->load(['items.product.units', 'payments', 'parent']);

        return view('invoices::sales_debit_notes.edit', $data);
    }

    /**
     * Update the specified Debit Note.
     */
    public function update(UpdateSalesDebitNoteRequest $request, $id)
    {
        try {
            $this->repository->update($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('invoices::models/sales_debit_notes.singular')]));
            return redirect(route('invoices.sales_debit.index'));

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // التحقق من طلب تأكيد الفاتورة المبسطة
            if (str_starts_with($e->getMessage(), 'CONFIRM_SIMPLIFIED')) {
                $message = explode('|', $e->getMessage())[1] ?? 'بيانات العميل غير مكتملة';
                return redirect()->back()->with('confirm_simplified', $message)->withInput();
            }

            flash()->error(__('messages.error_creating', ['model' => __('invoices::models/sales_debit_notes.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Debit Note from storage.
     */
    public function destroy($id)
    {
        try {
            $debitNote = SalesInvoice::where('type_inv', SalesInvoice::TYPE_DEBIT_NOTE)->findOrFail($id);

            if ($debitNote->status != SalesInvoice::STATUS_DRAFT) {
                flash()->error('لا يمكن حذف الإشعار المدين بعد اعتماده.');
                return redirect(route('invoices.sales_debit.index'));
            }

            $debitNote->delete();

            flash()->success(__('messages.deleted', ['model' => __('invoices::models/sales_debit_notes.singular')]));
            return redirect(route('invoices.sales_debit.index'));
        } catch (Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('invoices::models/sales_debit_notes.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_debit_notes.xlsx');
    }

    public function csv()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'sales_debit_notes.csv');
    }

    public function pdf()
    {
        $headers   = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        $name      = __('invoices::messages.debit_notes');

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

