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
        <!-- 3. END ZONE: Fullscreen + Notifications + User Profile (Themes & Lang inside) -->
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

            <!--begin::User Profile Account (Containing Theme Modes & Language Switcher)-->
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

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2 rounded-3 border-0 w-275px" style="border: 1px solid #e2e8f0 !important;">
                        
                        <!-- User Info Header -->
                        <li class="px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-40px symbol-circle">
                                    <div class="symbol-label fw-bold fs-6 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                        {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column text-truncate">
                                    <div class="fw-bold fs-7 text-gray-900 text-truncate">{{ $user->name }}</div>
                                    <div class="fs-9 text-muted text-truncate">{{ $user->email }}</div>
                                    <span class="badge badge-light-success fs-9 fw-bold px-2 py-0 mt-1 align-self-start">
                                        {{ $user->roles->first()?->name ?? 'Super Admin' }}
                                    </span>
                                </div>
                            </div>
                        </li>

                        <!-- Theme Mode Switcher -->
                        <li class="px-3 pt-3 pb-2">
                            <div class="text-muted fs-9 fw-bold px-1 pb-2 text-uppercase d-flex align-items-center justify-content-between">
                                <span>المظهر (Themes)</span>
                                <i class="fas fa-palette fs-8 text-primary"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-1 rounded-2 bg-light border">
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-30px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700 active" 
                                   data-kt-element="mode" data-kt-value="light" title="Light Mode">
                                    <i class="fas fa-sun text-warning fs-8"></i>
                                    <span class="fs-9 fw-semibold">فاتح</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-30px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="dark" title="Dark Mode">
                                    <i class="fas fa-moon text-info fs-8"></i>
                                    <span class="fs-9 fw-semibold">داكن</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-30px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="system" title="System Mode">
                                    <i class="fas fa-desktop text-gray-600 fs-8"></i>
                                    <span class="fs-9 fw-semibold">تلقائي</span>
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-2" style="border-color: #e2e8f0;"></li>

                        <!-- Language Switcher -->
                        <li class="px-3 py-1">
                            <div class="text-muted fs-9 fw-bold px-1 pb-2 text-uppercase d-flex align-items-center justify-content-between">
                                <span>@lang('lang.language')</span>
                                <i class="fas fa-globe fs-8 text-primary"></i>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('switchLang', 'ar') }}" 
                                   class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 fs-7 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'ar' ? 'background: #f1faff;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                                        <span>العربية (Arabic)</span>
                                    </div>
                                    @if(app()->getLocale() == 'ar')
                                        <i class="fas fa-check fs-8 text-primary"></i>
                                    @endif
                                </a>
                                <a href="{{ route('switchLang', 'en') }}" 
                                   class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 fs-7 text-decoration-none {{ app()->getLocale() == 'en' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'en' ? 'background: #f1faff;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-18px h-18px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                                        <span>English (الإنجليزية)</span>
                                    </div>
                                    @if(app()->getLocale() == 'en')
                                        <i class="fas fa-check fs-8 text-primary"></i>
                                    @endif
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-2" style="border-color: #e2e8f0;"></li>

                        <!-- Settings Link -->
                        @can('settings.edit')
                            <li class="px-2">
                                <a href="{{ route('settings.edit', 1) }}" class="d-flex align-items-center gap-2 py-2 px-3 rounded-2 fs-7 text-gray-700 text-decoration-none" wire:navigate>
                                    <i class="fas fa-cog fs-7 text-muted"></i>
                                    <span>@lang('lang.settings')</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Logout Button -->
                        <li class="px-2 pt-1">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();"
                               class="d-flex align-items-center gap-2 py-2 px-3 rounded-2 fs-7 text-danger text-decoration-none">
                                <i class="fas fa-power-off fs-7 text-danger"></i>
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
