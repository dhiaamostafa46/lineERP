@php
    $currentBranch = auth()->user()?->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : \App\Models\Branch::first();
    $allBranches = \App\Models\Branch::all();
    $user = auth('web')->user();
@endphp

<div id="kt_app_header" class="app-header no-print d-flex align-items-center" 
     style="height: 65px; background: #ffffff !important; border-bottom: 1px solid #e2e8f0 !important; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;">
    
    <!--begin::Header container (Enterprise Flexbox 3-Zone Layout)-->
    <div class="app-container container-fluid d-flex align-items-center justify-content-between w-100 px-4 px-lg-8" id="kt_app_header_container">
        
        <!-- ============================================================== -->
        <!-- ZONE 1: START (Mobile Toggle + Mobile Logo + Branch Switcher) -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-3">
            
            <!--begin::Sidebar mobile toggle-->
            <div class="d-flex align-items-center d-lg-none">
                <button type="button" 
                        class="btn btn-icon btn-sm w-36px h-36px rounded-2 border" 
                        id="kt_app_sidebar_mobile_toggle"
                        style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <i class="ki-duotone ki-abstract-14 fs-2 text-gray-700">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
            <!--end::Sidebar mobile toggle-->

            <!--begin::Mobile logo-->
            <div class="d-flex align-items-center d-lg-none me-1">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img alt="LineERP" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-28px" />
                </a>
            </div>
            <!--end::Mobile logo-->

            <!--begin::Branch Selector Pill-->
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-sm d-flex align-items-center gap-2 px-3 py-1 rounded-2 border transition-all"
                        style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 38px;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';"
                        onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                    <div class="d-flex align-items-center justify-content-center rounded-2" 
                         style="width: 24px; height: 24px; background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-store-alt fs-8"></i>
                    </div>
                    <div class="d-flex flex-column text-start">
                        <span class="text-muted fw-bold" style="font-size: 9.5px; text-transform: uppercase; line-height: 1;">@lang('models/Branches.singular')</span>
                        <span class="fw-bold text-truncate" style="font-size: 12.5px; max-width: 140px; color: #0f172a; line-height: 1.2;">
                            {{ $currentBranch?->name ?? __('lang.all_branches') }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 9px !important;"></i>
                </button>

                <ul class="dropdown-menu shadow-lg py-2 rounded-3 border-0 mt-1" style="min-width: 220px; border: 1px solid #e2e8f0 !important;">
                    <li class="px-3 py-1 border-bottom">
                        <div class="text-muted fw-bold text-uppercase" style="font-size: 10px;">@lang('models/Branches.plural')</div>
                    </li>
                    <div class="scroll-y mh-200px py-1">
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
        <!-- END ZONE 1 -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- ZONE 2: CENTER (Live Real-Time Clock & Date)                  -->
        <!-- ============================================================== -->
        <div class="d-none d-md-flex align-items-center gap-3">
            
            <!--begin::Live Real-Time Date & Clock Badge-->
            <div class="d-flex align-items-center gap-3 px-3 py-1 rounded-2 border" 
                 style="background: #f8fafc; border-color: #e2e8f0 !important; color: #1e293b; height: 38px;"
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
                    <i class="far fa-calendar-alt text-primary fs-7"></i>
                    <span class="fw-semibold text-gray-800" style="font-size: 12.5px;" x-text="date"></span>
                </div>

                <span style="display: inline-block; width: 1px; height: 14px; background: #cbd5e1;"></span>

                <!-- Live Clock -->
                <div class="d-flex align-items-center gap-2">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                    <i class="far fa-clock text-success fs-7"></i>
                    <span class="fw-bold font-monospace text-gray-900" style="font-size: 12.5px;" x-text="time"></span>
                </div>
            </div>
            <!--end::Live Real-Time Date & Clock Badge-->

        </div>
        <!-- ============================================================== -->
        <!-- END ZONE 2 -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- ZONE 3: END (Search + Fullscreen + Notifications + User Menu) -->
        <!-- ============================================================== -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            
            <!--begin::Global Search Bar-->
            <div class="position-relative d-none d-xl-block" style="width: 220px;">
                <input type="text" 
                       id="globalHeaderSearch"
                       class="form-control form-control-sm rounded-pill px-3 fs-8 border" 
                       placeholder="@lang('lang.search')..." 
                       style="background: #f8fafc; border-color: #e2e8f0 !important; color: #0f172a; height: 36px; transition: all 0.2s ease;"
                       onfocus="this.style.background='#ffffff'; this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 2px rgba(59, 130, 246, 0.15)';"
                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';" />
            </div>
            <!--end::Global Search Bar-->

            <!--begin::Fullscreen Toggle-->
            <button type="button" 
                    class="btn btn-icon btn-sm w-36px h-36px rounded-2 border"
                    onclick="toggleFullScreen()" 
                    title="Fullscreen"
                    style="background: #f8fafc; border-color: #e2e8f0 !important;">
                <i class="fas fa-expand-arrows-alt text-gray-600 fs-7"></i>
            </button>
            <!--end::Fullscreen Toggle-->

            <!--begin::Notifications Bell-->
            @can('notifications.index')
                <div class="position-relative">
                    @livewire('notifications')
                </div>
            @endcan
            <!--end::Notifications Bell-->

            <!--begin::User Profile Button & Dropdown Menu-->
            @auth('web')
                <div class="dropdown">
                    
                    <!-- Profile Pill Trigger Button -->
                    <div class="cursor-pointer d-flex align-items-center gap-2 p-1 pe-3 rounded-pill border transition-all"
                         data-bs-toggle="dropdown" 
                         aria-expanded="false"
                         style="background: #f8fafc; border-color: #e2e8f0 !important; height: 38px;"
                         onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';"
                         onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                        
                        <!-- Avatar -->
                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-xs" 
                             style="width: 28px; height: 28px; font-size: 11px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                            {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                        </div>
                        
                        <!-- Name & Role -->
                        <div class="d-none d-lg-flex flex-column text-start">
                            <span class="fw-bold text-gray-900" style="font-size: 12.5px; line-height: 1.1;">{{ $user->name }}</span>
                            <span class="text-muted" style="font-size: 10px !important;">{{ $user->roles->first()?->name ?? 'Super Admin' }}</span>
                        </div>
                        
                        <i class="fas fa-chevron-down text-muted d-none d-lg-inline ms-1" style="font-size: 8px !important;"></i>
                    </div>

                    <!-- Personal Dropdown Card -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-xl py-2 rounded-3 border-0 mt-1" 
                        style="width: 275px; border: 1px solid #e2e8f0 !important;">
                        
                        <!-- 1. User Header Area -->
                        <li class="px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-xs" 
                                     style="width: 40px; height: 40px; font-size: 15px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                    {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                                </div>
                                <div class="d-flex flex-column text-truncate">
                                    <div class="fw-bold text-gray-900 text-truncate" style="font-size: 13.5px;">{{ $user->name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 11.5px;">{{ $user->email }}</div>
                                    <span class="badge badge-light-primary fw-bold px-2 py-0 mt-1 align-self-start" style="font-size: 9.5px !important;">
                                        {{ $user->roles->first()?->name ?? 'Super Admin' }}
                                    </span>
                                </div>
                            </div>
                        </li>

                        <!-- 2. Themes Switcher Section -->
                        <li class="px-3 pt-3 pb-2">
                            <div class="text-muted fw-bold px-1 pb-2 text-uppercase d-flex align-items-center justify-content-between" style="font-size: 10px !important;">
                                <span>المظهر (Themes)</span>
                                <i class="fas fa-palette text-primary fs-8"></i>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-1 rounded-2 bg-light border">
                                <a href="#" 
                                   class="btn btn-xs flex-grow-1 h-28px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700 active bg-white shadow-xs" 
                                   data-kt-element="mode" data-kt-value="light" title="Light Mode">
                                    <i class="fas fa-sun text-warning fs-8"></i>
                                    <span class="fw-bold" style="font-size: 11px;">فاتح</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs flex-grow-1 h-28px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="dark" title="Dark Mode">
                                    <i class="fas fa-moon text-info fs-8"></i>
                                    <span class="fw-semibold" style="font-size: 11px;">داكن</span>
                                </a>
                                <a href="#" 
                                   class="btn btn-xs flex-grow-1 h-28px rounded-2 d-flex align-items-center justify-content-center gap-1 text-gray-700" 
                                   data-kt-element="mode" data-kt-value="system" title="System Mode">
                                    <i class="fas fa-desktop text-gray-600 fs-8"></i>
                                    <span class="fw-semibold" style="font-size: 11px;">تلقائي</span>
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-2" style="border-color: #e2e8f0;"></li>

                        <!-- 3. Language Switcher Section -->
                        <li class="px-3 py-1">
                            <div class="text-muted fw-bold px-1 pb-2 text-uppercase d-flex align-items-center justify-content-between" style="font-size: 10px !important;">
                                <span>@lang('lang.language')</span>
                                <i class="fas fa-globe text-primary fs-8"></i>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('switchLang', 'ar') }}" 
                                   class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'ar' ? 'background: #f1faff;' : '' }}; font-size: 12.5px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                                        <span>العربية (Arabic)</span>
                                    </div>
                                    @if(app()->getLocale() == 'ar')
                                        <i class="fas fa-check fs-8 text-primary"></i>
                                    @endif
                                </a>
                                <a href="{{ route('switchLang', 'en') }}" 
                                   class="d-flex align-items-center justify-content-between py-2 px-3 rounded-2 text-decoration-none {{ app()->getLocale() == 'en' ? 'bg-light-primary text-primary fw-bold' : 'text-gray-700' }}"
                                   style="{{ app()->getLocale() == 'en' ? 'background: #f1faff;' : '' }}; font-size: 12.5px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                                        <span>English (الإنجليزية)</span>
                                    </div>
                                    @if(app()->getLocale() == 'en')
                                        <i class="fas fa-check fs-8 text-primary"></i>
                                    @endif
                                </a>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider my-2" style="border-color: #e2e8f0;"></li>

                        <!-- 4. Settings Link -->
                        @can('settings.edit')
                            <li class="px-2">
                                <a href="{{ route('settings.edit', 1) }}" 
                                   class="d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-gray-700 text-decoration-none" 
                                   style="font-size: 12.5px;"
                                   wire:navigate>
                                    <i class="fas fa-cog fs-8 text-muted"></i>
                                    <span>@lang('lang.settings')</span>
                                </a>
                            </li>
                        @endcan

                        <!-- 5. Logout Button -->
                        <li class="px-2 pt-1">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();"
                               class="d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-danger text-decoration-none"
                               style="font-size: 12.5px;">
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
            <!--end::User Profile Button & Dropdown Menu-->

        </div>
        <!-- ============================================================== -->
        <!-- END ZONE 3 -->
        <!-- ============================================================== -->

    </div>
    <!--end::Header container-->
</div>