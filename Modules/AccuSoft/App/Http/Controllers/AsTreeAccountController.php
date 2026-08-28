<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Modules\AccuSoft\App\Services\ImportService;

class AsTreeAccountController extends Controller
{
    private $asTreeAccountsRepository;

    public function __construct(AsTreeAccountsRepository $asTreeAccountsRepo)
    {
        $this->asTreeAccountsRepository = $asTreeAccountsRepo;
    }

    // ✅ دالة مساعدة لتنسيق النص (مع تجنب الازدواجية)
    private function formatAccountText($account)
    {
        $code = trim($account->code ?? '');
        $name = trim($account->translations->first()?->name ?? '');

        // تجنب التكرار إذا كان الاسم يحتوي على الكود
        if (! empty($code) && ! empty($name)) {
            // إذا كان الاسم يبدأ بالكود، لا تكرره
            if (strpos($name, $code) === 0) {
                return $name;
            }

            return $code.' - '.$name;
        }

        if (! empty($code)) {
            return $code;
        }

        if (! empty($name)) {
            return $name;
        }

        return 'حساب #'.$account->id;
    }

    public function getChildren(Request $request)
    {
        $parentId = $request->input('id');
        $locale = app()->getLocale();

        // Query base with eager loading only for current locale translation
        $query = TreeAccounts::query()
            ->select(['id', 'parent_id', 'code', 'type', 'status'])
            ->with(['translations' => function ($q) use ($locale) {
                $q->select(['tree_accounts_id', 'locale', 'name'])
                    ->where('locale', $locale);
            }])
            ->orderBy('code');

        // Root nodes
        if ($parentId === '#' || empty($parentId)) {
            $query->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            });
        }
        // Children nodes
        else {
            if (! ctype_digit((string) $parentId)) {
                return response()->json([], 400);
            }
            $query->where('parent_id', (int) $parentId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            return response()->json([]);
        }

        // Optimized children count in one query
        $childrenCount = TreeAccounts::whereIn('parent_id', $accounts->pluck('id'))
            ->selectRaw('parent_id, COUNT(*) as count')
            ->groupBy('parent_id')
            ->pluck('count', 'parent_id');

        $treeData = $accounts->map(function ($account) use ($childrenCount) {
            $count = $childrenCount[$account->id] ?? 0;
            $hasChildren = $count > 0;

            $badgeClass = match ($account->type) {
                1 => 'text-success',
                2 => 'text-danger',
                default => 'text-info',
            };

            $accountName = $account->translations->first()?->name ?? '';

            // Actions HTML - streamlined
            $actions = sprintf(
                '<span class="tree-actions">
                    <button class="btn btn-view" onclick="viewAccount(%d)" title="عرض"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-edit" onclick="editAccount(%d)" title="تعديل"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-delete" onclick="deleteAccount(%d)" title="حذف"><i class="fas fa-trash-alt"></i></button>
                </span>',
                $account->id,
                $account->id,
                $account->id
            );

            // Text with better styling
            $text = sprintf(
                '<span class="account-item d-inline-flex align-items-center">
                    <span class="account-name %s fw-bold me-2" style="font-size: 16px;">%s</span>
                    <span class="account-code text-muted small me-2">(%s)</span>
                    %s
                </span>',
                $badgeClass,
                e($accountName),
                e($account->code),
                $actions
            );

            return [
                'id' => (string) $account->id,
                'text' => $text,
                'children' => $hasChildren,
                'icon' => $hasChildren ? "fas fa-folder $badgeClass" : "fas fa-file-invoice-dollar $badgeClass",
                'a_attr' => [
                    'href' => '#',
                    'data-id' => $account->id,
                    'class' => 'tree-account-link',
                    'title' => $accountName.' - '.$account->code,
                ],
            ];
        });

        return response()->json($treeData);
    }

    public function getNextCode(Request $request)
    {
        $parentId = $request->input('parent_id');
        $code = TreeAccounts::generateCode($parentId);
        return response()->json($code);
    }

    /**
     * تأكد من إضافة Indexes في Migration للأداء الأفضل:
     *
     * Schema::table('tree_accounts', function (Blueprint $table) {
     *     $table->index('parent_id');
     *     $table->index('code');
     *     $table->index(['parent_id', 'code']);
     * });
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('accusoft::tree_accounts.index');
    }

    private function buildTree(Collection $elements, $parentId = null)
    {
        $branch = new Collection;

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children->isNotEmpty()) {
                    $element->children = $children;
                }
                $branch->push($element);
            }
        }

        return $branch;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['TreeAccounts'] = $this->asTreeAccountsRepository->TreeAccounts();
        $data['statuses'] = $this->asTreeAccountsRepository->statuses();
        $data['types'] = $this->asTreeAccountsRepository->types();

        return view('accusoft::tree_accounts.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // توليد كود فريد للحساب بناءً على الأب ودمجه في الطلب
            $request->merge(['code' => TreeAccounts::generateCode($request->parent_id)]);

            // تعيين نوع الحساب (مدين/دائن) تلقائياً بناءً على حساب الأب
            if ($request->filled('parent_id')) {
                $parent = TreeAccounts::find($request->parent_id);
                if ($parent) {
                    $request->merge(['account_type' => $parent->account_type]);
                }
            }

            $validator = Validator::make($request->all(), TreeAccounts::rules());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $asTreeAccount = $this->asTreeAccountsRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_tree_accounts.singular')]));

            return redirect()->route('accusoft.TreeAccounts.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_tree_accounts.singular')]).': '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.s
     */
    public function show($id)
    {
        $TreeAccount = $this->asTreeAccountsRepository->find($id);

        if (empty($TreeAccount)) {
            flash()->error(__('accusoft::models/as_tree_accounts.singular').' '.__('messages.not_found'));

            return redirect(route('accusoft.tree_accounts.index'));
        }

        return view('accusoft::tree_accounts.show')->with('TreeAccount', $TreeAccount);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $asTreeAccount = $this->asTreeAccountsRepository->find($id);
        $data['treeAccount'] = $asTreeAccount;

        $excludeIds = array_merge([$id], $asTreeAccount ? $asTreeAccount->getDescendantIds() : []);
        $allAccounts = $this->asTreeAccountsRepository->TreeAccounts();
        $data['TreeAccounts'] = array_filter($allAccounts, function ($key) use ($excludeIds) {
            return !in_array($key, $excludeIds);
        }, ARRAY_FILTER_USE_KEY);

        $data['statuses'] = $this->asTreeAccountsRepository->statuses();
        $data['types'] = $this->asTreeAccountsRepository->types();

        if (empty($asTreeAccount)) {
            flash()->error(__('accusoft::models/as_tree_accounts.singular').' '.__('messages.not_found'));

            return redirect(route('accusoft.tree_accounts.index'));
        }

        return view('accusoft::tree_accounts.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $asTreeAccount = $this->asTreeAccountsRepository->find($id);

            if (empty($asTreeAccount)) {
                flash()->error(__('accusoft::models/as_tree_accounts.singular').' '.__('messages.not_found'));

                return redirect(route('accusoft.tree_accounts.index'));
            }

            if ($request->filled('parent_id')) {
                if ($request->parent_id == $id) {
                    flash()->error('لا يمكن تعيين الحساب كأب لنفسه.');
                    return redirect()->back()->withInput();
                }
                if (in_array($request->parent_id, $asTreeAccount->getDescendantIds())) {
                    flash()->error('لا يمكن تعيين الحساب كأب لأحد أبنائه أو أحفاده.');
                    return redirect()->back()->withInput();
                }

                $parent = TreeAccounts::find($request->parent_id);
                if ($parent) {
                    $request->merge(['account_type' => $parent->account_type]);
                }
            }

            $validator = Validator::make($request->all(), TreeAccounts::rules($id));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $asTreeAccount = $this->asTreeAccountsRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_tree_accounts.singular')]));

            return redirect()->route('accusoft.TreeAccounts.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_tree_accounts.singular')]).': '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $asTreeAccount = $this->asTreeAccountsRepository->find($id);

            if (empty($asTreeAccount)) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json(['success' => false, 'message' => __('messages.not_found', ['model' => __('accusoft::models/as_tree_accounts.singular')])], 404);
                }
                flash()->error(__('messages.not_found', ['model' => __('accusoft::models/as_tree_accounts.singular')]));

                return redirect(route('accusoft.TreeAccounts.index'));
            }

            $this->asTreeAccountsRepository->delete($id);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => __('messages.deleted', ['model' => __('accusoft::models/as_tree_accounts.singular')])]);
            }
            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_tree_accounts.singular')]));

            return redirect()->route('accusoft.TreeAccounts.index');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_tree_accounts.singular')]).': '.$e->getMessage());

            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->asTreeAccountsRepository->getHeaders();
        $dataExcel = $this->asTreeAccountsRepository->dataExcel();
        $name = $this->asTreeAccountsRepository->name();

        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), $name.'.xlsx');
    }

    public function csv()
    {
        $headers = $this->asTreeAccountsRepository->getHeaders();
        $dataExcel = $this->asTreeAccountsRepository->dataExcel();
        $name = $this->asTreeAccountsRepository->name();

        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), $name.'.csv');
    }

    public function pdf()
    {
        $headers = $this->asTreeAccountsRepository->getHeaders();
        $dataExcel = $this->asTreeAccountsRepository->dataExcel();
        $name = $this->asTreeAccountsRepository->name();

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

    public function import()
    {
        return view('accusoft::tree_accounts.import');
    }

    public function importProcess(Request $request, ImportService $importService)
    {
        $sourceType = $request->input('source_type');

        if ($sourceType === 'file') {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);
            $source = $request->file('file');
            $format = 'excel';
        } elseif ($sourceType === 'api') {
            $request->validate([
                'api_url' => 'required|url',
                'api_format' => 'required|in:json,xml',
            ]);
            $source = $request->input('api_url');
            $format = $request->input('api_format');
        } elseif ($sourceType === 'manual') {
            $request->validate([
                'manual_data' => 'required|string',
                'manual_format' => 'required|in:manual_json,manual_csv',
            ]);
            $source = $request->input('manual_data');
            $format = $request->input('manual_format');
        } else {
            flash()->error('نوع مصدر غير صالح.');

            return redirect()->back();
        }

        try {
            // Step 1: Parse
            $parsed = $importService->parse($source, $format);
            if (empty($parsed)) {
                throw new \Exception('لم يتم العثور على أي بيانات صالحة لمعالجتها.');
            }

            // Step 2: Normalize
            $normalized = $importService->normalize($parsed);

            // Step 3: generateCode()
            $processed = $importService->generateCodes($normalized);

            // Store in session
            session(['import_tree_accounts' => $processed]);

            return redirect()->route('accusoft.TreeAccounts.importReview');
        } catch (\Exception $e) {
            flash()->error('حدث خطأ أثناء معالجة البيانات: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function importsave(Request $request)
    {
        return $this->importProcess($request, app(ImportService::class));
    }

    public function importReview()
    {
        $tree = session('import_tree_accounts');
        if (empty($tree)) {
            flash()->warning('لا توجد بيانات مستوردة للمراجعة حالياً.');

            return redirect()->route('accusoft.TreeAccounts.import');
        }

        return view('accusoft::tree_accounts.review', compact('tree'));
    }

    public function importConfirm(ImportService $importService)
    {
        $tree = session('import_tree_accounts');
        if (empty($tree)) {
            flash()->warning('لا توجد بيانات مستوردة للمراجعة حالياً.');

            return redirect()->route('accusoft.TreeAccounts.import');
        }

        // Check if there are critical errors in the tree
        $hasErrors = false;
        foreach ($tree as $node) {
            if (! empty($node['errors'])) {
                $hasErrors = true;
                break;
            }
        }

        if ($hasErrors) {
            flash()->error('لا يمكن حفظ الشجرة لوجود أخطاء في المدخلات. يرجى تعديل الملف أو المدخلات وإعادة المحاولة.');

            return redirect()->route('accusoft.TreeAccounts.importReview');
        }

        try {
            $savedCount = $importService->saveToDatabase($tree);
            session()->forget('import_tree_accounts');

            flash()->success("تم حفظ شجرة الحسابات بنجاح. تم استيراد/تحديث ($savedCount) حساب برمجياً وتحديث توجيهاتها المحاسبية تلقائياً.");

            return redirect()->route('accusoft.TreeAccounts.index');
        } catch (\Exception $e) {
            flash()->error('فشل حفظ البيانات في قاعدة البيانات: '.$e->getMessage());

            return redirect()->route('accusoft.TreeAccounts.importReview');
        }
    }

    public function importCancel()
    {
        session()->forget('import_tree_accounts');
        flash()->info('تم إلغاء عملية الاستيراد بنجاح.');

        return redirect()->route('accusoft.TreeAccounts.import');
    }

    public function downloadTemplate()
    {
        return $this->asTreeAccountsRepository->downloadTemplate();
    }
}
