<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\JournalEntry;
use App\Services\AccuSoft\JournalEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AccuSoft\App\Http\Requests\StoreJournalEntryRequest;
use Modules\AccuSoft\App\Http\Requests\UpdateJournalEntryRequest;
use Modules\AccuSoft\App\Repositories\AsJournalEntryRepository;
use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Modules\AccuSoft\App\Imports\JournalEntryImport;
use Modules\AccuSoft\App\Exports\JournalEntryImportErrorExport;
use Illuminate\Support\Facades\Storage;

class AsJournalEntryController extends Controller
{
    public function __construct(private readonly AsJournalEntryRepository $repository, private readonly JournalEntryService $service) {}

    /**
     * Display a listing of journal entries.
     */
    public function index(Request $request): View
    {
        $query = $this->repository->allQuery($request->except('pagination'))->where('status', '!=', JournalEntry::STATUS_PENDING);

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $limit = $request->input('pagination', 10);
        $journalEntries = $query->paginate($limit)->appends($request->query());

        $data = [
            'journalEntries' => $journalEntries,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'sources' => $this->repository->sources(),
        ];
        return view('accusoft::journal_entries.index', $data);
    }

    /**
     * Display a listing of pending journal entries.
     */
    public function pending(Request $request): View
    {
        $query = $this->repository
            ->allQuery($request->except('pagination'))
            ->where('status', JournalEntry::STATUS_PENDING);

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->latest();
        }

        $limit = $request->input('pagination', 10);
        $journalEntries = $query->paginate($limit)->appends($request->query());

        $data = [
            'journalEntries' => $journalEntries,
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->types(),
            'sources' => $this->repository->sources(),
        ];
        
