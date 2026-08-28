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
        <!-- 3. END ZONE: Fullscreen + Notifications + User Profile (Themes & Lang inside) -->
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

            <!--begin::User Profile Account (Containing Theme Modes & Language Switcher)-->
            @auth('web')
                <div class="dropdown">
                    <div class="cursor-pointer d-flex align-items-center gap-2 p-1 rounded-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="symbol symbol-30px symbol-circle">
                            <div class="symbol-label fw-bold fs-7 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                            </div>
                        </div>
                        <div class="d-none d-lg-flex flex-column text-start">
                            <span class="fs-8 fw-bold text-gray-900" style="line-height: 1.1;">{{ $user->name }}</span>
                            <span class="fs-9 text-muted">{{ $user->roles->first()?->name ?? 'Admin' }}</span>
                        </div>
                        <i class="fas fa-chevron-down fs-9 text-muted d-none d-lg-inline ms-1" style="font-size: 8px !important;"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2 rounded-2 border-0 w-240px" style="border: 1px solid #e2e8f0 !important;">
                        
                        <!-- User Info Header -->
                        <li class="px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="symbol symbol-32px symbol-circle">
                                    <div class="symbol-label fw-bold fs-7 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                        {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column text-truncate">
                                    <div class="fw-bold fs-8 text-gray-900 text-truncate">{{ $user->name }}</div>
                                    <div class="fs-9 text-muted text-truncate">{{ $user->email }}</div>
                                    <span class="badge badge-light-success fs-9 fw-bold px-1 py-0 mt-1 align-self-start" style="font-size: 9px !important;">
                                        {{ $user->roles->first()?->name ?? 'Super Admin' }}
                                    </span>
                                </div>
                            </div>
                        </li>

                        <!-- Theme Mode Switcher -->
                        <li class="px-3 pt-2 pb-1">
                            <div class="text-muted fs-9 fw-bold px-1 pb-1 text-uppercase d-flex align-items-center justify-content-between" style="font-size: 10px !important;">
                                <span>المظهر (Themes)</span>
                                <i class="fas fa-palette fs-9 text-primary"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-1 rounded-2 bg-light border">
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-24px rounded-1 d-flex align-items-center justify-content-center gap-1 text-gray-700 active" 
                                   data-kt-element="mode" data-kt-value="light" title="Light Mode">
                                    <i class="fas fa-sun text-warning fs-9"></i>
                                    <span class="fs-9 fw-semibold">فاتح</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-24px rounded-1 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="dark" title="Dark Mode">
                                    <i class="fas fa-moon text-info fs-9"></i>
                                    <span class="fs-9 fw-semibold">داكن</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs btn-icon flex-grow-1 h-24px rounded-1 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="system" title="System Mode">
                                    <i class="fas fa-desktop text-gray-600 fs-9"></i>
                                    <span class="fs-9 fw-semibold">تلقائي</span>
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-1" style="border-color: #e2e8f0;"></li>

                        <!-- Language Switcher -->
                        <li class="px-3 py-1">
                            <div class="text-muted fs-9 fw-bold px-1 pb-1 text-uppercase d-flex align-items-center justify-content-between" style="font-size: 10px !important;">
                                <span>@lang('lang.language')</span>
                                <i class="fas fa-globe fs-9 text-primary"></i>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('switchLang', 'ar') }}" 
                                   class="d-flex align-items-center justify-content-between py-1 px-2 rounded-1 fs-8 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'ar' ? 'background: #f1faff;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-14px h-14px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                                        <span>العربية (Arabic)</span>
                                    </div>
                                    @if(app()->getLocale() == 'ar')
                                        <i class="fas fa-check fs-9 text-primary"></i>
                                    @endif
                                </a>
                                <a href="{{ route('switchLang', 'en') }}" 
                                   class="d-flex align-items-center justify-content-between py-1 px-2 rounded-1 fs-8 text-decoration-none {{ app()->getLocale() == 'en' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'en' ? 'background: #f1faff;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-14px h-14px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                                        <span>English (الإنجليزية)</span>
                                    </div>
                                    @if(app()->getLocale() == 'en')
                                        <i class="fas fa-check fs-9 text-primary"></i>
                                    @endif
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-1" style="border-color: #e2e8f0;"></li>

                        <!-- Settings Link -->
                        @can('settings.edit')
                            <li class="px-2">
                                <a href="{{ route('settings.edit', 1) }}" class="d-flex align-items-center gap-2 py-1 px-2 rounded-1 fs-8 text-gray-700 text-decoration-none" wire:navigate>
                                    <i class="fas fa-cog fs-9 text-muted"></i>
                                    <span>@lang('lang.settings')</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Logout Button -->
                        <li class="px-2 pt-1">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();"
                               class="d-flex align-items-center gap-2 py-1 px-2 rounded-1 fs-8 text-danger text-decoration-none">
                                <i class="fas fa-power-off fs-9 text-danger"></i>
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
