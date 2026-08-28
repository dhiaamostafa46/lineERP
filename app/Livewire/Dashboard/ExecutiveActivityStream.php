<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveActivityStream extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $activities = [];

    public function mount()
    {
        $this->loadActivities();
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
        $this->loadActivities();
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

    public function loadActivities()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();
        $rawEvents = [];

        // 1. Recent Sales Invoices
        $salesQ = DB::table('sales_invoices')
            ->leftJoin('users', 'sales_invoices.created_by', '=', 'users.id')
            ->whereNull('sales_invoices.deleted_at')
            ->whereBetween(DB::raw('COALESCE(sales_invoices.issue_date, sales_invoices.created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $salesQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $salesQ->where('sales_invoices.store_id', $this->storeId);
        }

        $sales = $salesQ->select(
            'sales_invoices.id',
            'sales_invoices.invoice_number',
            'sales_invoices.type_inv',
            'sales_invoices.total_inclusive_vat',
            'sales_invoices.created_at',
            DB::raw("COALESCE(users.name, '—') as user_name")
        )
            ->orderBy('sales_invoices.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($sales as $s) {
            $isReturn = $s->type_inv == 2;
            $title = ($isReturn ? (__('lang.activity_sales_return') ?: 'مرتجع مبيعات') : (__('lang.activity_sales_invoice') ?: 'فاتورة مبيعات جديدة'))
                .' #'.($s->invoice_number ?: 'INV-'.$s->id);

            $rawEvents[] = [
                'timestamp' => Carbon::parse($s->created_at),
                'time_formatted' => Carbon::parse($s->created_at)->format('h:i A'),
                'badge_color' => $isReturn ? 'warning' : 'success',
                'title' => $title,
                'user' => $s->user_name,
                'amount' => number_format((float) $s->total_inclusive_vat, 2),
            ];
        }

        // 2. Recent Purchase Invoices
        $purchasesQ = DB::table('purchase_invoices')
            ->leftJoin('users', 'purchase_invoices.created_by', '=', 'users.id')
            ->whereNull('purchase_invoices.deleted_at')
            ->whereBetween('purchase_invoices.created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $purchasesQ->where('purchase_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $purchasesQ->where('purchase_invoices.store_id', $this->storeId);
        }

        $purchases = $purchasesQ->select(
            'purchase_invoices.id',
            'purchase_invoices.invoice_number',
            'purchase_invoices.total_inclusive_vat',
            'purchase_invoices.created_at',
            DB::raw("COALESCE(users.name, '—') as user_name")
        )
            ->orderBy('purchase_invoices.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($purchases as $p) {
            $rawEvents[] = [
                'timestamp' => Carbon::parse($p->created_at),
                'time_formatted' => Carbon::parse($p->created_at)->format('h:i A'),
                'badge_color' => 'primary',
                'title' => (__('lang.activity_purchase_invoice') ?: 'فاتورة مشتريات جديدة').' #'.($p->invoice_number ?: 'PO-'.$p->id),
                'user' => $p->user_name,
                'amount' => number_format((float) $p->total_inclusive_vat, 2),
            ];
        }

        // 3. Recent POS Sessions
        $posQ = DB::table('pos_sessions')
            ->leftJoin('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->leftJoin('users', 'pos_sessions.user_id', '=', 'users.id')
            ->whereBetween('pos_sessions.created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $posQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $posQ->where('pos_devices.store_id', $this->storeId);
        }

        $pos = $posQ->select(
            'pos_sessions.id',
            'pos_sessions.status',
            'pos_sessions.created_at',
            DB::raw("COALESCE(pos_devices.name, 'POS') as device_name"),
            DB::raw("COALESCE(users.name, '—') as cashier_name")
        )
            ->orderBy('pos_sessions.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($pos as $ps) {
            $statusText = ($ps->status === 'open' || $ps->status == 1) ? (__('lang.shift_status_open') ?: 'مفتوحة') : (__('lang.shift_status_closed') ?: 'مغلقة');
            $rawEvents[] = [
                'timestamp' => Carbon::parse($ps->created_at),
                'time_formatted' => Carbon::parse($ps->created_at)->format('h:i A'),
                'badge_color' => 'info',
                'title' => (__('lang.activity_pos_session') ?: 'وردية كوانتر POS').' '.$ps->device_name.' ('.$statusText.')',
                'user' => $ps->cashier_name,
                'amount' => null,
            ];
        }

        // 4. Recent Journal Entries
        $journalsQ = DB::table('journal_entries')
            ->leftJoin('users', 'journal_entries.created_by', '=', 'users.id')
            ->whereNull('journal_entries.deleted_at')
            ->whereBetween(DB::raw('COALESCE(journal_entries.entry_date, journal_entries.created_at)'), [$start, $end]);
        if ($this->branchId !== 'all') {
            $journalsQ->where('journal_entries.branch_id', $this->branchId);
        }

        $journals = $journalsQ->select(
            'journal_entries.id',
            'journal_entries.entry_number',
            'journal_entries.total_debit',
            'journal_entries.created_at',
            DB::raw("COALESCE(users.name, '—') as user_name")
        )
            ->orderBy('journal_entries.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($journals as $j) {
            $rawEvents[] = [
                'timestamp' => Carbon::parse($j->created_at),
                'time_formatted' => Carbon::parse($j->created_at)->format('h:i A'),
                'badge_color' => 'primary',
                'title' => (__('lang.activity_journal_entry') ?: 'قيد محاسبي جديد').' #'.($j->entry_number ?: 'JE-'.$j->id),
                'user' => $j->user_name,
                'amount' => number_format((float) $j->total_debit, 2),
            ];
        }

        // Sort combined events by timestamp desc, deduplicate by title, and take top 8
        usort($rawEvents, function ($a, $b) {
            return $b['timestamp']->timestamp <=> $a['timestamp']->timestamp;
        });

        $uniqueEvents = [];
        $seenTitles = [];
        foreach ($rawEvents as $ev) {
            if (! in_array($ev['title'], $seenTitles)) {
                $seenTitles[] = $ev['title'];
                $uniqueEvents[] = $ev;
            }
        }

        $this->activities = array_slice($uniqueEvents, 0, 8);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-activity-stream', [
            'activities' => $this->activities,
        ]);
    }
}
