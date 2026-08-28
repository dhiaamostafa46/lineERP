<?php

namespace Modules\Store\App\Http\Controllers;

use App\Helpers\StockManagementTrait;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\Store\App\Repositories\StReceivingRepository;
use Modules\Store\App\Repositories\StReceivingItemRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;

class StReceivingController extends AppBaseController
{
    use StockManagementTrait;
    private $stReceivingRepository;
    private $stReceivingItemRepository;

    public function __construct(StReceivingRepository $stReceivingRepository, StReceivingItemRepository $stReceivingItemRepository)
    {
        $this->stReceivingRepository = $stReceivingRepository;
        $this->stReceivingItemRepository = $stReceivingItemRepository;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['receivings'] = $this->stReceivingRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stReceivingRepository->statuses();
        $data['stores'] = $this->stReceivingRepository->stores();
        return view('store::receivings.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->stReceivingRepository->statuses();
        $data['stores'] = $this->stReceivingRepository->stores();
        $data['accounts'] = \App\Models\AccuSoft\TreeAccounts::active()->where('is_leaf', true)->get()->pluck('name', 'id')->toArray();
        $data['document_number'] = \Modules\Store\App\Models\StReceiving::generateDocumentNumber();
        return view('store::receivings.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $this->stReceivingRepository->createReceiving($request->all());
            flash()->success(__('messages.saved', ['model' => 'استلام مخزني']));
            return redirect()->route('store.receiving.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $receiving = $this->stReceivingRepository->find($id);
        if (empty($receiving)) {
            flash()->error('سند غير موجود');
            return redirect(route('store.receiving.index'));
        }
        $receiving->load(['items.product', 'items.ProductUnit.unit', 'store', 'account']);
        return view('store::receivings.show')->with('receiving', $receiving);
    }

    public function edit($id)
    {
        $receiving = $this->stReceivingRepository->find($id);
        if (empty($receiving)) {
            flash()->error('سند غير موجود');
            return redirect(route('store.receiving.index'));
        }
        $receiving->load(['items.product', 'items.ProductUnit']);
        $data['receiving'] = $receiving;
        $data['statuses'] = $this->stReceivingRepository->statuses();
        $data['stores'] = $this->stReceivingRepository->stores();
        $data['accounts'] = \App\Models\AccuSoft\TreeAccounts::active()->where('is_leaf', true)->get()->pluck('name', 'id')->toArray();
        return view('store::receivings.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $this->stReceivingRepository->updateReceiving($request->all(), $id);
            flash()->success(__('messages.updated', ['model' => 'استلام مخزني']));
            return redirect()->route('store.receiving.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->stReceivingRepository->delete($id);
            flash()->success(__('messages.deleted', ['model' => 'استلام مخزني']));
            return redirect()->route('store.receiving.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stReceivingRepository->header();
        $dataExcel = $this->stReceivingRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'receivings.xlsx');
    }

    public function csv()
    {
        $headers = $this->stReceivingRepository->header();
        $dataExcel = $this->stReceivingRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'receivings.csv');
    }

    public function pdf()
    {
        $headers = $this->stReceivingRepository->header();
        $dataExcel = $this->stReceivingRepository->dataExel();
        $name = $this->stReceivingRepository->name();

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
