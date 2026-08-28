<?php

namespace Modules\Store\App\Http\Controllers;

use App\Helpers\StockManagementTrait;
use App\Http\Controllers\AppBaseController;
use App\Models\StoreApp\StockMovement;
use Illuminate\Http\Request;
use Modules\Store\App\Exports\StoreExport;
use Modules\Store\App\Http\Requests\CreateStStoreRequest;
use Modules\Store\App\Http\Requests\CreateStDamagedRequest;
use Modules\Store\App\Http\Requests\UpdateStStoreRequest;
use Modules\Store\App\Http\Requests\UpdateStDamagedRequest;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Store\App\Repositories\StDamagedItemRepository;
use Modules\Store\App\Repositories\StDamagedRepository;
use Illuminate\Support\Facades\DB;
use App\Models\StoreApp\Store;
use Modules\Store\App\Models\StDamaged;

class StDamagedController extends AppBaseController
{
    use StockManagementTrait;
    private $stDamagedRepository;
    private $stDamagedItemRepository;

    public function __construct(StDamagedRepository $stDamagedRepository, StDamagedItemRepository $stDamagedItemRepository)
    {
        $this->stDamagedRepository = $stDamagedRepository;
        $this->stDamagedItemRepository = $stDamagedItemRepository;
    }

    /**
     * 
     * Display a listing of the Opening Balance.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('pagination', 10) ?: 10;
        $data['damageds'] = $this->stDamagedRepository
            ->allQuery($request->except('pagination'))
            ->latest()
            ->paginate($perPage)
            ->appends($request->all());
        $data['statuses'] = $this->stDamagedRepository->statuses();
        $data['stores'] = $this->stDamagedRepository->stores();
        return view('store::damageds.index', $data);
    }

    /**
     * Show the form for creating a new Damaged.
     */
    public function create()
    {
        $data['statuses'] = $this->stDamagedRepository->statuses();
        //$data['products'] = $this->stDamagedRepository->products();
        $data['stores'] = $this->stDamagedRepository->stores();
        return view('store::damageds.create', $data);
    }

    /**
     * Store a newly created Damaged in storage.
     */
    /**
     * Store a newly created Damaged in storage.
     */
    public function store(CreateStDamagedRequest $request)
    {
        try {

            // dd($request->all());
            $this->stDamagedRepository->createDamaged($request->all());

            flash()->success(__('messages.saved', ['model' => __('store::models/st_damageds.singular')]));

            return redirect()->route('store.damaged.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('store::models/st_damageds.singular')]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Damaged.
     */
    public function show($id)
    {
        $damaged = $this->stDamagedRepository->find($id);
        $data['damaged'] = $damaged;
        if (empty($damaged)) {
            flash()->error(__('store::models/st_damageds.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.damaged.index'));
        }

        return view('store::damageds.show', $data);
    }

    /**
     * Show the form for editing the specified Damaged.
     */
    public function edit($id)
    {
        $damaged = $this->stDamagedRepository->find($id);

        if (empty($damaged)) {
            flash()->error(__('store::models/st_damageds.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.damaged.index'));
        }

        // تحميل العلاقات بكفاءة لتجنب N+1
        $damaged->load([
            'items.product.units.unit',
            'items.productSize.product.units.unit',
            'items.ProductUnit.unit'
        ]);
        $data['damaged'] = $damaged;
        $data['statuses'] = $this->stDamagedRepository->statuses();
        $data['stores'] = $this->stDamagedRepository->stores();


        return view('store::damageds.edit', $data);
    }

    /**
     * Update the specified Damaged in storage.
     *
     * @param int $id
     * @param UpdateStDamagedRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateStDamagedRequest $request, $id)
    {
        // try {

        //     dd( $request->all() );   
        $damaged = $this->stDamagedRepository->find($id);

        if (empty($damaged)) {
            flash()->error(__('store::models/st_damageds.singular') . ' ' . __('messages.not_found'));
            return redirect(route('store.damaged.index'));
        }

        $this->stDamagedRepository->updateDamaged($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('store::models/st_damageds.singular')]));

        return redirect()->route('store.damaged.index');
        // } catch (\Exception $e) {
        //     flash()->error(__('messages.error_updating', ['model' => __('store::models/st_damageds.singular')]) . ': ' . $e->getMessage());

        //     return redirect()->back()->withInput();
        // }
    }

    /**
     * Remove the specified Damaged from storage.
     */
    public function destroy($id)
    {
        try {
            $damaged = $this->stDamagedRepository->find($id);

            if (empty($damaged)) {
                flash()->error(__('store::models/st_damageds.singular') . ' ' . __('messages.not_found'));
                return redirect(route('store.damaged.index'));
            }

            $this->stDamagedRepository->deleteDamaged($id);

            flash()->success(__('messages.deleted', ['model' => __('store::models/st_damageds.singular')]));

            return redirect()->route('store.damaged.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('store::models/st_damageds.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->stDamagedRepository->header();
        $dataExcel = $this->stDamagedRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'damageds.xlsx');
    }

    public function csv()
    {
        $headers = $this->stDamagedRepository->header();
        $dataExcel = $this->stDamagedRepository->dataExel();

        return Excel::download(new StoreExport($dataExcel, $headers), 'damageds.csv');
    }

    public function pdf()
    {
        $headers = $this->stDamagedRepository->header();
        $dataExcel = $this->stDamagedRepository->dataExel();
        $name = $this->stDamagedRepository->name();

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
