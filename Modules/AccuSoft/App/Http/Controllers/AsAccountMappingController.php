<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\AccountMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Modules\AccuSoft\App\Repositories\AsAccountMappingRepository;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Modules\AccuSoft\App\Models\AsAccountMapping;

class AsAccountMappingController extends Controller
{
    private $asAccountMappingRepository;
    private $asTreeAccountsRepository;

    public function __construct(AsAccountMappingRepository $asAccountMappingRepo, AsTreeAccountsRepository $asTreeAccountsRepo)
    {
        $this->asAccountMappingRepository = $asAccountMappingRepo;
        $this->asTreeAccountsRepository = $asTreeAccountsRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['accountMappings'] = $this->asAccountMappingRepository->allQuery($request->except('pagination'))->latest()->get();
        return view('accusoft::account_mappings.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['accounts'] = $this->asTreeAccountsRepository->Ajex();
        return view('accusoft::account_mappings.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), AccountMapping::rules());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $this->asAccountMappingRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_account_mappings.singular')]));

            return redirect()->route('accusoft.AccountMapping.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_account_mappings.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $accountMapping = $this->asAccountMappingRepository->find($id);

        if (empty($accountMapping)) {
            flash()->error(__('accusoft::models/as_account_mappings.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.AccountMapping.index'));
        }

        return view('accusoft::account_mappings.show')->with('accountMapping', $accountMapping);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $accountMapping = $this->asAccountMappingRepository->find($id);

        if (empty($accountMapping)) {
            flash()->error(__('accusoft::models/as_account_mappings.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusosoft.AccountMapping.index'));
        }
        $data['accountMapping'] = $accountMapping;
        $data['accounts'] = $this->asTreeAccountsRepository->Ajex();

        return view('accusoft::account_mappings.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $accountMapping = $this->asAccountMappingRepository->find($id);

            if (empty($accountMapping)) {
                flash()->error(__('accusoft::models/as_account_mappings.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.AccountMapping.index'));
            }

            $validator = Validator::make($request->all(), AccountMapping::rules($id));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            
            $input = $request->all();
            $this->asAccountMappingRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_account_mappings.singular')]));

            return redirect()->route('accusoft.AccountMapping.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_account_mappings.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $accountMapping = $this->asAccountMappingRepository->find($id);

            if (empty($accountMapping)) {
                flash()->error(__('accusoft::models/as_account_mappings.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.AccountMapping.index'));
            }

            $this->asAccountMappingRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_account_mappings.singular')]));

            return redirect()->route('accusoft.AccountMapping.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_account_mappings.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->asAccountMappingRepository->getHeaders();
        $dataExcel = $this->asAccountMappingRepository->dataExcel();
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'AccountMapping.xlsx');
    }

    public function csv()
    {
        $headers = $this->asAccountMappingRepository->getHeaders();
        $dataExcel = $this->asAccountMappingRepository->dataExcel();

        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'AccountMapping.csv');
    }

    public function pdf()
    {
        $headers = $this->asAccountMappingRepository->getHeaders();
        $dataExcel = $this->asAccountMappingRepository->dataExcel();
        $name = $this->asAccountMappingRepository->name();

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
        $mpdf->WriteHTML(view('accusoft::exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
