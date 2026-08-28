<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveInventoryPanel extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $kpis = [];

    public $stockStatusDonut = [];

    public $lowStockProducts = [];

    public function mount()
    {
        $this->loadInventoryData();
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

        $this->loadInventoryData();
    }

    private function resolveDateRange(): array
    {
        $now = \Carbon\Carbon::now();

        switch ($this->period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'this_quarter':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                break;
            case 'custom':
                $start = $this->startDate ? \Carbon\Carbon::parse($this->startDate)->startOfDay() : $now->copy()->startOfMonth();
                $end = $this->endDate ? \Carbon\Carbon::parse($this->endDate)->endOfDay() : $now->copy()->endOfDay();
                break;
            default: // this_year
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
        }

        return compact('start', 'end');
    }

    public function loadInventoryData()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();

        // ── 1. Total products ───────────────────────────────────────
        $productsQ = DB::table('products');
        if ($this->branchId !== 'all' || $this->storeId !== 'all') {
            $productsQ->join('stocks', 'products.id', '=', 'stocks.product_id')
                ->when($this->branchId !== 'all', fn ($q) => $q->where('stocks.branch_id', $this->branchId))
                ->when($this->storeId !== 'all', fn ($q) => $q->where('stocks.store_id', $this->storeId))
                ->distinct();
        }
        $totalProductsCount = (int) $productsQ->count('products.id');

        // ── 2. Stock Cost Value & Real Stock Sale Value ──
        $stockValues = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->when($this->branchId !== 'all', fn ($q) => $q->where('stocks.branch_id', $this->branchId))
            ->when($this->storeId !== 'all', fn ($q) => $q->where('stocks.store_id', $this->storeId))
            ->selectRaw('
                SUM(stocks.current_quantity * COALESCE(NULLIF(products.cost_price, 0), NULLIF(stocks.average_cost, 0), 0)) as cost_val,
                SUM(stocks.current_quantity * COALESCE(NULLIF(products.prod_price, 0), NULLIF(products.cost_price, 0) * 1.25, NULLIF(stocks.average_cost, 0) * 1.25, 0)) as sale_val
            ')
            ->first();

        $totalCostVal = max(0, (float) ($stockValues->cost_val ?? 0));
        $totalSaleVal = max(0, (float) ($stockValues->sale_val ?? 0));
        $expectedMargin = max(0, $totalSaleVal - $totalCostVal);

        // ── 3. Stock Status Counts (Single CASE WHEN query) ─────────
        $statusCounts = DB::table('stocks')
            ->when($this->branchId !== 'all', fn ($q) => $q->where('stocks.branch_id', $this->branchId))
            ->when($this->storeId !== 'all', fn ($q) => $q->where('stocks.store_id', $this->storeId))
            ->selectRaw('
                SUM(CASE WHEN current_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN current_quantity > 0 AND current_quantity <= min_quantity THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN current_quantity > min_quantity THEN 1 ELSE 0 END) as in_stock
            ')->first();

        $outOfStockCount = (int) ($statusCounts->out_of_stock ?? 0);
        $lowStockCount = (int) ($statusCounts->low_stock ?? 0);
        $availableCount = (int) ($statusCounts->in_stock ?? 0);

        // ── 4. Stock Turnover Ratio: (COGS / Stock Cost Value) ──────
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

        $totalCogs = (float) $cogsQ->sum(DB::raw('sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)'));

        $stockTurnover = $totalCostVal > 0 ? round($totalCogs / $totalCostVal, 1) : 0;

        // ── 5. Days of Stock ─────────────────────────────────────────
        $dailyCOGS = $totalCogs > 0 ? round($totalCogs / 365, 2) : 0;
        $daysOfStock = $dailyCOGS > 0 ? (int) round($totalCostVal / $dailyCOGS) : 0;
        $daysUnit = (app()->getLocale() === 'ar') ? 'يوم' : 'Days';

        $this->kpis = [
            'total_products' => $totalProductsCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'stock_value_cost' => round($totalCostVal, 2),
            'stock_value_sale' => round($totalSaleVal, 2),
            'expected_margin' => round($expectedMargin, 2),
            'stock_turnover' => $stockTurnover > 0 ? $stockTurnover.'x' : '—',
            'days_of_stock' => $daysOfStock > 0 ? number_format($daysOfStock).' '.$daysUnit : '—',
        ];

        $this->stockStatusDonut = [
            'labels' => [
                __('lang.in_stock_full'),
                __('lang.low_stock_label'),
                __('lang.out_of_stock_label'),
            ],
            'values' => [$availableCount, $lowStockCount, $outOfStockCount],
        ];

        // ── 6. Top low-stock products ────────────────────────────────
        $itemsQ = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->leftJoin('product_translations', function ($join) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', app()->getLocale() ?? 'ar');
            })
            ->select(
                DB::raw("COALESCE(product_translations.name, CONCAT('#', products.id)) as product_name"),
                'stocks.current_quantity',
                'stocks.min_quantity'
            )
            ->whereRaw('stocks.current_quantity <= stocks.min_quantity + 20');

        if ($this->branchId !== 'all') {
            $itemsQ->where('stocks.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $itemsQ->where('stocks.store_id', $this->storeId);
        }

        $items = $itemsQ->orderBy('stocks.current_quantity', 'asc')->limit(5)->get();

        $list = [];
        foreach ($items as $it) {
            $qty = (int) $it->current_quantity;
            $min = (int) ($it->min_quantity > 0 ? $it->min_quantity : 10);

            if ($qty <= 0) {
                $badge = __('lang.stock_out');
                $badgeClass = 'badge-danger';
            } elseif ($qty <= $min) {
                $badge = __('lang.stock_critical');
                $badgeClass = 'badge-danger';
            } else {
                $badge = __('lang.stock_low');
                $badgeClass = 'badge-warning';
            }

            $list[] = [
                'name' => mb_substr($it->product_name, 0, 30),
                'stock' => $qty,
                'min' => $min,
                'badge' => $badge,
                'badge_class' => $badgeClass,
            ];
        }

        $this->lowStockProducts = $list;

        $this->dispatch('update-inventory-chart', donut: $this->stockStatusDonut);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-inventory-panel', [
            'kpis' => $this->kpis,
            'stockStatusDonut' => $this->stockStatusDonut,
            'lowStockProducts' => $this->lowStockProducts,
        ]);
    }
}
