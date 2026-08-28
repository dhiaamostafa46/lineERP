@canany(['store.stores.index', 'store.openingbalance.index', 'store.damaged.index', 'store.transfer_out.index',
    'store.settlement.index', 'store.reports.index', 'store.setting.index', 'store.pending.index',
    'store.transfer_in.index', 'store.inventory_orders.index', 'store.receiving.index', 'store.issuing.index',
    'store.direct_transfer.index', 'store.reservation.index'])

    @php
        $isStoreActive = Route::is('store.*');
    @endphp

    <div x-data="{ open: {{ $isStoreActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isStoreActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge">
                    <i class="fas fa-boxes"></i>
                </div>
                <span class="line-menu-title">@lang('store::lang.store')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('store.stores.index')
                <a class="line-sub-item {{ Route::is('store.stores*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.stores.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_stores.plural')</span>
                </a>
            @endcan

            @can('store.openingbalance.index')
                <a class="line-sub-item {{ Route::is('store.openingbalance*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.openingbalance.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_opening_balances.plural')</span>
                </a>
            @endcan

            @can('store.direct_transfer.index')
                <a class="line-sub-item {{ Route::is('store.direct_transfer*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.direct_transfer.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_direct_transfers.plural')</span>
                </a>
            @endcan

            @can('store.receiving.index')
                <a class="line-sub-item {{ Route::is('store.receiving*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.receiving.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_receivings.plural')</span>
                </a>
            @endcan

            @can('store.issuing.index')
                <a class="line-sub-item {{ Route::is('store.issuing*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.issuing.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_issuings.plural')</span>
                </a>
            @endcan

            @can('store.damaged.index')
                <a class="line-sub-item {{ Route::is('store.damaged*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.damaged.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_damageds.plural')</span>
                </a>
            @endcan

            @can('store.reservation.index')
                <a class="line-sub-item {{ Route::is('store.reservation*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.reservation.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_reservations.plural')</span>
                </a>
            @endcan

            @can('store.settlement.index')
                <a class="line-sub-item {{ Route::is('store.settlement*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.settlement.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_adjustment.list_types.settlement')</span>
                </a>
            @endcan

            @can('store.reports.index')
                <a class="line-sub-item {{ Route::is('store.reports*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.reports.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_reports.plural')</span>
                </a>
            @endcan

            @can('store.setting.index')
                <a class="line-sub-item {{ Route::is('store.setting*') ? 'active-sub' : '' }}" 
                   href="{{ route('store.setting.edit', 1) }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('store::models/st_setting.plural')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
