<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AccuSoft\App\Models\Asset;
use App\Models\AccuSoft\TreeAccounts;
use App\Services\AccuSoft\AssetService;
use Modules\AccuSoft\App\Repositories\AsAssetRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Modules\AccuSoft\App\Http\Requests\StoreAssetRequest;
use Modules\AccuSoft\App\Http\Requests\UpdateAssetRequest;

class AssetController extends Controller
{
    private AssetService $assetService;
    private AsAssetRepository $assetRepository;

    public function __construct(AssetService $assetService, AsAssetRepository $assetRepository)
    {
        $this->assetService = $assetService;
        $this->assetRepository = $assetRepository;
    }

    public function index(Request $request)
    {
        $assets = $this->assetRepository->allQuery($request->except('pagination'))
            ->with(['translations', 'assetCategory', 'branch'])
            ->latest()
            ->paginate($request->get('pagination', 10));

        return view('accusoft::assets.index', compact('assets'));
    }

    public function unactivated()
    {
        // Get HrAssets that don't have a financial asset
        $hrAssets = [];
        if (class_exists(\Modules\HR\App\Models\HrAsset::class)) {
            $hrAssets = \Modules\HR\App\Models\HrAsset::doesntHave('financialAsset')->get();
        }

        // Get Vehicles that don't have a financial asset
        $vehicles = [];

        return view('accusoft::assets.unactivated', compact('hrAssets', 'vehicles'));
    }

    public function create(Request $request)
    {
        $data['categories'] = $this->assetRepository->AssetCategory();
        $data['cost_centers'] = $this->assetRepository->costcenters();
        $data['fixedassets'] = $this->assetRepository->fixedassets();
        
        $data['taxes'] = $this->assetRepository->taxes();
        $data['paymentAccounts'] = $this->assetRepository->paymentAccounts();
    
        return view('accusoft::assets.create', $data);
    }

    public function store(StoreAssetRequest $request)
    {
        $validated = $request->validated();




        $depreciationStatus = $request->input('depreciation_status');
        if ($depreciationStatus == 'category' && !empty($validated['asset_category_id'])) {
            $category = \Modules\AccuSoft\App\Models\AssetCategory::find($validated['asset_category_id']);
            if ($category) {
                $methodMap = ['none' => 0, 'straight_line' => 1, 'declining_balance' => 2];
                $validated['parent_account_id'] = $category->asset_account_id;
                $validated['depreciation_method'] = $methodMap[$category->default_depreciation_method] ?? 0;
                $validated['useful_life'] = $category->default_useful_life;
                $validated['calculation_type'] = $category->calculation_type;
                $validated['useful_life_type'] = $category->useful_life_type;
            }
        } elseif ($depreciationStatus == 'none') {
            $validated['depreciation_method'] = 0;
            $validated['useful_life'] = null;
            $validated['asset_category_id'] = null;
        } elseif ($depreciationStatus == 'custom') {
            $validated['asset_category_id'] = null;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                $this->assetService->purchaseAsset(
                    $validated,
                    $validated['payment_account_id'] ?? null
                );
            });

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_asset.singular')]));
            return redirect(route('accusoft.assets.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_asset.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function excel()
    {
        $headers = $this->assetRepository->getHeaders();
        $dataExcel = $this->assetRepository->dataExcel();
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'Assets.xlsx');
    }

    public function csv()
    {
        $headers = $this->assetRepository->getHeaders();
        $dataExcel = $this->assetRepository->dataExcel();
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'Assets.csv');
    }

