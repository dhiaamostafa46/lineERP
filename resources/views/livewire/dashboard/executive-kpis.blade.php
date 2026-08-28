<div class="row g-3 mb-5">
    <!-- Card 1: إجمالي المبيعات (Evix Navy) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.total_sales') }}</span>
                @if($kpis['growth_sales'] !== null)
                    <span class="badge fw-bold fs-9 py-1 px-2" style="{{ $kpis['growth_sales'] >= 0 ? 'color: #2D6A4F !important; background: rgba(134, 179, 107, 0.15) !important;' : 'color: #685E99 !important; background: rgba(104, 94, 153, 0.12) !important;' }}">
                        {{ $kpis['growth_sales'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['growth_sales']) }}%
                    </span>
                @else
                    <span class="badge bg-light text-gray-500 fw-bold fs-9 py-1 px-2">—</span>
                @endif
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: #1B325B;" title="{{ number_format($kpis['total_sales'], 2) }}">{{ number_format($kpis['total_sales'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad1" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#1B325B" stop-opacity="0.12" />
                            <stop offset="100%" stop-color="#1B325B" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,24 Q 40,10 80,18 T 160,4 L 160,30 L 0,30 Z" fill="url(#kpiGrad1)" />
                    <path class="liner-path" d="M 0,24 Q 40,10 80,18 T 160,4" fill="none" stroke="#1B325B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: صافي المبيعات (Evix Purple) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.net_sales') }}</span>
                @if($kpis['growth_net_sales'] !== null)
                    <span class="badge fw-bold fs-9 py-1 px-2" style="{{ $kpis['growth_net_sales'] >= 0 ? 'color: #2D6A4F !important; background: rgba(134, 179, 107, 0.15) !important;' : 'color: #685E99 !important; background: rgba(104, 94, 153, 0.12) !important;' }}">
                        {{ $kpis['growth_net_sales'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['growth_net_sales']) }}%
                    </span>
                @else
                    <span class="badge bg-light text-gray-500 fw-bold fs-9 py-1 px-2">—</span>
                @endif
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: #685E99;" title="{{ number_format($kpis['net_sales'], 2) }}">{{ number_format($kpis['net_sales'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad2" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#685E99" stop-opacity="0.12" />
                            <stop offset="100%" stop-color="#685E99" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,26 Q 35,20 75,12 T 160,5 L 160,30 L 0,30 Z" fill="url(#kpiGrad2)" />
                    <path class="liner-path" d="M 0,26 Q 35,20 75,12 T 160,5" fill="none" stroke="#685E99" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: إجمالي المشتريات (Evix Slate) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.total_purchases') }}</span>
                @if($kpis['growth_purchases'] !== null)
                    <span class="badge fw-bold fs-9 py-1 px-2" style="{{ $kpis['growth_purchases'] <= 0 ? 'color: #2D6A4F !important; background: rgba(134, 179, 107, 0.15) !important;' : 'color: #685E99 !important; background: rgba(104, 94, 153, 0.12) !important;' }}">
                        {{ $kpis['growth_purchases'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['growth_purchases']) }}%
                    </span>
                @else
                    <span class="badge bg-light text-gray-500 fw-bold fs-9 py-1 px-2">—</span>
                @endif
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: #334155;" title="{{ number_format($kpis['total_purchases'], 2) }}">{{ number_format($kpis['total_purchases'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad3" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#334155" stop-opacity="0.10" />
                            <stop offset="100%" stop-color="#334155" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,10 Q 40,20 80,14 T 160,24 L 160,30 L 0,30 Z" fill="url(#kpiGrad3)" />
                    <path class="liner-path" d="M 0,10 Q 40,20 80,14 T 160,24" fill="none" stroke="#334155" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4: صافي الربح (Evix Sage Green) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.net_profit') }}</span>
                @if($kpis['growth_profit'] !== null)
                    <span class="badge fw-bold fs-9 py-1 px-2" style="{{ $kpis['growth_profit'] >= 0 ? 'color: #2D6A4F !important; background: rgba(134, 179, 107, 0.15) !important;' : 'color: #685E99 !important; background: rgba(104, 94, 153, 0.12) !important;' }}">
                        {{ $kpis['growth_profit'] >= 0 ? '↑' : '↓' }} {{ abs($kpis['growth_profit']) }}%
                    </span>
                @else
                    <span class="badge bg-light text-gray-500 fw-bold fs-9 py-1 px-2">—</span>
                @endif
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: {{ $kpis['net_profit'] >= 0 ? '#2D6A4F' : '#685E99' }};" title="{{ number_format($kpis['net_profit'], 2) }}">{{ number_format($kpis['net_profit'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad4" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="{{ $kpis['net_profit'] >= 0 ? '#86B36B' : '#685E99' }}" stop-opacity="0.15" />
                            <stop offset="100%" stop-color="{{ $kpis['net_profit'] >= 0 ? '#86B36B' : '#685E99' }}" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,28 Q 45,16 90,10 T 160,4 L 160,30 L 0,30 Z" fill="url(#kpiGrad4)" />
                    <path class="liner-path" d="M 0,28 Q 45,16 90,10 T 160,4" fill="none" stroke="{{ $kpis['net_profit'] >= 0 ? '#86B36B' : '#685E99' }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 5: المستحقات والذمم (Evix Purple Accent) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.receivables') }}</span>
                <span class="badge fw-bold fs-9 py-1 px-2" style="color: #685E99; background: rgba(104, 94, 153, 0.12);">متابعة</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: #1B325B;" title="{{ number_format($kpis['receivables'], 2) }}">{{ number_format($kpis['receivables'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad5" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#685E99" stop-opacity="0.12" />
                            <stop offset="100%" stop-color="#685E99" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,16 Q 40,24 80,12 T 160,18 L 160,30 L 0,30 Z" fill="url(#kpiGrad5)" />
                    <path class="liner-path" d="M 0,16 Q 40,24 80,12 T 160,18" fill="none" stroke="#685E99" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 6: النقدية والبنوك (Evix Navy) -->
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="card shadow-sm border border-gray-200 h-100 p-4"
            style="border-radius: 1rem;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-8 fw-semibold text-gray-500 text-uppercase">{{ __('lang.cash_and_banks') }}</span>
                <span class="badge fw-bold fs-9 py-1 px-2" style="color: #2D6A4F; background: rgba(134, 179, 107, 0.15);">متوفر</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between my-1">
                <span class="fs-3 fw-bolder text-truncate" style="color: #1B325B;" title="{{ number_format($kpis['cash_balances'], 2) }}">{{ number_format($kpis['cash_balances'], 0) }}</span>
            </div>
            <div class="chart-liner-wrapper mt-2">
                <svg class="kpi-chart-liner" viewBox="0 0 160 30" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="kpiGrad6" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#1B325B" stop-opacity="0.10" />
                            <stop offset="100%" stop-color="#1B325B" stop-opacity="0.0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0,22 Q 40,8 80,14 T 160,5 L 160,30 L 0,30 Z" fill="url(#kpiGrad6)" />
                    <path class="liner-path" d="M 0,22 Q 40,8 80,14 T 160,5" fill="none" stroke="#1B325B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    </div>
</div>
