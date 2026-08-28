<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveCriticalAlerts extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $criticalAlerts = [];

    public $warningAlerts = [];

    public $infoAlerts = [];

    public $totalAlerts = 0;

    public function mount()
    {
        $this->loadAlerts();
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
        $this->loadAlerts();
    }

    public function loadAlerts()
    {
        // ── Real DB counts ───────────────────────────────────────
        $outOfStockQ = DB::table('stocks')->where('current_quantity', '<=', 0);
        $lowStockQ = DB::table('stocks')->whereRaw('current_quantity <= min_quantity AND current_quantity > 0');

        if ($this->branchId !== 'all') {
            $outOfStockQ->where('branch_id', $this->branchId);
            $lowStockQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $outOfStockQ->where('store_id', $this->storeId);
            $lowStockQ->where('store_id', $this->storeId);
        }

        $outOfStockCount = (int) $outOfStockQ->count();
        $lowStockCount = (int) $lowStockQ->count();

        $pmtsSub = DB::table('sales_invoice_payments')
            ->where('payment_method_code', '!=', 'credit')
            ->select('sales_invoice_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('sales_invoice_id');

        $overdueQ = DB::table('sales_invoices')
            ->leftJoinSub($pmtsSub, 'pmts', 'sales_invoices.id', '=', 'pmts.sales_invoice_id')
            ->whereNull('sales_invoices.deleted_at')
            ->where('sales_invoices.status', '!=', 1)
            ->whereIn('sales_invoices.type_inv', [1, 4])
            ->whereDate(DB::raw('COALESCE(sales_invoices.issue_date, sales_invoices.created_at)'), '<', now())
            ->whereRaw('sales_invoices.total_inclusive_vat - COALESCE(pmts.total_paid, 0) > 0');

        if ($this->branchId !== 'all') {
            $overdueQ->where('sales_invoices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $overdueQ->where('sales_invoices.store_id', $this->storeId);
        }

        $overdueInvoicesCount = (int) $overdueQ->count();

        $maintQ = DB::table('vc_maintenance_requests')->where('vc_maintenance_requests.status', 'pending');
        if ($this->branchId !== 'all') {
            $maintQ->join('vehicles', 'vc_maintenance_requests.vehicle_id', '=', 'vehicles.id')
                ->where('vehicles.branch_id', $this->branchId);
        }
        $maintDueCount = (int) $maintQ->count();

        $openShiftsQ = DB::table('pos_sessions')
            ->join('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->where('pos_sessions.status', 'open');
        if ($this->branchId !== 'all') {
            $openShiftsQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $openShiftsQ->where('pos_devices.store_id', $this->storeId);
        }
        $openShiftsCount = (int) $openShiftsQ->count();

        // Late employees — use latest available date in DB
        $latestAttDate = DB::table('hr_attendances')->whereNull('deleted_at')->max('date');
        $lateCount = 0;
        if ($latestAttDate) {
            $lateQ = DB::table('hr_attendances')
                ->join('employees', 'hr_attendances.employee_id', '=', 'employees.id')
                ->whereNull('hr_attendances.deleted_at')
                ->whereDate('hr_attendances.date', $latestAttDate)
                ->where('hr_attendances.delay', '>', 0);
            if ($this->branchId !== 'all') {
                $lateQ->where('employees.branch_id', $this->branchId);
            }
            $lateCount = (int) $lateQ->count();
        }

        $receivingUrl = Route::has('receiving.index') ? route('receiving.index') : route('dashboard');
        $invoicesUrl = Route::has('invoices.sales.index') ? route('invoices.sales.index') : route('dashboard');

        // 🔴 Critical Alerts
        $this->criticalAlerts = array_filter([
            $outOfStockCount > 0 ? [
                'title' => __('lang.alert_out_of_stock', ['count' => $outOfStockCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => $receivingUrl,
            ] : null,
            $overdueInvoicesCount > 0 ? [
                'title' => __('lang.alert_overdue_invoices', ['count' => $overdueInvoicesCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => $invoicesUrl,
            ] : null,
        ]);

        // 🟠 Warning Alerts
        $this->warningAlerts = array_filter([
            $lowStockCount > 0 ? [
                'title' => __('lang.alert_low_stock', ['count' => $lowStockCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => $receivingUrl,
            ] : null,
            $maintDueCount > 0 ? [
                'title' => __('lang.alert_maintenance_due', ['count' => $maintDueCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => route('dashboard'),
            ] : null,
        ]);

        // 🔵 Info Alerts
        $this->infoAlerts = array_filter([
            $openShiftsCount > 0 ? [
                'title' => __('lang.alert_open_shifts', ['count' => $openShiftsCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => route('dashboard'),
            ] : null,
            $lateCount > 0 ? [
                'title' => __('lang.alert_late_employees', ['count' => $lateCount]),
                'action_text' => __('lang.alert_view_btn'),
                'action_url' => route('dashboard'),
            ] : null,
        ]);

        $this->totalAlerts = count($this->criticalAlerts) + count($this->warningAlerts) + count($this->infoAlerts);

        // Re-index arrays (array_filter may leave non-sequential keys)
        $this->criticalAlerts = array_values($this->criticalAlerts);
        $this->warningAlerts = array_values($this->warningAlerts);
        $this->infoAlerts = array_values($this->infoAlerts);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-critical-alerts', [
            'criticalAlerts' => $this->criticalAlerts,
            'warningAlerts' => $this->warningAlerts,
            'infoAlerts' => $this->infoAlerts,
            'totalAlerts' => $this->totalAlerts,
        ]);
    }
}
