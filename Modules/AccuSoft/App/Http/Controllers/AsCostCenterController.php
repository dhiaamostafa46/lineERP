<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccuSoft\CostCenters;
use Illuminate\Support\Facades\Validator;
use Modules\AccuSoft\App\Repositories\AsCostCenterRepository;

class AsCostCenterController extends Controller
{
    private $asCostCenterRepository;

    public function __construct(AsCostCenterRepository $asCostCenterRepo)
    {
        $this->asCostCenterRepository = $asCostCenterRepo;
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $data['asCostCenters'] = $this->asCostCenterRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
    //     $data['statuses'] = $this->asCostCenterRepository->statuses();
    //     return view('accusoft::cost_centers.index', $data);
    // }





    public function index(Request $request)
    {
        return view('accusoft::cost_centers.index', $request);
    }

    public function getChildren(Request $request)
    {
        $parentId = $request->input('id');

        // Query base
        $query = CostCenters::query()
            ->select(['id', 'parent_id', 'code', 'status'])
            ->orderBy('code');

        // Root nodes
        if ($parentId === '#' || empty($parentId)) {
            $query->whereNull('parent_id')->orWhere('parent_id', 0);
        } else {
            if (!ctype_digit((string) $parentId)) {
                return response()->json([], 400);
            }
            $query->where('parent_id', (int) $parentId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            return response()->json([]);
        }

        // Get children count in one query
        $childrenCount = CostCenters::whereIn('parent_id', $accounts->pluck('id'))->selectRaw('parent_id, COUNT(*) as count')->groupBy('parent_id')->pluck('count', 'parent_id');

        $treeData = $accounts->map(function ($account) use ($childrenCount) {
            $hasChildren = isset($childrenCount[$account->id]);
            $count = $childrenCount[$account->id] ?? 0;

            // Badge style
            $badgeStyle = match ($account->status) {
                1 => 'color: #198754;', // success green
                2 => 'color: #dc3545;', // danger red
                default => 'color: #6a669d;', // default custom color
            };

            $typeBadge = '';
            if (!empty($account->status_text)) {
                $typeBadge = sprintf('<span class="badge ms-2" style="%s">%s</span>', $badgeStyle, e($account->type_text));
            }

            $childrenBadge = $count > 0 ? '<span class="badge bg-primary bg-opacity-10 text-primary ms-1">' . $count . '</span>' : '';

            $actions =
                '
        <span class="tree-actions">
            <button class="btn btn-view" onclick="viewAccount(' .
                $account->id .
                ')">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-edit" onclick="editAccount(' .
                $account->id .
                ')">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-delete" onclick="deleteAccount(' .
                $account->id .
                ')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </span>';

            return [
                'id' => (string) $account->id,
                'text' =>
                    '<span class="account-name" style="font-weight: bold; font-size: 17px; padding: 5px 12px;">' .
                    e($account->name) .
                    '</span>
                 <span class="text-muted ms-2" style="' .
                    $badgeStyle .
                    '">(' .
                    e($account->code) .
                    ')</span>
                 ' .
                    $typeBadge .
                    $childrenBadge .
                    $actions,

                'children' => $hasChildren,
                'icon' => $hasChildren ? 'fas fa-2x fa-folder' : 'fas fa-2x fa-file-invoice-dollar',

                'data' => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'status' => $account->status,
                    'children_count' => $count,
                ],

                'a_attr' => [
                    'href' => '#',
                    'data-id' => $account->id,
                    'data-url' => route('accusoft.TreeAccounts.show', $account->id),
                    'class' => 'tree-account-link',
                    'title' => $account->name . ' - ' . $account->code,
                ],
            ];
        });

        return response()->json($treeData);
    }

    public function getNextCode(Request $request)
    {
        $parentId = $request->input('parent_id');
        $code = CostCenters::generateCode($parentId);
        return response()->json($code);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['CostCenters'] = $this->asCostCenterRepository->CostCenters();
        $data['statuses'] = $this->asCostCenterRepository->statuses();

        return view('accusoft::cost_centers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {



           $request->merge(['code' => CostCenters::generateCode($request->parent_id)]);

            $validator = Validator::make($request->all(), CostCenters::rules());



            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $asCostCenter = $this->asCostCenterRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_cost_centers.singular')]));

            return redirect()->route('accusoft.CostCenter.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_cost_centers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $CostCenter = $this->asCostCenterRepository->find($id);

        if (empty($CostCenter)) {
            flash()->error(__('accusoft::models/as_cost_centers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.CostCenter.index'));
        }

        return view('accusoft::cost_centers.show')->with('CostCenter', $CostCenter);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $CostCenter = $this->asCostCenterRepository->find($id);
        $data['CostCenter'] = $CostCenter;

        $excludeIds = array_merge([$id], $CostCenter ? $CostCenter->getDescendantIds() : []);
        $allCenters = $this->asCostCenterRepository->CostCenters();
        $data['CostCenters'] = array_filter($allCenters, function ($key) use ($excludeIds) {
            return !in_array($key, $excludeIds);
        }, ARRAY_FILTER_USE_KEY);

        $data['statuses'] = $this->asCostCenterRepository->statuses();

        if (empty($CostCenter)) {
            flash()->error(__('accusoft::models/as_cost_centers.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.CostCenter.index'));
        }

        return view('accusoft::cost_centers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $asCostCenter = $this->asCostCenterRepository->find($id);

            if (empty($asCostCenter)) {
                flash()->error(__('accusoft::models/as_cost_centers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.CostCenter.index'));
            }

            if ($request->filled('parent_id')) {
                if ($request->parent_id == $id) {
                    flash()->error('لا يمكن تعيين مركز التكلفة كأب لنفسه.');
                    return redirect()->back()->withInput();
                }
                if (in_array($request->parent_id, $asCostCenter->getDescendantIds())) {
                    flash()->error('لا يمكن تعيين مركز التكلفة كأب لأحد فروعه أو أحفاده.');
                    return redirect()->back()->withInput();
                }
            }

            $validator = Validator::make($request->all(), CostCenters::rules($id));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $asCostCenter = $this->asCostCenterRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_cost_centers.singular')]));

            return redirect()->route('accusoft.CostCenter.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_cost_centers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $asCostCenter = $this->asCostCenterRepository->find($id);

            if (empty($asCostCenter)) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json(['success' => false, 'message' => __('messages.not_found', ['model' => __('accusoft::models/as_cost_centers.singular')])], 404);
                }
                flash()->error(__('accusoft::models/as_cost_centers.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.CostCenter.index'));
            }

            $this->asCostCenterRepository->delete($id);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => __('messages.deleted', ['model' => __('accusoft::models/as_cost_centers.singular')])]);
            }
            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_cost_centers.singular')]));

            return redirect()->route('accusoft.CostCenter.index');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_cost_centers.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->asCostCenterRepository->getHeaders();
        $dataExcel = $this->asCostCenterRepository->dataExcel();
        $name = $this->asCostCenterRepository->name();

        return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\AccuSoft\App\Exports\AccuSoftDataExport($dataExcel, $headers), $name . '.xlsx');
    }

    public function csv()
    {
        $headers = $this->asCostCenterRepository->getHeaders();
        $dataExcel = $this->asCostCenterRepository->dataExcel();
        $name = $this->asCostCenterRepository->name();

        return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\AccuSoft\App\Exports\AccuSoftDataExport($dataExcel, $headers), $name . '.csv');
    }

    public function pdf()
    {
        $headers = $this->asCostCenterRepository->getHeaders();
        $dataExcel = $this->asCostCenterRepository->dataExcel();
        $name = $this->asCostCenterRepository->name();

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
