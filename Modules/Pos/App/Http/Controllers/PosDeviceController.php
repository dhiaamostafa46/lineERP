<?php

namespace Modules\Pos\App\Http\Controllers;

use Exception;
use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\StoreApp\Store;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\AppBaseController;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\Pos\App\Models\PosPaymentMethod;
use Modules\Pos\App\Services\PosDeviceService;
use Modules\Pos\App\Repositories\PosDeviceRepository;
use Modules\Pos\App\Http\Requests\CreatePosDeviceRequest;
use Modules\Pos\App\Http\Requests\UpdatePosDeviceRequest;

class PosDeviceController extends AppBaseController
{
    private $posDeviceRepository;
    private $posDeviceService;

    public function __construct(PosDeviceRepository $posDeviceRepo, PosDeviceService $posDeviceService)
    {
        $this->posDeviceRepository = $posDeviceRepo;
        $this->posDeviceService = $posDeviceService;
    }

    /**
     * Display a listing of the PosDevice.
     */
    public function index(Request $request)
    {
        $data['devices'] = $this->posDeviceRepository->allQuery($request->except('pagination'))->latest()->paginate($request->input('pagination', 10));
        return view('pos::devices.index', $data);
    }

    /**
     * Show the form for creating a new PosDevice.
     */
    public function create()
    {
        // Determine default branch and store
        $defaultBranch = Branch::where('status', 1)->where('is_main', 1)->first() 
            ?? Branch::where('status', 1)->first() 
            ?? Branch::first();

        $defaultStore = $defaultBranch ? Store::where('branch_id', $defaultBranch->id)->first() : null;

        $data = $this->posDeviceService->getFormData();
        $data['users'] = User::pluck('name', 'id')->toArray();
        $data['fixedMethods'] = PosPaymentMethod::types();
        $data['deviceMethods'] = collect();
        
        $data['defaultBranchId'] = $defaultBranch ? $defaultBranch->id : null;
        $data['defaultStoreId'] = $defaultStore ? $defaultStore->id : null;

        return view('pos::devices.create', $data);
    }

    /**
     * Store a newly created PosDevice in storage.
     */
    public function store(CreatePosDeviceRequest $request)
    {
        try {
            $input = $request->all();
            
            $this->posDeviceService->createDevice($input, $request);

            flash()->success(__('messages.saved', ['model' => __('pos::models/devices.singular')]));

            return redirect()->route('pos.devices.index');
        } catch (Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('pos::models/devices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified PosDevice.
     */
    public function edit($id)
    {
        $device = $this->posDeviceRepository->find($id);
        
        if (empty($device)) {
            flash()->error(__('pos::models/devices.singular') . ' ' . __('messages.not_found'));
            return redirect(route('pos.devices.index'));
        }

        // Ensure legacy devices have the 5 payment methods
        $this->posDeviceService->ensurePaymentMethodsExist($device);
        
        $device->load('paymentMethods');

        $data = $this->posDeviceService->getFormData();
        $data['device'] = $device;
        $data['users'] = User::pluck('name', 'id')->toArray();
        $data['fixedMethods'] = PosPaymentMethod::types();
        $data['deviceMethods'] = $device->paymentMethods->keyBy('type');

        return view('pos::devices.edit', $data);
    }

    /**
     * Update the specified PosDevice in storage.
     */
    public function update(UpdatePosDeviceRequest $request, $id)
    {
        try {
            $device = $this->posDeviceRepository->find($id);

            if (empty($device)) {
                flash()->error(__('pos::models/devices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('pos.devices.index'));
            }

            $input = $request->all();
            
            $this->posDeviceService->updateDevice($id, $input, $request);

            flash()->success(__('messages.updated', ['model' => __('pos::models/devices.singular')]));

            return redirect()->route('pos.devices.index');
        } catch (Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('pos::models/devices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified PosDevice from storage.
     */
    public function destroy($id)
    {
        try {
            $device = $this->posDeviceRepository->find($id);

            if (empty($device)) {
                flash()->error(__('pos::models/devices.singular') . ' ' . __('messages.not_found'));
                return redirect(route('pos.devices.index'));
            }

            $this->posDeviceRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('pos::models/devices.singular')]));

            return redirect()->route('pos.devices.index');
        } catch (Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('pos::models/devices.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->posDeviceRepository->header();
        $dataExcel = $this->posDeviceRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'devices.xlsx');
    }

    public function csv()
    {
        $headers = $this->posDeviceRepository->header();
        $dataExcel = $this->posDeviceRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'devices.csv');
    }

    public function pdf()
    {
        $headers = $this->posDeviceRepository->header();
        $dataExcel = $this->posDeviceRepository->dataExel();
        $name = $this->posDeviceRepository->name();

        $mpdf = new Mpdf(['mode' => 'utf-8']);
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
        
        $mpdf->WriteHTML(view('basicdata::exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