    public function pdf()
    {
        $headers = $this->assetRepository->getHeaders();
        $dataExcel = $this->assetRepository->dataExcel();
        $name = $this->assetRepository->name();

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

    public function show($id)
    {
        $asset = $this->assetRepository->find($id);

        if (empty($asset)) {
            flash()->error(__('accusoft::models/as_asset.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.assets.index'));
        }

        $asset->load('depreciations', 'translations', 'transactions', 'assetCategory');
        return view('accusoft::assets.show', compact('asset'));
    }

    public function edit($id)
    {
        $asset = $this->assetRepository->find($id);

        if (empty($asset)) {
            flash()->error(__('accusoft::models/as_asset.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.assets.index'));
        }

        $categories = $this->assetRepository->AssetCategory();
        $cost_centers = $this->assetRepository->costcenters();
        $fixedassets = $this->assetRepository->fixedassets();

  
        $taxes = $this->assetRepository->taxes();

        $oldAccountIds = array_filter([
            old('payment_account_id', $asset->payment_account_id ?? null),
            old('parent_account_id', $asset->parent_account_id ?? null)
        ]);

        $accounts = [];
        if (!empty($oldAccountIds)) {
            $accountsModels = \App\Models\AccuSoft\TreeAccounts::whereIn('id', $oldAccountIds)->get();
            foreach ($accountsModels as $acc) {
                $accounts[$acc->id] = $acc->name;
            }
        }
        $paymentAccounts = $this->assetRepository->paymentAccounts();

        return view('accusoft::assets.edit', compact('asset', 'accounts', 'categories', 'cost_centers', 'fixedassets', 'taxes', 'paymentAccounts'));
    }

    public function update(UpdateAssetRequest $request, $id)
    {
        try {
            $asset = $this->assetRepository->find($id);

            if (empty($asset)) {
                flash()->error(__('accusoft::models/as_asset.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.assets.index'));
            }

            $validated = $request->validated();

            $isUsed = $asset->isUsedInAccounting();
            $hasPostedDepreciations = $asset->hasPostedDepreciations();
            
            if ($hasPostedDepreciations) {
                // If it has posted depreciations, fundamental purchase and depreciation data are locked
                $depreciationStatus = $asset->depreciation_status;
                $validated['depreciation_status'] = $depreciationStatus;
                $validated['purchase_value'] = $asset->purchase_value;
                $validated['purchase_date'] = $asset->purchase_date;
                $validated['depreciation_method'] = $asset->depreciation_method;
                $validated['salvage_value'] = $asset->salvage_value;
                $validated['tax_amount'] = $asset->tax_amount;
                $validated['tax_type'] = $asset->tax_type;
                
                if ($depreciationStatus == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY) {
                    $validated['asset_category_id'] = $asset->asset_category_id;
                }
            } elseif ($isUsed) {
                // Prevent changing the asset type and category if the asset is already used in accounting (e.g. has purchase entry)
                $depreciationStatus = $asset->depreciation_status;
                $validated['depreciation_status'] = $depreciationStatus;
                if ($depreciationStatus == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY) {
                    $validated['asset_category_id'] = $asset->asset_category_id;
                }
            } else {
                $depreciationStatus = $request->input('depreciation_status');
            }

            if (!$hasPostedDepreciations) {
                // We only allow re-evaluation of category details if it doesn't have posted depreciations
                if ($depreciationStatus == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY && !empty($validated['asset_category_id'])) {
                    $category = \Modules\AccuSoft\App\Models\AssetCategory::find($validated['asset_category_id']);
                    if ($category) {
                        $methodMap = ['none' => 0, 'straight_line' => 1, 'declining_balance' => 2];
                        $validated['asset_account_id'] = $category->asset_account_id;
                        $validated['accumulated_depreciation_account_id'] = $category->accumulated_depreciation_account_id;
                        $validated['depreciation_expense_account_id'] = $category->depreciation_expense_account_id;
                        $validated['depreciation_method'] = $methodMap[$category->default_depreciation_method] ?? 0;
                        $validated['useful_life'] = $category->default_useful_life;
                        $validated['calculation_type'] = $category->calculation_type;
                        $validated['useful_life_type'] = $category->useful_life_type;
                    }
                } elseif ($depreciationStatus == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) {
                    $validated['depreciation_method'] = 0;
                    $validated['useful_life'] = null;
                    $validated['asset_category_id'] = null;
                    $validated['asset_account_id'] = null;
                    $validated['accumulated_depreciation_account_id'] = null;
                    $validated['depreciation_expense_account_id'] = null;
                } elseif ($depreciationStatus == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CUSTOM) {
                    $validated['asset_category_id'] = null;
                }
            }

            if (array_key_exists('parent_account_id', $validated)) {
                unset($validated['parent_account_id']);
            }

            
            \Illuminate\Support\Facades\DB::transaction(function () use ($asset, $validated, $id) {
                $oldUsefulLife = $asset->useful_life;
                $oldUsefulLifeType = $asset->useful_life_type;
                $oldSalvageValue = $asset->salvage_value;
                $oldMethod = $asset->depreciation_method;
                $oldPurchaseValue = $asset->purchase_value;
                $oldTaxAmount = $asset->tax_amount;
                $oldTaxType = $asset->tax_type;
                $oldPaymentAccountId = $asset->payment_account_id;
                $oldPurchaseDate = $asset->purchase_date;

                $this->assetRepository->update($validated, $id);
                $updatedAsset = $this->assetRepository->find($id);

                if (
                    $oldPurchaseValue != $updatedAsset->purchase_value ||
                    $oldTaxAmount != $updatedAsset->tax_amount ||
                    $oldTaxType != $updatedAsset->tax_type ||
                    $oldPaymentAccountId != $updatedAsset->payment_account_id ||
                    $oldPurchaseDate != $updatedAsset->purchase_date
                ) {
                    $this->assetService->updatePurchaseEntry($updatedAsset, $validated);
                    // Reload asset as current_book_value might have changed due to purchase value change 
                    // (Actually current_book_value is calculated dynamically in the model now, but good to refresh)
                    $updatedAsset = $updatedAsset->fresh();
                }

                if (
                    $oldUsefulLife != $updatedAsset->useful_life ||
                    $oldUsefulLifeType != $updatedAsset->useful_life_type ||
                    $oldSalvageValue != $updatedAsset->salvage_value ||
                    $oldMethod != $updatedAsset->depreciation_method ||
                    $oldPurchaseValue != $updatedAsset->purchase_value ||
                    $oldPurchaseDate != $updatedAsset->purchase_date
                ) {
                    $this->assetService->generateDepreciationSchedule($updatedAsset);
                    if ($updatedAsset->calculation_type === 'automatic' && $updatedAsset->depreciation_status !== \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) {
                        $this->assetService->catchUpDepreciation($updatedAsset);
                    }
                }
            });

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_asset.singular')]));
            return redirect()->route('accusoft.assets.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_asset.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $asset = $this->assetRepository->find($id);

            if (empty($asset)) {
                flash()->error(__('accusoft::models/as_asset.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.assets.index'));
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
                $this->assetRepository->delete($id);
            });

            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_asset.singular')]));
            return redirect(route('accusoft.assets.index'));
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_asset.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function depreciate(Request $request, $id)
    {
        $asset = $this->assetRepository->find($id);

        if (empty($asset)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('messages.not_found', ['model' => __('lang.asset')])], 404);
            }
            flash()->error(__('lang.asset') . ' ' . __('messages.not_found'));
            return redirect()->back();
        }

        if ($asset->depreciation_status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) {
            $message = __('accusoft::models/as_asset.cannot_depreciate_informational');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            flash()->error($message);
            return redirect()->back();
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'depreciation_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $success = \Illuminate\Support\Facades\DB::transaction(function () use ($asset, $request) {
                return $this->assetService->depreciateAsset(
                    $asset,
                    Carbon::parse($request->depreciation_date),
                    $request->filled('amount') ? (float) $request->input('amount') : null,
                    $request->input('notes') ?? ''
                );
            });

