<div id="kt_app_sidebar" class="app-sidebar flex-column no-print" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="260px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    style="width: 260px;">
    
    <!--begin::Sidebar Header / Logo-->
    <div class="line-sidebar-header d-flex align-items-center justify-content-between px-6 position-relative" id="kt_app_sidebar_logo" style="height: 65px; overflow: visible;">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center" wire:navigate style="max-height: 42px; overflow: hidden;">
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" 
                 class="app-sidebar-logo-default" 
                 style="max-height: 38px; max-width: 160px; width: auto; height: 38px; object-fit: contain;" />
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" 
                 class="app-sidebar-logo-minimize" 
                 style="max-height: 28px; max-width: 35px; width: auto; height: 28px; object-fit: contain; display: none;" />
        </a>

        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-28px w-28px position-absolute top-50 start-100 translate-middle rotate rounded-circle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize"
            style="background: #ffffff; border: 1px solid #e2e8f0; z-index: 105;">
            <i class="ki-duotone ki-black-left-line fs-5 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Sidebar Header-->

    <!--begin::Sidebar Menu Scrollable Container-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid d-flex flex-column py-2">
        <div id="kt_app_sidebar_menu_scroll" class="line-custom-scroll scroll-y flex-grow-1 px-3" data-kt-scroll="true"
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
        <div class="line-sidebar-footer d-flex align-items-center justify-content-between px-4 py-3 mt-auto border-top" style="background: #f8fafc; border-color: #e2e8f0 !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-xs" 
                         style="width: 30px; height: 30px; font-size: 11.5px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                        {{ mb_substr(auth()->user()->name ?? 'U', 0, 1, 'utf-8') }}
                    </div>
                </div>
                <div class="d-flex flex-column overflow-hidden" style="max-width: 140px;">
                    <span class="fw-bold text-truncate" style="font-size: 12.5px; color: #0f172a;">{{ auth()->user()->name }}</span>
                    <span class="text-muted text-truncate" style="font-size: 10.5px;">{{ auth()->user()->roles->first()?->name ?? 'User' }}</span>
                </div>
            </div>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
               class="btn btn-icon btn-xs btn-light-danger h-28px w-28px rounded-2"
               title="@lang('lang.logout')">
                <i class="fas fa-power-off fs-8 text-danger"></i>
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    @endauth
    <!--end::Sidebar Footer User Card-->

</div>
