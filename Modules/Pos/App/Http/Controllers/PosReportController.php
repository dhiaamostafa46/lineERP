<?php

namespace Modules\Pos\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pos\App\Repositories\PosReportRepository;
use Modules\Pos\App\Services\PosShiftReportingService;
use Modules\Pos\App\Models\PosSession;
use App\Models\invApp\SalesInvoice;

class PosReportController extends Controller
{
    protected $reportRepo;

    public function __construct(PosReportRepository $reportRepo)
    {
        $this->reportRepo = $reportRepo;
    }

    /**
     * Display the POS reports dashboard.
     */
    public function index()
    {
        return view('pos::reports.index');
    }

    private function exportReport($exportType, $headers, $data, $name)
    {
        if ($exportType === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Pos\App\Exports\PosExport($data, $headers), $name . '.xlsx');
        } elseif ($exportType === 'csv') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Pos\App\Exports\PosExport($data, $headers), $name . '.csv', \Maatwebsite\Excel\Excel::CSV);
        } elseif ($exportType === 'pdf') {
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
            $mpdf->WriteHTML(view('exports.pdf', ['headers' => $headers, 'data' => $data, 'name' => $name]));
            return $mpdf->Output($name . '.pdf', 'I');
        }
    }

    /**
     * الحركات - المبيعات نقاط البيع
     */
    public function sales(Request $request)
    {
        $data['customers'] = $this->reportRepo->getCustomers();
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['devices'] = $this->reportRepo->getDevices();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['customer_id', 'store_id', 'branch_id', 'user_id', 'device_id', 'pos_session_id', 'fromDate', 'toDate']);
        $data['sales'] = $this->reportRepo->getPosSalesReport($filters);

        if ($request->ajax()) {
            return response()->json($data);
        }

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.invoice_number'),
                __('invoices::models/inv_reports.columns.issue_date'),
                __('invoices::models/inv_reports.columns.customer'),
                __('invoices::models/inv_reports.columns.employee'),
                __('pos::lang.device'),
                __('invoices::models/inv_reports.columns.total_exclusive_vat'),
                __('invoices::models/inv_reports.columns.total_vat'),
                __('invoices::models/inv_reports.columns.total_inclusive_vat'),
                __('pos::lang.session'),
                __('invoices::models/inv_reports.columns.status')
            ];
            $dataExcel = [];
            foreach ($data['sales'] as $inv) {
                $dataExcel[] = [
                    $inv->invoice_number,
                    $inv->issue_date->format('Y-m-d'),
                    $inv->customer?->name,
                    $inv->user?->name,
                    $inv->posSession?->device?->name ?? '---',
                    number_format($inv->total_exclusive_vat, 2),
                    number_format($inv->total_vat, 2),
                    number_format($inv->total_inclusive_vat, 2),
                    $inv->pos_session_id ?? '---',
                    $inv->status_text
                ];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.pos_sales'));
        }

        return view('pos::reports.sales.index', $data);
    }

    /**
     * الحركات - مبيعات الورديات
     */
    public function sessions(Request $request)
    {
        $data['devices'] = $this->reportRepo->getDevices();
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['device_id', 'user_id', 'status', 'fromDate', 'toDate']);
        $data['sessions'] = $this->reportRepo->getSessionsReport($filters);

        if ($request->filled('export')) {
            $headers = [
                '#',
                __('pos::lang.device'),
                __('pos::lang.cashier'),
                __('pos::lang.opened_at'),
                __('pos::lang.closed_at'),
                __('pos::lang.difference'),
                __('pos::lang.status')
            ];
            $dataExcel = [];
            foreach ($data['sessions'] as $session) {
                $dataExcel[] = [
                    $session->id,
                    $session->device?->name,
                    $session->cashier?->name,
                    $session->opened_at ? $session->opened_at->format('Y-m-d H:i') : '---',
                    $session->closed_at ? $session->closed_at->format('Y-m-d H:i') : '---',
                    number_format($session->difference, 2),
                    $session->status == \Modules\Pos\App\Models\PosSession::STATUS_CLOSED ? __('pos::lang.closed') : __('pos::lang.active')
                ];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.session_sales'));
        }
            
        return view('pos::reports.sessions.index', $data);
    }

    /**
     * الحركات - اجمالى مبيعات التصنيفات
     */
    public function categorySales(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['store_id', 'branch_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getCategorySalesReport($filters);

        if ($request->filled('export')) {
            $headers = ['#', __('basicdata::models/db_products.fields.category_id'), __('pos::lang.quantity'), __('pos::lang.total')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->category_id, $report->category_name, $report->total_quantity, number_format($report->total_amount, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.category_sales'));
        }

        return view('pos::reports.category_sales.index', $data);
    }

    /**
     * الحركات - اجمالى مبيعات المنتجات
     */
    public function productSales(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['store_id', 'branch_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getProductSalesReport($filters);

        if ($request->filled('export')) {
            $headers = ['#', __('basicdata::models/db_products.fields.product'), __('pos::lang.quantity'), __('pos::lang.total')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->product_id, $report->product_name, $report->total_quantity, number_format($report->total_amount, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.product_sales'));
        }

        return view('pos::reports.product_sales.index', $data);
    }

    /**
     * الحركات - حركة الورديات تفصيلى
     */
    public function sessionsDetailed(Request $request)
    {
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['user_id', 'pos_session_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getSessionsDetailedReport($filters);

        if ($request->filled('export')) {
            $headers = [__('invoices::models/inv_reports.columns.invoice_number'), __('invoices::models/inv_reports.columns.issue_date'), __('invoices::models/inv_reports.columns.employee'), __('pos::lang.session'), __('pos::lang.total')];
            $dataExcel = [];
            foreach ($data['reports'] as $inv) {
                $dataExcel[] = [$inv->invoice_number, $inv->issue_date->format('Y-m-d'), $inv->user?->name, $inv->pos_session_id ?? '---', number_format($inv->total_inclusive_vat, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.sessions_detailed'));
        }

        return view('pos::reports.sessions_detailed.index', $data);
    }

    /**
     * اﻻرباح - ربحية الورديات
     */
    public function profitSessions(Request $request)
    {
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['pos_session_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getProfitSessionsReport($filters);

        if ($request->filled('export')) {
            $headers = [__('pos::lang.session'), __('pos::lang.quantity'), __('pos::lang.total'), __('pos::lang.cost'), __('pos::lang.profit')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->pos_session_id, $report->total_quantity, number_format($report->total_revenue, 2), number_format($report->total_cost, 2), number_format($report->total_profit, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.profit_sessions'));
        }

        return view('pos::reports.profit_sessions.index', $data);
    }

    /**
     * اﻻرباح - ربحية التصنيفات
     */
    public function profitCategories(Request $request)
    {
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getProfitCategoriesReport($filters);

        if ($request->filled('export')) {
            $headers = [__('basicdata::models/db_products.fields.category_id'), __('pos::lang.quantity'), __('pos::lang.total'), __('pos::lang.cost'), __('pos::lang.profit')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->category_name, $report->total_quantity, number_format($report->total_revenue, 2), number_format($report->total_cost, 2), number_format($report->total_profit, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.profit_categories'));
        }

        return view('pos::reports.profit_categories.index', $data);
    }

    /**
     * اﻻرباح - ربحية المنتجات
     */
    public function profitProducts(Request $request)
    {
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getProfitProductsReport($filters);

        if ($request->filled('export')) {
            $headers = [__('basicdata::models/db_products.fields.product'), __('pos::lang.quantity'), __('pos::lang.total'), __('pos::lang.cost'), __('pos::lang.profit')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->product_name, $report->total_quantity, number_format($report->total_revenue, 2), number_format($report->total_cost, 2), number_format($report->total_profit, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.profit_products'));
        }

        return view('pos::reports.profit_products.index', $data);
    }

    /**
     * Unified Shift Z-Report (Dashboard / Summary)
     */
    public function shiftZReport(Request $request, $sessionId, PosShiftReportingService $service)
    {
        $session = PosSession::findOrFail($sessionId);
        $summary = $service->getShiftSummary($session);
        return view('pos::reports.shift.z_report', compact('summary'));
    }

    /**
     * Unified Shift Cash Drawer Ledger
     */
    public function shiftCashLedger(Request $request, $sessionId, PosShiftReportingService $service)
    {
        $session = PosSession::findOrFail($sessionId);
        $summary = $service->getShiftSummary($session);
        $ledger = $service->getShiftCashLedger($session);
        return view('pos::reports.shift.cash_ledger', compact('summary', 'ledger'));
    }

    /**
     * Unified Shift Detailed Sales
     */
    public function shiftDetailedSales(Request $request, $sessionId, PosShiftReportingService $service)
    {
        $session = PosSession::findOrFail($sessionId);
        $summary = $service->getShiftSummary($session);
        $invoices = SalesInvoice::with(['user', 'customer', 'items.product', 'payments'])
            ->where('pos_session_id', $sessionId)
            ->where('type_inv', SalesInvoice::TYPE_POS)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pos::reports.shift.detailed_sales', compact('summary', 'invoices'));
    }

    /**
     * Unified Shift Accounting Entries
     */
    public function shiftJournalEntries(Request $request, $sessionId, PosShiftReportingService $service)
    {
        $session = PosSession::findOrFail($sessionId);
        $summary = $service->getShiftSummary($session);
        return view('pos::reports.shift.journal_entries', compact('summary'));
    }

    /**
     * Unified Shift Sold Items & Profitability
     */
    public function shiftSoldItems(Request $request, $sessionId, PosShiftReportingService $service)
    {
        $session = PosSession::findOrFail($sessionId);
        $summary = $service->getShiftSummary($session);
        $items = $service->getShiftSoldItems($session);
        return view('pos::reports.shift.sold_items', compact('summary', 'items'));
    }

    /**
     * Payment Methods Global Report
     */
    public function paymentMethods(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['store_id', 'branch_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getPaymentMethodsReport($filters);

        if ($request->filled('export')) {
            $headers = ['#', __('pos::lang.payment_method'), __('pos::lang.total')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->payment_method_code, $report->payment_method_name, number_format($report->total_amount, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.payment_methods'));
        }

        return view('pos::reports.payment_methods.index', $data);
    }

    /**
     * Cash Movements Global Report
     */
    public function cashMovements(Request $request)
    {
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        $data['users'] = $this->reportRepo->getEmployees();
        
        $filters = $request->only(['fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getCashMovementsReport($filters);

        if ($request->filled('export')) {
            $headers = ['#', __('pos::lang.date'), __('pos::lang.session'), __('pos::lang.type'), __('pos::lang.amount'), __('pos::lang.user')];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->id, $report->created_at->format('Y-m-d H:i'), $report->pos_session_id, $report->type, number_format($report->amount, 2), $report->user?->name];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.cash_movements'));
        }

        return view('pos::reports.cash_movements.index', $data);
    }

    /**
     * Returns Global Report
     */
    public function returns(Request $request)
    {
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['user_id', 'fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getReturnsReport($filters);

        if ($request->filled('export')) {
            $headers = [__('invoices::models/inv_reports.columns.invoice_number'), __('invoices::models/inv_reports.columns.issue_date'), __('invoices::models/inv_reports.columns.employee'), __('pos::lang.total')];
            $dataExcel = [];
            foreach ($data['reports'] as $inv) {
                $dataExcel[] = [$inv->invoice_number, $inv->issue_date->format('Y-m-d'), $inv->user?->name, number_format($inv->total_inclusive_vat, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.returns'));
        }

        return view('pos::reports.returns.index', $data);
    }

    /**
     * Taxes Global Report
     */
    public function taxes(Request $request)
    {
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        
        $filters = $request->only(['fromDate', 'toDate']);
        $data['reports'] = $this->reportRepo->getTaxesReport($filters);

        if ($request->filled('export')) {
            $headers = [__('pos::lang.date'), 'المبلغ الخاضع للضريبة', 'الضريبة'];
            $dataExcel = [];
            foreach ($data['reports'] as $report) {
                $dataExcel[] = [$report->tax_date, number_format($report->taxable_amount, 2), number_format($report->tax_amount, 2)];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('pos::reports.types.taxes'));
        }

        return view('pos::reports.taxes.index', $data);
    }
}
