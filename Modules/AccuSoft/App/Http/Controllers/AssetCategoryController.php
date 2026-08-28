<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\AccuSoft\App\Models\AssetCategory;
use Modules\AccuSoft\App\Models\Asset;
use App\Models\AccuSoft\TreeAccounts;
use Modules\AccuSoft\App\Repositories\AsAssetCategoryRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Modules\AccuSoft\App\Http\Requests\StoreAssetCategoryRequest;
use Modules\AccuSoft\App\Http\Requests\UpdateAssetCategoryRequest;

class AssetCategoryController extends AppBaseController
{
    private AsAssetCategoryRepository $assetCategoryRepository;

    public function __construct(AsAssetCategoryRepository $assetCategoryRepository)
    {
        $this->assetCategoryRepository = $assetCategoryRepository;
    }

    public function index(Request $request)
    {
        $data['categories'] = $this->assetCategoryRepository->allQuery($request->except('pagination'))
            ->with(['assetAccount', 'accumulatedDepreciationAccount', 'depreciationExpenseAccount'])
            ->latest()
            ->paginate($request->get('pagination', 10));

        $data['depreciationMethods'] = $this->assetCategoryRepository->depreciationMethods();

        return view('accusoft::asset_categories.index', $data);
    }

    public function create()
    {
        $data['accounts'] = [];
        $data['depreciationMethods'] = $this->assetCategoryRepository->depreciationMethods();
        return view('accusoft::asset_categories.create', $data);
    }

    public function store(StoreAssetCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['has_accounting_effect'] = $request->has('has_accounting_effect');

            $this->assetCategoryRepository->create($validated);

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_asset_categories.singular')]));

            return redirect(route('accusoft.assetcategories.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_asset_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $category = $this->assetCategoryRepository->find($id);

        if (empty($category)) {
            flash()->error(__('accusoft::models/as_asset_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.assetcategories.index'));
        }

        return view('accusoft::asset_categories.show', compact('category'));
    }

    public function edit($id)
    {
        $data['category'] = $this->assetCategoryRepository->find($id);

        if (empty($data['category'])) {
            flash()->error(__('accusoft::models/as_asset_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.assetcategories.index'));
        }

        $data['accounts'] = [];
        if ($data['category']->assetAccount)
            $data['accounts'][$data['category']->assetAccount->id] = $data['category']->assetAccount->name;
        if ($data['category']->accumulatedDepreciationAccount)
            $data['accounts'][$data['category']->accumulatedDepreciationAccount->id] = $data['category']->accumulatedDepreciationAccount->name;
        if ($data['category']->depreciationExpenseAccount)
            $data['accounts'][$data['category']->depreciationExpenseAccount->id] = $data['category']->depreciationExpenseAccount->name;
            
        $data['depreciationMethods'] = $this->assetCategoryRepository->depreciationMethods();
        return view('accusoft::asset_categories.edit', $data);
    }

    public function update(UpdateAssetCategoryRequest $request, $id)
    {
        try {
            $category = $this->assetCategoryRepository->find($id);

            if (empty($category)) {
                flash()->error(__('accusoft::models/as_asset_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.assetcategories.index'));
            }

            $validated = $request->validated();
            $validated['has_accounting_effect'] = $request->has('has_accounting_effect');

            $this->assetCategoryRepository->update($validated, $id);

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_asset_categories.singular')]));

            return redirect(route('accusoft.assetcategories.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_asset_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $category = $this->assetCategoryRepository->find($id);

            if (empty($category)) {
                flash()->error(__('accusoft::models/as_asset_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.assetcategories.index'));
            }

            if (Asset::where('asset_category_id', $id)->exists()) {
                flash()->error(__('accusoft::messages.cannot_delete_category_has_assets'));
                return redirect()->back();
            }

            $this->assetCategoryRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_asset_categories.singular')]));

            return redirect(route('accusoft.assetcategories.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_asset_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->assetCategoryRepository->getHeaders();
        $dataExcel = $this->assetCategoryRepository->dataExcel();
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'AssetCategories.xlsx');
    }

    public function csv()
    {
        $headers = $this->assetCategoryRepository->getHeaders();
        $dataExcel = $this->assetCategoryRepository->dataExcel();
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'AssetCategories.csv');
    }

    public function pdf()
    {
        $headers = $this->assetCategoryRepository->getHeaders();
        $dataExcel = $this->assetCategoryRepository->dataExcel();
        $name = $this->assetCategoryRepository->name();

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
