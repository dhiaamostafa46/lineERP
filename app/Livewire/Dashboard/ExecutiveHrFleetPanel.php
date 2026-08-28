<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ExecutiveHrFleetPanel extends Component
{
    public $branchId = 'all';

    public $storeId = 'all';

    public $hrKpis = [];

    public $fleetKpis = [];

    public $driverKpis = [];

    public $lateEmployees = [];

    public $maintenanceDueVehicles = [];

    public $driverPerformance = [];

    public $latestAttDate = null;

    public function mount()
    {
        $this->loadHrFleetData();
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
        $this->loadHrFleetData();
    }

    public function loadHrFleetData()
    {
        $this->loadHrData();
        $this->loadFleetData();
        $this->loadDriverData();

        $this->dispatch('update-hr-fleet-charts', hrKpis: $this->hrKpis, fleetKpis: $this->fleetKpis);
    }

    private function loadHrData(): void
    {
        // ── Total employees ──────────────────────────────────────
        $empQ = DB::table('employees');
        if ($this->branchId !== 'all') {
            $empQ->where('branch_id', $this->branchId);
        }
        $totalEmp = (int) $empQ->count();

        // ── Use latest available date (avoid always-zero today) ──
        $latestDate = DB::table('hr_attendances')->whereNull('deleted_at')->max('date');
        $this->latestAttDate = $latestDate;

        $presentCount = 0;
        $lateCount = 0;
        $totalRecords = 0;

        if ($latestDate) {
            $presentQ = DB::table('hr_attendances')
                ->join('employees', 'hr_attendances.employee_id', '=', 'employees.id')
                ->whereNull('hr_attendances.deleted_at')
                ->whereDate('hr_attendances.date', $latestDate);
            if ($this->branchId !== 'all') {
                $presentQ->where('employees.branch_id', $this->branchId);
            }
            $presentCount = (int) $presentQ->distinct('hr_attendances.employee_id')->count('hr_attendances.employee_id');

            $lateQ = DB::table('hr_attendances')
                ->join('employees', 'hr_attendances.employee_id', '=', 'employees.id')
                ->whereNull('hr_attendances.deleted_at')
                ->whereDate('hr_attendances.date', $latestDate)
                ->where('hr_attendances.delay', '>', 0);
            if ($this->branchId !== 'all') {
                $lateQ->where('employees.branch_id', $this->branchId);
            }
            $lateCount = (int) $lateQ->count();

            $totalRecQ = DB::table('hr_attendances')
                ->join('employees', 'hr_attendances.employee_id', '=', 'employees.id')
                ->whereNull('hr_attendances.deleted_at');
            if ($this->branchId !== 'all') {
                $totalRecQ->where('employees.branch_id', $this->branchId);
            }
            $totalRecords = (int) $totalRecQ->count();
        }

        $absentCount = max(0, $totalEmp - $presentCount);
        $attRate = $totalEmp > 0 ? round(($presentCount / $totalEmp) * 100, 1) : 0;

        $this->hrKpis = [
            'total_employees' => $totalEmp,
            'present_today' => $presentCount,
            'absent_today' => $absentCount,
            'attendance_records' => $totalRecords,
            'attendance_rate' => $attRate,
        ];

        // ── Late employees list ──────────────────────────────────
        $lateList = [];
        if ($latestDate) {
            $lateRowsQ = DB::table('hr_attendances')
                ->join('employees', 'hr_attendances.employee_id', '=', 'employees.id')
                ->leftJoin('hr_employees', 'employees.id', '=', 'hr_employees.employee_id')
                ->leftJoin('hr_jobs', 'hr_employees.job_id', '=', 'hr_jobs.id')
                ->leftJoin('hr_job_translations', function ($join) {
                    $join->on('hr_jobs.id', '=', 'hr_job_translations.hr_job_id')
                        ->where('hr_job_translations.locale', '=', app()->getLocale() ?? 'ar');
                })
                ->whereNull('hr_attendances.deleted_at')
                ->whereDate('hr_attendances.date', $latestDate)
                ->where('hr_attendances.delay', '>', 0);

            if ($this->branchId !== 'all') {
                $lateRowsQ->where('employees.branch_id', $this->branchId);
            }

            $lateRows = $lateRowsQ->select(
                DB::raw("COALESCE(employees.full_name, '—') as emp_name"),
                DB::raw("COALESCE(hr_job_translations.name, '—') as job_name"),
                'hr_attendances.check_time',
                'hr_attendances.delay'
            )
                ->orderBy('hr_attendances.delay', 'desc')
                ->limit(5)
                ->get();

            foreach ($lateRows as $r) {
                $delayMins = (int) ($r->delay / 60); // DB stores delay in seconds!
                $delayStr = $delayMins > 0 ? $delayMins . ' ' . (__('lang.minutes') ?? 'دقيقة') : ($r->delay . ' ' . (__('lang.seconds') ?? 'ثانية'));
                $lateList[] = [
                    'name' => $r->emp_name,
                    'job' => $r->job_name,
                    'shift' => $r->job_name,
                    'time' => $r->check_time ? \Carbon\Carbon::parse($r->check_time)->format('h:i A') : '—',
                    'late_mins' => $delayStr,
                    'delay' => $delayStr,
                ];
            }
        }

        $this->lateEmployees = $lateList;
    }

    private function loadFleetData(): void
    {
        $this->fleetKpis = [
            'total_vehicles' => 0,
            'available' => 0,
            'in_operation' => 0,
            'in_maintenance' => 0,
            'expiring_insurance' => 0,
            'expiring_license' => 0,
        ];
        $this->maintenanceDueVehicles = [];

        if (! \Illuminate\Support\Facades\Schema::hasTable('vehicles')) {
            return;
        }

        // ── Vehicle counts by status ─────────────────────────────
        $vehQ = DB::table('vehicles')->whereNull('deleted_at');
        if ($this->branchId !== 'all') {
            $vehQ->where('branch_id', $this->branchId);
        }
        if ($this->storeId !== 'all') {
            $vehQ->where('store_id', $this->storeId);
        }

        $total = (int) (clone $vehQ)->count();
        // status: 1=available, 2=in_operation, 3=maintenance
        $available = (int) (clone $vehQ)->where('status', 1)->count();
        $inOp = (int) (clone $vehQ)->where('status', 2)->count();
        $inMaint = (int) (clone $vehQ)->where('status', 3)->count();

        // If all vehicles are status=1 (available), show all as available
        if ($total > 0 && $available === 0 && $inOp === 0 && $inMaint === 0) {
            $available = $total; // status-1 or all same status
        }

        // ── Expiring license within 30 days ──────────────────────
        $expiringLicense = (int) DB::table('vehicles')
            ->whereNull('deleted_at')
            ->whereNotNull('license_expiry_date')
            ->whereDate('license_expiry_date', '<=', now()->addDays(30))
            ->whereDate('license_expiry_date', '>=', now())
            ->count();

        // ── Pending maintenance requests ─────────────────────────
        $maintPending = \Illuminate\Support\Facades\Schema::hasTable('vc_maintenance_requests')
            ? (int) DB::table('vc_maintenance_requests')->where('status', 'pending')->count()
            : 0;

        $this->fleetKpis = [
            'total_vehicles' => $total,
            'available' => $available,
            'in_operation' => $inOp,
            'in_maintenance' => $maintPending,
            'expiring_insurance' => $expiringLicense,
            'expiring_license' => $expiringLicense,
        ];

        // ── Vehicles with expiring licenses ──────────────────────
        $vehList = DB::table('vehicles')
            ->whereNull('deleted_at')
            ->whereNotNull('license_expiry_date')
            ->whereDate('license_expiry_date', '<=', now()->addDays(60))
            ->orderBy('license_expiry_date', 'asc')
            ->select('id', 'plate', 'plate_letters', 'plate_numbers', 'license_expiry_date')
            ->limit(5)
            ->get();

        $maintVehicles = [];
        foreach ($vehList as $v) {
            $daysLeft = now()->diffInDays($v->license_expiry_date, false);
            $plate = trim(($v->plate_letters ?? '').' '.($v->plate_numbers ?? '').' '.($v->plate ?? ''));
            $plate = $plate ?: "#{$v->id}";

            if ($daysLeft <= 0) {
                $status = '🔴 '.__('lang.maintenance');
            } elseif ($daysLeft <= 14) {
                $status = '🔴 '.__('lang.expiring_insurance_label');
            } else {
                $status = '🟠 '.__('lang.expiring_insurance_label');
            }

            $dueLabel = $daysLeft <= 0
                ? __('lang.due_date_col').': '.now()->addDays($daysLeft)->format('Y-m-d')
                : __('lang.in').' '.$daysLeft.' '.__('lang.days');

            $maintVehicles[] = [
                'code' => "Vehicle #{$v->id}",
                'plate' => $plate,
                'due' => $dueLabel,
                'status' => $status,
            ];
        }

        $this->maintenanceDueVehicles = $maintVehicles;
    }

    private function loadDriverData(): void
    {
        $this->driverKpis = [
            'total_drivers' => 0,
            'available' => 0,
            'on_trip' => 0,
            'on_leave' => 0,
        ];
        $this->driverPerformance = [
            'drivers' => [],
            'trips' => [],
            'deliveries' => [],
        ];

        if (! \Illuminate\Support\Facades\Schema::hasTable('drivers')) {
            return;
        }

        $drCount = (int) DB::table('drivers')->count();
        $available = (int) DB::table('drivers')->where('status', 'available')->count();
        $onTrip = (int) DB::table('drivers')->where('status', 'on_trip')->count();
        $onLeave = (int) DB::table('drivers')->where('status', 'on_leave')->count();

        // If no status column data, show total as available
        if ($drCount > 0 && ($available + $onTrip + $onLeave) === 0) {
            $available = $drCount;
        }

        $this->driverKpis = [
            'total_drivers' => $drCount,
            'available' => $available,
            'on_trip' => $onTrip,
            'on_leave' => $onLeave,
        ];

        // ── Top drivers by completed trips ────────────────────────
        $driverNames = [];
        $driverTrips = [];

        $drivers = DB::table('drivers')
            ->select(DB::raw('drivers.name as driver_name'))
            ->limit(5)
            ->get();

        foreach ($drivers as $d) {
            $driverNames[] = $d->driver_name;
            $driverTrips[] = 0;
        }

        $this->driverPerformance = [
            'drivers' => $driverNames,
            'trips' => $driverTrips,
            'deliveries' => $driverTrips,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.executive-hr-fleet-panel', [
            'hrKpis' => $this->hrKpis,
            'fleetKpis' => $this->fleetKpis,
            'driverKpis' => $this->driverKpis,
            'lateEmployees' => $this->lateEmployees,
            'maintenanceDueVehicles' => $this->maintenanceDueVehicles,
            'latestAttDate' => $this->latestAttDate,
            'driverPerformance' => $this->driverPerformance,
        ]);
    }
}
