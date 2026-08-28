<div class="row g-4 mb-5">
    <!-- 1. Main Trend Chart: Sales, Purchases, Expenses, Net Profit -->
    <div class="col-xl-8 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bolder text-gray-900 mb-0 fs-5">{{ __('lang.sales_and_profit_trend') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.chart_sales_subtitle') }}</span>
                </div>
            </div>
            <!-- Chart Container -->
            <div id="executive_main_trend_chart" wire:ignore style="height: 260px;"></div>
        </div>
    </div>

    <!-- 2. Sales by Payment Method -->
    <div class="col-xl-4 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bolder text-gray-900 mb-0 fs-5">{{ __('lang.payment_methods') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.chart_payment_subtitle') }}</span>
                </div>
            </div>
            <div id="executive_payment_donut_chart" wire:ignore style="height: 240px;"></div>
        </div>
    </div>

    <!-- 3. Top Products Premium UI Progress List -->
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div>
                    <h4 class="fw-bolder text-gray-900 mb-0 fs-5">{{ $productTitle ?? __('lang.chart_products_title_value') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.chart_products_subtitle') }}</span>
                </div>
                <!-- 3 Toggles -->
                <div class="btn-group btn-group-sm" role="group">
                    <button wire:click="toggleProductView('value')" type="button"
                        class="btn btn-sm {{ $productToggle === 'value' ? 'text-white' : 'btn-light' }} fw-bold fs-9 px-3"
                        style="{{ $productToggle === 'value' ? 'background: #1B325B !important; border-color: #1B325B;' : '' }}">{{ __('lang.chart_products_by_value') }}</button>
                    <button wire:click="toggleProductView('quantity')" type="button"
                        class="btn btn-sm {{ $productToggle === 'quantity' ? 'text-white' : 'btn-light' }} fw-bold fs-9 px-3"
                        style="{{ $productToggle === 'quantity' ? 'background: #685E99 !important; border-color: #685E99;' : '' }}">{{ __('lang.chart_products_by_qty') }}</button>
                    <button wire:click="toggleProductView('profit')" type="button"
                        class="btn btn-sm {{ $productToggle === 'profit' ? 'text-white' : 'btn-light' }} fw-bold fs-9 px-3"
                        style="{{ $productToggle === 'profit' ? 'background: #2D6A4F !important; border-color: #2D6A4F;' : '' }}">{{ __('lang.chart_products_by_profit') }}</button>
                </div>
            </div>
            @php
                $names = $topProducts['names'] ?? [];
                $values = $topProducts['values'] ?? [];
                $toggle = $topProducts['toggle'] ?? 'value';
                $isQty = $toggle === 'quantity';
                $isProfit = $toggle === 'profit';
                $barColor = $isProfit
                    ? 'linear-gradient(90deg, #2D6A4F 0%, #86B36B 100%)'
                    : ($isQty
                        ? 'linear-gradient(90deg, #685E99 0%, #8E84BF 100%)'
                        : 'linear-gradient(90deg, #1B325B 0%, #2A4B7C 100%)');
                $maxVal = !empty($values) ? max(max($values), 1) : 1;
            @endphp

            <div id="executive_top_products_chart" style="min-height: 250px;">
                @if(count($names) > 0)
                    <div class="d-flex flex-column gap-3 py-1" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        @foreach($names as $idx => $name)
                            @php
                                $val = $values[$idx] ?? 0;
                                $percent = min(100, max(8, round(($val / $maxVal) * 100)));
                                $formattedVal = $isQty
                                    ? number_format($val) . ' ' . (__('lang.piece') ?? 'قطعة')
                                    : number_format($val, 2);
                            @endphp
                            <div class="top-product-item p-3 rounded-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center overflow-hidden me-2">
                                        @if($idx === 0)
                                            <span class="badge fw-bolder px-2 me-2" style="background: rgba(134, 179, 107, 0.2); color: #2D6A4F;">#1 👑</span>
                                        @else
                                            <span class="badge bg-light text-gray-600 fw-bold px-2 me-2">#{{ $idx + 1 }}</span>
                                        @endif
                                        <span class="fw-bold text-gray-800 fs-7 text-truncate" style="max-width:220px;" title="{{ $name }}">{{ $name }}</span>
                                    </div>
                                    <span class="badge bg-white text-gray-900 border border-gray-200 fw-bold fs-8 ms-2 px-3 py-1 text-nowrap">{{ $formattedVal }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                                    <div class="progress-bar rounded-3" role="progressbar"
                                        style="width: {{ $percent }}%; background: {{ $barColor }}; transition: width 0.7s cubic-bezier(0.4,0,0.2,1);">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 py-5 text-muted">
                        <i class="ki-outline ki-box fs-2x text-gray-400 mb-2"></i>
                        <span class="fs-7">{{ __('lang.no_data_available') ?? 'لا توجد بيانات أصناف في هذه الفترة' }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Sales by Branch -->
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bolder text-gray-900 mb-0 fs-5">{{ __('lang.top_branches') }}</h4>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.chart_branches_subtitle') }}</span>
                </div>
                @if(Route::has('Branches.index'))
                    <a href="{{ route('Branches.index') }}" class="btn btn-sm fw-bold fs-8" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">{{ __('lang.view_all_branches') }}</a>
                @endif
            </div>
            <div id="executive_branch_bar_chart" wire:ignore style="height: 250px;"></div>
        </div>
    </div>

    @script
    <script>
        $wire.on('update-main-charts', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            if (payload) {
                initExecutiveMainCharts(
                    payload.mainTrend,
                    payload.paymentMethods,
                    payload.branchSales
                );
            }
        });

        setTimeout(() => {
            initExecutiveMainCharts(
                @json($mainTrend),
                @json($paymentMethods),
                @json($branchSales)
            );
        }, 100);
    </script>
    @endscript
</div>

@push('scripts')
    <script>
        window.dashboardCharts = window.dashboardCharts || {};

        function initExecutiveMainCharts(trendData, paymentData, branchData) {

            // 1. Main Area Trend Chart (Evix Palette: Navy, Purple, Slate, Sage Green)
            var trendEl = document.querySelector('#executive_main_trend_chart');
            if (trendEl) {
                if (window.dashboardCharts.mainTrend) {
                    try { window.dashboardCharts.mainTrend.destroy(); } catch(e){}
                    window.dashboardCharts.mainTrend = null;
                }
                trendEl.innerHTML = '';
                if (trendData.months && trendData.months.length > 0) {
                    var optionsTrend = {
                        series: [
                            { name: '{{ __('lang.chart_sales_series') }}',     data: trendData.sales },
                            { name: '{{ __('lang.chart_purchases_series') }}', data: trendData.purchases },
                            { name: '{{ __('lang.chart_expenses_series') }}',  data: trendData.expenses },
                            { name: '{{ __('lang.chart_profit_series') }}',    data: trendData.profit }
                        ],
                        chart: {
                            type: 'area', height: 260, toolbar: { show: false },
                            animations: { enabled: true, speed: 600 }
                        },
                        colors: ['#1B325B', '#685E99', '#64748B', '#86B36B'],
                        fill: {
                            type: 'gradient',
                            gradient: { shadeIntensity: 1, opacityFrom: 0.20, opacityTo: 0.01, stops: [0, 90, 100] }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: [2.5, 2, 2, 2.5] },
                        xaxis: {
                            categories: trendData.months,
                            labels: { style: { fontSize: '11px', fontFamily: 'inherit', colors: '#64748b' } },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                style: { colors: '#64748b', fontSize: '11px' },
                                formatter: (val) => val >= 1000000
                                    ? (val / 1000000).toFixed(1) + 'M'
                                    : val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val
                            }
                        },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
                        legend: {
                            position: 'top', horizontalAlign: 'right',
                            fontSize: '12px', fontFamily: 'inherit'
                        },
                        tooltip: { shared: true, intersect: false }
                    };
                    window.dashboardCharts.mainTrend = new ApexCharts(trendEl, optionsTrend);
                    window.dashboardCharts.mainTrend.render();
                } else {
                    trendEl.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7">{{ __('lang.no_data_available') ?? "لا توجد بيانات" }}</div>';
                }
            }

            // 2. Payment Methods — Donut (Evix Logo Palette)
            var payEl = document.querySelector('#executive_payment_donut_chart');
            if (payEl) {
                if (window.dashboardCharts.payment) {
                    try { window.dashboardCharts.payment.destroy(); } catch(e){}
                    window.dashboardCharts.payment = null;
                }
                payEl.innerHTML = '';
                var total = (paymentData.values || []).reduce((a, b) => a + b, 0);
                if (total > 0) {
                    var optionsPay = {
                        series: paymentData.values,
                        chart: { type: 'donut', height: 240 },
                        labels: paymentData.labels,
                        colors: ['#1B325B', '#685E99', '#86B36B', '#2D6A4F', '#475569'],
                        legend: {
                            position: 'bottom',
                            fontSize: '11px',
                            fontFamily: 'inherit',
                            formatter: function(val, opts) {
                                var v = opts.w.globals.series[opts.seriesIndex];
                                var pct = total > 0 ? ((v / total) * 100).toFixed(0) : 0;
                                return val + ' (' + pct + '%)';
                            }
                        },
                        dataLabels: { enabled: false },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: '{{ __('lang.total') }}',
                                            fontSize: '13px',
                                            fontFamily: 'inherit',
                                            fontWeight: '700',
                                            color: '#1B325B',
                                            formatter: () => total >= 1000 ? (total / 1000).toFixed(1) + 'k' : total
                                        }
                                    }
                                }
                            }
                        },
                        stroke: { width: 2, colors: ['#fff'] }
                    };
                    window.dashboardCharts.payment = new ApexCharts(payEl, optionsPay);
                    window.dashboardCharts.payment.render();
                } else {
                    payEl.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7">{{ __('lang.no_data_available') ?? "لا توجد مدفوعات" }}</div>';
                }
            }

            // 4. Branch Sales — Horizontal Bar Chart
            var branchEl = document.querySelector('#executive_branch_bar_chart');
            if (branchEl) {
                if (window.dashboardCharts.branch) {
                    try { window.dashboardCharts.branch.destroy(); } catch(e){}
                    window.dashboardCharts.branch = null;
                }
                branchEl.innerHTML = '';
                var optionsBranch = {
                    series: [{ name: '{{ __('lang.chart_sales_series') }}', data: branchData.sales || [] }],
                    chart: {
                        type: 'bar', height: 250, toolbar: { show: false },
                        animations: { speed: 500 }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            barHeight: '55%',
                            distributed: true,
                        }
                    },
                    colors: ['#1B325B', '#685E99', '#86B36B', '#2D6A4F', '#A5CE8D'],
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val >= 1000000
                            ? (val / 1000000).toFixed(1) + 'M'
                            : val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val,
                        style: { fontSize: '11px', fontFamily: 'inherit', fontWeight: '600', colors: ['#fff'] }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 3, padding: { left: 8 } },
                    xaxis: {
                        categories: branchData.branches || [],
                        labels: {
                            formatter: (val) => val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val,
                            style: { fontSize: '11px', fontFamily: 'inherit' }
                        }
                    },
                    yaxis: {
                        labels: { style: { fontSize: '12px', fontFamily: 'inherit', fontWeight: '600' } }
                    },
                    legend: { show: false },
                    tooltip: {
                        y: {
                            formatter: (val) => Number(val).toLocaleString()
                        }
                    }
                };
                window.dashboardCharts.branch = new ApexCharts(branchEl, optionsBranch);
                window.dashboardCharts.branch.render();
            }
        }
    </script>
@endpush

