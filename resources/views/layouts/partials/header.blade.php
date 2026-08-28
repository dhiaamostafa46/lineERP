@php
    $currentBranch = auth()->user()?->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : \App\Models\Branch::first();
    $allBranches = \App\Models\Branch::all();
    $user = auth('web')->user();
@endphp

<div id="kt_app_header" class="app-header no-print d-flex align-items-center" 
     style="height: 65px; background: #ffffff !important; border-bottom: 1px solid #cbdfe6 !important; box-shadow: 0 2px 10px rgba(203, 223, 230, 0.25) !important;">
    
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-center justify-content-between w-100 px-4 px-lg-6" id="kt_app_header_container">
        
        <!-- ==================== LEFT / START SECTION (Toggle + Logo + Branch Switcher) ==================== -->
        <div class="d-flex align-items-center gap-3">
            
            <!--begin::Sidebar mobile toggle-->
            <div class="d-flex align-items-center d-lg-none" title="Show sidebar menu">
                <div class="btn btn-icon btn-sm btn-color-gray-600 btn-active-color-primary w-35px h-35px rounded-2" id="kt_app_sidebar_mobile_toggle" style="background: #f0f6f8; border: 1px solid #cbdfe6;">
                    <i class="ki-duotone ki-abstract-14 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <!--end::Sidebar mobile toggle-->

            <!--begin::Mobile logo-->
            <div class="d-flex align-items-center d-lg-none">
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <img alt="Logo" src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" class="h-28px" />
                </a>
            </div>
            <!--end::Mobile logo-->

            <!--begin::Branch Selector Dropdown-->
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0"
                        style="background: #f0f6f8; border: 1px solid #cbdfe6 !important; color: #1B325B;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <div class="d-flex align-items-center justify-content-center rounded-2" style="width: 26px; height: 26px; background: #1B325B; color: #ffffff;">
                        <i class="fas fa-building fs-8 text-white"></i>
                    </div>
                    <div class="d-flex flex-column text-start">
                        <span class="fs-9 text-muted fw-semibold" style="line-height: 1;">@lang('models/Branches.singular')</span>
                        <span class="fs-7 fw-bold text-truncate" style="max-width: 140px; color: #1B325B;">
                            {{ $currentBranch?->name ?? __('lang.all_branches') }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down fs-9 text-gray-500 ms-1"></i>
                </button>

                <ul class="dropdown-menu shadow-sm py-2 rounded-3 border-0" style="border: 1px solid #cbdfe6 !important; min-width: 210px;">
                    <li class="px-3 py-1">
                        <span class="fs-8 text-muted fw-bold text-uppercase">@lang('models/Branches.plural')</span>
                    </li>
                    <li><hr class="dropdown-divider my-1" style="border-color: #cbdfe6;"></li>
                    @forelse($allBranches as $branch)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 fs-7 {{ ($currentBranch?->id == $branch->id) ? 'active-branch-item fw-bold' : '' }}" 
                               href="{{ route('branches.switch', $branch->id) }}"
                               style="{{ ($currentBranch?->id == $branch->id) ? 'background: #cbdfe6; color: #1B325B;' : 'color: #2C3E50;' }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-store fs-8 {{ ($currentBranch?->id == $branch->id) ? 'text-primary' : 'text-gray-400' }}"></i>
                                    <span>{{ $branch->name }}</span>
                                </div>
                                @if($currentBranch?->id == $branch->id)
                                    <i class="fas fa-check fs-8 text-primary"></i>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="px-3 py-2 text-muted fs-8 text-center">لا توجد فروع مسجلة</li>
                    @endforelse
                </ul>
            </div>
            <!--end::Branch Selector Dropdown-->

        </div>
        <!-- ==================== END LEFT SECTION ==================== -->


        <!-- ==================== CENTER SECTION (Live Time & Date + Search) ==================== -->
        <div class="d-flex align-items-center gap-3">

            <!--begin::Live Real-Time Date & Clock Badge-->
            <div class="d-none d-md-flex align-items-center gap-3 px-3 py-2 rounded-3" 
                 style="background: #f0f6f8; border: 1px solid #cbdfe6; color: #1B325B;"
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
                
                <!-- Date Part -->
                <div class="d-flex align-items-center gap-2">
                    <i class="far fa-calendar-alt fs-7" style="color: #0284C7;"></i>
                    <span class="fs-8 fw-semibold" x-text="date"></span>
                </div>

                <span class="text-gray-300">|</span>

                <!-- Time Part with Pulsing Dot -->
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-dot-live"></span>
                    <i class="far fa-clock fs-7" style="color: #059669;"></i>
                    <span class="fs-8 fw-bold font-monospace" x-text="time"></span>
                </div>
            </div>
            <!--end::Live Real-Time Date & Clock Badge-->

            <!--begin::Header Search Bar-->
            <div class="header-search-box position-relative d-none d-lg-block" style="width: 260px;">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 fs-7" style="color: #7a99a3;"></i>
                <input type="text" 
                       id="globalHeaderSearch"
                       class="form-control form-control-sm rounded-pill ps-9 pe-4 border-0 fs-7" 
                       placeholder="@lang('lang.search')... (Ctrl + K)" 
                       style="background: #f0f6f8; border: 1px solid #cbdfe6 !important; color: #1B325B; height: 36px; transition: all 0.2s ease;"
                       onfocus="this.style.background='#ffffff'; this.style.borderColor='#8cb9c7'; this.style.boxShadow='0 0 0 3px rgba(203, 223, 230, 0.45)';"
                       onblur="this.style.background='#f0f6f8'; this.style.borderColor='#cbdfe6'; this.style.boxShadow='none';" />
            </div>
            <!--end::Header Search Bar-->

        </div>
        <!-- ==================== END CENTER SECTION ==================== -->


        <!-- ==================== RIGHT / END SECTION (Fullscreen + Alerts + Language + Profile) ==================== -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            
            <!--begin::Fullscreen Toggle-->
            <button type="button" 
                    class="btn btn-icon btn-sm btn-color-gray-600 btn-active-color-primary h-36px w-36px rounded-circle"
                    onclick="toggleFullScreen()" 
                    title="Fullscreen"
                    style="background: #f0f6f8; border: 1px solid #cbdfe6;">
                <i class="fas fa-expand-arrows-alt fs-7" style="color: #1B325B;"></i>
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
                        class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0"
                        style="background: #f0f6f8; border: 1px solid #cbdfe6 !important; color: #1B325B;"
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    @if (app()->getLocale() == 'en')
                        <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                        <span class="fs-8 fw-bold">EN</span>
                    @else
                        <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                        <span class="fs-8 fw-bold">العربية</span>
                    @endif
                    <i class="fas fa-chevron-down fs-9 text-gray-500"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2 rounded-3 border-0" style="border: 1px solid #cbdfe6 !important; min-width: 140px;">
                    <li>
                        <a href="{{ route('switchLang', 'ar') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7 {{ app()->getLocale() == 'ar' ? 'active fw-bold' : '' }}"
                           style="{{ app()->getLocale() == 'ar' ? 'background: #cbdfe6; color: #1B325B;' : 'color: #2C3E50;' }}">
                            <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/saudi-arabia.svg" alt="AR" />
                            <span>العربية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('switchLang', 'en') }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7 {{ app()->getLocale() == 'en' ? 'active fw-bold' : '' }}"
                           style="{{ app()->getLocale() == 'en' ? 'background: #cbdfe6; color: #1B325B;' : 'color: #2C3E50;' }}">
                            <img class="w-16px h-16px rounded-circle" src="{{ asset('admin_assets') }}/media/flags/united-states.svg" alt="EN" />
                            <span>English</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!--end::Language Switcher Dropdown-->

            <!--begin::User Account Dropdown-->
            @auth('web')
                <div class="dropdown">
                    <div class="cursor-pointer d-flex align-items-center gap-2 ps-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="line-user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem;">
                            {{ mb_substr($user->name ?? 'U', 0, 1, 'utf-8') }}
                        </div>
                        <div class="d-none d-xl-flex flex-column text-start">
                            <span class="fs-7 fw-bold" style="color: #1B325B;">{{ $user->name }}</span>
                            <span class="fs-9 text-muted">{{ $user->roles->first()?->name ?? 'Super Admin' }}</span>
                        </div>
                        <i class="fas fa-chevron-down fs-9 text-gray-500 d-none d-xl-inline"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2 rounded-3 border-0 w-225px" style="border: 1px solid #cbdfe6 !important;">
                        <li class="px-3 py-2 border-bottom" style="border-color: #cbdfe6 !important;">
                            <div class="fw-bold fs-7" style="color: #1B325B;">{{ $user->name }}</div>
                            <div class="fs-8 text-muted text-truncate">{{ $user->email }}</div>
                        </li>
                        @can('settings.edit')
                            <li>
                                <a href="{{ route('settings.edit', 1) }}" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7" wire:navigate>
                                    <i class="fas fa-cog fs-8 text-gray-500"></i>
                                    <span>@lang('lang.settings')</span>
                                </a>
                            </li>
                        @endcan
                        <li><hr class="dropdown-divider my-1" style="border-color: #cbdfe6;"></li>
                        <li>
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();"
                               class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 fs-7 text-danger">
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
            <!--end::User Account Dropdown-->

        </div>
        <!-- ==================== END RIGHT SECTION ==================== -->

    </div>
    <!--end::Header container-->
</div>
