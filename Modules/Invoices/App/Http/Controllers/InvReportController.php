<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\PurchaseInvoice;
use App\Models\invApp\SalesInvoiceItem;
use Modules\Invoices\App\Models\PurchaseInvoiceItem;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\InvSupplier;
use App\Models\StoreApp\Store;
use App\Models\BasicDataApp\Product;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\App\Repositories\SalesInvoiceRepository;
use Modules\Invoices\App\Repositories\PurchaseInvoiceRepository;
use Modules\Invoices\App\Repositories\InvReportRepository;
use App\Models\User;

class InvReportController extends Controller
{
    protected $reportRepo;
    protected $salesRepo;
    protected $purchaseRepo;

    public function __construct(InvReportRepository $reportRepo, SalesInvoiceRepository $salesRepo, PurchaseInvoiceRepository $purchaseRepo)
    {
        $this->reportRepo = $reportRepo;
        $this->salesRepo = $salesRepo;
        $this->purchaseRepo = $purchaseRepo;
    }

    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        return view('invoices::reports.index');
    }

    private function exportReport($exportType, $headers, $data, $name)
    {
        if ($exportType === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Invoices\App\Exports\InvoicesExport($data, $headers), $name . '.xlsx');
        } elseif ($exportType === 'csv') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Invoices\App\Exports\InvoicesExport($data, $headers), $name . '.csv', \Maatwebsite\Excel\Excel::CSV);
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
     * VAT Return Report (ZATCA Format)
     * تقرير الإقرار الضريبي (نموذج هيئة الزكاة)
     */
    public function taxReport(Request $request)
    {
        $data['branches'] = $this->reportRepo->getBranches();
        $data['fromDate'] = $request->get('fromDate', now()->startOfMonth()->format('Y-m-d'));
        $data['toDate'] = $request->get('toDate', now()->format('Y-m-d'));
        $data['data'] = $this->reportRepo->getTaxReportData($request->all());

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.tax.row_description'),
                __('invoices::models/inv_reports.tax.amount'),
                __('invoices::models/inv_reports.tax.adjustment'),
                __('invoices::models/inv_reports.tax.vat_amount'),
            ];
            $dataExcel = [];
            $salesTotalAdj = $data['data']['sales']['standard']['adj'] + $data['data']['sales']['zero']['adj'] + $data['data']['sales']['exempt']['adj'];
            $salesTotalVat = $data['data']['sales']['standard']['vat'] + $data['data']['sales']['zero']['vat'] + $data['data']['sales']['exempt']['vat'];

            $dataExcel[] = [__('invoices::models/inv_reports.tax.sales'), '', '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_standard'), number_format($data['data']['sales']['standard']['amount'], 2), number_format($data['data']['sales']['standard']['adj'], 2), number_format($data['data']['sales']['standard']['vat'], 2)];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_zero'), number_format($data['data']['sales']['zero']['amount'], 2), number_format($data['data']['sales']['zero']['adj'], 2), '0.00'];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_exempt'), number_format($data['data']['sales']['exempt']['amount'], 2), number_format($data['data']['sales']['exempt']['adj'], 2), '0.00'];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.total'), number_format($data['data']['sales']['standard']['amount'] + $data['data']['sales']['zero']['amount'] + $data['data']['sales']['exempt']['amount'], 2), number_format($salesTotalAdj, 2), number_format($salesTotalVat, 2)];

            $dataExcel[] = ['', '', '', ''];

            $purchasesTotalAdj = $data['data']['purchases']['standard']['adj'] + $data['data']['purchases']['zero']['adj'] + $data['data']['purchases']['exempt']['adj'];
            $purchasesTotalVat = $data['data']['purchases']['standard']['vat'] + $data['data']['purchases']['zero']['vat'] + $data['data']['purchases']['exempt']['vat'];

            $dataExcel[] = [__('invoices::models/inv_reports.tax.purchases'), '', '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_standard'), number_format($data['data']['purchases']['standard']['amount'], 2), number_format($data['data']['purchases']['standard']['adj'], 2), number_format($data['data']['purchases']['standard']['vat'], 2)];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_zero'), number_format($data['data']['purchases']['zero']['amount'], 2), number_format($data['data']['purchases']['zero']['adj'], 2), '0.00'];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.vat_exempt'), number_format($data['data']['purchases']['exempt']['amount'], 2), number_format($data['data']['purchases']['exempt']['adj'], 2), '0.00'];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.total'), number_format($data['data']['purchases']['standard']['amount'] + $data['data']['purchases']['zero']['amount'] + $data['data']['purchases']['exempt']['amount'], 2), number_format($purchasesTotalAdj, 2), number_format($purchasesTotalVat, 2)];

            $dataExcel[] = ['', '', '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.summary_rows.total_due'), number_format($data['data']['summary']['total_due'], 2), '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.summary_rows.carried_forward'), number_format($data['data']['summary']['carried_forward'], 2), '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.summary_rows.corrections'), number_format($data['data']['summary']['corrections'], 2), '', ''];
            $dataExcel[] = [__('invoices::models/inv_reports.tax.summary_rows.net_final'), number_format($data['data']['summary']['net_final'], 2), '', ''];

            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.tax_report'));
        }

        return view('invoices::reports.taxReport.index', $data);
    }

    /**
     * Customer Aging Report (Accounts Receivable)
     * تقرير أعمار ديون العملاء
     */
    public function customerAging(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['customers'] = $this->reportRepo->getCustomers();
        $data['data'] = $this->reportRepo->getCustomerAging($request->all());

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.customer'),
                __('invoices::models/inv_reports.columns.current') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.current') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.1_30') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.1_30') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.31_60') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.31_60') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.61_90') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.61_90') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.over_90') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.over_90') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('lang.total') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('lang.total') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.total_due')
            ];
            $dataExcel = [];
            foreach ($data['data'] as $item) {
                $dataExcel[] = [
                    $item->name,
                    number_format($item->aging['current']['debit'] ?? 0, 2),
                    number_format($item->aging['current']['credit'] ?? 0, 2),
                    number_format($item->aging['1_30']['debit'] ?? 0, 2),
                    number_format($item->aging['1_30']['credit'] ?? 0, 2),
                    number_format($item->aging['31_60']['debit'] ?? 0, 2),
                    number_format($item->aging['31_60']['credit'] ?? 0, 2),
                    number_format($item->aging['61_90']['debit'] ?? 0, 2),
                    number_format($item->aging['61_90']['credit'] ?? 0, 2),
                    number_format($item->aging['over_90']['debit'] ?? 0, 2),
                    number_format($item->aging['over_90']['credit'] ?? 0, 2),
                    number_format($item->aging['total']['debit'] ?? 0, 2),
                    number_format($item->aging['total']['credit'] ?? 0, 2),
                    number_format($item->aging['total']['balance'] ?? 0, 2)
                ];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.customer_aging'));
        }

        return view('invoices::reports.aging.customer', $data);
    }

    /**
     * Supplier Aging Report (Accounts Payable)
     * تقرير أعمار ديون الموردين
     */
    public function supplierAging(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['suppliers'] = $this->reportRepo->getSuppliers();
        $data['data'] = $this->reportRepo->getSupplierAging($request->all());

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.supplier'),
                __('invoices::models/inv_reports.columns.current') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.current') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.1_30') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.1_30') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.31_60') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.31_60') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.61_90') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.61_90') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.over_90') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('invoices::models/inv_reports.columns.over_90') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('lang.total') . ' (' . __('invoices::models/inv_reports.columns.debit') . ')',
                __('lang.total') . ' (' . __('invoices::models/inv_reports.columns.credit') . ')',
                __('invoices::models/inv_reports.columns.total_due')
            ];
            $dataExcel = [];
            foreach ($data['data'] as $item) {
                $dataExcel[] = [
                    $item->name,
                    number_format($item->aging['current']['debit'] ?? 0, 2),
                    number_format($item->aging['current']['credit'] ?? 0, 2),
                    number_format($item->aging['1_30']['debit'] ?? 0, 2),
                    number_format($item->aging['1_30']['credit'] ?? 0, 2),
                    number_format($item->aging['31_60']['debit'] ?? 0, 2),
                    number_format($item->aging['31_60']['credit'] ?? 0, 2),
                    number_format($item->aging['61_90']['debit'] ?? 0, 2),
                    number_format($item->aging['61_90']['credit'] ?? 0, 2),
                    number_format($item->aging['over_90']['debit'] ?? 0, 2),
                    number_format($item->aging['over_90']['credit'] ?? 0, 2),
                    number_format($item->aging['total']['debit'] ?? 0, 2),
                    number_format($item->aging['total']['credit'] ?? 0, 2),
                    number_format($item->aging['total']['balance'] ?? 0, 2)
                ];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.supplier_aging'));
        }

        return view('invoices::reports.aging.supplier', $data);
    }

    /**
     * Product Profit Report
     * تقرير أرباح المنتجات
     */
    public function productProfit(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['fromDate'] = $request->get('fromDate', now()->startOfMonth()->format('Y-m-d'));
        $data['toDate'] = $request->get('toDate', now()->format('Y-m-d'));

        $data['data'] = $this->reportRepo->getProductProfit($request->all());

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.product'),
                __('invoices::models/inv_reports.columns.quantity'),
                __('invoices::models/inv_reports.columns.sales_total'),
                __('invoices::models/inv_reports.columns.cost_total'),
                __('invoices::models/inv_reports.columns.profit'),
                __('invoices::models/inv_reports.columns.margin') . ' %'
            ];
            $dataExcel = [];
            foreach ($data['data'] as $item) {
                $margin = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales) * 100 : 0;
                $dataExcel[] = [
                    $item->product_name,
                    number_format($item->total_qty, 2),
                    number_format($item->total_sales, 2),
                    number_format($item->total_cost, 2),
                    number_format($item->total_profit, 2),
                    number_format($margin, 1) . '%'
                ];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.product_profit'));
        }

        return view('invoices::reports.profit.index', $data);
    }

    /**
     * Daily Summary Report
     * ملخص اليوم
     */
    public function dailySummary(Request $request)
    {
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['employees'] = $this->reportRepo->getEmployees();

        $data['fromDate'] = $request->get('fromDate', now()->startOfMonth()->format('Y-m-d'));
        $data['toDate'] = $request->get('toDate', now()->format('Y-m-d'));

        $data['data'] = $this->reportRepo->getDailySummary($request->all());

        if ($request->filled('export')) {
            $headers = [
                __('lang.description'),
                __('lang.amount')
            ];
            $dataExcel = [];
            $dataExcel[] = [__('invoices::models/inv_reports.columns.sales_total'), number_format($data['data']['sales']['total'] ?? 0, 2)];
            $dataExcel[] = [__('invoices::models/inv_reports.columns.sales_return'), number_format($data['data']['sales_return']['total'] ?? 0, 2)];
            $dataExcel[] = [__('invoices::models/inv_reports.columns.purchases_total'), number_format($data['data']['purchases']['total'] ?? 0, 2)];
            $dataExcel[] = [__('invoices::models/inv_reports.columns.purchases_return'), number_format($data['data']['purchases_return']['total'] ?? 0, 2)];

            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.daily_summary'));
        }

        return view('invoices::reports.daily.index', $data);
    }

    public function salesInvoices(Request $request)
    {
        $data['customers'] = $this->reportRepo->getCustomers();
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['invoice_types'] = $this->reportRepo->getInvoiceTypes();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        // الفلترة الشاملة

        $filters = $request->only(['customer_id', 'store_id', 'branch_id', 'user_id', 'type_inv', 'fromDate', 'toDate']);
        // جلب البيانات
        $data['invoices'] = $this->reportRepo->getInvoicesReport($filters, SalesInvoice::class);

        // إذا كان الطلب AJAX، نرجع البيانات كـ JSON
        if ($request->ajax()) {
            return response()->json($data);
        }

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.invoice_number'),
                __('invoices::models/inv_reports.columns.type'),
                __('invoices::models/inv_reports.columns.issue_date'),
                __('invoices::models/inv_reports.columns.customer'),
                __('invoices::models/inv_reports.columns.employee'),
                __('invoices::models/inv_reports.columns.total_exclusive_vat'),
                __('invoices::models/inv_reports.columns.total_vat'),
                __('invoices::models/inv_reports.columns.total_inclusive_vat'),
                __('invoices::models/inv_reports.columns.status')
            ];
            $dataExcel = [];
            foreach ($data['invoices'] as $inv) {
                $isReturn = $inv->type_inv == SalesInvoice::TYPE_RETURN;
                $sign = $isReturn ? '-' : '';
                $dataExcel[] = [
                    $inv->invoice_number,
                    $inv->type_text,
                    $inv->issue_date->format('Y-m-d'),
                    $inv->customer?->name,
                    $inv->user?->name,
                    $sign . number_format($inv->total_exclusive_vat, 2),
                    $sign . number_format($inv->total_vat, 2),
                    $sign . number_format($inv->total_inclusive_vat, 2),
                    $inv->status_text
                ];
            }
            $accTotals = $data['invoices']->accountingTotals ?? null;
            if ($accTotals) {
                $dataExcel[] = ['', '', '', '', '', '', '', '', ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.gross_total'), '', '', '', '', number_format($accTotals['gross_exclusive_vat'], 2), number_format($accTotals['gross_vat'], 2), number_format($accTotals['gross_inclusive_vat'], 2), ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.total_returns'), '', '', '', '', '-' . number_format($accTotals['return_exclusive_vat'], 2), '-' . number_format($accTotals['return_vat'], 2), '-' . number_format($accTotals['return_inclusive_vat'], 2), ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.final_net'), '', '', '', '', number_format($accTotals['net_exclusive_vat'], 2), number_format($accTotals['net_vat'], 2), number_format($accTotals['net_inclusive_vat'], 2), ''];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.sales_invoices'));
        }

        return view('invoices::reports.salesInvoices.index', $data);
    }

    /**
     * Purchase Invoices Report
     */

    public function purchaseInvoices(Request $request)
    {
        $data['suppliers'] = $this->reportRepo->getSuppliers();
        $data['stores'] = $this->reportRepo->getStores();
        $data['branches'] = $this->reportRepo->getBranches();
        $data['employees'] = $this->reportRepo->getEmployees();
        $data['invoice_types'] = $this->reportRepo->getPurchaseInvoiceTypes();
        $data['fromDate'] = $request->get('fromDate', $this->reportRepo->getFromDate());
        $data['toDate'] = $request->get('toDate', $this->reportRepo->getToDate());
        // الفلترة الشاملة


        $filters = $request->only(['supplier_id', 'store_id', 'branch_id', 'user_id', 'type_inv', 'fromDate', 'toDate']);
        // جلب البيانات
        $data['invoices'] = $this->reportRepo->getInvoicesReport($filters, PurchaseInvoice::class);
        $data['statuses'] = PurchaseInvoice::statuses();

        if ($request->filled('export')) {
            $headers = [
                __('invoices::models/inv_reports.columns.invoice_number'),
                __('invoices::models/inv_reports.columns.type'),
                __('invoices::models/inv_reports.columns.issue_date'),
                __('invoices::models/inv_reports.columns.supplier'),
                __('invoices::models/inv_reports.columns.employee'),
                __('invoices::models/inv_reports.columns.total_exclusive_vat'),
                __('invoices::models/inv_reports.columns.total_vat'),
                __('invoices::models/inv_reports.columns.total_inclusive_vat'),
                __('invoices::models/inv_reports.columns.status')
            ];
            $dataExcel = [];
            foreach ($data['invoices'] as $inv) {
                $isReturn = $inv->type_inv == PurchaseInvoice::TYPE_RETURN;
                $sign = $isReturn ? '-' : '';
                $dataExcel[] = [
                    $inv->invoice_number,
                    $inv->type_text,
                    $inv->issue_date->format('Y-m-d'),
                    $inv->supplier?->name,
                    $inv->createdBy?->name ?? $inv->user?->name,
                    $sign . number_format($inv->total_exclusive_vat, 2),
                    $sign . number_format($inv->total_vat, 2),
                    $sign . number_format($inv->total_inclusive_vat, 2),
                    $inv->status_text
                ];
            }
            $accTotals = $data['invoices']->accountingTotals ?? null;
            if ($accTotals) {
                $dataExcel[] = ['', '', '', '', '', '', '', '', ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.gross_total'), '', '', '', '', number_format($accTotals['gross_exclusive_vat'], 2), number_format($accTotals['gross_vat'], 2), number_format($accTotals['gross_inclusive_vat'], 2), ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.total_returns'), '', '', '', '', '-' . number_format($accTotals['return_exclusive_vat'], 2), '-' . number_format($accTotals['return_vat'], 2), '-' . number_format($accTotals['return_inclusive_vat'], 2), ''];
                $dataExcel[] = [__('invoices::models/inv_reports.accounting_totals.final_net'), '', '', '', '', number_format($accTotals['net_exclusive_vat'], 2), number_format($accTotals['net_vat'], 2), number_format($accTotals['net_inclusive_vat'], 2), ''];
            }
            return $this->exportReport($request->input('export'), $headers, $dataExcel, __('invoices::models/inv_reports.types.purchase_invoices'));
        }

        return view('invoices::reports.purchaseInvoices.index', $data);
    }
}

