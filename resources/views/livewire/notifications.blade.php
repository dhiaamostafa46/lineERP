<div class="app-navbar-item ms-1 ms-md-4 notification-font-family" wire:poll.10s>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .notification-font-family {
            font-family: 'Cairo', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        }

        .notification-pulse-ring {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: var(--bs-danger, #f1416c);
            animation: notificationPulse 2s infinite;
        }

        @keyframes notificationPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(241, 65, 108, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(241, 65, 108, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(241, 65, 108, 0); }
        }

        .notification-item-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .notification-item-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(27, 50, 91, 0.1);
            background-color: #f8fafc !important;
        }

        .notification-tab-pill {
            font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            transition: all 0.2s ease-in-out;
        }

        .notification-tab-pill:hover {
            transform: scale(1.02);
        }

        .notification-tab-pill-active {
            background-color: var(--bs-primary, #6A669D) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(106, 102, 157, 0.25) !important;
        }

        .notification-header-bg {
            background: linear-gradient(135deg, var(--bs-primary-active, #1B325B) 0%, var(--bs-primary, #6A669D) 100%) !important;
        }

        .notification-title {
            font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            line-height: 1.35;
            color: var(--bs-text-primary, #1E2B50);
        }

        .notification-body {
            font-family: 'Cairo', 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 500 !important;
            line-height: 1.45;
            color: #4b5563;
        }
    </style>

    <!-- Trigger Button (Single Click: Open Dropdown | Double Click: Open All Notifications Index) -->
    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative cursor-pointer"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end" id="kt_menu_item_notifications"
        ondblclick="window.location.href='{{ route('notifications.index') }}'"
        title="@lang('models/notifications.plural')">
        <i class="ki-duotone ki-notification-on fs-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
            <span class="path5"></span>
        </i>
        @if ($unreadCount > 0)
            <span class="notification-pulse-ring"></span>
            <span class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle fw-bold"
                style="font-size: 0.7rem; width: 19px; height: 19px; line-height: 19px; padding: 0; text-align: center;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </div>

    <!-- Dropdown Container -->
    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-425px shadow-2xl rounded-4 overflow-hidden border-0 notification-font-family" data-kt-menu="true"
        id="kt_menu_notifications" style="box-shadow: 0 12px 40px rgba(27,50,91,0.18);">

        <!-- Header with System Color Palette -->
        <div class="d-flex flex-stack px-6 py-5 notification-header-bg">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-35px circle me-3" style="background: rgba(255,255,255,0.18);">
                    <span class="symbol-label">
                        <i class="ki-duotone ki-notification-on fs-2 text-white">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                </div>
                <div>
                    <h3 class="text-white notification-title fs-5 mb-0">
                        @lang('models/notifications.plural')
                    </h3>
                    <span class="text-white-50 fs-8 fw-semibold">
                        {{ $unreadCount }} {{ __('models/notifications.status.pending') }}
                    </span>
                </div>
            </div>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="btn btn-sm btn-light px-3 py-1 fs-8 fw-bold rounded-pill notification-tab-pill" style="color: var(--bs-primary-active, #1B325B);">
                    <i class="fa-solid fa-check-double me-1 fs-9"></i>
                    {{ __('models/notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        <!-- Module Filter Tabs -->
        <div class="px-4 py-2 border-bottom bg-light">
            <div class="d-flex flex-nowrap overflow-auto py-1 gap-1 text-nowrap scroll-x">
                <button wire:click="setModule('all')"
                    class="btn btn-xs rounded-pill px-3 py-1 fs-8 notification-tab-pill {{ $activeModule === 'all' ? 'notification-tab-pill-active' : 'btn-white text-gray-700 border-0' }}">
                    {{ __('models/notifications.all') }}
                </button>
                @foreach ($modules as $key => $label)
                    <button wire:click="setModule('{{ $key }}')"
                        class="btn btn-xs rounded-pill px-3 py-1 fs-8 notification-tab-pill {{ $activeModule === $key ? 'notification-tab-pill-active' : 'btn-white text-gray-700 border-0' }}">
                        <i class="fa-solid {{ $moduleIcons[$key] ?? 'fa-circle' }} me-1 fs-9"></i>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Notification List -->
        <div class="scroll-y mh-350px my-2 px-4">
            @forelse ($notifications as $notification)
                <div class="notification-item-card d-flex align-items-start p-3 rounded-3 mb-2 cursor-pointer position-relative border-start border-4 border-{{ $notification->color }} bg-white"
                    wire:click="markAsRead({{ $notification->id }})">
                    
                    <!-- Symbol Icon -->
                    <div class="symbol symbol-40px circle me-3 flex-shrink-0">
                        <span class="symbol-label bg-light-{{ $notification->color }} text-{{ $notification->color }}">
                            <i class="ki-duotone {{ $notification->icon }} fs-2 text-{{ $notification->color }}">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fs-7 notification-title text-truncate me-2">
                                {{ $notification->title ?: $notification->type_name }}
                            </span>
                            <span class="badge badge-light-{{ $notification->color }} fs-9 px-2 py-1 rounded-pill fw-bold">
                                {{ $notification->module_name }}
                            </span>
                        </div>

                        @if ($notification->body)
                            <p class="notification-body fs-8 mb-2 line-clamp-2">
                                {{ $notification->body }}
                            </p>
                        @endif

                        <div class="d-flex align-items-center justify-content-between text-muted fs-9">
                            <span class="fw-semibold">
                                <i class="ki-duotone ki-time me-1 fs-9">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                            </span>
                            @if ($notification->user)
                                <span class="fw-bold" style="color: var(--bs-primary-active, #1B325B);">
                                    <i class="fa-solid fa-user me-1 fs-9"></i>
                                    {{ $notification->user->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="symbol symbol-60px circle mb-3" style="background: rgba(106, 102, 157, 0.12);">
                        <span class="symbol-label">
                            <i class="ki-duotone ki-notification-status fs-2x" style="color: var(--bs-primary, #6A669D);">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                    </div>
                    <h5 class="fs-6 notification-title mb-1">@lang('models/notifications.all_caught_up')</h5>
                    <p class="fs-7 text-gray-500 notification-body mb-0">@lang('models/notifications.no_notifications')</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="py-3 text-center border-top bg-light">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm fw-bold fs-7 px-4 rounded-pill notification-tab-pill" style="color: var(--bs-primary, #6A669D); background-color: rgba(106, 102, 157, 0.1);">
                {{ __('models/notifications.view_all') }}
                <i class="fa-solid fa-arrow-left ms-1 fs-8"></i>
            </a>
        </div>
    </div>
</div>
