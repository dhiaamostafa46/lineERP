@canany([
    'basicdata.categories.index',
    'basicdata.units.index',
    'basicdata.products.index',
    'basicdata.kitchens.index',
    'basicdata.service_points.index'
])

    @php
        $isBasicDataActive = Route::is('basicdata.*');
    @endphp

    <div x-data="{ open: {{ $isBasicDataActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isBasicDataActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="line-menu-title">@lang('basicdata::lang.basicdata')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('basicdata.categories.index')
                <a class="line-sub-item {{ Route::is('basicdata.categories*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.categories.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('basicdata::models/db_categories.plural')</span>
                </a>
            @endcan

            @can('basicdata.units.index')
                <a class="line-sub-item {{ Route::is('basicdata.units*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.units.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('basicdata::models/db_units.plural')</span>
                </a>
            @endcan

            @can('basicdata.products.index')
                <a class="line-sub-item {{ Route::is('basicdata.products*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.products.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('basicdata::models/db_products.plural')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
