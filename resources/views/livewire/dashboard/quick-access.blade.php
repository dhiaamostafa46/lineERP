<div class="quick-access-container mb-10" dir="rtl">
    <style>
        .quick-access-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 4px 20px rgba(27, 54, 93, 0.04);
            border: 1px solid #eef2f7;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
            display: flex;
            align-items: center;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .quick-access-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(27, 54, 93, 0.12);
            border-color: #685e99 !important;
        }
        .quick-access-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .quick-access-card:hover .quick-access-icon {
            transform: scale(1.1) rotate(-5deg);
        }
        .quick-access-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1b365d;
            margin-bottom: 2px;
            transition: color 0.3s ease;
        }
        .quick-access-subtitle {
            font-size: 0.78rem;
            color: #64748b;
        }
        .quick-access-card:hover .quick-access-title {
            color: #685e99;
        }
        .quick-access-arrow {
            margin-right: auto;
            opacity: 0.4;
            transition: all 0.3s ease;
        }
        .quick-access-card:hover .quick-access-arrow {
            opacity: 1;
            transform: translateX(-4px);
            color: #685e99;
        }

        /* Dark Mode */
        [data-bs-theme="dark"] .quick-access-card {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        }
        [data-bs-theme="dark"] .quick-access-title {
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .quick-access-subtitle {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .section-header-title {
            color: #f1f5f9 !important;
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-5">
        <div class="section-header-title" style="border-color: #86b86b; color: #1B325B;">
            <i class="ki-outline ki-flash fs-1 me-2" style="color: #d97706;"></i> {{ __('lang.quick_access_title') }}
        </div>
        <span class="badge px-4 py-2 fw-bold text-white" style="background: #1B325B; border-radius: 8px;">{{ __('lang.quick_shortcuts') }}</span>
    </div>

    <div class="row g-4">
        @foreach($shortcuts as $shortcut)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ $shortcut['url'] }}" class="quick-access-card" style="border-right: 4px solid {{ $shortcut['border_color'] }};">
                    <div class="quick-access-icon me-3" style="background: {{ $shortcut['bg_color'] }}; color: {{ $shortcut['icon_color'] }};">
                        <i class="ki-outline {{ $shortcut['icon'] }}"></i>
                    </div>
                    <div class="overflow-hidden me-2">
                        <div class="quick-access-title text-truncate">{{ $shortcut['title'] }}</div>
                        <div class="quick-access-subtitle text-truncate">{{ $shortcut['subtitle'] }}</div>
                    </div>
                    <div class="quick-access-arrow">
                        <i class="ki-outline ki-arrow-left fs-3"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

