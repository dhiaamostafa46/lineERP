<div class="row g-4 mb-6">
    <!-- AR Summary + Overdue Customers Table -->
    <div class="col-xl-7 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bolder fs-4 mb-1" style="color: #1B325B;">{{ __('lang.dashboard_ar_title') }}</h3>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_ar_subtitle') }}</span>
                </div>
                <span class="badge px-3 py-2 fw-bold fs-8" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">{{ __('lang.dashboard_ar_badge') }}</span>
            </div>

            <!-- AR 3 Mini Cards -->
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background: rgba(27, 50, 91, 0.05);">
                        <span class="fs-8 text-gray-600 fw-bold d-block">{{ __('lang.total_ar_label') }}</span>
                        <span class="fs-5 fw-bolder" style="color: #1B325B;">{{ number_format($arSummary['total_ar']) }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background: rgba(104, 94, 153, 0.08);">
                        <span class="fs-8 text-gray-600 fw-bold d-block">{{ __('lang.overdue_ar_label') }}</span>
                        <span class="fs-5 fw-bolder" style="color: #685E99;">{{ number_format($arSummary['overdue_ar']) }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 text-center" style="background: rgba(134, 179, 107, 0.10);">
                        <span class="fs-8 text-gray-600 fw-bold d-block">{{ __('lang.due_soon_ar_label') }}</span>
                        <span class="fs-5 fw-bolder" style="color: #2D6A4F;">{{ number_format($arSummary['due_soon_ar']) }}</span>
                    </div>
                </div>
            </div>

            <!-- AR Stacked Bar: overdue vs due-soon -->
            <div class="mb-4">
                <div id="executive_ar_stacked_bar" wire:ignore style="height: 60px;"></div>
            </div>

            <!-- Top Overdue Customers Table -->
            @if(count($overdueCustomers) > 0)
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bolder text-muted fs-7 text-uppercase">
                                <th>{{ __('lang.customer_name_col') }}</th>
                                <th class="text-center">{{ __('lang.amount_due_col') }}</th>
                                <th class="text-center">{{ __('lang.days_overdue_col') }}</th>
                                <th class="text-end">{{ __('lang.required_action_col') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueCustomers as $c)
                                <tr>
                                    <td>
                                        <span class="text-dark fw-bold fs-7">{{ $c['name'] }}</span>
                                    </td>
                                    <td class="text-center fw-bolder fs-6" style="color: #1B325B;">
                                        {{ number_format($c['amount']) }} 
                                    </td>
                                    <td class="text-center">
                                        @if($c['days'] > 0)
                                            <span class="badge fw-bolder px-3 py-1" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">{{ $c['days'] }} {{ __('lang.day') ?? 'يوم' }}</span>
                                        @else
                                            <span class="badge fw-bolder px-3 py-1" style="background: rgba(134, 179, 107, 0.15); color: #2D6A4F;">{{ __('lang.due_soon_ar_label') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-xs fw-bold fs-9 px-3" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">{{ __('lang.send_reminder_btn') }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="ki-outline ki-check-circle fs-3x opacity-50 mb-2" style="color: #2D6A4F;"></i>
                    <p class="text-muted fs-7">{{ __('lang.no_overdue_customers') ?? 'لا توجد ذمم متأخرة' }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Invoices Status Breakdown -->
    <div class="col-xl-5 col-lg-12">
        <div class="card shadow-sm border border-gray-200 h-100 p-5" style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bolder fs-4 mb-1" style="color: #1B325B;">{{ __('lang.invoice_status_chart') }}</h3>
                    <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.invoice_status_subtitle') }}</span>
                </div>
            </div>

            <!-- Invoice total count badge -->
            @php $totalInvoices = array_sum($invoiceStatusDonut['values']); @endphp
            <div class="text-center mb-3">
                <span class="badge px-4 py-2 fs-7 fw-bold" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">
                    {{ __('lang.total_issued_invoices') }}: {{ number_format($totalInvoices) }}
                </span>
            </div>

            <div id="executive_invoice_status_chart" wire:ignore style="height: 280px;"></div>

            <!-- Legend with counts -->
            <div class="row g-2 mt-2">
                @php
                    $invBadgeStyles = [
                        ['color' => '#2D6A4F', 'bg' => 'rgba(134, 179, 107, 0.2)'],
                        ['color' => '#1B325B', 'bg' => 'rgba(27, 50, 91, 0.1)'],
                        ['color' => '#685E99', 'bg' => 'rgba(104, 94, 153, 0.15)'],
                        ['color' => '#8E84BF', 'bg' => 'rgba(142, 132, 191, 0.15)'],
                    ];
                    $invLabels = $invoiceStatusDonut['labels'];
                    $invValues = $invoiceStatusDonut['values'];
                @endphp
                @foreach($invLabels as $i => $label)
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge p-1" style="width:12px; height:12px; border-radius:3px; background: {{ $invBadgeStyles[$i]['color'] ?? '#64748b' }};"></span>
                            <span class="fs-8 text-gray-700 fw-semibold">{{ $label }}</span>
                            <span class="badge fw-bold ms-auto" style="color: {{ $invBadgeStyles[$i]['color'] ?? '#64748b' }}; background: {{ $invBadgeStyles[$i]['bg'] ?? '#f1f5f9' }};">{{ number_format($invValues[$i]) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('update-accounting-charts', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            if (payload) {
                initExecutiveAccountingCharts(payload.invoiceStatusDonut, payload.arSummary);
            }
        });

        setTimeout(() => {
            initExecutiveAccountingCharts(@json($invoiceStatusDonut), @json($arSummary));
        }, 100);
    </script>
    @endscript
</div>

@push('scripts')
    <script>
        window.dashboardCharts = window.dashboardCharts || {};

        function initExecutiveAccountingCharts(invData, arData) {
            // Invoice Status Pie Chart (Evix Logo Palette)
            var invEl = document.querySelector('#executive_invoice_status_chart');
            if (invEl) {
                if (window.dashboardCharts.invoiceStatus) {
                    try { window.dashboardCharts.invoiceStatus.destroy(); } catch(e){}
                    window.dashboardCharts.invoiceStatus = null;
                }
                invEl.innerHTML = '';
                var hasData = invData.values && invData.values.some(v => v > 0);
                if (hasData) {
                    var optionsInv = {
                        series: invData.values,
                        chart: { type: 'pie', height: 280, animations: { speed: 600 } },
                        labels: invData.labels,
                        colors: ['#2D6A4F', '#1B325B', '#685E99', '#86B36B'],
                        legend: { show: false },
                        dataLabels: {
                            enabled: true,
                            formatter: (val, opts) => {
                                var count = invData.values[opts.seriesIndex];
                                return count > 0 ? count.toLocaleString() : '';
                            },
                            style: {
                                fontSize: '13px', fontFamily: 'inherit', fontWeight: '700',
                                colors: ['#fff', '#fff', '#fff', '#fff']
                            },
                            dropShadow: { enabled: false }
                        },
                        plotOptions: {
                            pie: {
                                expandOnClick: false,
                                dataLabels: { offset: -10, minAngleToShowLabel: 5 }
                            }
                        },
                        stroke: { width: 3, colors: ['#fff'] },
                        tooltip: {
                            y: { formatter: (val) => val.toLocaleString() + ' {{ __('lang.invoice') ?? "فاتورة" }}' }
                        }
                    };
                    window.dashboardCharts.invoiceStatus = new ApexCharts(invEl, optionsInv);
                    window.dashboardCharts.invoiceStatus.render();
                } else {
                    invEl.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7">{{ __('lang.no_data_available') ?? "لا توجد فواتير" }}</div>';
                }
            }

            // AR Stacked Horizontal Bar
            var arEl = document.querySelector('#executive_ar_stacked_bar');
            if (arEl && arData) {
                if (window.dashboardCharts.arStacked) {
                    try { window.dashboardCharts.arStacked.destroy(); } catch(e){}
                    window.dashboardCharts.arStacked = null;
                }
                arEl.innerHTML = '';
                var totalAr   = arData.total_ar   || 0;
                var overdueAr = arData.overdue_ar  || 0;
                var dueSoonAr = arData.due_soon_ar || 0;

                if (totalAr > 0) {
                    var optionsAr = {
                        series: [
                            { name: '{{ __('lang.overdue_ar_label') }}',  data: [overdueAr] },
                            { name: '{{ __('lang.due_soon_ar_label') }}', data: [dueSoonAr] }
                        ],
                        chart: {
                            type: 'bar', height: 60, stacked: true,
                            stackType: '100%', toolbar: { show: false },
                            sparkline: { enabled: true }
                        },
                        plotOptions: {
                            bar: { horizontal: true, borderRadius: 4, barHeight: '80%' }
                        },
                        colors: ['#685E99', '#2D6A4F'],
                        dataLabels: {
                            enabled: true,
                            formatter: (val) => val.toFixed(0) + '%',
                            style: { fontSize: '11px', fontFamily: 'inherit', fontWeight: '700', colors: ['#fff'] }
                        },
                        xaxis: { labels: { show: false } },
                        yaxis: { labels: { show: false } },
                        legend: { show: false },
                        grid: { show: false },
                        tooltip: {
                            y: { formatter: (val) => Number(val).toLocaleString() }
                        }
                    };
                    window.dashboardCharts.arStacked = new ApexCharts(arEl, optionsAr);
                    window.dashboardCharts.arStacked.render();
                }
            }
        }
    </script>
@endpush