        return view('accusoft::journal_entries.pending', $data);
    }

    /**
     * Post a pending journal entry (change status from PENDING to POSTED).
     */
    public function post(int $id): \Illuminate\Http\RedirectResponse
    {
        $entry = $this->repository->find($id);

        if (!$entry) {
            flash()->error(__('messages.not_found'));
            return redirect()->back();
        }

        if ($entry->status != \App\Models\AccuSoft\JournalEntry::STATUS_PENDING) {
            flash()->error(__('accusoft::lang.entry_not_pending'));
            return redirect()->back();
        }

        $entry->status = \App\Models\AccuSoft\JournalEntry::STATUS_POSTED;
        $entry->posted_at = now();
        $entry->posted_by = auth()->id();
        $entry->save();

        flash()->success(__('accusoft::lang.entry_posted_successfully'));
        return redirect()->back();
    }

    /**
     * Bulk post pending journal entries.
     */
    public function bulkPost(Request $request): \Illuminate\Http\RedirectResponse
    {
        $entryIds = $request->input('entry_ids', []);

        if (empty($entryIds) || !is_array($entryIds)) {
            flash()->error(__('accusoft::lang.no_entries_selected'));
            return redirect()->back();
        }

        $entries = JournalEntry::whereIn('id', $entryIds)
            ->where('status', JournalEntry::STATUS_PENDING)
            ->get();

        if ($entries->isEmpty()) {
            flash()->error(__('accusoft::lang.no_entries_selected'));
            return redirect()->back();
        }

        $count = 0;
        foreach ($entries as $entry) {
            $entry->status = JournalEntry::STATUS_POSTED;
            $entry->posted_at = now();
            $entry->posted_by = auth()->id();
            $entry->save();
            $count++;
        }

        flash()->success(__('accusoft::lang.bulk_post_success', ['count' => $count]));
        return redirect()->back();
    }

    /**
     * Show the form for creating a new journal entry.
     */
    public function create(): View
    {
        $data = [
            'CostCenters' => $this->repository->CostCenters(),
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->typesList(),
            'sources' => $this->repository->sources(),
            'branchs' => $this->repository->branchs(),
        ];

        return view('accusoft::journal_entries.create', $data);
    }

    /**
     * Store a newly created journal entry.
     */
    public function store(StoreJournalEntryRequest $request): RedirectResponse
    {
        try {
            $input = $request->all();
            $this->service->create($input);
            flash()->success(__('accusoft::models/as_journal_entries.singular') . ' ' . __('messages.saved'));
            return redirect()->route('accusoft.JournalEntry.index');
        } catch (\Exception $e) {
            flash()->error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified journal entry.
     */
    public function show(int $id): View|RedirectResponse
    {
        $journalEntry = $this->repository->find($id);

        if (empty($journalEntry)) {
            flash()->error(__('accusoft::models/as_journal_entries.singular') . ' ' . __('messages.not_found'));
            return redirect()->route('accusoft.JournalEntry.index');
        }

        $org = $this->repository->getdataorganization();
        return view('accusoft::journal_entries.show', compact('journalEntry', 'org'));
    }

    /**
     * Show the form for editing the specified journal entry.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $journalEntry = $this->repository->find($id);

        if (empty($journalEntry)) {
            flash()->error(__('accusoft::models/as_journal_entries.singular') . ' ' . __('messages.not_found'));
            return redirect()->route('accusoft.JournalEntry.index');
        }

        if ($journalEntry->is_locked) {
            $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();
            if (!$settings || !$settings->lock_period_pwd_enabled) {
                flash()->error(__('lang.locked') ?? 'القيد مغلق ولا يمكن التعديل عليه.');
                return redirect()->route('accusoft.JournalEntry.index');
            }
            if (!session()->get('unlocked_journal_entry_' . $id)) {
                flash()->error(__('lang.password_incorrect') ?? 'يجب إدخال كلمة المرور لتعديل قيد مغلق.');
                return redirect()->route('accusoft.JournalEntry.index');
            }
        }

        $data = [
            'journalEntry' => $journalEntry,
            'CostCenters' => $this->repository->CostCenters(),
            'statuses' => $this->repository->statuses(),
            'types' => $this->repository->typesList(),
            'sources' => $this->repository->sources(),
            'branchs' => $this->repository->branchs(),
        ];

        return view('accusoft::journal_entries.edit', $data);
    }

    /**
     * Update the specified journal entry.
     */
    public function update(UpdateJournalEntryRequest $request, int $id): RedirectResponse
    {
        try {
            $journalEntry = $this->repository->find($id);
            if (empty($journalEntry)) {
                flash()->error(__('accusoft::models/as_journal_entries.singular') . ' ' . __('messages.not_found'));
                return redirect()->route('accusoft.JournalEntry.index');
            }

            if ($journalEntry->is_locked) {
                $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();
                if (!$settings || !$settings->lock_period_pwd_enabled) {
                    flash()->error(__('lang.locked') ?? 'القيد مغلق ولا يمكن التعديل عليه.');
                    return redirect()->route('accusoft.JournalEntry.index');
                }
                if (!session()->get('unlocked_journal_entry_' . $id)) {
                    flash()->error(__('lang.password_incorrect') ?? 'يجب إدخال كلمة المرور لتعديل قيد مغلق.');
                    return redirect()->route('accusoft.JournalEntry.index');
                }
                session()->forget('unlocked_journal_entry_' . $id);
            }

            $data = $request->validated();
            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment'); // استخدم 'img' وليس 'attachment'
            }

            $this->service->update($journalEntry, $data, $request->input('lock_password') ?? null);
            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_journal_entries.singular')]));

            return redirect()->route('accusoft.JournalEntry.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_journal_entries.singular')]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified journal entry.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $journalEntry = $this->repository->find($id);

            if (empty($journalEntry)) {
                flash()->error(__('accusoft::models/as_journal_entries.singular') . ' ' . __('messages.not_found'));
                return redirect()->route('accusoft.JournalEntry.index');
            }

            try {
                $this->service->delete($journalEntry, true);
            } catch (\Exception $e) {
                $journalEntry->details()->delete();
                $journalEntry->delete();
            }

            flash()->success(__('messages.deleted', ['model' => __('accusoft::models/as_journal_entries.singular')]));

            return redirect()->route('accusoft.JournalEntry.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('accusoft::models/as_journal_entries.singular')]) . ': ' . $e->getMessage());

            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel(); // استخدم Unit بدلاً من dataExel
        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'FiscalYear.xlsx');
    }

    public function csv()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();

        return Excel::download(new AccuSoftDataExport($dataExcel, $headers), 'FiscalYear.csv');
    }

    public function pdf()
    {
        $headers = $this->repository->getHeaders();
        $dataExcel = $this->repository->dataExcel();
        $name = $this->repository->name();

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

    public function pdfdetails($id)
    {
        $journalEntry = $this->repository->find($id);
        $headers = $this->repository->getHeadersdetails();
        $dataExcel = $this->repository->dataExceldetails($id);
        $name = $this->repository->name();
        $org = $this->repository->getdataorganization();

        // dd($org );

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

        // return view('accusoft::exports.pdfdetials', ['headers' => $headers ,'org' => $org , 'data' => $dataExcel, 'name' => $name ,'journalEntry'=>$journalEntry]);
        $mpdf->WriteHTML(view('accusoft::exports.pdfdetials', ['headers' => $headers, 'org' => $org, 'data' => $dataExcel, 'name' => $name, 'journalEntry' => $journalEntry]));
        $mpdf->Output();
    }

    public function verifyLockPassword(Request $request, int $id)
    {
        $settings = \Illuminate\Support\Facades\DB::table('accounting_settings')->first();

        if (!$settings || !$settings->lock_period_pwd_enabled) {
            return response()->json(['success' => false, 'message' => __('lang.locked')]);
        }

        if (Hash::check($request->password, $settings->lock_period_pwd)) {
            session()->put('unlocked_journal_entry_' . $id, true);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => __('lang.invalid_password')]);
    }

    public function import(Request $request)
    {
        return view('accusoft::journal_entries.import');
    }

    public function importsave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new JournalEntryImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();

            if (!empty($errors)) {
                $errorFileName = 'journal_entry_import_errors_' . time() . '.xlsx';
                Excel::store(new JournalEntryImportErrorExport($errors), $errorFileName, 'public');
                
                $errorUrl = asset('uploads/' . $errorFileName);
                
                flash()->warning(__('تم استيراد ' . $successCount . ' قيود بنجاح، ووجد أخطاء في بعض القيود. يمكنك تحميل ملف الأخطاء من الرابط التالي: <br><a href="' . $errorUrl . '" class="btn btn-sm btn-danger mt-2"><i class="fas fa-download"></i> تحميل ملف الأخطاء</a>'))->important()->html();
            } else {
                flash()->success(__('تم استيراد ' . $successCount . ' قيود بنجاح.'));
            }

            return redirect()->route('accusoft.JournalEntry.index');
        } catch (\Exception $e) {
            flash()->error('حدث خطأ أثناء عملية الاستيراد: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'journal_entry_template.xlsx';
        $headers = [
            'A1' => 'Journal Code',
            'B1' => 'Journal Date',
            'C1' => 'branch',
            'D1' => 'Journal Description',
            'E1' => 'Account code',
            'F1' => 'Account Name',
            'G1' => 'Transaction Description',
            'H1' => 'Cost Center',
            'I1' => 'Debit',
            'J1' => 'Credit'
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Fetch an actual account code from the DB for the template
        $sampleAccount = \App\Models\AccuSoft\TreeAccounts::where('is_leaf', true)->first();
        $sampleAccountCode = $sampleAccount ? $sampleAccount->code : '11201001';
        $sampleAccountName = $sampleAccount ? $sampleAccount->name : 'حساب تجريبي';

        // Add some sample data
        $sheet->setCellValue('A2', 'JE-001');
        $sheet->setCellValue('B2', '3/1/2026');
        $sheet->setCellValue('C2', 'الفرع الرئيسي');
        $sheet->setCellValue('D2', 'وصف القيد بشكل عام');
        $sheet->setCellValue('E2', $sampleAccountCode);
        $sheet->setCellValue('F2', $sampleAccountName);
        $sheet->setCellValue('G2', 'وصف السطر الأول');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', '1000');
        $sheet->setCellValue('J2', '0');

        $sheet->setCellValue('A3', 'JE-001');
        $sheet->setCellValue('B3', '3/1/2026');
        $sheet->setCellValue('C3', 'الفرع الرئيسي');
        $sheet->setCellValue('D3', 'وصف القيد بشكل عام');
        $sheet->setCellValue('E3', '21101');
        $sheet->setCellValue('F3', 'حساب آخر');
        $sheet->setCellValue('G3', 'وصف السطر الثاني');
        $sheet->setCellValue('H3', '');
        $sheet->setCellValue('I3', '0');
        $sheet->setCellValue('J3', '1000');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    /**
     * Delete all journal entries created by a specific user/employee ID (default: 59).
     */
    public function deleteUserEntries(Request $request, $userId = 59)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $targetUserId = $userId ?: $request->input('user_id', 59);

            $entries = JournalEntry::where('created_by', $targetUserId)->get();
            $count = $entries->count();

            foreach ($entries as $entry) {
                try {
                    $this->service->delete($entry, true);
                } catch (\Exception $e) {
                    $entry->details()->delete();
                    $entry->delete();
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            flash()->success("تم حذف $count قيد للموظف/المستخدم رقم $targetUserId بنجاح مع كافة تفاصيلها.");
            return redirect()->route('accusoft.JournalEntry.index');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            flash()->error('حدث خطأ أثناء حذف قيود الموظف: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
