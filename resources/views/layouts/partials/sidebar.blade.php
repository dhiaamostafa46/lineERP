<div id="kt_app_sidebar" class="app-sidebar flex-column no-print" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="265px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
    style="width: 265px;">
    
    <!--begin::Sidebar Header / Logo-->
    <div class="line-sidebar-header d-flex align-items-center justify-content-between" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2" wire:navigate>
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-40px app-sidebar-logo-default" />
            <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-30px app-sidebar-logo-minimize" />
        </a>

        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-sm btn-color-gray-500 btn-active-color-primary h-28px w-28px rounded-circle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize"
            style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
            <i class="ki-duotone ki-black-left-line fs-4 text-gray-400 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Sidebar Header-->

    <!--begin::Sidebar Menu & Search-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid d-flex flex-column" x-data="{ searchQuery: '' }">
        
        <!--begin::Search Box-->
        <div class="px-4 mt-3 mb-2">
            <div class="line-search-wrapper d-flex align-items-center px-3 py-2 gap-2">
                <i class="fas fa-search fs-7 text-gray-500"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       class="line-search-input w-100 p-0" 
                       placeholder="@lang('lang.search')..." />
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="btn btn-icon btn-xs p-0 text-gray-400 hover:text-white" style="display: none;">
                    <i class="fas fa-times fs-8"></i>
                </button>
            </div>
        </div>
        <!--end::Search Box-->

        <!--begin::Scrollable Menu-->
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y flex-grow-1 px-2" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
            data-kt-scroll-save-state="true">
            
            <nav class="line-sidebar-menu" id="kt_app_sidebar_menu">
                @include('layouts.partials.menu')
            </nav>

        </div>
        <!--end::Scrollable Menu-->

        <!--begin::Sidebar Footer User Card-->
        @auth
            <div class="line-sidebar-footer d-flex align-items-center justify-content-between mt-auto">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative">
                        <div class="line-user-avatar">
                            {{ mb_substr(auth()->user()->name ?? 'U', 0, 1, 'utf-8') }}
                        </div>
                        <span class="status-dot-online position-absolute bottom-0 end-0"></span>
                    </div>
                    <div class="d-flex flex-column overflow-hidden" style="max-width: 135px;">
                        <span class="text-white fw-bold fs-7 text-truncate">{{ auth()->user()->name }}</span>
                        <span class="text-gray-500 fs-8 text-truncate">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                   class="btn btn-icon btn-sm btn-color-gray-500 btn-active-color-danger h-30px w-30px"
                   title="@lang('lang.logout')">
                    <i class="fas fa-power-off fs-7"></i>
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endauth
        <!--end::Sidebar Footer User Card-->

    </div>
    <!--end::Sidebar Menu-->
</div>
