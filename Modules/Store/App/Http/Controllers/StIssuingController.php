<?php

namespace Modules\Store\App\Http\Controllers;

use App\Helpers\StockManagementTrait;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Store\App\Repositories\StIssuingRepository;
use Modules\Store\App\Repositories\StIssuingItemRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;

class StIssuingController extends AppBaseController
{
    use StockManagementTrait;
    private $stIssuingRepository;
    private $stIssuingItemRepository;

    public function __construct(StIssuingRepository $stIssuingRepository, StIssuingItemRepository $stIssuingItemRepository)
    {
        $this->stIssuingRepository = $stIssuingRepository;
        $this->stIssuingItemRepository = $stIssuingItemRepository;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['issuings'] = $this->stIssuingRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stIssuingRepository->statuses();
        $data['stores'] = $this->stIssuingRepository->stores();
        return view('store::issuings.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->stIssuingRepository->statuses();
        $data['stores'] = $this->stIssuingRepository->stores();
        $data['accounts'] = \App\Models\AccuSoft\TreeAccounts::active()->where('is_leaf', true)->get()->pluck('name', 'id')->toArray();
        $data['document_number'] = \Modules\Store\App\Models\StIssuing::generateDocumentNumber();
        return view('store::issuings.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $this->stIssuingRepository->createIssuing($request->all());
            flash()->success(__('messages.saved', ['model' => 'صرف مخزني']));
            return redirect()->route('store.issuing.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $issuing = $this->stIssuingRepository->find($id);
        if (empty($issuing)) {
            flash()->error('سند غير موجود');
            return redirect(route('store.issuing.index'));
        }
        $issuing->load(['items.product', 'items.ProductUnit.unit', 'store', 'account']);
        return view('store::issuings.show')->with('issuing', $issuing);
    }

    public function edit($id)
    {
        $issuing = $this->stIssuingRepository->find($id);
        if (empty($issuing)) {
            flash()->error('سند غير موجود');
            return redirect(route('store.issuing.index'));
        }
        $issuing->load(['items.product', 'items.ProductUnit']);
        $data['issuing'] = $issuing;
        $data['statuses'] = $this->stIssuingRepository->statuses();
        $data['stores'] = $this->stIssuingRepository->stores();
        $data['accounts'] = \App\Models\AccuSoft\TreeAccounts::active()->where('is_leaf', true)->get()->pluck('name', 'id')->toArray();
        return view('store::issuings.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $this->stIssuingRepository->updateIssuing($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => 'صرف مخزني']));
            return redirect()->route('store.issuing.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->stIssuingRepository->delete($id);
            flash()->success(__('messages.deleted', ['model' => 'صرف مخزني']));
            return redirect()->route('store.issuing.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stIssuingRepository->header();
        $dataExcel = $this->stIssuingRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'issuings.xlsx');
    }

    public function csv()
    {
        $headers = $this->stIssuingRepository->header();
        $dataExcel = $this->stIssuingRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'issuings.csv');
    }

    public function pdf()
    {
        $headers = $this->stIssuingRepository->header();
        $dataExcel = $this->stIssuingRepository->dataExel();
        $name = $this->stIssuingRepository->name();

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
