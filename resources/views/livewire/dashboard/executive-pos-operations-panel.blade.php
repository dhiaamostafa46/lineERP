<div class="card shadow-sm border border-gray-200 mb-6 p-5" style="border-radius: 1rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bolder fs-4 mb-0" style="color: #1B325B;">{{ __('lang.dashboard_pos_title') }}</h4>
            <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_pos_subtitle') }}</span>
        </div>
        <span class="badge px-3 py-2 fw-bold fs-8" style="color: #1B325B; background: rgba(27, 50, 91, 0.08);">{{ __('lang.dashboard_pos_badge') }}</span>
    </div>

    <!-- 6 POS Mini Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.active_devices') }}</span>
                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ $kpis['active_devices'] }}</span>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.open_shifts_label') }}</span>
                <span class="fs-4 fw-bolder" style="color: #2D6A4F;">{{ $kpis['open_shifts'] }}</span>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.06);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.pos_today_sales') }}</span>
                <span class="fs-4 fw-bolder" style="color: #685E99;">{{ number_format($kpis['today_sales']) }}</span>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.total_transactions') }}</span>
                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ number_format($kpis['tx_count']) }}</span>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.06);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.avg_transaction') }}</span>
                <span class="fs-4 fw-bolder" style="color: #685E99;">{{ number_format($kpis['avg_tx_val']) }}</span>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.top_pos_device') }}</span>
                <span class="fs-7 fw-bolder" style="color: #2D6A4F;">{{ $kpis['top_pos_device'] }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales by POS Device â€” Improved Column Bar Chart -->
        <div class="col-xl-5 col-lg-12">
            <div class="border rounded-3 p-3">
                <h6 class="fw-bolder text-dark mb-3 text-center">{{ __('lang.pos_sales_by_device') }}</h6>
                @if(count($posDeviceSales['devices'] ?? []) > 0)
                    <div id="executive_pos_device_bar" wire:ignore style="height: 220px;"></div>
                @else
                    <div class="d-flex align-items-center justify-content-center" style="height: 220px;">
                        <div class="text-center text-muted">
                            <i class="ki-outline ki-abstract-26 fs-3x mb-2"></i>
                            <p class="fs-8">{{ __('lang.no_data_available') ?? 'Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¨ÙŠØ§Ù†Ø§Øª' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Live POS Shifts Table -->
        <div class="col-xl-7 col-lg-12">
            <div class="border rounded-3 p-3">
                <h6 class="fw-bolder text-dark mb-3">{{ __('lang.live_shifts_table') }}</h6>
                @if(count($liveShifts) > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-2">
                            <thead>
                                <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                    <th>{{ __('lang.pos_device_col') }}</th>
                                    <th>{{ __('lang.cashier_col') }}</th>
                                    <th class="text-center">{{ __('lang.session_sales_col') }}</th>
                                    <th class="text-center">{{ __('lang.operations_col') }}</th>
                                    <th class="text-end">{{ __('lang.status_col') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liveShifts as $s)
                                    <tr>
                                        <td><span class="badge badge-light-dark fw-bold fs-8">{{ $s['device'] }}</span></td>
                                        <td><span class="text-dark fw-bold fs-7">{{ $s['cashier'] }}</span></td>
                                        <td class="text-center fw-bolder text-primary fs-7">
                                            {{ number_format($s['sales']) }} 
                                        </td>
                                        <td class="text-center fw-bold fs-8">{{ number_format($s['tx']) }}</td>
                                        <td class="text-end">
                                            <span class="badge badge-light-success text-success fs-9 fw-bold">{{ $s['status'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ki-outline ki-abstract-26 fs-3x text-muted mb-2"></i>
                        <p class="text-muted fs-8">{{ __('lang.no_active_shifts') ?? 'Ù„Ø§ ØªÙˆØ¬Ø¯ ÙˆØ±Ø¯ÙŠØ§Øª Ù†Ø´Ø·Ø©' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('update-pos-chart', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            const posData = payload?.posDeviceSales || payload;
            if (posData) {
                initExecutivePosChart(posData);
            }
        });

        setTimeout(() => {
            initExecutivePosChart(@json($posDeviceSales));
        }, 100);
    </script>
    @endscript
</div>

@push('scripts')
<script>
    window.dashboardCharts = window.dashboardCharts || {};

    function initExecutivePosChart(posData) {
        var el = document.querySelector('#executive_pos_device_bar');
        if (!el) return;

        if (window.dashboardCharts.posDevice) {
            try { window.dashboardCharts.posDevice.destroy(); } catch(e){}
            window.dashboardCharts.posDevice = null;
        }
        el.innerHTML = '';

        var hasSales = posData.sales && posData.sales.some(v => v > 0);

        var options = {
            series: [{ name: '{{ __('lang.chart_sales_series') }}', data: posData.sales || [] }],
            chart: {
                type: 'bar', height: 220, toolbar: { show: false },
                animations: { speed: 600 }
            },
            colors: ['#1B325B', '#685E99', '#86B36B', '#2D6A4F', '#A5CE8D'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '50%',
                    distributed: true,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: hasSales,
                formatter: (val) => val >= 1000000
                    ? (val / 1000000).toFixed(1) + 'M'
                    : val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val,
                offsetY: -20,
                style: { fontSize: '11px', fontFamily: 'inherit', fontWeight: '700', colors: ['#374151'] }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
            xaxis: {
                categories: posData.devices || [],
                labels: { style: { fontSize: '11px', fontFamily: 'inherit', fontWeight: '600' } }
            },
            yaxis: {
                labels: {
                    formatter: (val) => val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val,
                    style: { fontSize: '11px' }
                }
            },
            legend: { show: false },
            tooltip: {
                y: { formatter: (val) => Number(val).toLocaleString() }
            }
        };
        window.dashboardCharts.posDevice = new ApexCharts(el, options);
        window.dashboardCharts.posDevice.render();
    }
</script>
@endpush