            if ($success) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => __('accusoft::messages.asset_depreciation_success')]);
                }
                flash()->success(__('accusoft::messages.asset_depreciation_success'));
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('accusoft::messages.asset_depreciation_failed')], 409);
                }
                flash()->warning(__('accusoft::messages.asset_depreciation_failed'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Asset Depreciate Error: ", [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            flash()->error(__('accusoft::messages.error_prefix') . $e->getMessage());
        }

        return redirect()->back();
    }

    public function executeDepreciation(Request $request, $id, $depreciationId)
    {
        $asset = $this->assetRepository->find($id);

        if (empty($asset)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('messages.not_found', ['model' => __('lang.asset')])], 404);
            }
            flash()->error(__('lang.asset') . ' ' . __('messages.not_found'));
            return redirect()->back();
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'execution_date' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $success = $this->assetService->executeScheduledDepreciation(
                $asset,
                $depreciationId,
                Carbon::parse($request->execution_date),
                $request->input('notes') ?? ''
            );

            if ($success) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => __('accusoft::messages.asset_depreciation_success')]);
                }
                flash()->success(__('accusoft::messages.asset_depreciation_success') ?? 'تم تنفيذ قسط الإهلاك بنجاح');
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('accusoft::messages.asset_depreciation_failed')], 409);
                }
                flash()->warning(__('accusoft::messages.asset_depreciation_failed') ?? 'تعذر تنفيذ قسط الإهلاك');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Asset Execute Scheduled Depreciation Error: ", [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            flash()->error(__('accusoft::messages.error_prefix') . $e->getMessage());
        }

        return redirect()->back();
    }

    public function dispose(Request $request, $id)
    {
        $asset = $this->assetRepository->find($id);

        if (empty($asset)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => __('messages.not_found', ['model' => __('lang.asset')])], 404);
            }
            flash()->error(__('lang.asset') . ' ' . __('messages.not_found'));
            return redirect()->route('accusoft.assets.index');
        }

        if ($asset->depreciation_status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) {
            $message = __('accusoft::models/as_asset.cannot_dispose_informational');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            flash()->error($message);
            return redirect()->back();
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'disposal_date' => 'required|date',
            'disposal_value' => 'required|numeric|min:0',
            'disposal_type' => 'required|integer|in:1,2,3,4,5',
            'cash_account_id' => 'required|exists:tree_accounts,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($asset, $request) {
                $this->assetService->disposeAsset(
                    $asset,
                    Carbon::parse($request->disposal_date),
                    $request->disposal_value,
                    $request->disposal_type,
                    $request->cash_account_id
                );
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => __('accusoft::messages.asset_disposal_success')]);
            }
            flash()->success(__('accusoft::messages.asset_disposal_success'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Asset Dispose Error: ", [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            flash()->error(__('accusoft::messages.error_prefix') . $e->getMessage());
        }

        return redirect()->route('accusoft.assets.index');
    }

    public function forecast(Request $request)
    {
        $purchase_value = (float) $request->input('purchase_value');
        $salvage_value = (float) $request->input('salvage_value');
        $purchase_date = $request->input('purchase_date');
        $asset_category_id = $request->input('asset_category_id');
        $useful_life_years = (float) $request->input('useful_life');
        $period_type = $request->input('useful_life_type', 'monthly');
        $depreciation_method = $request->input('depreciation_method', Asset::METHOD_STRAIGHT_LINE);

        if ($asset_category_id) {
            $category = \Modules\AccuSoft\App\Models\AssetCategory::find($asset_category_id);
            if ($category) {
                $useful_life_years = (float) $category->default_useful_life;
                // Since category has no explicit period preference, assume monthly for forecasting
                $period_type = 'monthly';
                $depreciation_method = $category->default_depreciation_method;
            }
        }

        if ($useful_life_years <= 0 || $purchase_value <= 0 || !$purchase_date) {
            return response()->json(['html' => '<div class="alert alert-danger">بيانات غير صحيحة لمحاكاة الإهلاك. يرجى التأكد من تعبئة كافة الحقول أو اختيار فئة صحيحة.</div>']);
        }

        $depreciable_base = $purchase_value - $salvage_value;
        $total_months = $useful_life_years * 12;

        $monthly_schedule = [];
        $yearly_schedule = [];

        $accumulated = 0;
        $book_value = $purchase_value;
        $date = \Carbon\Carbon::parse($purchase_date)->addMonth()->startOfMonth();
        $current_year = $date->year;

        if ($period_type === 'yearly') {
            // Yearly periods
            $yearly_depreciation = $depreciable_base / $useful_life_years;

            for ($year = 1; $year <= $useful_life_years; $year++) {
                $expense = $yearly_depreciation;

                if ($year == $useful_life_years) {
                    $expense = $book_value - $salvage_value;
                }

                $accumulated += $expense;
                $book_value -= $expense;

                $yearly_schedule[] = [
                    'year' => $current_year,
                    'expense' => $expense,
                    'accumulated' => $accumulated,
                    'book_value' => $book_value,
                ];

                $current_year++;
            }
            // We just show the yearly schedule
            $monthly_schedule = [];
        } else {
            // Monthly periods
            $monthly_depreciation = $depreciable_base / $total_months;
            $yearly_expense = 0;

            for ($i = 1; $i <= $total_months; $i++) {
                $expense = $monthly_depreciation;

                if ($i == $total_months) {
                    $expense = $book_value - $salvage_value;
                }

                $accumulated += $expense;
                $book_value -= $expense;

                $monthly_schedule[] = [
                    'year' => $date->year,
                    'month' => $date->month,
                    'expense' => $expense,
                    'accumulated' => $accumulated,
                    'book_value' => $book_value,
                ];

                $yearly_expense += $expense;

                if ($date->month == 12 || $i == $total_months) {
                    $yearly_schedule[] = [
                        'year' => $current_year,
                        'expense' => $yearly_expense,
                        'accumulated' => $accumulated,
                        'book_value' => $book_value,
                    ];
                    $current_year++;
                    $yearly_expense = 0;
                }

                $date->addMonth();
            }
        }

        $html = view('accusoft::assets.forecast', compact('monthly_schedule', 'yearly_schedule'))->render();

        return response()->json(['html' => $html]);
    }
}
