<div id="kt_app_sidebar" class="app-sidebar flex-column no-print" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="260px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-inline-end: 1px solid rgba(106, 102, 157, 0.12); width: 260px;">
    
    <!--begin::Logo-->
    @php
        $org = App\Models\Organization::first();
    @endphp
    <div class="app-sidebar-logo px-6 py-4 w-100 d-flex align-items-center justify-content-between bg-white border-bottom" id="kt_app_sidebar_logo" style="background-color: #ffffff !important; border-bottom: 1px solid rgba(0,0,0,0.06) !important;">
        <!--begin::Logo image-->
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2" wire:navigate>
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-45px app-sidebar-logo-default" />
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-30px app-sidebar-logo-minimize" />
        </a>
        <!--end::Logo image-->

        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->

    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid" x-data="{ searchQuery: '' }">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            
            <!--begin::Menu search-->
            <div class="app-sidebar-search px-4 mb-2 mt-3" id="kt_app_sidebar_menu_search">
                <div class="position-relative">
                    <i class="fas fa-search fs-6 text-gray-400 position-absolute top-50 translate-middle-y ms-3"></i>
                    <input type="text" 
                           id="menuSearch" 
                           x-model="searchQuery" 
                           class="form-control form-control-sm form-control-solid ps-9 rounded-3 border-0 bg-light-subtle fs-7" 
                           placeholder="@lang('lang.search')..." />
                </div>
            </div>
            <!--end::Menu search-->

            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y mb-5 px-2" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                
                <!--begin::Menu Content-->
                <nav class="line-sidebar-menu" id="kt_app_sidebar_menu">
                    @include('layouts.partials.menu')
                </nav>
                <!--end::Menu Content-->

            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
