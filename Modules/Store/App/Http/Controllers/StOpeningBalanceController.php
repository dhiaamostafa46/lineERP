<?php

namespace Modules\Store\App\Http\Controllers;

use App\Helpers\StockManagementTrait;
use App\Http\Controllers\AppBaseController;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\StockMovement;
use App\Models\StoreApp\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Http\Requests\CreateStOpeningBalanceRequest;
use Modules\Store\App\Http\Requests\UpdateStOpeningBalanceRequest;
use Modules\Store\App\Models\StOpeningBalance;
use Modules\Store\App\Repositories\StOpeningBalanceItemRepository;
use Modules\Store\App\Repositories\StOpeningBalanceRepository;
use Modules\Store\App\Imports\OpeningBalanceImport;
use Modules\Store\App\Exports\OpeningBalanceTemplateExport;
use Modules\Store\App\Exports\OpeningBalanceImportErrorsExport;

class StOpeningBalanceController extends AppBaseController
{
    use StockManagementTrait;
    private $stOpeningBalanceRepository;
    private $stOpeningBalanceItemRepository;

    public function __construct(StOpeningBalanceRepository $stOpeningBalanceRepository, StOpeningBalanceItemRepository $stOpeningBalanceItemRepository)
    {
        $this->stOpeningBalanceRepository = $stOpeningBalanceRepository;
        $this->stOpeningBalanceItemRepository = $stOpeningBalanceItemRepository;
    }

    /**
     * Display a listing of the Opening Balance.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['openingBalances'] = $this->stOpeningBalanceRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stOpeningBalanceRepository->statuses();
        $data['stores'] = $this->stOpeningBalanceRepository->stores();
        return view('store::OpeningBalances.index', $data);
    }

    /**
     * Show the form for creating a new Opening Balance.
     */
    public function create()
    {
        $data['statuses'] = $this->stOpeningBalanceRepository->statuses();
        $data['stores'] = $this->stOpeningBalanceRepository->stores();
        return view('store::OpeningBalances.create', $data);
    }

    /**
     * Store a newly created Opening Balance in storage.
     */
    /**
     * Store a newly created Opening Balance in storage.
     */
    public function store(CreateStOpeningBalanceRequest $request)
    {
        try {

            // dd($request->all());
            $this->stOpeningBalanceRepository->createOpeningBalance($request->all());

            flash()->success(__('messages.saved', ['model' => __('store::models/st_opening_balances.singular')]));

            return redirect()->route('store.openingbalance.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('store::models/st_opening_balances.singular')]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Update the specified Opening Balance in storage.
     */

    /**
     * Display the specified Opening Balance.
     */
    public function show($id)
    {
        $openingBalance = $this->stOpeningBalanceRepository->find($id);
        $data['openingBalance'] = $openingBalance;
        if (empty($openingBalance)) {
            flash()->error(__('store::models/st_opening_balances.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.openingbalance.index'));
        }

        return view('store::OpeningBalances.show', $data);
    }

    /**
     * Show the form for editing the specified Opening Balance.
     */
    public function edit($id)
    {
        $openingBalance = $this->stOpeningBalanceRepository->find($id);

        if (empty($openingBalance)) {
            flash()->error(__('store::models/st_opening_balances.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.openingbalance.index'));
        }

        // تحميل العلاقات
        $openingBalance->load(['items.product', 'items.ProductUnit']);

        // جلب الكمية الدفترية الحالية لكل صنف
        foreach ($openingBalance->items as $item) {
            $stock = \App\Models\StoreApp\Stock::where('store_id', $openingBalance->store_id)
                ->where('product_id', $item->product_id)
                ->where('is_size', $item->have_sizes)
                ->first();
            $item->system_quantity = $stock ? $stock->current_quantity : 0;
        }

        $data['openingBalance'] = $openingBalance;
        $data['statuses'] = $this->stOpeningBalanceRepository->statuses();
        $data['stores'] = $this->stOpeningBalanceRepository->stores();

        return view('store::OpeningBalances.edit', $data);
    }
    /**
     * Update the specified Opening Balance in storage.
     *
     * @param int $id
     * @param UpdateStOpeningBalanceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateStOpeningBalanceRequest $request, $id)
    {
        try {
            $openingBalance = $this->stOpeningBalanceRepository->find($id);

            if (empty($openingBalance)) {
                flash()->error(__('store::models/st_opening_balances.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.openingbalance.index')); // Fix route typo here
            }

            $this->stOpeningBalanceRepository->updateOpeningBalance($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('store::models/st_opening_balances.singular')]));

            return redirect()->route('store.openingbalance.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('store::models/st_opening_balances.singular')]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Opening Balance from storage.
     */
    public function destroy($id)
    {
        try {
            $openingBalance = $this->stOpeningBalanceRepository->find($id);

            if (empty($openingBalance)) {
                flash()->error(__('store::models/st_opening_balances.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.openingbalance.index'));
            }

            if ($openingBalance->status == StockMovement::STATUS_APPROVED) {
                flash()->error(__('messages.error_deleting', ['model' => __('store::models/st_opening_balances.singular')]) . ': Cannot delete an approved document.');
                return redirect()->back();
            }

            $this->stOpeningBalanceRepository->deleteOpeningBalance($id);

            flash()->success(__('messages.deleted', ['model' => __('store::models/st_opening_balances.singular')]));

            return redirect()->route('store.openingbalance.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('store::models/st_opening_balances.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stOpeningBalanceRepository->header();
        $dataExcel = $this->stOpeningBalanceRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'opening_balances.xlsx');
    }

    public function csv()
    {
        $headers = $this->stOpeningBalanceRepository->header();
        $dataExcel = $this->stOpeningBalanceRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'opening_balances.csv');
    }

    public function pdf()
    {
        $headers = $this->stOpeningBalanceRepository->header();
        $dataExcel = $this->stOpeningBalanceRepository->dataExel();
        $name = $this->stOpeningBalanceRepository->name();

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

    public function import(Request $request)
    {
        if ($request->has('template')) {
            return Excel::download(new OpeningBalanceTemplateExport(), 'Opening_Balance_Import_Template.xlsx');
        }
        return view('store::OpeningBalances.import');
    }

    public function importsave(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '1G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $import = new OpeningBalanceImport();
            Excel::import($import, $request->file('file'));
            
            $summary = $import->getSummary();
            
            if ($summary['error_count'] > 0) {
                flash()->warning("تم استيراد {$summary['success_count']} سجل بنجاح، ووجد {$summary['error_count']} خطأ. يتم الآن تحميل ملف الأخطاء...");
                
                return Excel::download(
                    new OpeningBalanceImportErrorsExport($summary['errors']), 
                    'Opening_Balance_Import_Errors_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                );
            }

            flash()->success("تم استيراد الرصيد الافتتاحي بنجاح ({$summary['success_count']} سجل).");
            return redirect()->route('store.openingbalance.index');

        } catch (\Exception $e) {
            flash()->error("خطأ أثناء الاستيراد: " . $e->getMessage());
            return redirect()->back();
        }
    }
}
