<div class="card shadow-sm border border-gray-200 mb-5 p-5" style="border-radius: 1rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-35px me-3 p-2 rounded-3 d-flex align-items-center justify-content-center" style="background: rgba(27, 50, 91, 0.08); color: #1B325B;">
                <i class="ki-outline ki-time fs-3" style="color: #1B325B;"></i>
            </div>
            <div>
                <h4 class="fw-bolder mb-0 fs-5" style="color: #1B325B;">{{ __('lang.recent_activity_title') ?? 'أحدث النشاطات والحركات' }}</h4>
                <span class="fs-8 text-gray-500 fw-semibold">{{ __('lang.recent_activity_subtitle') ?? 'سجل زمني يوثق أحدث المعاملات بالنظام' }}</span>
            </div>
        </div>
        <span class="badge px-3 py-2 fw-bold fs-8" style="background: rgba(134, 179, 107, 0.15); color: #2D6A4F;">{{ __('lang.live_stream') ?? 'بث حي' }}</span>
    </div>

    <div class="d-flex flex-column gap-2">
        @forelse($activities as $act)
            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border-bottom border-light">
                <div class="d-flex align-items-center me-3">
                    <span class="bullet bullet-vertical h-35px bg-{{ $act['badge_color'] }} me-3"></span>
                    <div>
                        <div class="fw-bolder text-dark fs-7 mb-1">{{ $act['title'] }}</div>
                        @if(!empty($act['user']) && $act['user'] !== '—')
                            <span class="text-gray-500 fs-8">{{ __('lang.by') ?? 'بواسطة' }}: <strong class="text-gray-700">{{ $act['user'] }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($act['amount']))
                        <span class="badge bg-light-{{ $act['badge_color'] }} text-{{ $act['badge_color'] }} fw-bolder fs-8 px-3 py-2">
                            {{ $act['amount'] }}
                        </span>
                    @endif
                    <span class="badge badge-light fw-bold text-gray-600 fs-8 px-3 py-1">
                        {{ $act['time_formatted'] }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted fs-7">
                <i class="ki-outline ki-time fs-2x mb-2 text-gray-400"></i>
                <p class="mb-0 fs-8">{{ __('lang.no_recent_activities') ?? 'لا توجد نشاطات حديثة مسجلة في النظام' }}</p>
            </div>
        @endforelse
    </div>
</div>
