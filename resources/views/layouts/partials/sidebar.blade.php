<div id="kt_app_sidebar" class="app-sidebar flex-column no-print" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="280px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    style="width: 280px;">
    
    <!--begin::Sidebar Header / Logo-->
    <div class="line-sidebar-header d-flex align-items-center justify-content-between px-5 py-3" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2" wire:navigate>
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-35px app-sidebar-logo-default" />
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-28px app-sidebar-logo-minimize" />
        </a>

        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-sm btn-color-gray-600 btn-active-color-primary h-26px w-26px rounded-circle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize"
            style="background: #f0f6f8; border: 1px solid #cbdfe6;">
            <i class="ki-duotone ki-black-left-line fs-5 text-gray-600 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Sidebar Header-->

    <!--begin::Sidebar Menu Scrollable Container-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid d-flex flex-column py-2">
        <div id="kt_app_sidebar_menu_scroll" class="line-custom-scroll scroll-y flex-grow-1 px-2" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
            data-kt-scroll-save-state="true">
            
            <nav class="line-sidebar-menu" id="kt_app_sidebar_menu">
                @include('layouts.partials.menu')
            </nav>

        </div>
    </div>
    <!--end::Sidebar Menu Scrollable Container-->

    <!--begin::Sidebar Footer User Card-->
    @auth
        <div class="line-sidebar-footer d-flex align-items-center justify-content-between px-4 py-3 mt-auto">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="line-user-avatar">
                        {{ mb_substr(auth()->user()->name ?? 'U', 0, 1, 'utf-8') }}
                    </div>
                    <span class="status-dot-online position-absolute bottom-0 end-0"></span>
                </div>
                <div class="d-flex flex-column overflow-hidden" style="max-width: 120px;">
                    <span class="fw-bold fs-8 text-truncate" style="color: #1B325B;">{{ auth()->user()->name }}</span>
                    <span class="fs-9 text-truncate" style="color: #7a99a3;">{{ auth()->user()->roles->first()?->name ?? 'User' }}</span>
                </div>
            </div>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
               class="btn btn-icon btn-xs btn-color-gray-500 btn-active-color-danger h-26px w-26px rounded-2"
               title="@lang('lang.logout')">
                <i class="fas fa-power-off fs-8"></i>
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    @endauth
    <!--end::Sidebar Footer User Card-->

</div>
