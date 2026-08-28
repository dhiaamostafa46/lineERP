@php
    $currentBranch = auth()->user()?->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : \App\Models\Branch::first();
    $allBranches = \App\Models\Branch::all();
    $user = auth('web')->user();
@endphp

<div id="kt_app_header" class="app-header no-print" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}"
    data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}"
    data-kt-sticky-animation="false"
    style="height: 56px; background: #ffffff !important; border-bottom: 1px solid #e9edf2 !important; box-shadow: 0 1px 6px rgba(0, 0, 0, 0.02) !important;">
    
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-center justify-content-between h-100 px-3 px-lg-6" id="kt_app_header_container">
        
        <!-- ============================================================== -->
        <!-- 1. START ZONE: Mobile Toggle + Mobile Logo + Branch Switcher -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-2">
            
            <!--begin::Sidebar mobile toggle-->
            <div class="d-flex align-items-center d-lg-none">
                <button type="button" class="btn btn-icon btn-sm btn-light w-30px h-30px rounded-2" id="kt_app_sidebar_mobile_toggle">
                    <i class="ki-duotone ki-abstract-14 fs-4 text-gray-700">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
            <!--end::Sidebar mobile toggle-->

            <!--begin::Mobile logo-->
            <div class="d-flex align-items-center d-lg-none me-1">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-24px" />
                </a>
            </div>
            <!--end::Mobile logo-->

            <!--begin::Branch Selector Pill-->
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-xs d-flex align-items-center gap-2 px-2 py-1 rounded-2 border transition-all"
                        style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 32px;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f1f5f9';"
                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                    <div class="d-flex align-items-center justify-content-center rounded-1" style="width: 20px; height: 20px; background: #0f172a; color: #ffffff;">
                        <i class="fas fa-store fs-9 text-white"></i>
                    </div>
                    <div class="d-flex flex-column text-start">
                        <span class="fs-9 text-truncate fw-bold" style="max-width: 130px; color: #0f172a; line-height: 1.1;">
                            {{ $currentBranch?->name ?? __('lang.all_branches') }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down fs-9 text-muted ms-1" style="font-size: 8px !important;"></i>
                </button>

                <ul class="dropdown-menu shadow-sm py-1 rounded-2 border-0" style="min-width: 200px; border: 1px solid #e2e8f0 !important;">
                    <li class="px-3 py-1 border-bottom">
                        <div class="fs-9 text-muted fw-bold text-uppercase">@lang('models/Branches.plural')</div>
                    </li>
                    <div class="scroll-y mh-180px py-1">
                        @forelse($allBranches as $branch)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-1 px-3 fs-8 {{ ($currentBranch?->id == $branch->id) ? 'active bg-light-primary text-primary fw-bold' : 'text-gray-700' }}" 
                                   href="{{ route('branches.switch', $branch->id) }}">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="fas fa-building fs-9 {{ ($currentBranch?->id == $branch->id) ? 'text-primary' : 'text-muted' }}"></i>
                                        <span class="text-truncate">{{ $branch->name }}</span>
                                    </div>
                                    @if($currentBranch?->id == $branch->id)
                                        <i class="fas fa-check fs-9 text-primary ms-1"></i>
                                    @endif
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-1 text-muted fs-9 text-center">لا توجد فروع مسجلة</li>
                        @endforelse
                    </div>
                </ul>
            </div>
            <!--end::Branch Selector Pill-->

        </div>
        <!-- ============================================================== -->
        <!-- END START ZONE -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- 2. CENTER ZONE: Live Date & Clock + Quick Search -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-2">
            
            <!--begin::Live Real-Time Date & Clock Badge-->
            <div class="d-none d-md-flex align-items-center gap-2 px-2 py-1 rounded-2 border" 
                 style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 32px;"
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
                
                <!-- Date -->
                <div class="d-flex align-items-center gap-1">
                    <i class="far fa-calendar-alt fs-8 text-primary"></i>
                    <span class="fs-8 fw-semibold text-gray-700" x-text="date"></span>
                </div>

                <span class="text-gray-300">|</span>

                <!-- Live Clock -->
                <div class="d-flex align-items-center gap-1">
                    <span class="badge-dot-live"></span>
                    <i class="far fa-clock fs-8 text-success"></i>
                    <span class="fs-8 fw-bold font-monospace text-gray-900" x-text="time"></span>
                </div>
            </div>
            <!--end::Live Real-Time Date & Clock Badge-->

            <!--begin::Global Search Bar-->
            <div class="header-search-box position-relative d-none d-xl-block" style="width: 220px;">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 fs-8 text-muted"></i>
                <input type="text" 
                       id="globalHeaderSearch"
                       class="form-control form-control-sm rounded-pill ps-8 pe-3 fs-8 border" 
                       placeholder="@lang('lang.search')... (Ctrl + K)" 
                       style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 32px; transition: all 0.2s ease;"
                       onfocus="this.style.background='#ffffff'; this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.15)';"
                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';" />
            </div>
            <!--end::Global Search Bar-->

        </div>
        <!-- ============================================================== -->
        <!-- END CENTER ZONE -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- 3. END ZONE: Fullscreen + Notifications + User Menu (Metronic Native) -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-2">
            
            <!--begin::Fullscreen Toggle-->
            <button type="button" 
                    class="btn btn-icon btn-sm btn-light btn-active-light-primary w-32px h-32px rounded-2 border"
                    onclick="toggleFullScreen()" 
                    title="Fullscreen"
                    style="border-color: #e2e8f0 !important;">
                <i class="fas fa-expand-arrows-alt fs-8 text-gray-600"></i>
            </button>
            <!--end::Fullscreen Toggle-->

            <!--begin::Notifications Bell-->
            @can('notifications.index')
                <div class="position-relative">
                    @livewire('notifications')
                </div>
            @endcan
            <!--end::Notifications Bell-->

            <!--begin::User Menu (Metronic Native Pro Engine)-->
            @auth('web')
                <div class="app-navbar-item" id="kt_header_user_menu_toggle">
                    <!--begin::User avatar trigger-->
                    <div class="cursor-pointer symbol symbol-30px symbol-circle"
                        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" 
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-7">
                            {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                        </div>
                    </div>
                    <!--end::User avatar trigger-->

                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-3 fs-7 w-260px"
                        data-kt-menu="true">
                        
                        <!--begin::Menu item (User Info)-->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-40px symbol-circle me-3">
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                                        {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Username-->
                                <div class="d-flex flex-column text-truncate">
                                    <div class="fw-bold d-flex align-items-center fs-7 text-gray-900 text-truncate">
                                        {{ $user->name }}
                                    </div>
                                    <span class="text-muted fs-8 text-truncate">{{ $user->email }}</span>
                                    <span class="badge badge-light-success fw-bold fs-9 px-2 py-0 mt-1 align-self-start">
                                        {{ $user->getRoleNames()->first() ?? 'Super Admin' }}
                                    </span>
                                </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->

                        <div class="separator my-2"></div>

                        <!--begin::Menu item (Theme Mode)-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="left-start" data-kt-menu-offset="-10px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">
                                    المظهر (Theme)
                                    <span class="fs-8 rounded bg-light px-2 py-1 position-absolute translate-middle-y top-50 end-0">
                                        <i class="ki-duotone ki-night-day fs-4 theme-light-show text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                        <i class="ki-duotone ki-moon fs-4 theme-dark-show text-info"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </span>
                            </a>
                            <!--begin::Menu sub-->
                            <div class="menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-2 fs-7 w-150px"
                                data-kt-menu="true" data-kt-element="theme-mode-menu">
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-night-day fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                        </span>
                                        <span class="menu-title fs-8">فاتح (Light)</span>
                                    </a>
                                </div>
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-moon fs-4"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                        <span class="menu-title fs-8">داكن (Dark)</span>
                                    </a>
                                </div>
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                        <span class="menu-icon" data-kt-element="icon">
                                            <i class="ki-duotone ki-screen fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </span>
                                        <span class="menu-title fs-8">تلقائي (System)</span>
                                    </a>
                                </div>
                            </div>
                            <!--end::Menu sub-->
                        </div>
                        <!--end::Menu item-->

                        <!--begin::Menu item (Language)-->
                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="left-start" data-kt-menu-offset="-10px, 0">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">
                                    @lang('lang.language')
                                    @if (app()->getLocale() == 'en')
                                        <span class="fs-8 rounded bg-light px-2 py-1 position-absolute translate-middle-y top-50 end-0">
                                            EN <img class="w-12px h-12px rounded-circle ms-1" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="" />
                                        </span>
                                    @else
                                        <span class="fs-8 rounded bg-light px-2 py-1 position-absolute translate-middle-y top-50 end-0">
                                            العربية <img class="w-12px h-12px rounded-circle ms-1" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="" />
                                        </span>
                                    @endif
                                </span>
                            </a>
                            <!--begin::Menu sub-->
                            <div class="menu-sub menu-sub-dropdown w-160px py-2">
                                <div class="menu-item px-3">
                                    <a href="{{ route('switchLang', 'ar') }}" class="menu-link d-flex px-3 {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                        <span class="symbol symbol-15px me-3">
                                            <img class="rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="" />
                                        </span>
                                        <span class="fs-8">@lang('lang.arabic')</span>
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="{{ route('switchLang', 'en') }}" class="menu-link d-flex px-3 {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                        <span class="symbol symbol-15px me-3">
                                            <img class="rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="" />
                                        </span>
                                        <span class="fs-8">@lang('lang.english')</span>
                                    </a>
                                </div>
                            </div>
                            <!--end::Menu sub-->
                        </div>
                        <!--end::Menu item-->

                        @can('settings.edit')
                            <div class="menu-item px-5 my-1">
                                <a href="{{ route('settings.edit', 1) }}" class="menu-link px-5 fs-7" wire:navigate>
                                    @lang('lang.settings')
                                </a>
                            </div>
                        @endcan

                        <div class="separator my-1"></div>

                        <div class="menu-item px-5">
                            <a href="{{ route('logout') }}" class="menu-link px-5 text-danger fs-7">
                                @lang('lang.logout')
                            </a>
                        </div>

                    </div>
                    <!--end::User account menu-->
                </div>
            @endauth
            <!--end::User Menu-->

        </div>
        <!-- ============================================================== -->
        <!-- END END ZONE -->
        <!-- ============================================================== -->

    </div>
    <!--end::Header container-->
</div>
