@canany([
    'fnc.banks.index',
    'fnc.safes.index',
    'fnc.bonds.index'
])

    @php
        $isFinanceActive = Route::is('fnc.*');
    @endphp

    <div x-data="{ open: {{ $isFinanceActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isFinanceActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge">
                    <i class="fas fa-university"></i>
                </div>
                <span class="line-menu-title">@lang('finance::lang.finance')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('fnc.safes.index')
                <a class="line-sub-item {{ Route::is('fnc.safes*') ? 'active-sub' : '' }}" 
                   href="{{ route('fnc.safes.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('finance::models/fnc_safe.plural')</span>
                </a>
            @endcan

            @can('fnc.banks.index')
                <a class="line-sub-item {{ Route::is('fnc.banks*') ? 'active-sub' : '' }}" 
                   href="{{ route('fnc.banks.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('finance::models/fnc_bank.plural')</span>
                </a>
            @endcan

            @can('fnc.bonds.index')
                <a class="line-sub-item {{ Route::is('fnc.bonds*') ? 'active-sub' : '' }}" 
                   href="{{ route('fnc.bonds.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('finance::models/fnc_bond.plural')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
