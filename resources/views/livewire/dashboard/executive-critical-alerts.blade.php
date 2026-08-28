<div class="card shadow-sm border border-gray-200 mb-6 p-5" style="border-radius: 1rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-40px me-3 p-2 rounded-3 d-flex align-items-center justify-content-center" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">
                <i class="ki-outline ki-notification-on fs-2" style="color: #1B325B;"></i>
            </div>
            <div>
                <h4 class="fw-bolder mb-0 fs-5" style="color: #1B325B;">{{ __('lang.action_required') }}</h4>
                <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.dashboard_alerts_priority_subtitle') }}</span>
            </div>
        </div>
        <span class="badge px-3 py-2 fw-bold fs-8" style="background: rgba(104, 94, 153, 0.12); color: #685E99;">
            {{ __('lang.dashboard_alerts_count', ['count' => $totalAlerts]) }}
        </span>
    </div>

    <div class="row g-4">
        <!-- 1. Critical Category (Evix Navy) -->
        <div class="col-md-4">
            <div class="border border-gray-200 rounded-3 p-3 h-100" style="background: rgba(27, 50, 91, 0.04);">
                <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #1B325B;">
                    <span class="badge me-2 px-2 py-1 fs-9" style="background: rgba(27, 50, 91, 0.12); color: #1B325B; border: 1px solid rgba(27, 50, 91, 0.2);">{{ __('lang.dashboard_alerts_critical') }}</span>
                </h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($criticalAlerts as $c)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border border-gray-100 shadow-xs">
                            <span class="text-gray-800 fw-semibold fs-8 me-2">{{ $c['title'] }}</span>
                            <a href="{{ $c['action_url'] }}"
                                class="btn btn-xs fw-bold px-2 py-1 fs-9 text-nowrap" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">{{ __('lang.dashboard_alert_action') }}</a>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="ki-outline ki-check-circle fs-2x opacity-50 mb-2" style="color: #2D6A4F;"></i>
                            <p class="text-muted fs-8 mb-0">{{ __('lang.no_issues') ?? 'لا توجد تنبيهات حرجة' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 2. Warning Category (Evix Purple) -->
        <div class="col-md-4">
            <div class="border border-gray-200 rounded-3 p-3 h-100" style="background: rgba(104, 94, 153, 0.04);">
                <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #685E99;">
                    <span class="badge me-2 px-2 py-1 fs-9" style="background: rgba(104, 94, 153, 0.12); color: #685E99; border: 1px solid rgba(104, 94, 153, 0.2);">{{ __('lang.dashboard_alerts_warning') }}</span>
                </h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($warningAlerts as $w)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border border-gray-100 shadow-xs">
                            <span class="text-gray-800 fw-semibold fs-8 me-2">{{ $w['title'] }}</span>
                            <a href="{{ $w['action_url'] }}"
                                class="btn btn-xs fw-bold px-2 py-1 fs-9 text-nowrap" style="background: rgba(104, 94, 153, 0.1); color: #685E99;">{{ __('lang.dashboard_alert_action') }}</a>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="ki-outline ki-check-circle fs-2x opacity-50 mb-2" style="color: #2D6A4F;"></i>
                            <p class="text-muted fs-8 mb-0">{{ __('lang.no_issues') ?? 'لا توجد تحذيرات' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3. Info Category (Evix Sage Green) -->
        <div class="col-md-4">
            <div class="border border-gray-200 rounded-3 p-3 h-100" style="background: rgba(134, 179, 107, 0.06);">
                <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #2D6A4F;">
                    <span class="badge me-2 px-2 py-1 fs-9" style="background: rgba(134, 179, 107, 0.15); color: #2D6A4F; border: 1px solid rgba(134, 179, 107, 0.25);">{{ __('lang.dashboard_alerts_info') }}</span>
                </h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($infoAlerts as $inf)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border border-gray-100 shadow-xs">
                            <span class="text-gray-800 fw-semibold fs-8 me-2">{{ $inf['title'] }}</span>
                            <a href="{{ $inf['action_url'] }}"
                                class="btn btn-xs fw-bold px-2 py-1 fs-9 text-nowrap" style="background: rgba(134, 179, 107, 0.15); color: #2D6A4F;">{{ __('lang.dashboard_alert_action') }}</a>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="ki-outline ki-check-circle fs-2x opacity-50 mb-2" style="color: #2D6A4F;"></i>
                            <p class="text-muted fs-8 mb-0">{{ __('lang.no_issues') ?? 'لا توجد إشعارات' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
