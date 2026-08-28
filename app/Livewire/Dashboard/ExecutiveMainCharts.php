<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveMainCharts extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $productToggle = 'value';

    public $mainTrend = [];

    public $branchSales = [];

    public $paymentMethods = [];

    public $topProducts = [];

    public function mount()
    {
        $this->loadChartData();
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

        $this->loadChartData();
    }

    public function toggleProductView($type)
    {
        $this->productToggle = $type;
        $this->loadTopProducts();
    }

    public function getProductTitle(): string
    {
        return match ($this->productToggle) {
            'quantity' => __('lang.chart_products_title_qty'),
            'profit' => __('lang.chart_products_title_profit'),
            default => __('lang.chart_products_title_value'),
        };
    }

    private function resolveDateRange(): array
    {
        $now = Carbon::now();

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
                $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : $now->copy()->startOfMonth();
                $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : $now->copy()->endOfDay();
                break;
            default: // this_year
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
        }

        return compact('start', 'end');
    }

    public function loadChartData()
    {
        $this->loadMainTrend();
        $this->loadBranchSales();
        $this->loadPaymentMethods();
        $this->loadTopProducts();

        $this->dispatch('update-main-charts',
            mainTrend: $this->mainTrend,
            paymentMethods: $this->paymentMethods,
            branchSales: $this->branchSales,
            topProducts: $this->topProducts
        );
    }

    public function loadMainTrend()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();

        $locale = app()->getLocale() ?? 'ar';
        $am = __('lang.am') ?: ($locale === 'ar' ? 'ص' : 'AM');
        $pm = __('lang.pm') ?: ($locale === 'ar' ? 'م' : 'PM');

        $monthNames = [
            1 => __('lang.month_1') ?: 'يناير',
            2 => __('lang.month_2') ?: 'فبراير',
            3 => __('lang.month_3') ?: 'مارس',
            4 => __('lang.month_4') ?: 'أبريل',
            5 => __('lang.month_5') ?: 'مايو',
            6 => __('lang.month_6') ?: 'يونيو',
            7 => __('lang.month_7') ?: 'يوليو',
            8 => __('lang.month_8') ?: 'أغسطس',
            9 => __('lang.month_9') ?: 'سبتمبر',
            10 => __('lang.month_10') ?: 'أكتوبر',
            11 => __('lang.month_11') ?: 'نوفمبر',
            12 => __('lang.month_12') ?: 'ديسمبر',
        ];

        // 1. Sales query
        $salesQ = DB::table('sales_invoices')
            ->whereNull('deleted_at')->where('status', '!=', 1)->whereIn('type_inv', [1, 4])
            ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $salesQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $salesQ->where('store_id', $this->storeId);
        }

        // 2. Purchases query
        $purchasesQ = DB::table('purchase_invoices')
            ->whereNull('deleted_at')->where('status', '!=', 1)->whereIn('type_inv', [1])
            ->whereBetween('created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $purchasesQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $purchasesQ->where('store_id', $this->storeId);
        }

        // 3. Returns query
        $returnsQ = DB::table('sales_invoices')
            ->whereNull('deleted_at')->where('status', '!=', 1)->whereIn('type_inv', [2, 5])
            ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $returnsQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $returnsQ->where('store_id', $this->storeId);
        }

        // 4. COGS query
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

        // 5. Operating Expenses
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

        $categories = [];
        $sales = [];
        $purchases = [];
        $expenses = [];
        $profit = [];

        if (in_array($this->period, ['today', 'yesterday'])) {
            // ── Hourly breakdown for single day ──
            $hoursMap = [
                0 => "12 {$am}", 2 => "02 {$am}", 4 => "04 {$am}", 6 => "06 {$am}",
                8 => "08 {$am}", 10 => "10 {$am}", 12 => "12 {$pm}", 14 => "02 {$pm}",
                16 => "04 {$pm}", 18 => "06 {$pm}", 20 => "08 {$pm}", 22 => "10 {$pm}",
            ];

            $salesData = (clone $salesQ)->selectRaw('HOUR(COALESCE(issue_date, created_at)) as h, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('h')->get()->keyBy('h');
            $purchasesData = (clone $purchasesQ)->selectRaw('HOUR(created_at) as h, SUM(total_inclusive_vat) as val')->groupBy('h')->pluck('val', 'h')->toArray();
            $returnsData = (clone $returnsQ)->selectRaw('HOUR(COALESCE(issue_date, created_at)) as h, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('h')->get()->keyBy('h');
            $cogsData = (clone $cogsQ)->selectRaw('HOUR(COALESCE(sales_invoices.issue_date, sales_invoices.created_at)) as h, SUM(sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)) as val')->groupBy('h')->pluck('val', 'h')->toArray();
            $expensesData = (clone $expensesQ)->selectRaw('HOUR(journal_entries.created_at) as h, SUM(journal_entry_details.debit - journal_entry_details.credit) as val')->groupBy('h')->pluck('val', 'h')->toArray();

            foreach ($hoursMap as $hSlot => $label) {
                $categories[] = $label;
                $sVal = 0; $sExcl = 0; $rExcl = 0; $pVal = 0; $cVal = 0; $eVal = 0;
                for ($step = 0; $step < 2; $step++) {
                    $curH = $hSlot + $step;
                    $sRow = $salesData->get($curH);
                    $rRow = $returnsData->get($curH);
                    $sVal += (float) ($sRow->val ?? 0);
                    $sExcl += (float) ($sRow->val_excl ?? 0);
                    $rExcl += (float) ($rRow->val_excl ?? 0);
                    $pVal += (float) ($purchasesData[$curH] ?? 0);
                    $cVal += (float) ($cogsData[$curH] ?? 0);
                    $eVal += (float) ($expensesData[$curH] ?? 0);
                }

                $grossProfit = $sExcl - $rExcl - $cVal;
                $prof = round($grossProfit - $eVal, 2);

                $sales[] = round($sVal, 2);
                $purchases[] = round($pVal, 2);
                $expenses[] = max(0, round($eVal, 2));
                $profit[] = $prof;
            }
        } elseif ($this->period === 'this_week') {
            // ── 7-Day breakdown for the week ──
            $dayNames = [
                'Saturday' => __('lang.day_saturday') ?: 'السبت',
                'Sunday' => __('lang.day_sunday') ?: 'الأحد',
                'Monday' => __('lang.day_monday') ?: 'الإثنين',
                'Tuesday' => __('lang.day_tuesday') ?: 'الثلاثاء',
                'Wednesday' => __('lang.day_wednesday') ?: 'الأربعاء',
                'Thursday' => __('lang.day_thursday') ?: 'الخميس',
                'Friday' => __('lang.day_friday') ?: 'الجمعة',
            ];

            $salesData = (clone $salesQ)->selectRaw('DATE(COALESCE(issue_date, created_at)) as dt, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('dt')->get()->keyBy('dt');
            $purchasesData = (clone $purchasesQ)->selectRaw('DATE(created_at) as dt, SUM(total_inclusive_vat) as val')->groupBy('dt')->pluck('val', 'dt')->toArray();
            $returnsData = (clone $returnsQ)->selectRaw('DATE(COALESCE(issue_date, created_at)) as dt, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('dt')->get()->keyBy('dt');
            $cogsData = (clone $cogsQ)->selectRaw('DATE(COALESCE(sales_invoices.issue_date, sales_invoices.created_at)) as dt, SUM(sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)) as val')->groupBy('dt')->pluck('val', 'dt')->toArray();
            $expensesData = (clone $expensesQ)->selectRaw('DATE(journal_entries.entry_date) as dt, SUM(journal_entry_details.debit - journal_entry_details.credit) as val')->groupBy('dt')->pluck('val', 'dt')->toArray();

            for ($i = 0; $i < 7; $i++) {
                $dayDate = $start->copy()->addDays($i);
                $dateStr = $dayDate->format('Y-m-d');
                $engDay = $dayDate->format('l');
                $categories[] = $dayNames[$engDay] ?? $dayDate->format('d/m');

                $sRow = $salesData->get($dateStr);
                $rRow = $returnsData->get($dateStr);

                $s = round((float) ($sRow->val ?? 0), 2);
                $sExcl = (float) ($sRow->val_excl ?? 0);
                $p = round((float) ($purchasesData[$dateStr] ?? 0), 2);
                $rExcl = (float) ($rRow->val_excl ?? 0);
                $c = round((float) ($cogsData[$dateStr] ?? 0), 2);
                $e = max(0, round((float) ($expensesData[$dateStr] ?? 0), 2));

                $grossProfit = $sExcl - $rExcl - $c;
                $prof = round($grossProfit - $e, 2);

                $sales[] = $s;
                $purchases[] = $p;
                $expenses[] = $e;
                $profit[] = $prof;
            }
        } elseif ($this->period === 'this_month' || ($start->year === $end->year && $start->month === $end->month)) {
            // ── Days of the month ──
            $salesData = (clone $salesQ)->selectRaw('DAY(COALESCE(issue_date, created_at)) as d, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('d')->get()->keyBy('d');
            $purchasesData = (clone $purchasesQ)->selectRaw('DAY(created_at) as d, SUM(total_inclusive_vat) as val')->groupBy('d')->pluck('val', 'd')->toArray();
            $returnsData = (clone $returnsQ)->selectRaw('DAY(COALESCE(issue_date, created_at)) as d, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('d')->get()->keyBy('d');
            $cogsData = (clone $cogsQ)->selectRaw('DAY(COALESCE(sales_invoices.issue_date, sales_invoices.created_at)) as d, SUM(sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)) as val')->groupBy('d')->pluck('val', 'd')->toArray();
            $expensesData = (clone $expensesQ)->selectRaw('DAY(journal_entries.entry_date) as d, SUM(journal_entry_details.debit - journal_entry_details.credit) as val')->groupBy('d')->pluck('val', 'd')->toArray();

            $daysInMonth = $start->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $categories[] = (string) $d;
                $sRow = $salesData->get($d);
                $rRow = $returnsData->get($d);

                $s = round((float) ($sRow->val ?? 0), 2);
                $sExcl = (float) ($sRow->val_excl ?? 0);
                $p = round((float) ($purchasesData[$d] ?? 0), 2);
                $rExcl = (float) ($rRow->val_excl ?? 0);
                $c = round((float) ($cogsData[$d] ?? 0), 2);
                $e = max(0, round((float) ($expensesData[$d] ?? 0), 2));

                $grossProfit = $sExcl - $rExcl - $c;
                $prof = round($grossProfit - $e, 2);

                $sales[] = $s;
                $purchases[] = $p;
                $expenses[] = $e;
                $profit[] = $prof;
            }
        } else {
            // ── Group by Month for Quarter / Year / Custom ──
            $salesData = (clone $salesQ)->selectRaw('MONTH(COALESCE(issue_date, created_at)) as m, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('m')->get()->keyBy('m');
            $purchasesData = (clone $purchasesQ)->selectRaw('MONTH(created_at) as m, SUM(total_inclusive_vat) as val')->groupBy('m')->pluck('val', 'm')->toArray();
            $returnsData = (clone $returnsQ)->selectRaw('MONTH(COALESCE(issue_date, created_at)) as m, SUM(total_inclusive_vat) as val, SUM(total_exclusive_vat) as val_excl')->groupBy('m')->get()->keyBy('m');
            $cogsData = (clone $cogsQ)->selectRaw('MONTH(COALESCE(sales_invoices.issue_date, sales_invoices.created_at)) as m, SUM(sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0)) as val')->groupBy('m')->pluck('val', 'm')->toArray();
            $expensesData = (clone $expensesQ)->selectRaw('MONTH(journal_entries.entry_date) as m, SUM(journal_entry_details.debit - journal_entry_details.credit) as val')->groupBy('m')->pluck('val', 'm')->toArray();

            $mStart = $start->year === $end->year ? (int) $start->month : 1;
            $mEnd = $start->year === $end->year ? (int) $end->month : 12;
            for ($m = $mStart; $m <= $mEnd; $m++) {
                $categories[] = $monthNames[$m] ?? "M{$m}";
                $sRow = $salesData->get($m);
                $rRow = $returnsData->get($m);

                $s = round((float) ($sRow->val ?? 0), 2);
                $sExcl = (float) ($sRow->val_excl ?? 0);
                $p = round((float) ($purchasesData[$m] ?? 0), 2);
                $rExcl = (float) ($rRow->val_excl ?? 0);
                $c = round((float) ($cogsData[$m] ?? 0), 2);
                $e = max(0, round((float) ($expensesData[$m] ?? 0), 2));

                $grossProfit = $sExcl - $rExcl - $c;
                $prof = round($grossProfit - $e, 2);

                $sales[] = $s;
                $purchases[] = $p;
                $expenses[] = $e;
                $profit[] = $prof;
            }
        }

        $this->mainTrend = [
            'months' => $categories,
            'sales' => $sales,
            'purchases' => $purchases,
            'expenses' => $expenses,
            'profit' => $profit,
        ];
    }

    public function loadBranchSales()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();
        $locale = app()->getLocale() ?? 'ar';

        $branchesQ = DB::table('branches')
            ->leftJoin('branch_translations', function ($join) use ($locale) {
                $join->on('branches.id', '=', 'branch_translations.branch_id')
                    ->where('branch_translations.locale', '=', $locale);
            })
            ->select('branches.id', DB::raw("COALESCE(branch_translations.name, CONCAT('فرع #', branches.id)) as name"));

        if ($this->branchId !== 'all') {
            $branchesQ->where('branches.id', $this->branchId);
        }

        $branches = $branchesQ->limit(5)->get();

        $bNames = [];
        $bSales = [];

        foreach ($branches as $b) {
            $q = DB::table('sales_invoices')
                ->whereNull('deleted_at')
                ->where('status', '!=', 1)
                ->whereIn('type_inv', [1, 4])
                ->where('branch_id', $b->id)
                ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$start, $end]);

            if ($this->storeId !== 'all') {
                $q->where('store_id', $this->storeId);
            }

            $sum = round((float) $q->sum('total_inclusive_vat'), 2);

            $bNames[] = mb_substr($b->name, 0, 15);
            $bSales[] = $sum;
        }

        $this->branchSales = [
            'branches' => $bNames,
            'sales' => $bSales,
        ];
    }

    public function loadPaymentMethods()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();

        $methodsQ = DB::table('sales_invoice_payments')
            ->join('sales_invoices', 'sales_invoice_payments.sales_invoice_id', '=', 'sales_invoices.id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->where('sales_invoice_payments.payment_method_code', '!=', 'credit')
            ->whereBetween('sales_invoice_payments.created_at', [$start, $end]);

        if ($this->branchId !== 'all') {
            $methodsQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $methodsQ->where('sales_invoices.store_id', $this->storeId);
        }

        $methods = $methodsQ->select('sales_invoice_payments.payment_method_code', DB::raw('SUM(sales_invoice_payments.amount) as total'))
            ->groupBy('sales_invoice_payments.payment_method_code')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $values = [];
        $total = $methods->sum('total');

        $methodTranslations = [
            'cash' => \Illuminate\Support\Facades\Lang::has('lang.cash') ? __('lang.cash') : (app()->getLocale() === 'ar' ? 'نقداً' : 'Cash'),
            'card' => \Illuminate\Support\Facades\Lang::has('lang.credit_card') ? __('lang.credit_card') : (app()->getLocale() === 'ar' ? 'بطاقة / شبكة' : 'Card'),
            'bank' => \Illuminate\Support\Facades\Lang::has('lang.bank') ? __('lang.bank') : (app()->getLocale() === 'ar' ? 'تحويل بنكي' : 'Bank Transfer'),
            'mada' => \Illuminate\Support\Facades\Lang::has('lang.mada') ? __('lang.mada') : (app()->getLocale() === 'ar' ? 'مدى' : 'Mada'),
            'cheque' => app()->getLocale() === 'ar' ? 'شيك' : 'Cheque',
        ];

        foreach ($methods as $m) {
            $pct = $total > 0 ? round(($m->total / $total) * 100, 1) : 0;
            $code = strtolower($m->payment_method_code ?? 'other');
            $label = $methodTranslations[$code] ?? (app()->getLocale() === 'ar' ? "طريقة ({$code})" : strtoupper($code));
            $labels[] = "{$label} ({$pct}% - ".number_format((float) $m->total).' '.__('lang.local_currency').')';
            $values[] = round((float) $m->total, 2);
        }

        if (empty($labels)) {
            $labels = [__('lang.cash'), __('lang.mada'), __('lang.credit_card')];
            $values = [0, 0, 0];
        }

        $this->paymentMethods = [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function loadTopProducts()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();
        $locale = app()->getLocale() ?? 'ar';

        $topQuery = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('products', 'sales_invoice_items.product_id', '=', 'products.id')
            ->leftJoin('product_translations', function ($join) use ($locale) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', $locale);
            })
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4])
            ->whereBetween(DB::raw('COALESCE(sales_invoices.issue_date, sales_invoices.created_at)'), [$start, $end])
            ->select(
                DB::raw("COALESCE(NULLIF(sales_invoice_items.product_name, ''), product_translations.name, CONCAT('صنف #', sales_invoice_items.product_id)) as p_name"),
                DB::raw('SUM(sales_invoice_items.subtotal_with_vat) as total_val'),
                DB::raw('SUM(sales_invoice_items.quantity) as total_qty'),
                DB::raw('SUM((sales_invoice_items.subtotal_with_vat - COALESCE(sales_invoice_items.vat_amount, 0)) - (sales_invoice_items.quantity * COALESCE(NULLIF(products.cost_price, 0), 0))) as total_profit')
            )
            ->groupBy('p_name');

        if ($this->branchId !== 'all') {
            $topQuery->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $topQuery->where('sales_invoices.store_id', $this->storeId);
        }

        if ($this->productToggle === 'profit') {
            $topQuery->orderBy('total_profit', 'desc');
        } elseif ($this->productToggle === 'quantity') {
            $topQuery->orderBy('total_qty', 'desc');
        } else {
            $topQuery->orderBy('total_val', 'desc');
        }

        $top = $topQuery->limit(5)->get();

        $names = [];
        $values = [];

        foreach ($top as $t) {
            if (!$t->p_name) {
                continue;
            }
            $names[] = mb_substr($t->p_name, 0, 30);
            if ($this->productToggle === 'quantity') {
                $values[] = (int) $t->total_qty;
            } elseif ($this->productToggle === 'profit') {
                $values[] = max(0, round((float) $t->total_profit, 2));
            } else {
                $values[] = round((float) $t->total_val, 2);
            }
        }

        $this->topProducts = [
            'names' => $names,
            'values' => $values,
            'toggle' => $this->productToggle,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.executive-main-charts', [
            'mainTrend' => $this->mainTrend,
            'paymentMethods' => $this->paymentMethods,
            'branchSales' => $this->branchSales,
            'topProducts' => $this->topProducts,
            'productToggle' => $this->productToggle,
            'productTitle' => $this->getProductTitle(),
        ]);
    }
}
