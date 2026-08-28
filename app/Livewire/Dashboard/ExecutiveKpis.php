<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveKpis extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $kpis = [];

    public function mount()
    {
        $this->loadExecutiveKpis();
    }

    #[On('executiveFiltersUpdated')]
    public function handleFiltersUpdated($branchId = 'all', $storeId = 'all', $period = 'this_month', $startDate = null, $endDate = null)
    {
        if (is_array($branchId)) {
            $data = $branchId;
            $branchId = $data['branchId'] ?? 'all';
            $storeId = $data['storeId'] ?? 'all';
            $period = $data['period'] ?? 'this_month';
            $startDate = $data['startDate'] ?? null;
            $endDate = $data['endDate'] ?? null;
        }

        $this->branchId = $branchId;
        $this->storeId = $storeId;
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->loadExecutiveKpis();
    }

    /**
     * Resolve the date range for the current period and prior period.
     */
    private function resolveDateRange(): array
    {
        $now = Carbon::now();

        switch ($this->period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $priorStart = $now->copy()->subDay()->startOfDay();
                $priorEnd = $now->copy()->subDay()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                $priorStart = $now->copy()->subDays(2)->startOfDay();
                $priorEnd = $now->copy()->subDays(2)->endOfDay();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $priorStart = $now->copy()->subWeek()->startOfWeek();
                $priorEnd = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $priorStart = $now->copy()->subMonth()->startOfMonth();
                $priorEnd = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                $priorStart = $now->copy()->subQuarter()->startOfQuarter();
                $priorEnd = $now->copy()->subQuarter()->endOfQuarter();
                break;
            case 'custom':
                $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : $now->copy()->startOfMonth();
                $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : $now->copy()->endOfDay();
                $diff = $start->diffInDays($end);
                $priorStart = $start->copy()->subDays($diff + 1)->startOfDay();
                $priorEnd = $start->copy()->subDay()->endOfDay();
                break;
            default: // this_year
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $priorStart = $now->copy()->subYear()->startOfYear();
                $priorEnd = $now->copy()->subYear()->endOfYear();
        }

        return compact('start', 'end', 'priorStart', 'priorEnd');
    }

    private function growthPercent(float $current, float $prior): ?float
    {
        if ($prior == 0) {
            return null; // Return null (N/A) instead of fake 100% when prior is 0
        }

        return round((($current - $prior) / $prior) * 100, 1);
    }

    public function loadExecutiveKpis()
    {
        ['start' => $start, 'end' => $end, 'priorStart' => $priorStart, 'priorEnd' => $priorEnd] = $this->resolveDateRange();

        // ── 1. Current Period Sales & Returns (Exclude Drafts: status != 1) ──
        $salesAggregate = DB::table('sales_invoices')
            ->whereNull('deleted_at')
            ->where('status', '!=', 1)
            ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $salesAggregate->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $salesAggregate->where('store_id', $this->storeId);
        }

        $sums = $salesAggregate->selectRaw('
            SUM(CASE WHEN type_inv IN (1, 4) THEN total_inclusive_vat ELSE 0 END) as sales,
            SUM(CASE WHEN type_inv IN (2, 5) THEN total_inclusive_vat ELSE 0 END) as returns,
            SUM(CASE WHEN type_inv IN (1, 4) THEN total_exclusive_vat ELSE 0 END) as sales_excl_vat,
            SUM(CASE WHEN type_inv IN (2, 5) THEN total_exclusive_vat ELSE 0 END) as returns_excl_vat
        ')->first();

        $totalSales = (float) ($sums->sales ?? 0);
        $totalReturns = (float) ($sums->returns ?? 0);
        $netSales = max(0, $totalSales - $totalReturns);
        $netSalesExclVat = max(0, (float) ($sums->sales_excl_vat ?? 0) - (float) ($sums->returns_excl_vat ?? 0));

        // ── 2. Prior Period Sales & Returns ──
        $priorSalesAggregate = DB::table('sales_invoices')
            ->whereNull('deleted_at')
            ->where('status', '!=', 1)
            ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$priorStart, $priorEnd]);
        if ($this->branchId !== 'all') {
            $priorSalesAggregate->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $priorSalesAggregate->where('store_id', $this->storeId);
        }

        $priorSums = $priorSalesAggregate->selectRaw('
            SUM(CASE WHEN type_inv IN (1, 4) THEN total_inclusive_vat ELSE 0 END) as sales,
            SUM(CASE WHEN type_inv IN (2, 5) THEN total_inclusive_vat ELSE 0 END) as returns,
            SUM(CASE WHEN type_inv IN (1, 4) THEN total_exclusive_vat ELSE 0 END) as sales_excl_vat,
            SUM(CASE WHEN type_inv IN (2, 5) THEN total_exclusive_vat ELSE 0 END) as returns_excl_vat
        ')->first();

        $priorTotalSales = (float) ($priorSums->sales ?? 0);
        $priorTotalReturns = (float) ($priorSums->returns ?? 0);
        $priorNetSales = max(0, $priorTotalSales - $priorTotalReturns);
        $priorNetSalesExclVat = max(0, (float) ($priorSums->sales_excl_vat ?? 0) - (float) ($priorSums->returns_excl_vat ?? 0));

        // ── 3. Purchases (Exclude Drafts: status != 1) ──
        $purchasesQ = DB::table('purchase_invoices')
            ->whereNull('deleted_at')
            ->where('status', '!=', 1)
            ->whereIn('type_inv', [1])
            ->whereBetween('created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $purchasesQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $purchasesQ->where('store_id', $this->storeId);
        }
        $totalPurchases = (float) $purchasesQ->sum('total_inclusive_vat');

        $priorPurchasesQ = DB::table('purchase_invoices')
            ->whereNull('deleted_at')
            ->where('status', '!=', 1)
            ->whereIn('type_inv', [1])
            ->whereBetween('created_at', [$priorStart, $priorEnd]);
        if ($this->branchId !== 'all') {
            $priorPurchasesQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $priorPurchasesQ->where('store_id', $this->storeId);
        }
        $priorTotalPurchases = (float) $priorPurchasesQ->sum('total_inclusive_vat');

        // ── 4. Cost of Goods Sold (COGS) ──
        $cogsQ = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('products', 'sales_invoice_items.product_id', '=', 'products.id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4])
            ->whereBetween(DB::raw('COALESCE(sales_invoices.issue_date, sales_invoices.created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $cogsQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $cogsQ->where('sales_invoices.store_id', $this->storeId);
        }
        $cogs = (float) $cogsQ->sum(DB::raw('sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)'));

        $priorCogsQ = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('products', 'sales_invoice_items.product_id', '=', 'products.id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4])
            ->whereBetween(DB::raw('COALESCE(sales_invoices.issue_date, sales_invoices.created_at)'), [$priorStart, $priorEnd]);
        if ($this->branchId !== 'all') {
            $priorCogsQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $priorCogsQ->where('sales_invoices.store_id', $this->storeId);
        }
        $priorCogs = (float) $priorCogsQ->sum(DB::raw('sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)'));

        // ── 5. Operating Expenses (account_type = 5) ──
        $expensesQ = DB::table('journal_entry_details')
            ->join('tree_accounts', 'journal_entry_details.tree_account_id', '=', 'tree_accounts.id')
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->whereNull('journal_entries.deleted_at')
            ->where('journal_entries.status', 2)
            ->where('tree_accounts.account_type', 5)
            ->whereBetween('journal_entries.entry_date', [$start, $end]);
        if ($this->branchId !== 'all') {
            $expensesQ->where('journal_entries.branch_id', $this->branchId);
        }
        $operatingExpenses = (float) $expensesQ->sum(DB::raw('journal_entry_details.debit - journal_entry_details.credit'));

        $priorExpensesQ = DB::table('journal_entry_details')
            ->join('tree_accounts', 'journal_entry_details.tree_account_id', '=', 'tree_accounts.id')
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->whereNull('journal_entries.deleted_at')
            ->where('journal_entries.status', 2)
            ->where('tree_accounts.account_type', 5)
            ->whereBetween('journal_entries.entry_date', [$priorStart, $priorEnd]);
        if ($this->branchId !== 'all') {
            $priorExpensesQ->where('journal_entries.branch_id', $this->branchId);
        }
        $priorOperatingExpenses = (float) $priorExpensesQ->sum(DB::raw('journal_entry_details.debit - journal_entry_details.credit'));

        // ── 6. Correct Net Profit Standard Equation ──
        // Gross Profit = Net Sales (Excl VAT) - COGS
        // Net Profit = Gross Profit - Operating Expenses
        $grossProfit = $netSalesExclVat - $cogs;
        $priorGrossProfit = $priorNetSalesExclVat - $priorCogs;

        $netProfit = $grossProfit - $operatingExpenses;
        $priorNetProfit = $priorGrossProfit - $priorOperatingExpenses;

        // ── 7. Receivables (Unpaid remaining balance excluding 'credit' payment method records) ──
        $pmtsSub = DB::table('sales_invoice_payments')
            ->where('payment_method_code', '!=', 'credit')
            ->select('sales_invoice_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('sales_invoice_id');

        $receivablesQ = DB::table('sales_invoices')
            ->leftJoinSub($pmtsSub, 'pmts', function ($join) {
                $join->on('sales_invoices.id', '=', 'pmts.sales_invoice_id');
            })
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4]);
        if ($this->branchId !== 'all') {
            $receivablesQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $receivablesQ->where('sales_invoices.store_id', $this->storeId);
        }
        $receivables = max(0, (float) $receivablesQ->sum(DB::raw('GREATEST(0, sales_invoices.total_inclusive_vat - COALESCE(pmts.total_paid, 0))')));

        // ── 8. Cash/Banks (Total collected payments in date range, excluding 'credit') ──
        $cashPaymentsQ = DB::table('sales_invoice_payments')
            ->join('sales_invoices', 'sales_invoice_payments.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->where('sales_invoice_payments.payment_method_code', '!=', 'credit')
            ->whereBetween('sales_invoice_payments.created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $cashPaymentsQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $cashPaymentsQ->where('sales_invoices.store_id', $this->storeId);
        }
        $cashPayments = (float) $cashPaymentsQ->sum('sales_invoice_payments.amount');

        $this->kpis = [
            'total_sales' => $totalSales,
            'net_sales' => $netSales,
            'total_purchases' => $totalPurchases,
            'net_profit' => $netProfit,
            'receivables' => $receivables,
            'cash_balances' => $cashPayments,

            // Growth % vs prior period
            'growth_sales' => $this->growthPercent($totalSales, $priorTotalSales),
            'growth_net_sales' => $this->growthPercent($netSales, $priorNetSales),
            'growth_purchases' => $this->growthPercent($totalPurchases, $priorTotalPurchases),
            'growth_profit' => $this->growthPercent($netProfit, $priorNetProfit),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.executive-kpis', [
            'kpis' => $this->kpis,
        ]);
    }
}
