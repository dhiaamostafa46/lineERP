@php
    $currentBranch = auth()->user()?->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : \App\Models\Branch::first();
    $allBranches = \App\Models\Branch::all();
    $user = auth('web')->user();
@endphp

<div id="kt_app_header" class="app-header no-print" data-kt-sticky="true" data-kt-sticky-activate="{default: true, lg: true}"
    data-kt-sticky-name="app-header-minimize" data-kt-sticky-offset="{default: '200px', lg: '0'}"
    data-kt-sticky-animation="false"
    style="height: 70px; background: #ffffff !important; border-bottom: 1px solid #e9edf2 !important; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03) !important;">
    
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-center justify-content-between h-100 px-4 px-lg-8" id="kt_app_header_container">
        
        <!-- ============================================================== -->
        <!-- 1. START ZONE: Mobile Toggle + Mobile Logo + Branch Switcher -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-3">
            
            <!--begin::Sidebar mobile toggle-->
            <div class="d-flex align-items-center d-lg-none">
                <button type="button" class="btn btn-icon btn-sm btn-light-primary w-38px h-38px rounded-3" id="kt_app_sidebar_mobile_toggle">
                    <i class="ki-duotone ki-abstract-14 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
            <!--end::Sidebar mobile toggle-->

            <!--begin::Mobile logo-->
            <div class="d-flex align-items-center d-lg-none me-2">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-30px" />
                </a>
            </div>
            <!--end::Mobile logo-->

            <!--begin::Branch Selector Pill-->
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 border transition-all"
                        style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f1f5f9';"
                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                    <div class="d-flex align-items-center justify-content-center rounded-2 shadow-xs" style="width: 26px; height: 26px; background: #0f172a; color: #ffffff;">
                        <i class="fas fa-store-alt fs-8 text-white"></i>
                    </div>
                    <div class="d-flex flex-column text-start">
                        <span class="fs-9 text-muted fw-semibold" style="line-height: 1.1;">@lang('models/Branches.singular')</span>
                        <span class="fs-7 fw-bold text-truncate" style="max-width: 150px; color: #0f172a;">
                            {{ $currentBranch?->name ?? __('lang.all_branches') }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down fs-9 text-muted ms-1"></i>
                </button>

                <ul class="dropdown-menu shadow-lg py-2 rounded-3 border-0" style="min-width: 230px; border: 1px solid #e2e8f0 !important;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fs-8 text-muted fw-bold text-uppercase">@lang('models/Branches.plural')</div>
                    </li>
                    <div class="scroll-y mh-220px py-1">
                        @forelse($allBranches as $branch)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 fs-7 {{ ($currentBranch?->id == $branch->id) ? 'active bg-light-primary text-primary fw-bold' : 'text-gray-700' }}" 
                                   href="{{ route('branches.switch', $branch->id) }}">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="fas fa-building fs-8 {{ ($currentBranch?->id == $branch->id) ? 'text-primary' : 'text-muted' }}"></i>
                                        <span class="text-truncate">{{ $branch->name }}</span>
                                    </div>
                                    @if($currentBranch?->id == $branch->id)
                                        <i class="fas fa-check fs-8 text-primary ms-2"></i>
                                    @endif
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-2 text-muted fs-8 text-center">لا توجد فروع مسجلة</li>
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
        <div class="d-flex align-items-center gap-3">
            
            <!--begin::Live Real-Time Date & Clock Badge-->
            <div class="d-none d-md-flex align-items-center gap-3 px-3 py-2 rounded-3 border" 
                 style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b;"
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
                <div class="d-flex align-items-center gap-2">
                    <i class="far fa-calendar-alt fs-7 text-primary"></i>
                    <span class="fs-8 fw-semibold text-gray-700" x-text="date"></span>
                </div>

                <span class="text-gray-300">|</span>

                <!-- Live Clock -->
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-dot-live"></span>
                    <i class="far fa-clock fs-7 text-success"></i>
                    <span class="fs-8 fw-bold font-monospace text-gray-900" x-text="time"></span>
                </div>
            </div>
            <!--end::Live Real-Time Date & Clock Badge-->

            <!--begin::Global Search Bar-->
            <div class="header-search-box position-relative d-none d-xl-block" style="width: 260px;">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 fs-7 text-muted"></i>
                <input type="text" 
                       id="globalHeaderSearch"
                       class="form-control form-control-sm rounded-pill ps-9 pe-4 fs-7 border" 
                       placeholder="@lang('lang.search')... (Ctrl + K)" 
                       style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 38px; transition: all 0.2s ease;"
                       onfocus="this.style.background='#ffffff'; this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.15)';"
                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';" />
            </div>
            <!--end::Global Search Bar-->

        </div>
        <!-- ============================================================== -->
        <!-- END CENTER ZONE -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- 3. END ZONE: Fullscreen + Notifications + Language + Profile -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            
            <!--begin::Fullscreen Toggle-->
            <button type="button" 
                    class="btn btn-icon btn-sm btn-light btn-active-light-primary w-38px h-38px rounded-3 border"
                    onclick="toggleFullScreen()" 
                    title="Fullscreen"
                    style="border-color: #e2e8f0 !important;">
                <i class="fas fa-expand-arrows-alt fs-7 text-gray-600"></i>
            </button>
            <!--end::Fullscreen Toggle-->

            <!--begin::Notifications Bell-->
            @can('notifications.index')
                <div class="position-relative">
                    @livewire('notifications')
                </div>
            @endcan
            <!--end::Notifications Bell-->

            <!--begin::Language Switcher Dropdown-->
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                        style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 38px;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    @if (app()->getLocale() == 'en')
                        <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                        <span class="fs-8 fw-bold">EN</span>
                    @else
                        <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                        <span class="fs-8 fw-bold">العربية</span>
                    @endif
                    <i class="fas fa-chevron-down fs-9 text-muted ms-1"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2 rounded-3 border-0" style="min-width: 150px; border: 1px solid #e2e8f0 !important;">
                    <li>
                        <a href="{{ route('switchLang', 'ar') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7 {{ app()->getLocale() == 'ar' ? 'active bg-light-primary text-primary fw-bold' : 'text-gray-700' }}">
                            <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                            <span>العربية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('switchLang', 'en') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7 {{ app()->getLocale() == 'en' ? 'active bg-light-primary text-primary fw-bold' : 'text-gray-700' }}">
                            <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                            <span>English</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!--end::Language Switcher Dropdown-->

            <!--begin::User Profile Account-->
            @auth('web')
                <div class="dropdown">
                    <div class="cursor-pointer d-flex align-items-center gap-2 p-1 rounded-3" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="symbol symbol-38px symbol-circle">
                            <div class="symbol-label fw-bold fs-6 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                            </div>
                        </div>
                        <div class="d-none d-lg-flex flex-column text-start">
                            <span class="fs-7 fw-bold text-gray-900" style="line-height: 1.1;">{{ $user->name }}</span>
                            <span class="fs-9 text-muted">{{ $user->roles->first()?->name ?? 'Super Admin' }}</span>
                        </div>
                        <i class="fas fa-chevron-down fs-9 text-muted d-none d-lg-inline ms-1"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2 rounded-3 border-0 w-240px" style="border: 1px solid #e2e8f0 !important;">
                        <li class="px-4 py-3 border-bottom">
                            <div class="fw-bold fs-7 text-gray-900">{{ $user->name }}</div>
                            <div class="fs-8 text-muted text-truncate">{{ $user->email }}</div>
                        </li>
                        @can('settings.edit')
                            <li>
                                <a href="{{ route('settings.edit', 1) }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-4 fs-7 text-gray-700" wire:navigate>
                                    <i class="fas fa-cog fs-8 text-muted"></i>
                                    <span>@lang('lang.settings')</span>
                                </a>
                            </li>
                        @endcan
                        <li><hr class="dropdown-divider my-1" style="border-color: #e2e8f0;"></li>
                        <li>
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();"
                               class="dropdown-item d-flex align-items-center gap-2 py-2 px-4 fs-7 text-danger">
                                <i class="fas fa-power-off fs-8 text-danger"></i>
                                <span>@lang('lang.logout')</span>
                            </a>
                            <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
            <!--end::User Profile Account-->

        </div>
        <!-- ============================================================== -->
        <!-- END END ZONE -->
        <!-- ============================================================== -->

    </div>
    <!--end::Header container-->
</div>
