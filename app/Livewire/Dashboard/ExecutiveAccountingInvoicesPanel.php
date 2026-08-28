<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveAccountingInvoicesPanel extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $arSummary = [
        'total_ar' => 0,
        'overdue_ar' => 0,
        'due_soon_ar' => 0,
    ];

    public $overdueCustomers = [];

    public $invoiceStatusDonut = [];

    public function mount()
    {
        $this->loadAccountingInvoicesData();
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

        $this->loadAccountingInvoicesData();
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

    public function loadAccountingInvoicesData()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();
        $locale = app()->getLocale() ?? 'ar';

        // ── 1. Invoice Status Donut ──
        $statusQ = DB::table('sales_invoices')
            ->whereNull('deleted_at')
            ->whereBetween(DB::raw('COALESCE(issue_date, created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $statusQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $statusQ->where('store_id', $this->storeId);
        }

        $statusCounts = $statusQ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $clearedCount = (int) ($statusCounts[4] ?? 0);
        $submittedCount = (int) ($statusCounts[2] ?? 0);
        $draftCount = (int) ($statusCounts[1] ?? 0);
        $returnedCount = (int) ($statusCounts[6] ?? 0);

        $this->invoiceStatusDonut = [
            'labels' => [
                __('lang.invoice_paid'),
                __('lang.invoice_credit'),
                __('lang.invoice_draft'),
                __('lang.invoice_returned'),
            ],
            'values' => [$clearedCount, $submittedCount, $draftCount, $returnedCount],
        ];

        // ── 2. AR Summary (Net unpaid balance after payments) ────────
        $pmtsSub = DB::table('sales_invoice_payments')
            ->where('payment_method_code', '!=', 'credit')
            ->select('sales_invoice_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('sales_invoice_id');

        $arRowsQuery = DB::table('sales_invoices')
            ->leftJoinSub($pmtsSub, 'pmts', 'sales_invoices.id', '=', 'pmts.sales_invoice_id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1) // Exclude Drafts
            ->whereIn('sales_invoices.type_inv', [1, 4]);

        if ($this->branchId !== 'all') {
            $arRowsQuery->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $arRowsQuery->where('sales_invoices.store_id', $this->storeId);
        }

        $arRows = $arRowsQuery->select(
            'sales_invoices.id',
            'sales_invoices.issue_date',
            DB::raw('GREATEST(0, sales_invoices.total_inclusive_vat - COALESCE(pmts.total_paid, 0)) as balance')
        )->get();

        $totalAr = 0.0;
        $overdueAr = 0.0;
        $now = \Carbon\Carbon::now();

        foreach ($arRows as $r) {
            $bal = (float) $r->balance;
            if ($bal <= 0) {
                continue;
            }
            $totalAr += $bal;
            if ($r->issue_date && \Carbon\Carbon::parse($r->issue_date)->isPast()) {
                $overdueAr += $bal;
            }
        }
        $dueSoonAr = max(0, $totalAr - $overdueAr);

        $this->arSummary = [
            'total_ar' => round($totalAr, 2),
            'overdue_ar' => round($overdueAr, 2),
            'due_soon_ar' => round($dueSoonAr, 2),
        ];

        // ── 3. Top Customers by Net Outstanding AR ────────────────────
        $custQuery = DB::table('sales_invoices')
            ->leftJoinSub($pmtsSub, 'pmts', 'sales_invoices.id', '=', 'pmts.sales_invoice_id')
            ->join('inv_customers', 'sales_invoices.customer_id', '=', 'inv_customers.id')
            ->leftJoin('inv_customer_translations', function ($join) use ($locale) {
                $join->on('inv_customers.id', '=', 'inv_customer_translations.inv_customer_id')
                    ->where('inv_customer_translations.locale', '=', $locale);
            })
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4]);

        if ($this->branchId !== 'all') {
            $custQuery->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $custQuery->where('sales_invoices.store_id', $this->storeId);
        }

        $customers = $custQuery->select(
            DB::raw("COALESCE(inv_customer_translations.name, CONCAT('#', inv_customers.id)) as name"),
            DB::raw('SUM(GREATEST(0, sales_invoices.total_inclusive_vat - COALESCE(pmts.total_paid, 0))) as total_outstanding'),
            DB::raw('MAX(sales_invoices.issue_date) as latest_due')
        )
            ->groupBy('inv_customers.id', 'inv_customer_translations.name')
            ->having('total_outstanding', '>', 0)
            ->orderBy('total_outstanding', 'desc')
            ->limit(5)
            ->get();

        $list = [];
        foreach ($customers as $c) {
            $daysOverdue = 0;
            if ($c->latest_due) {
                $daysOverdue = max(0, $now->diffInDays(\Carbon\Carbon::parse($c->latest_due), false) * -1);
            }
            $list[] = [
                'name' => mb_substr($c->name, 0, 30),
                'amount' => round((float) $c->total_outstanding, 2),
                'days' => (int) $daysOverdue,
            ];
        }

        $this->overdueCustomers = $list;

        $this->dispatch('update-accounting-charts', invoiceStatusDonut: $this->invoiceStatusDonut, arSummary: $this->arSummary);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-accounting-invoices-panel', [
            'arSummary' => $this->arSummary,
            'overdueCustomers' => $this->overdueCustomers,
            'invoiceStatusDonut' => $this->invoiceStatusDonut,
        ]);
    }
}
