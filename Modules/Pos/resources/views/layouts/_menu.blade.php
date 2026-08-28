@canany(['pos.index', 'pos.devices.index', 'pos.reports.index'])

    @php
        $isPosActive = Route::is('pos.*');
    @endphp

    <div x-data="{ open: {{ $isPosActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isPosActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge">
                    <i class="fas fa-cash-register"></i>
                </div>
                <span class="line-menu-title">@lang('pos::lang.pos')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('pos.index')
                <a class="line-sub-item {{ Route::is('pos.select_device') || Route::is('pos.terminal') ? 'active-sub' : '' }}" 
                   href="{{ route('pos.select_device') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('pos::lang.pos_screen')</span>
                </a>
            @endcan
            
            @can('pos.devices.index')
                <a class="line-sub-item {{ Route::is('pos.devices.*') ? 'active-sub' : '' }}" 
                   href="{{ route('pos.devices.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('pos::models/devices.plural')</span>
                </a>
            @endcan
            
            @can('pos.reports.index')
                <a class="line-sub-item {{ Route::is('pos.reports.*') ? 'active-sub' : '' }}" 
                   href="{{ route('pos.reports.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('pos::lang.reports')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
