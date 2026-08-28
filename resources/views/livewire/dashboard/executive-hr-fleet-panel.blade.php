<div class="row g-4 mb-6">
    <!-- HR & Attendance Section -->
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bolder fs-4 mb-0" style="color: #1B325B;">{{ __('lang.dashboard_hr_title') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_hr_subtitle') }}</span>
                </div>
                <span class="badge px-3 py-2 fw-bold fs-8" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">
                    {{ __('lang.attendance_rate_label') }}: {{ $hrKpis['attendance_rate'] }}%
                </span>
            </div>

            <div class="row g-3 mb-4">
                <!-- Radial Bar for Attendance Rate -->
                <div class="col-5">
                    <div id="executive_hr_radial" wire:ignore style="height: 140px;"></div>
                </div>
                <!-- 4 Mini cards -->
                <div class="col-7">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.total_employees_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ $hrKpis['total_employees'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.present_today_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #2D6A4F;">{{ $hrKpis['present_today'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.08);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.absent_today_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #685E99;">{{ $hrKpis['absent_today'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.06);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.attendance_records_label') }}</span>
                                <span class="fs-5 fw-bolder" style="color: #685E99;">{{ number_format($hrKpis['attendance_records']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($latestAttDate)
                <p class="text-muted fs-9 mb-3">
                    <i class="ki-outline ki-calendar fs-8 me-1"></i>
                    {{ __('lang.data_date_note', ['date' => $latestAttDate]) }}
                </p>
            @endif

            <!-- Late Employees List -->
            <h6 class="fw-bolder text-dark mb-3">{{ __('lang.late_employees_title') ?? 'الموظفون المتأخرون' }}</h6>
            @if(count($lateEmployees) > 0)
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-2">
                        <thead>
                            <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                <th>{{ __('lang.employee_name_col') ?? 'الموظف' }}</th>
                                <th>{{ __('lang.check_in_time_col') ?? 'وقت الحضور' }}</th>
                                <th class="text-center">{{ __('lang.delay_col') ?? 'التأخير' }}</th>
                                <th class="text-end">{{ __('lang.job_col') ?? 'الوظيفة' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lateEmployees as $e)
                                <tr>
                                    <td><span class="text-dark fw-bold fs-7">{{ $e['name'] ?? '—' }}</span></td>
                                    <td><span class="badge badge-light fs-8 text-muted">{{ $e['time'] ?? '—' }}</span></td>
                                    <td class="text-center"><span class="badge fw-bolder fs-8" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">{{ $e['delay'] ?? $e['late_mins'] ?? '—' }}</span></td>
                                    <td class="text-end"><span class="text-gray-600 fs-8">{{ $e['job'] ?? $e['shift'] ?? '—' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3">
                    <i class="ki-outline ki-check-circle fs-2x opacity-50 mb-2" style="color: #2D6A4F;"></i>
                    <p class="text-muted fs-8">{{ __('lang.no_late_employees') ?? 'لا يوجد متأخرين اليوم' }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Fleet & Maintenance Section -->
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bolder fs-4 mb-0" style="color: #1B325B;">{{ __('lang.dashboard_fleet_title') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_fleet_subtitle') }}</span>
                </div>
                <span class="badge px-3 py-2 fw-bold fs-8" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">{{ __('lang.dashboard_fleet_badge') }}</span>
            </div>

            <!-- Fleet: Donut + Cards row -->
            <div class="row g-3 mb-4">
                <!-- Fleet Donut Chart -->
                <div class="col-5">
                    <div id="executive_fleet_donut" wire:ignore style="height: 140px;"></div>
                </div>
                <!-- Fleet Mini Cards -->
                <div class="col-7">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.total_vehicles_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ $fleetKpis['total_vehicles'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.available_vehicles_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #2D6A4F;">{{ $fleetKpis['available'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.expiring_insurance_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ $fleetKpis['expiring_insurance'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.08);">
                                <span class="fs-9 text-gray-600 fw-bold d-block">{{ __('lang.in_maintenance_label') }}</span>
                                <span class="fs-4 fw-bolder" style="color: #685E99;">{{ $fleetKpis['in_maintenance'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Due Table -->
            <h6 class="fw-bolder text-dark mb-3">{{ __('lang.maintenance_due_table') }}</h6>
            @if(count($maintenanceDueVehicles) > 0)
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-2">
                        <thead>
                            <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                <th>{{ __('lang.vehicle_code_col') }}</th>
                                <th>{{ __('lang.plate_col') }}</th>
                                <th class="text-center">{{ __('lang.due_date_col') }}</th>
                                <th class="text-end">{{ __('lang.status_col') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenanceDueVehicles as $v)
                                <tr>
                                    <td><span class="text-dark fw-bold fs-7">{{ $v['code'] }}</span></td>
                                    <td><span class="badge badge-light-dark fs-8">{{ $v['plate'] }}</span></td>
                                    <td class="text-center"><span class="text-gray-700 fw-bold fs-8">{{ $v['due'] }}</span></td>
                                    <td class="text-end"><span class="badge fw-bolder fs-8" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">{{ $v['status'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3">
                    <i class="ki-outline ki-check-circle fs-2x text-success mb-2"></i>
                    <p class="text-muted fs-8">{{ __('lang.no_maintenance_due') ?? 'لا توجد مركبات تستحق صيانة' }}</p>
                </div>
            @endif

            @if($driverKpis['total_drivers'] > 0)
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-gray-700 fs-8">{{ __('lang.total_drivers_count') }}: <strong class="text-dark">{{ $driverKpis['total_drivers'] }}</strong></span>
                        <span class="badge badge-light-success fs-8 fw-bold">{{ __('lang.available_drivers_service') }}: {{ $driverKpis['available'] }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        $wire.on('update-hr-fleet-charts', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            if (payload) {
                initExecutiveHrFleetCharts(payload.hrKpis, payload.fleetKpis);
            }
        });

        setTimeout(() => {
            initExecutiveHrFleetCharts(@json($hrKpis), @json($fleetKpis));
        }, 100);
    </script>
    @endscript
</div>

@push('scripts')
<script>
    window.dashboardCharts = window.dashboardCharts || {};

    function initExecutiveHrFleetCharts(hrData, fleetData) {
        // HR Attendance Radial Bar
        var hrEl = document.querySelector('#executive_hr_radial');
        if (hrEl) {
            if (window.dashboardCharts.hr) {
                try { window.dashboardCharts.hr.destroy(); } catch(e){}
                window.dashboardCharts.hr = null;
            }
            hrEl.innerHTML = '';
            var attRate = hrData.attendance_rate || 0;
            var optionsHr = {
                series: [attRate],
                chart: { type: 'radialBar', height: 140, sparkline: { enabled: true } },
                plotOptions: {
                    radialBar: {
                        startAngle: -130,
                        endAngle: 130,
                        hollow: { size: '58%' },
                        track: { background: '#f1f5f9', strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                fontSize: '18px', fontFamily: 'inherit', fontWeight: '800',
                                color: '#1B325B', offsetY: 6,
                                formatter: (val) => val + '%'
                            }
                        }
                    }
                },
                colors: [attRate >= 80 ? '#2D6A4F' : '#685E99'],
                stroke: { lineCap: 'round' }
            };
            window.dashboardCharts.hr = new ApexCharts(hrEl, optionsHr);
            window.dashboardCharts.hr.render();
        }

        // Fleet Status Donut
        var fleetEl = document.querySelector('#executive_fleet_donut');
        if (fleetEl) {
            if (window.dashboardCharts.fleet) {
                try { window.dashboardCharts.fleet.destroy(); } catch(e){}
                window.dashboardCharts.fleet = null;
            }
            fleetEl.innerHTML = '';
            var total     = fleetData.total_vehicles || 0;
            var available = fleetData.available || 0;
            var inOp      = fleetData.in_operation || 0;
            var inMaint   = fleetData.in_maintenance || 0;

            if (total > 0) {
                var optionsFleet = {
                    series: [available, inOp, inMaint].map(v => Math.max(0, v)),
                    chart: { type: 'donut', height: 140, sparkline: { enabled: true } },
                    labels: [
                        '{{ __('lang.available_vehicles_label') }}',
                        '{{ __('lang.working_vehicle') ?? "في التشغيل" }}',
                        '{{ __('lang.in_maintenance_label') }}'
                    ],
                    colors: ['#2D6A4F', '#1B325B', '#685E99'],
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: {
                        pie: { donut: { size: '65%', labels: {
                            show: true,
                            total: {
                                show: true, label: '{{ __('lang.total') }}',
                                fontSize: '12px', fontFamily: 'inherit', fontWeight: '700', color: '#64748b',
                                formatter: () => total
                            }
                        }}}
                    },
                    stroke: { width: 2, colors: ['#fff'] }
                };
                window.dashboardCharts.fleet = new ApexCharts(fleetEl, optionsFleet);
                window.dashboardCharts.fleet.render();
            } else {
                fleetEl.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted fs-8">—</div>';
            }
        }
    }
</script>
@endpush

