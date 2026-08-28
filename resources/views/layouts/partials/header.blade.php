@php
    $currentBranch = auth()->user()?->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : \App\Models\Branch::first();
    $allBranches = \App\Models\Branch::all();
    $user = auth('web')->user();
@endphp

<div id="kt_app_header" class="app-header no-print" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}"
    data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}"
    data-kt-sticky-animation="false">
    
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
        
        <!--begin::Sidebar mobile toggle-->
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        <!--end::Sidebar mobile toggle-->

        <!--begin::Mobile logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ route('dashboard') }}" class="d-lg-none" wire:navigate>
                <img alt="Logo" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-30px" />
            </a>
        </div>
        <!--end::Mobile logo-->

        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            
            <!--begin::Left Area: Branch Switcher-->
            <div class="d-flex align-items-center">
                <div class="app-navbar-item">
                    <button type="button" 
                            class="btn btn-sm btn-custom btn-light-primary fw-bold d-flex align-items-center gap-2 px-3 py-2"
                            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" 
                            data-kt-menu-attach="parent"
                            data-kt-menu-placement="bottom-start">
                        <i class="fas fa-store fs-7"></i>
                        <span class="fs-7 text-truncate" style="max-width: 140px;">
                            {{ $currentBranch?->name ?? __('lang.all_branches') }}
                        </span>
                        <i class="fas fa-chevron-down fs-9 opacity-75"></i>
                    </button>
                    
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-2 fs-7 w-200px" data-kt-menu="true">
                        <div class="px-4 py-2 text-muted fs-8 fw-bold text-uppercase border-bottom">
                            @lang('models/Branches.plural')
                        </div>
                        <div class="scroll-y mh-180px py-1">
                            @forelse($allBranches as $branch)
                                <div class="menu-item px-3 my-0">
                                    <a href="{{ route('branches.switch', $branch->id) }}" 
                                       class="menu-link px-3 py-2 d-flex align-items-center justify-content-between {{ ($currentBranch?->id == $branch->id) ? 'active' : '' }}">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <i class="fas fa-building fs-8 {{ ($currentBranch?->id == $branch->id) ? 'text-primary' : 'text-gray-400' }}"></i>
                                            <span class="text-truncate">{{ $branch->name }}</span>
                                        </div>
                                        @if($currentBranch?->id == $branch->id)
                                            <i class="fas fa-check fs-8 text-primary"></i>
                                        @endif
                                    </a>
                                </div>
                            @empty
                                <div class="px-3 py-2 text-muted fs-8 text-center">لا توجد فروع مسجلة</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Left Area: Branch Switcher-->

            <!--begin::Center Area: Live Date & Clock-->
            <div class="d-none d-md-flex align-items-center"
                 x-data="{
                     time: '',
                     date: '',
                     initClock() {
                         const update = () => {
                             const now = new Date();
                             this.time = now.toLocaleTimeString('{{ app()->getLocale() == 'ar' ? 'ar-SA' : 'en-US' }}', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                             this.date = now.toLocaleDateString('{{ app()->getLocale() == 'ar' ? 'ar-SA' : 'en-US' }}', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                         };
                         update();
                         setInterval(update, 1000);
                     }
                 }" 
                 x-init="initClock()">
                <div class="badge badge-light-primary fw-bold py-2 px-3 fs-7 d-flex align-items-center gap-2">
                    <i class="far fa-calendar-alt fs-7"></i>
                    <span x-text="date"></span>
                    <span class="text-muted mx-1">•</span>
                    <i class="far fa-clock fs-7 text-success"></i>
                    <span class="font-monospace" x-text="time"></span>
                </div>
            </div>
            <!--end::Center Area: Live Date & Clock-->

            <!--begin::Navbar-->
            <div class="app-navbar flex-shrink-0">
                
                <!--begin::Fullscreen item-->
                <div class="app-navbar-item ms-1 ms-md-3">
                    <a class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px" 
                       onclick="toggleFullScreen()" 
                       href="#" 
                       role="button"
                       title="Fullscreen">
                        <i class="fas fa-expand-arrows-alt text-primary fs-3"></i>
                    </a>
                </div>
                <!--end::Fullscreen item-->

                <!--begin::Notifications-->
                @can('notifications.index')
                    <div class="app-navbar-item ms-1 ms-md-3">
                        @livewire('notifications')
                    </div>
                @endcan
                <!--end::Notifications-->

                <!--begin::User menu-->
                @auth('web')
                <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                    <!--begin::User avatar trigger-->
                    <div class="cursor-pointer symbol symbol-35px"
                        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" 
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <img src="{{ asset('admin_assets/media/avatars/blanksmall.jpg') }}" class="rounded-3" alt="user" />
                    </div>
                    <!--end::User avatar trigger-->

                    <!--begin::User account menu (Metronic 8 Native with Themes & Language inside)-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                        data-kt-menu="true">
                        
                        <!--begin::Menu item (User Info)-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ asset('admin_assets/media/avatars/blank.png') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ $user->name }}
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">
                                            {{ $user->getRoleNames()->first() ?? 'Super Admin' }}
                                        </span>
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7 text-truncate" style="max-width: 150px;">
                                        {{ $user->email }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end::Menu item-->
                        
                        <div class="separator my-2"></div>

                        <!--begin::Menu item (Language Switcher)-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">
                                    @lang('lang.language')
                                    @if (app()->getLocale() == 'en')
                                    <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                        @lang('lang.english')
                                        <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="" />
                                    </span>
                                    @else
                                    <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                        @lang('lang.arabic')
                                        <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="" />
                                    </span>
                                    @endif
                                </span>
                            </a>
                            <!--begin::Language sub-menu-->
                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                <div class="menu-item px-3">
                                    <a href="{{ route('switchLang', 'en') }}" class="menu-link d-flex px-5 {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="" />
                                        </span>
                                        @lang('lang.english')
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="{{ route('switchLang', 'ar') }}" class="menu-link d-flex px-5 {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="" />
                                        </span>
                                        @lang('lang.arabic')
                                    </a>
                                </div>
                            </div>
                            <!--end::Language sub-menu-->
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu item (Theme Mode Switcher)-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">
                                    المظهر (Theme)
                                    <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                        <i class="ki-duotone ki-night-day theme-light-show fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                        <i class="ki-duotone ki-moon theme-dark-show fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </span>
                            </a>
                            <!--begin::Theme sub-menu-->
                            <div class="menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                                data-kt-menu="true" data-kt-element="theme-mode-menu">
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-night-day fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                        </span>
                                        <span class="menu-title">Light</span>
                                    </a>
                                </div>
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-moon fs-2"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <span class="menu-title">Dark</span>
                                    </a>
                                </div>
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-screen fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </span>
                                        <span class="menu-title">System</span>
                                    </a>
                                </div>
                            </div>
                            <!--end::Theme sub-menu-->
                        </div>
                        <!--end::Menu item-->

                        @can('settings.edit')
                        <div class="menu-item px-5 my-1">
                            <a href="{{ route('settings.edit', 1) }}" class="menu-link px-5" wire:navigate>@lang('lang.settings')</a>
                        </div>
                        @endcan

                        <div class="separator my-1"></div>

                        <div class="menu-item px-5">
                            <a href="{{ route('logout') }}" class="menu-link px-5 text-danger">@lang('lang.logout')</a>
                        </div>
                    </div>
                    <!--end::User account menu-->
                </div>
                @endauth
                <!--end::User menu-->

            </div>
            <!--end::Navbar-->

        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>