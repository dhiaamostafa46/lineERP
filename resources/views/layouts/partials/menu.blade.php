{{-- 1. Main Navigation --}}
<div class="line-section-header">
    <span>@lang('lang.dashboard')</span>
</div>

@can('dashboard')
    <div class="line-menu-item mb-1">
        <a class="line-menu-link {{ Route::is('dashboard') ? 'active-root' : '' }}" href="{{ route('dashboard') }}" wire:navigate>
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge icon-dashboard">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <span class="line-menu-title">@lang('lang.dashboard')</span>
            </div>
        </a>
    </div>
@endcan

{{-- System Core Modules Included Dynamically --}}
@if (auth()->user()?->user_type !== 'service_center')

    {{-- 2. Commercial & Supply Chain Modules --}}
    @if (View::exists('invoices::layouts._menu') || View::exists('store::layouts._menu') || View::exists('pos::layouts._menu'))
        <div class="line-section-header">
            <span>المبيعات والمخزون</span>
        </div>

        @if (View::exists('invoices::layouts._menu'))
            @include('invoices::layouts._menu')
        @endif

        @if (View::exists('store::layouts._menu'))
            @include('store::layouts._menu')
        @endif

        @if (View::exists('pos::layouts._menu'))
            @include('pos::layouts._menu')
        @endif
    @endif

    {{-- 3. Accounting & Financial Modules --}}
    @if (View::exists('accusoft::layouts._menu') || View::exists('finance::layouts._menu'))
        <div class="line-section-header">
            <span>الحسابات والمالية</span>
        </div>

        @if (View::exists('accusoft::layouts._menu'))
            @include('accusoft::layouts._menu')
        @endif

        @if (View::exists('finance::layouts._menu'))
            @include('finance::layouts._menu')
        @endif
    @endif

    {{-- 4. Administration, HR & System Settings --}}
    <div class="line-section-header">
        <span>الإدارة والنظام</span>
    </div>

    @if (View::exists('hr::layouts._menu'))
        @include('hr::layouts._menu')
    @endif

    @if (View::exists('basicdata::layouts._menu'))
        @include('basicdata::layouts._menu')
    @endif

    {{-- General System Settings & Administration --}}
    @php
        $isSettingsActive = Route::is([
            'Organization*', 'Templates*', 'Branches*', 'Areas*', 'Cities*', 
            'Companies*', 'CompanyContracts*', 'users*', 'roles*', 
            'DeviceSessions*', 'taxaccounts*', 'settings*'
        ]);
    @endphp

    <div x-data="{ open: {{ $isSettingsActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isSettingsActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge icon-settings">
                    <i class="fas fa-cog"></i>
                </div>
                <span class="line-menu-title">@lang('lang.system_settings')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('Organization.edit')
                <a class="line-sub-item {{ Route::is('Organization*') ? 'active-sub' : '' }}" href="{{ route('Organization.edit', 1) }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/Organization.plural')</span>
                </a>
            @endcan

            @can('Templates.index')
                <a class="line-sub-item {{ Route::is('Templates*') ? 'active-sub' : '' }}" href="{{ route('Templates.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/Templates.plural')</span>
                </a>
            @endcan

            @can('Branches.index')
                <a class="line-sub-item {{ Route::is('Branches*') ? 'active-sub' : '' }}" href="{{ route('Branches.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/Branches.plural')</span>
                </a>
            @endcan

            @if (\Illuminate\Support\Facades\Route::has('Areas.index'))
                @can('Areas.index')
                    <a class="line-sub-item {{ Route::is('Areas*') ? 'active-sub' : '' }}" href="{{ route('Areas.index') }}" wire:navigate>
                        <span class="line-sub-dot"></span>
                        <span>@lang('models/Areas.plural')</span>
                    </a>
                @endcan
            @endif

            @if (\Illuminate\Support\Facades\Route::has('Cities.index'))
                @can('Cities.index')
                    <a class="line-sub-item {{ Route::is('Cities*') ? 'active-sub' : '' }}" href="{{ route('Cities.index') }}" wire:navigate>
                        <span class="line-sub-dot"></span>
                        <span>@lang('models/Cities.plural')</span>
                    </a>
                @endcan
            @endif

            @if (\Illuminate\Support\Facades\Route::has('Companies.index'))
                @can('Companies.index')
                    <a class="line-sub-item {{ Route::is('Companies*') ? 'active-sub' : '' }}" href="{{ route('Companies.index') }}" wire:navigate>
                        <span class="line-sub-dot"></span>
                        <span>@lang('models/Companies.plural')</span>
                    </a>
                @endcan
            @endif

            @if (\Illuminate\Support\Facades\Route::has('CompanyContracts.index'))
                @can('CompanyContracts.index')
                    <a class="line-sub-item {{ Route::is('CompanyContracts*') ? 'active-sub' : '' }}" href="{{ route('CompanyContracts.index') }}" wire:navigate>
                        <span class="line-sub-dot"></span>
                        <span>@lang('models/CompanyContracts.plural')</span>
                    </a>
                @endcan
            @endif

            @can('users.index')
                <a class="line-sub-item {{ Route::is('users*') ? 'active-sub' : '' }}" href="{{ route('users.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/users.plural')</span>
                </a>
            @endcan

            @can('roles.index')
                <a class="line-sub-item {{ Route::is('roles*') ? 'active-sub' : '' }}" href="{{ route('roles.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/roles.plural')</span>
                </a>
            @endcan

            @can('DeviceSessions.index')
                <a class="line-sub-item {{ Route::is('DeviceSessions*') ? 'active-sub' : '' }}" href="{{ route('DeviceSessions.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/DeviceSessions.plural')</span>
                </a>
            @endcan

            @can('taxaccounts.index')
                <a class="line-sub-item {{ Route::is('taxaccounts*') ? 'active-sub' : '' }}" href="{{ route('taxaccounts.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('models/tax_accounts.plural')</span>
                </a>
            @endcan
        </div>
    </div>

@endif
