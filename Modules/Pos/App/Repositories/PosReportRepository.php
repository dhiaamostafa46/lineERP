<?php

namespace Modules\Pos\App\Repositories;

use App\Models\invApp\SalesInvoice;
use Modules\Pos\App\Models\PosSession;
use App\Models\invApp\InvCustomer;
use App\Models\StoreApp\Store;
use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosReportRepository
{
    /**
     * Get all customers for filters
     */
    public function getCustomers()
    {
        return InvCustomer::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all stores for filters
     */
    public function getStores()
    {
        return Store::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all branches for filters
     */
    public function getBranches()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get all employees (users) for filters
     */
    public function getEmployees()
    {
        return User::active()->get()->pluck('name', 'id')->toArray();
    }
    
    /**
     * Get all POS Devices for filters
     */
    public function getDevices()
    {
        return \Modules\Pos\App\Models\PosDevice::where('is_active', true)->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Get default from date
     */
    public function getFromDate()
    {
        return now()->startOfMonth()->format('Y-m-d');
    }

    /**
     * Get default to date
     */
    public function getToDate()
    {
        return now()->format('Y-m-d');
    }

    /**
     * Generic POS Sales Report
     */
    public function getPosSalesReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = SalesInvoice::with(['user', 'customer', 'store', 'branch', 'posSession.device'])
            ->whereIn('type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS]);

        // Date Range
        if ($fromDate && $toDate) {
            $query->whereBetween('issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        // Additional Filters
        if (!empty($filters['customer_id'])) $query->where('customer_id', $filters['customer_id']);
        if (!empty($filters['user_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('created_by', $filters['user_id'])->orWhere('user_id', $filters['user_id']);
            });
        }
        if (!empty($filters['store_id'])) $query->where('store_id', $filters['store_id']);
        if (!empty($filters['branch_id'])) $query->where('branch_id', $filters['branch_id']);
        if (!empty($filters['pos_session_id'])) $query->where('pos_session_id', $filters['pos_session_id']);
        if (!empty($filters['device_id'])) {
            $query->whereHas('posSession', function ($q) use ($filters) {
                $q->where('device_id', $filters['device_id']);
            });
        }

        $totalsQuery = clone $query;
        $allInvoices = $totalsQuery->get();

        $grossSalesExclVat = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_POS)->sum('total_exclusive_vat');
        $grossSalesVat     = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_POS)->sum('total_vat');
        $grossSalesInclVat = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_POS)->sum('total_inclusive_vat');

        $returnsExclVat    = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_RETURN_POS)->sum('total_exclusive_vat');
        $returnsVat        = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_RETURN_POS)->sum('total_vat');
        $returnsInclVat    = (float) $allInvoices->where('type_inv', SalesInvoice::TYPE_RETURN_POS)->sum('total_inclusive_vat');

        $netExclVat = $grossSalesExclVat - $returnsExclVat;
        $netVat     = $grossSalesVat - $returnsVat;
        $netInclVat = $grossSalesInclVat - $returnsInclVat;

        $results = $query->orderBy('issue_date', 'desc')->paginate($perPage);
        $results->accountingTotals = [
            'gross_exclusive_vat' => $grossSalesExclVat,
            'gross_vat'           => $grossSalesVat,
            'gross_inclusive_vat' => $grossSalesInclVat,
            'return_exclusive_vat'=> $returnsExclVat,
            'return_vat'          => $returnsVat,
            'return_inclusive_vat'=> $returnsInclVat,
            'net_exclusive_vat'   => $netExclVat,
            'net_vat'             => $netVat,
            'net_inclusive_vat'   => $netInclVat,
        ];

        return $results;
    }
    
    /**
     * Generic POS Sessions Report
     */
    public function getSessionsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = PosSession::with(['device', 'cashier']);

        // Date Range (using opened_at)
        if ($fromDate && $toDate) {
            $query->whereBetween('opened_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        // Additional Filters
        if (!empty($filters['device_id'])) $query->where('device_id', $filters['device_id']);
        if (!empty($filters['user_id'])) $query->where('user_id', $filters['user_id']);
        if (!empty($filters['status'])) $query->where('status', $filters['status']);

        return $query->orderBy('opened_at', 'desc')->paginate($perPage);
    }

    /**
     * Category Sales Report
     */
    public function getCategorySalesReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                     ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->whereIn('sales_invoices.type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        if (!empty($filters['branch_id'])) $query->where('sales_invoices.branch_id', $filters['branch_id']);
        if (!empty($filters['store_id'])) $query->where('sales_invoices.store_id', $filters['store_id']);

        $query->select(
            'categories.id as category_id',
            'category_translations.name as category_name',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.quantity ELSE sales_invoice_items.quantity END) as total_quantity'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.subtotal_with_vat ELSE sales_invoice_items.subtotal_with_vat END) as total_amount')
        )->groupBy('categories.id', 'category_translations.name')
         ->orderBy('total_amount', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_quantity) as grand_quantity, SUM(total_amount) as grand_amount')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Product Sales Report
     */
    public function getProductSalesReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->whereIn('sales_invoices.type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        if (!empty($filters['branch_id'])) $query->where('sales_invoices.branch_id', $filters['branch_id']);
        if (!empty($filters['store_id'])) $query->where('sales_invoices.store_id', $filters['store_id']);

        $query->select(
            'sales_invoice_items.product_id',
            'sales_invoice_items.product_name',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.quantity ELSE sales_invoice_items.quantity END) as total_quantity'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.subtotal_with_vat ELSE sales_invoice_items.subtotal_with_vat END) as total_amount')
        )->groupBy('sales_invoice_items.product_id', 'sales_invoice_items.product_name')
         ->orderBy('total_amount', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_quantity) as grand_quantity, SUM(total_amount) as grand_amount')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Sessions Detailed Report
     */
    public function getSessionsDetailedReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = SalesInvoice::with(['user', 'customer', 'store', 'branch'])
            ->where('type_inv', SalesInvoice::TYPE_POS)
            ->whereNotNull('pos_session_id');

        if ($fromDate && $toDate) {
            $query->whereBetween('issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        if (!empty($filters['pos_session_id'])) $query->where('pos_session_id', $filters['pos_session_id']);
        if (!empty($filters['user_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('created_by', $filters['user_id'])->orWhere('user_id', $filters['user_id']);
            });
        }

        $cloned = clone $query;
        $totals = new \stdClass();
        $totals->grand_amount = $cloned->sum('total_inclusive_vat');

        $paginator = $query->orderBy('pos_session_id', 'desc')->orderBy('issue_date', 'desc')->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Profit by Session Report
     */
    public function getProfitSessionsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->whereIn('sales_invoices.type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->whereNotNull('sales_invoices.pos_session_id');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        if (!empty($filters['pos_session_id'])) $query->where('sales_invoices.pos_session_id', $filters['pos_session_id']);

        $query->select(
            'sales_invoices.pos_session_id',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.quantity ELSE sales_invoice_items.quantity END) as total_quantity'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) ELSE (sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) END) as total_revenue'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(products.cost_price * sales_invoice_items.quantity) ELSE (products.cost_price * sales_invoice_items.quantity) END) as total_cost'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) ELSE ((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) END) as total_profit')
        )->groupBy('sales_invoices.pos_session_id')
         ->orderBy('sales_invoices.pos_session_id', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_quantity) as grand_quantity, SUM(total_revenue) as grand_revenue, SUM(total_cost) as grand_cost, SUM(total_profit) as grand_profit')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Profit by Category Report
     */
    public function getProfitCategoriesReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('category_translations', function ($join) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                     ->where('category_translations.locale', '=', app()->getLocale());
            })
            ->whereIn('sales_invoices.type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $query->select(
            'categories.id as category_id',
            'category_translations.name as category_name',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.quantity ELSE sales_invoice_items.quantity END) as total_quantity'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) ELSE (sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) END) as total_revenue'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(products.cost_price * sales_invoice_items.quantity) ELSE (products.cost_price * sales_invoice_items.quantity) END) as total_cost'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) ELSE ((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) END) as total_profit')
        )->groupBy('categories.id', 'category_translations.name')
         ->orderBy('total_profit', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_quantity) as grand_quantity, SUM(total_revenue) as grand_revenue, SUM(total_cost) as grand_cost, SUM(total_profit) as grand_profit')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Profit by Product Report
     */
    public function getProfitProductsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->join('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->whereIn('sales_invoices.type_inv', [SalesInvoice::TYPE_POS, SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $query->select(
            'sales_invoice_items.product_id',
            'sales_invoice_items.product_name',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_items.quantity ELSE sales_invoice_items.quantity END) as total_quantity'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) ELSE (sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) END) as total_revenue'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -(products.cost_price * sales_invoice_items.quantity) ELSE (products.cost_price * sales_invoice_items.quantity) END) as total_cost'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . SalesInvoice::TYPE_RETURN_POS . ' THEN -((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) ELSE ((sales_invoice_items.unit_price * sales_invoice_items.quantity - sales_invoice_items.total_discount) - (products.cost_price * sales_invoice_items.quantity)) END) as total_profit')
        )->groupBy('sales_invoice_items.product_id', 'sales_invoice_items.product_name')
         ->orderBy('total_profit', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_quantity) as grand_quantity, SUM(total_revenue) as grand_revenue, SUM(total_cost) as grand_cost, SUM(total_profit) as grand_profit')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Payment Methods Report
     */
    public function getPaymentMethodsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoice_payments')
            ->join('sales_invoices', 'sales_invoice_payments.sales_invoice_id', '=', 'sales_invoices.id')
            ->join('pos_payment_methods', 'sales_invoice_payments.payment_method_code', '=', 'pos_payment_methods.id')
            ->whereIn('sales_invoices.type_inv', [\App\Models\invApp\SalesInvoice::TYPE_POS, \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }
        
        // Negative amount for returns
        $query->select(
            'sales_invoice_payments.payment_method_code',
            'pos_payment_methods.name as payment_method_name',
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoice_payments.amount ELSE sales_invoice_payments.amount END) as total_amount')
        )->groupBy('sales_invoice_payments.payment_method_code', 'pos_payment_methods.name')
         ->orderBy('total_amount', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(total_amount) as grand_amount')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Cash Movements Report
     */
    public function getCashMovementsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = \Modules\Pos\App\Models\PosSessionTransaction::with(['session', 'user'])
            ->whereIn('type', ['deposit', 'withdrawal'])
            ->whereHas('session', function($q) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $q->whereBetween('opened_at', [
                        Carbon::parse($fromDate)->startOfDay(),
                        Carbon::parse($toDate)->endOfDay()
                    ]);
                }
            });
            
        $cloned = clone $query;
        $totals = new \stdClass();
        $totals->total_deposits = (clone $query)->where('type', 'deposit')->sum('amount');
        $totals->total_withdrawals = (clone $query)->where('type', 'withdrawal')->sum('amount');

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Returns Report
     */
    public function getReturnsReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = \App\Models\invApp\SalesInvoice::with(['user', 'customer', 'store', 'branch'])
            ->where('type_inv', \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS);

        if ($fromDate && $toDate) {
            $query->whereBetween('issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $cloned = clone $query;
        $totals = new \stdClass();
        $totals->grand_amount = $cloned->sum('total_inclusive_vat');

        $paginator = $query->orderBy('issue_date', 'desc')->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }

    /**
     * Taxes Report
     */
    public function getTaxesReport($filters, $perPage = 50)
    {
        $fromDate = !empty($filters['fromDate']) ? $filters['fromDate'] : $this->getFromDate();
        $toDate = !empty($filters['toDate']) ? $filters['toDate'] : $this->getToDate();

        $query = DB::table('sales_invoices')
            ->whereIn('sales_invoices.type_inv', [\App\Models\invApp\SalesInvoice::TYPE_POS, \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS])
            ->where('sales_invoices.status', '!=', 'cancelled');

        if ($fromDate && $toDate) {
            $query->whereBetween('sales_invoices.issue_date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $query->select(
            DB::raw('DATE(sales_invoices.issue_date) as tax_date'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoices.total_exclusive_vat ELSE sales_invoices.total_exclusive_vat END) as taxable_amount'),
            DB::raw('SUM(CASE WHEN sales_invoices.type_inv = ' . \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS . ' THEN -sales_invoices.total_vat ELSE sales_invoices.total_vat END) as tax_amount')
        )->groupBy(DB::raw('DATE(sales_invoices.issue_date)'))
         ->orderBy('tax_date', 'desc');

        $cloned = clone $query;
        $totals = DB::table(DB::raw("({$cloned->toSql()}) as sub"))
            ->mergeBindings($cloned)
            ->selectRaw('SUM(taxable_amount) as grand_taxable, SUM(tax_amount) as grand_tax')
            ->first();

        $paginator = $query->paginate($perPage);
        $paginator->grand_totals = $totals;
        return $paginator;
    }
}
