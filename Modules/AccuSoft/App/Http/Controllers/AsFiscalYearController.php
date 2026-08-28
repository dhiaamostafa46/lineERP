<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccuSoft\FiscalYear;
use App\Models\AccuSoft\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Modules\AccuSoft\App\Repositories\AsFiscalYearRepository;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Modules\AccuSoft\App\Services\AccountingClosureService;

class AsFiscalYearController extends Controller
{
    private $asFiscalYearRepository;
    private AccountingClosureService $closureService;

    public function __construct(AsFiscalYearRepository $asFiscalYearRepo, AccountingClosureService $closureService)
    {
        $this->asFiscalYearRepository = $asFiscalYearRepo;
        $this->closureService = $closureService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['fiscalYears'] = $this->asFiscalYearRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['statuses'] = $this->asFiscalYearRepository->statuses();
        return view('accusoft::fiscal_years.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $end_date = FiscalYear::open()->latest()->first();

        $data['lastdate'] = $end_date ? $end_date->end_date->addDay()->format('Y-m-d') : today()->format('Y-m-d');

        return view('accusoft::fiscal_years.create', $data);
    }

    public function close($id)
    {
        try {
            $fiscalYear = FiscalYear::findOrFail($id);
            $user = auth()->user();

            $options = [
                AccountingClosureService::OPT_AUTO_POST_DRAFTS => false,
            ];

            $closingEntry = $this->closureService->closeFiscalYear($fiscalYear, $user, $options);

            flash()->success('تم إقفال السنة المالية بنجاح وتصفير حسابات الأرباح والخسائر وتدوير الأرصدة.');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
        }
        return redirect()->route('accusoft.FiscalYear.index');
    }

    public function reopen($id)
    {
        try {
            $fiscalYear = FiscalYear::findOrFail($id);
            $user = auth()->user();

            $this->closureService->reopenFiscalYear($fiscalYear, $user);

            flash()->success('تمت إعادة فتح السنة المالية وإلغاء قيود الإقفال بنجاح.');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
        }
        return redirect()->route('accusoft.FiscalYear.index');
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), FiscalYear::rules());

      

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Close any currently open fiscal years before creating a new one
            $this->asFiscalYearRepository->closeFiscalYear();

            $input = $request->all();

            // New fiscal year should be open and current
            $input['is_closed'] = false;
            $input['is_current'] = true;

            $asFiscalYear = $this->asFiscalYearRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('accusoft::models/as_fiscal_years.singular')]));

            return redirect()->route('accusoft.FiscalYear.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('accusoft::models/as_fiscal_years.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fiscalYear = $this->asFiscalYearRepository->find($id);

        if (empty($fiscalYear)) {
            flash()->error(__('accusoft::models/as_fiscal_years.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.FiscalYear.index'));
        }

        // Summary calculations
        $revCredits = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '5%')
            ->selectRaw('COALESCE(SUM(jed.credit) - SUM(jed.debit), 0) as net_rev')
            ->value('net_rev') ?? 0;

        $expDebits = DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'jed.journal_entry_id', '=', 'je.id')
            ->join('tree_accounts as ta', 'jed.tree_account_id', '=', 'ta.id')
            ->where('je.fiscal_year_id', $fiscalYear->id)
            ->where('je.status', JournalEntry::STATUS_POSTED)
            ->whereNotIn('je.entry_type', [JournalEntry::ENTRY_TYPE_OPENING, JournalEntry::ENTRY_TYPE_CLOSING])
            ->where('ta.code', 'like', '4%')
            ->selectRaw('COALESCE(SUM(jed.debit) - SUM(jed.credit), 0) as net_exp')
            ->value('net_exp') ?? 0;

        $entriesStats = [
            'posted' => JournalEntry::where('fiscal_year_id', $fiscalYear->id)->where('status', JournalEntry::STATUS_POSTED)->count(),
            'pending' => JournalEntry::where('fiscal_year_id', $fiscalYear->id)->where('status', JournalEntry::STATUS_PENDING)->count(),
            'draft' => JournalEntry::where('fiscal_year_id', $fiscalYear->id)->where('status', JournalEntry::STATUS_DRAFT)->count(),
        ];

        $closingEntries = JournalEntry::where('fiscal_year_id', $fiscalYear->id)
            ->where('entry_type', JournalEntry::ENTRY_TYPE_CLOSING)
            ->with(['details.treeAccount', 'creator'])
            ->get();

        $data = [
            'fiscalYear' => $fiscalYear,
            'totalRevenues' => (float)$revCredits,
            'totalExpenses' => (float)$expDebits,
            'netIncome' => (float)($revCredits - $expDebits),
            'entriesStats' => $entriesStats,
            'closingEntries' => $closingEntries,
        ];

        return view('accusoft::fiscal_years.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $fiscalYear = $this->asFiscalYearRepository->find($id);

        if (empty($fiscalYear)) {
            flash()->error(__('accusoft::models/as_fiscal_years.singular') . ' ' . __('messages.not_found'));
            return redirect(route('accusoft.FiscalYear.index'));
        }

        return view('accusoft::fiscal_years.edit')->with('fiscalYear', $fiscalYear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $fiscalYear = $this->asFiscalYearRepository->find($id);

            if (empty($fiscalYear)) {
                flash()->error(__('accusoft::models/as_fiscal_years.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.FiscalYear.index'));
            }

            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $this->asFiscalYearRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_fiscal_years.singular')]));

            return redirect()->route('accusoft.FiscalYear.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_fiscal_years.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $fiscalYear = $this->asFiscalYearRepository->find($id);

            if (empty($fiscalYear)) {
                flash()->error(__('accusoft::models/as_fiscal_years.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.FiscalYear.index'));
            }

            $this->asFiscalYearRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_fiscal_years.singular')]));

            return redirect()->route('accusoft.FiscalYear.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_fiscal_years.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->asFiscalYearRepository->getHeaders();
        $dataExcel = $this->asFiscalYearRepository->dataExcel(); // استخدم Unit بدلاً من dataExel
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'FiscalYear.xlsx');
    }

    public function csv()
    {
        $headers = $this->asFiscalYearRepository->getHeaders();
        $dataExcel = $this->asFiscalYearRepository->dataExcel();

        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'FiscalYear.csv');
    }

    public function pdf()
    {
        $headers = $this->asFiscalYearRepository->getHeaders();
        $dataExcel = $this->asFiscalYearRepository->dataExcel();
        $name = $this->asFiscalYearRepository->name();

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
