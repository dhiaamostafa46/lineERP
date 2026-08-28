<div class="card shadow-sm border border-gray-200 mb-6 p-5" style="border-radius: 1rem;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bolder fs-4 mb-1" style="color: #1B325B;">{{ __('lang.dashboard_inventory_title') }}</h3>
            <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_inventory_subtitle') }}</span>
        </div>
        <span class="badge px-3 py-2 fw-bold fs-8" style="color: #2D6A4F; background: rgba(134, 179, 107, 0.15);">{{ __('lang.dashboard_inventory_sync') }}</span>
    </div>

    <!-- 7 Mini KPI Cards -->
    <div class="row g-3 mb-5">
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.total_items') }}</span>
                <span class="fs-3 fw-bolder" style="color: #1B325B;">{{ number_format($kpis['total_products']) }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.08);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.items_below_min') }}</span>
                <span class="fs-3 fw-bolder" style="color: #685E99;">{{ number_format($kpis['low_stock_count']) }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.08);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.out_of_stock_dashboard') }}</span>
                <span class="fs-3 fw-bolder" style="color: #1B325B;">{{ number_format($kpis['out_of_stock_count']) }}</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.06);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.stock_turnover') }}</span>
                <span class="fs-3 fw-bolder" style="color: #685E99;">{{ $kpis['stock_turnover'] }}
                    <span class="fs-9 fw-normal">({{ $kpis['days_of_stock'] }})</span>
                </span>
            </div>
        </div>

        <div class="col-md-4 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.stock_value_cost_label') }}</span>
                <span class="fs-4 fw-bolder" style="color: #1B325B;">{{ number_format($kpis['stock_value_cost']) }}</span>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="p-3 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.stock_value_sale_label') }}</span>
                <span class="fs-4 fw-bolder" style="color: #2D6A4F;">{{ number_format($kpis['stock_value_sale']) }}</span>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.06);">
                <span class="fs-8 text-gray-600 fw-bold d-block mb-1">{{ __('lang.expected_margin_label') }}</span>
                <span class="fs-4 fw-bolder" style="color: #685E99;">{{ number_format($kpis['expected_margin']) }}</span>
            </div>
        </div>
    </div>

    <div class="row g-6 align-items-start">
        <!-- Stock Status — Radial Bar Chart -->
        <div class="col-xl-5 col-lg-12">
            <div class="border rounded-3 p-4">
                <h5 class="fw-bolder mb-2 text-center" style="color: #1B325B;">{{ __('lang.stock_status_chart') }}</h5>
                <div id="executive_stock_status_chart" wire:ignore style="height: 280px;"></div>
            </div>
        </div>

        <!-- Top Low Stock Table -->
        <div class="col-xl-7 col-lg-12">
            <div class="border rounded-3 p-4">
                <h5 class="fw-bolder mb-4" style="color: #1B325B;">{{ __('lang.top_low_stock_table') }}</h5>
                @if(count($lowStockProducts) > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bolder text-muted fs-7 text-uppercase">
                                    <th class="min-w-150px">{{ __('lang.product_name_col') }}</th>
                                    <th class="text-center">{{ __('lang.current_stock_col') }}</th>
                                    <th class="text-center">{{ __('lang.min_stock_col') }}</th>
                                    <th class="text-end">{{ __('lang.status_col') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $p)
                                    <tr>
                                        <td>
                                            <span class="text-dark fw-bold fs-7">{{ $p['name'] }}</span>
                                        </td>
                                        <td class="text-center fw-bolder fs-6" style="color: #1B325B;">
                                            {{ number_format($p['stock']) }}
                                        </td>
                                        <td class="text-center text-gray-600 fs-7">{{ number_format($p['min']) }}</td>
                                        <td class="text-end">
                                            <span class="badge fs-8 fw-bolder px-3 py-1" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">{{ $p['badge'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ki-outline ki-check-circle fs-3x mb-3 opacity-50" style="color: #2D6A4F;"></i>
                        <p class="text-muted fs-7">{{ __('lang.all_stock_ok') ?? 'جميع المنتجات في مستوى مخزون جيد' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('update-inventory-chart', (data) => {
            const donutData = data.donut || (Array.isArray(data) ? data[0]?.donut : data);
            if (donutData) {
                initExecutiveInventoryChart(donutData);
            }
        });

        setTimeout(() => {
            initExecutiveInventoryChart(@json($stockStatusDonut));
        }, 100);
    </script>
    @endscript
</div>

@push('scripts')
    <script>
        window.dashboardCharts = window.dashboardCharts || {};

        function initExecutiveInventoryChart(donutData) {
            var el = document.querySelector('#executive_stock_status_chart');
            if (!el) return;

            if (window.dashboardCharts.stockStatus) {
                try { window.dashboardCharts.stockStatus.destroy(); } catch(e){}
                window.dashboardCharts.stockStatus = null;
            }
            el.innerHTML = '';

            var total = donutData.values.reduce((a, b) => a + b, 0);
            if (total === 0) {
                el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7">{{ __('lang.no_data_available') ?? "لا توجد بيانات" }}</div>';
                return;
            }

            var options = {
                series: donutData.values.map(v => total > 0 ? Math.round((v / total) * 100) : 0),
                chart: {
                    type: 'radialBar', height: 280,
                    animations: { speed: 700 }
                },
                plotOptions: {
                    radialBar: {
                        offsetY: 0,
                        startAngle: -130,
                        endAngle: 130,
                        hollow: { size: '30%', background: 'transparent' },
                        track: { background: '#f1f5f9', strokeWidth: '95%', margin: 6 },
                        dataLabels: {
                            name: { fontSize: '13px', fontFamily: 'inherit', fontWeight: '700', offsetY: -8 },
                            value: {
                                fontSize: '16px', fontFamily: 'inherit', fontWeight: '800', offsetY: 4,
                                formatter: (val) => {
                                    var idx = arguments.length > 1 ? arguments[1] : 0;
                                    return donutData.values[idx] !== undefined
                                        ? donutData.values[idx].toLocaleString()
                                        : val + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: '{{ __('lang.total_items') }}',
                                fontSize: '12px', fontFamily: 'inherit', fontWeight: '600', color: '#1B325B',
                                formatter: () => total.toLocaleString()
                            }
                        }
                    }
                },
                colors: ['#2D6A4F', '#685E99', '#1B325B'],
                labels: donutData.labels,
                legend: {
                    show: true, position: 'bottom', fontSize: '12px', fontFamily: 'inherit',
                    markers: { width: 10, height: 10, radius: 5 }
                },
                stroke: { lineCap: 'round' }
            };
            window.dashboardCharts.stockStatus = new ApexCharts(el, options);
            window.dashboardCharts.stockStatus.render();
        }
    </script>
@endpush
