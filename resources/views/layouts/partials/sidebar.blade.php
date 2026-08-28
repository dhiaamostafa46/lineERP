<div id="kt_app_sidebar" class="app-sidebar flex-column no-print" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    style="-webkit-box-shadow: -7px 0px 21px -7px rgba(0,0,0,0.75); -moz-box-shadow: -7px 0px 21px -7px rgba(0,0,0,0.75); box-shadow: -7px 0px 21px -7px rgba(0,0,0,0.75);">
    <!--begin::Logo-->
    @php
    $org = App\Models\Organization::first();
@endphp
    <div class="app-sidebar-logo px-6 w-100 border-0 bg-white" id="kt_app_sidebar_logo" style="background-color: #ffffff !important;">
        <!--begin::Logo image-->
        <a href="{{ route('dashboard') }}" class="mx-auto">
            <img alt="Logo" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png"
                class="h-70px  app-sidebar-logo-default " />
            <img alt="Logo" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png"
                class="h-70px app-sidebar-logo-minimize" />
        </a>
        <!--end::Logo image-->
        <!--begin::Sidebar toggle-->
        <!--begin::Minimized sidebar setup: if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") {
                1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
                2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
                3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
                4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
            }
        -->
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
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            
            <!--begin::Menu search-->
            <div class="app-sidebar-search px-5 mb-2 mt-2" id="kt_app_sidebar_menu_search">
                <div class="position-relative">
                    <i class="ki-outline ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-3"></i>
                    <input type="text" id="menuSearch" class="form-control form-control-sm form-control-solid ps-10" placeholder="@lang('lang.search')..." />
                </div>
            </div>
            <!--end::Menu search-->

            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y mb-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">
                    @include('layouts.partials.menu')
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
