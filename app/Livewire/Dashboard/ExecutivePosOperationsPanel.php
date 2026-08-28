<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutivePosOperationsPanel extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $period = 'this_month';

    public $startDate;

    public $endDate;

    public $kpis = [];

    public $posDeviceSales = [];

    public $liveShifts = [];

    public function mount()
    {
        $this->loadPosData();
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

        $this->loadPosData();
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

    public function loadPosData()
    {
        ['start' => $start, 'end' => $end] = $this->resolveDateRange();

        // ── Devices & Sessions ───────────────────────────────────
        $devicesQ = DB::table('pos_devices');
        if ($this->branchId !== 'all') {
            $devicesQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $devicesQ->where('store_id', $this->storeId);
        }
        $activeDevicesCount = (int) $devicesQ->count();

        $sessionsQ = DB::table('pos_sessions')
            ->join('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->where('pos_sessions.status', 'open');
        if ($this->branchId !== 'all') {
            $sessionsQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $sessionsQ->where('pos_devices.store_id', $this->storeId);
        }
        $openSessionsCount = (int) $sessionsQ->count();

        // ── Transactions aggregate ────────────────────────────────
        $txQ = DB::table('pos_session_transactions')
            ->join('pos_sessions', 'pos_session_transactions.pos_session_id', '=', 'pos_sessions.id')
            ->join('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->whereBetween('pos_session_transactions.created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $txQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $txQ->where('pos_devices.store_id', $this->storeId);
        }

        $totalTxAmount = (float) $txQ->sum('pos_session_transactions.amount');
        $txCount = (int) $txQ->count('pos_session_transactions.id');
        $avgTxVal = $txCount > 0 ? round($totalTxAmount / $txCount, 2) : 0;

        // ── Per-device sales ─────────────────────────────────────
        $deviceSalesQ = DB::table('pos_session_transactions')
            ->join('pos_sessions', 'pos_session_transactions.pos_session_id', '=', 'pos_sessions.id')
            ->join('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->whereBetween('pos_session_transactions.created_at', [$start, $end]);
        if ($this->branchId !== 'all') {
            $deviceSalesQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $deviceSalesQ->where('pos_devices.store_id', $this->storeId);
        }

        $deviceSalesRaw = $deviceSalesQ->select(
            'pos_devices.name as device_name',
            DB::raw('SUM(pos_session_transactions.amount) as total_sales'),
            DB::raw('COUNT(pos_session_transactions.id) as tx_count')
        )
            ->groupBy('pos_devices.id', 'pos_devices.name')
            ->orderBy('total_sales', 'desc')
            ->limit(6)
            ->get();

        $devices = [];
        $deviceSales = [];
        $topDevice = __('lang.top_pos_device').': —';

        foreach ($deviceSalesRaw as $idx => $d) {
            $devices[] = $d->device_name;
            $deviceSales[] = round((float) $d->total_sales, 2);
            if ($idx === 0) {
                $topDevice = $d->device_name.' ('.number_format((float) $d->total_sales).')';
            }
        }

        // If no transaction data, show device names with 0
        if (empty($devices)) {
            $allDevicesQ = DB::table('pos_devices');
            if ($this->branchId !== 'all') {
                $allDevicesQ->where('branch_id', $this->branchId);
            }
            if ($this->storeId !== 'all') {
                $allDevicesQ->where('store_id', $this->storeId);
            }
            $allDevices = $allDevicesQ->pluck('name')->toArray();
            foreach ($allDevices as $dn) {
                $devices[] = $dn;
                $deviceSales[] = 0;
            }
        }

        $this->kpis = [
            'active_devices' => $activeDevicesCount,
            'open_shifts' => $openSessionsCount,
            'today_sales' => round($totalTxAmount, 2),
            'tx_count' => $txCount,
            'avg_tx_val' => $avgTxVal,
            'top_pos_device' => count($devices) > 0 ? $topDevice : '—',
        ];

        $this->posDeviceSales = [
            'devices' => $devices,
            'sales' => $deviceSales,
        ];

        // ── Live Shifts (open sessions) ───────────────────────────
        $txSummarySub = DB::table('pos_session_transactions')
            ->select('pos_session_id', DB::raw('SUM(amount) as session_sales'), DB::raw('COUNT(id) as session_tx'))
            ->groupBy('pos_session_id');

        $liveQ = DB::table('pos_sessions')
            ->join('pos_devices', 'pos_sessions.device_id', '=', 'pos_devices.id')
            ->leftJoin('users', 'pos_sessions.user_id', '=', 'users.id')
            ->leftJoinSub($txSummarySub, 'tx_summary', 'pos_sessions.id', '=', 'tx_summary.pos_session_id')
            ->where('pos_sessions.status', 'open');

        if ($this->branchId !== 'all') {
            $liveQ->where('pos_devices.branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $liveQ->where('pos_devices.store_id', $this->storeId);
        }

        $sessions = $liveQ->select(
            'pos_devices.name as device',
            DB::raw("COALESCE(users.name, '—') as cashier"),
            DB::raw('COALESCE(tx_summary.session_sales, 0) as session_sales'),
            DB::raw('COALESCE(tx_summary.session_tx, 0) as session_tx'),
            'pos_sessions.status'
        )
            ->limit(5)
            ->get();

        $shifts = [];
        foreach ($sessions as $s) {
            $statusLabel = $s->status === 'open'
                ? __('lang.shift_status_open')
                : __('lang.shift_status_closed');

            $shifts[] = [
                'device' => $s->device,
                'cashier' => $s->cashier,
                'sales' => round((float) ($s->session_sales ?? 0), 2),
                'tx' => (int) ($s->session_tx ?? 0),
                'status' => $statusLabel,
            ];
        }

        $this->liveShifts = $shifts;

        $this->dispatch('update-pos-chart', posDeviceSales: $this->posDeviceSales);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-pos-operations-panel', [
            'kpis' => $this->kpis,
            'posDeviceSales' => $this->posDeviceSales,
            'liveShifts' => $this->liveShifts,
        ]);
    }
}
