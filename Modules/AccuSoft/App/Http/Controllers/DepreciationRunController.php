<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AccuSoft\App\Models\DepreciationRun;
use App\Services\AccuSoft\AssetService;
use Carbon\Carbon;
use Modules\AccuSoft\App\Http\Requests\StoreDepreciationRunRequest;

class DepreciationRunController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index()
    {
        $runs = DepreciationRun::with('creator', 'journalEntry')->latest()->paginate(10);
        return view('accusoft::depreciation_runs.index', compact('runs'));
    }

    public function create()
    {
        return view('accusoft::depreciation_runs.create');
    }

    public function store(StoreDepreciationRunRequest $request)
    {
        try {

            // Check if already run for this month
            $exists = DepreciationRun::where('run_month', $request->run_month)
                ->where('run_year', $request->run_year)
                ->exists();

            if ($exists) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => __('accusoft::messages.depreciation_run_closed')], 409);
                }
                flash()->error(__('accusoft::messages.depreciation_run_closed'));
                return redirect()->back();
            }

            $run = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                return $this->assetService->batchDepreciationRun(
                    $request->run_month,
                    $request->run_year,
                    auth()->id() ?? 1,
                    $request->notes ?? '',
                    $request->has('uses_individual_entries')
                );
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('accusoft::messages.depreciation_run_success', ['value' => number_format($run->total_depreciation, 2)])
                ]);
            }

            flash()->success(__('accusoft::messages.depreciation_run_success', ['value' => number_format($run->total_depreciation, 2)]));
            return redirect()->route('accusoft.depreciation_runs.index');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Depreciation Run Error: ", [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            flash()->error(__('accusoft::messages.error_prefix') . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
