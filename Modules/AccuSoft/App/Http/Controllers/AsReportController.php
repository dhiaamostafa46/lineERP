<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\FiscalYear;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\AccuSoft\App\Repositories\AsReportRepository;
use Modules\AccuSoft\App\Services\AccuSoftReportService;
use Maatwebsite\Excel\Facades\Excel;
use Modules\AccuSoft\App\Exports\GenericViewExport;

class AsReportController extends Controller
{
    protected $reportService;
    protected $reportRepository;

    public function __construct(AccuSoftReportService $reportService, AsReportRepository $reportRepository)
    {
        $this->reportService = $reportService;
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        return view('accusoft::report.index');
    }

    public function accountstatement(Request $request)
    {
        // تجهيز بيانات الفلاتر
        $data = [
            'accounts' => $this->reportRepository->treeAccounts(),
            'costCenters' => $this->reportRepository->costCenters(),
            'branchs' => $this->reportRepository->branchs(),
            'users' => $this->reportRepository->users(),
            'accountstat' => null,
        ];

        // قراءة التواريخ
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // في حال عدم تحديد التواريخ → استخدم السنة المالية الحالية
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        // تمرير التواريخ للـ View (مهم للعرض)
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $type = $request->input('export', 'RPT');

        // توليد التقرير في حال اختيار حساب
        if ($request->filled('accountId')) {
            try {
                $data['accountstat'] = $this->reportService->generateAccountStatement([
                    'accountId' => $request->accountId,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'costCenterId' => $request->costCenterId ?? $request->costCenter,
                    'branchId' => $this->getBranchId($request),
                    'createdBy' => $request->createdBy ?? $request->userId,
                ]);
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }
        }

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.accountstatement.pdf', $data, __('accusoft::models/as_reports.reports.account_statement')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.accountstatement.excel', $data), 'account_statement.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.accountstatement.excel', $data), 'account_statement.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.accountstatement.index', $data),
        };
    }

    public function costcenter(Request $request)
    {
        $data = [
            'costCenters' => $this->reportRepository->costCenters(),
            'branchs' => $this->reportRepository->branchs(),
            'datacostcenter' => null,
        ];

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        // تمرير التواريخ للـ View (مهم للعرض)
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $type = $request->input('export', 'RPT');

        if ($request->filled('costCenter')) {
            try {
                $data['datacostcenter'] = $this->reportService->generatecostcenterStatement([
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                    'costCenterId' => $request->costCenter,
                    'branchId' => $this->getBranchId($request),
                ]);
            } catch (\Exception $e) {
                // في حالة حدوث خطأ
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.costcenter.pdf', $data, __('accusoft::models/as_reports.reports.cost_centers')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.costcenter.excel', $data), 'cost_center.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.costcenter.excel', $data), 'cost_center.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.costcenter.index', $data),
        };
    }

    public function trialBalance(Request $request)
    {
        $type = $request->input('export', 'RPT');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $branchId = $this->getBranchId($request);

        // ✅ إذا لم يتم إدخال تواريخ
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $level = $request->input('level', 1);

        $data = [
            'branchs' => $this->reportRepository->branchs(),
            'trialBalance' => $this->reportService->generateTrialBalance($branchId, $fromDate, $toDate),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'level' => $level,
        ];
        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.trialBalance.pdf', $data, __('accusoft::models/as_reports.reports.trial_balance_balances')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.trialBalance.excel', $data), 'trial_balance.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.trialBalance.excel', $data), 'trial_balance.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.trialBalance.index', $data),
        };
    }

    public function incomeStatement(Request $request)
    {
        $type = $request->input('export', 'RPT');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $branchId = $this->getBranchId($request);

        // ✅ إذا لم يتم إدخال تواريخ
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $level = $request->input('level', 1);

        $data = [
            'branchs' => $this->reportRepository->branchs(),
            'incomeStatement' => $this->reportService->generateIncomeStatement($branchId, $fromDate, $toDate),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'level' => $level,
        ];
        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.incomeStatement.pdf', $data, __('accusoft::models/as_reports.types.income_statement')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.incomeStatement.excel', $data), 'income_statement.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.incomeStatement.excel', $data), 'income_statement.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.incomeStatement.index', $data),
        };
    }

    public function exportPdf($view, $data, $name)
    {
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
        
        // Pass $is_pdf = true to the view
        $data['is_pdf'] = true;
        $mpdf->WriteHTML(view($view, array_merge(['data' => $data, 'name' => $name], $data)));
        $mpdf->Output($name . '.pdf', 'I');
    }

    /**
     * الميزانية العمومية - Balance Sheet
     */

    public function balanceSheet(Request $request)
    {
        $type = $request->input('export', 'RPT');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $branchId = $this->getBranchId($request);

        // ✅ إذا لم يتم إدخال تواريخ
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $level = $request->input('level', 1);

        $data = [
            'branchs' => $this->reportRepository->branchs(),
            'balanceSheet' => $this->reportService->generateBalanceSheet($branchId, $fromDate, $toDate),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'level' => $level,
        ];

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.balanceSheet.pdf', $data, __('accusoft::models/as_reports.types.balance_sheet')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.balanceSheet.excel', $data), 'balance_sheet.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.balanceSheet.excel', $data), 'balance_sheet.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.balanceSheet.index', $data),
        };
    }

    /**
     * قائمة التدفقات النقدية - Cash Flow Statement
     */
    public function cashFlow(Request $request)
    {
        $type = $request->input('export', 'RPT');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $branchId = $this->getBranchId($request);

        // ✅ إذا لم يتم إدخال تواريخ - استخدام السنة المالية الحالية أو الشهر الحالي
        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                // ❌ لا توجد سنة مالية → استخدام الشهر الحالي
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $data = [
            'branchs' => $this->reportRepository->branchs(),
            'cashFlow' => $this->reportService->generateCashFlow($branchId, $fromDate, $toDate),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ];

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.cashFlow.pdf', $data, __('accusoft::models/as_reports.types.cash_flow_statement_indirect')),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.cashFlow.excel', $data), 'cash_flow.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.cashFlow.excel', $data), 'cash_flow.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.cashFlow.index', $data),
        };
    }

    public function assets(Request $request)
    {
        $type = $request->input('export', 'RPT');

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $data = [
            'branchs' => $this->reportRepository->branchs(),
            'costCenters' => $this->reportRepository->costCenters(),
            'categories' => \Modules\AccuSoft\App\Models\AssetCategory::get()->mapWithKeys(function ($item) { return [$item->id => $item->name]; })->toArray(),
            'statuses' => \Modules\AccuSoft\App\Models\Asset::getStatuses(),
            'depreciationStatuses' => \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses(),
            'depreciationMethods' => \Modules\AccuSoft\App\Models\AssetCategory::getDepreciationMethods(),
            'usefulLifeTypes' => ['yearly' => __('accusoft::models/as_asset.yearly'), 'monthly' => __('accusoft::models/as_asset.monthly')],
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ];

        $filters = $request->all();
        $filters['branchId'] = $this->getBranchId($request);
        $data['assets'] = $this->reportRepository->assetsReport($filters);

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.assets.pdf', $data, __('accusoft::models/as_reports.reports.assets') ?? 'تقرير الأصول'),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.assets.excel', $data), 'assets_report.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.assets.excel', $data), 'assets_report.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.assets.index', $data),
        };
    }
    public function journalEntries(Request $request)
    {
        $data = [
            'accounts' => $this->reportRepository->treeAccounts(),
            'costCenters' => $this->reportRepository->costCenters(),
            'branchs' => $this->reportRepository->branchs(),
            'journalTypes' => \App\Models\AccuSoft\JournalEntry::types(),
            'reportData' => null,
        ];

        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        if (!$fromDate || !$toDate) {
            try {
                $fiscalYear = FiscalYear::current()->open()->firstOrFail();
                $fromDate ??= $fiscalYear->start_date->toDateString();
                $toDate ??= $fiscalYear->end_date->toDateString();
            } catch (\Exception $e) {
                $fromDate ??= Carbon::now()->startOfMonth()->toDateString();
                $toDate ??= Carbon::now()->endOfMonth()->toDateString();
            }
        }

        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $type = $request->input('export', 'RPT');

        if ($request->has('search')) {
            try {
                $data['reportData'] = $this->reportService->generateJournalEntriesReport(
                    $fromDate,
                    $toDate,
                    $this->getBranchId($request),
                    $request->costCenterId,
                    $request->accountId,
                    $request->entryType
                );
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }
        }

        return match ($type) {
            'pdf' => $this->exportPdf('accusoft::report.journalEntries.pdf', $data, 'تقرير بنود القيد'),
            'excel' => Excel::download(new GenericViewExport('accusoft::report.journalEntries.excel', $data), 'journal_entries.xlsx'),
            'csv' => Excel::download(new GenericViewExport('accusoft::report.journalEntries.excel', $data), 'journal_entries.csv', \Maatwebsite\Excel\Excel::CSV),
            default => view('accusoft::report.journalEntries.index', $data),
        };
    }

    private function getBranchId(Request $request)
    {
        $branchId = $request->input('branchId');
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $branchId = auth()->user()->branch_id;
        }
        return $branchId;
    }
}

