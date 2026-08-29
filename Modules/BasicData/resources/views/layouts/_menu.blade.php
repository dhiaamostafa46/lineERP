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
                <div class="line-icon-badge icon-basicdata">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="line-menu-title">@lang('basicdata::lang.basicdata')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('basicdata.products.index')
                <!-- 1. Products (المنتجات) -->
                <a class="line-sub-item {{ Route::is('basicdata.products*') && request('type') != 2 ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.products.index', ['type' => 1]) }}" wire:navigate>
                    <i class="fa-solid fa-box-open fs-8 me-2 text-primary"></i>
                    <span>@lang('basicdata::models/db_products.products')</span>
                </a>

                <!-- 2. Services (الخدمات) -->
                <a class="line-sub-item {{ Route::is('basicdata.products*') && request('type') == 2 ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.products.index', ['type' => 2]) }}" wire:navigate>
                    <i class="fa-solid fa-bell-concierge fs-8 me-2 text-success"></i>
                    <span>@lang('basicdata::models/db_products.services')</span>
                </a>
            @endcan

            @can('basicdata.categories.index')
                <a class="line-sub-item {{ Route::is('basicdata.categories*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.categories.index') }}" wire:navigate>
                    <i class="fa-solid fa-folder-tree fs-8 me-2 text-warning"></i>
                    <span>@lang('basicdata::models/db_categories.plural')</span>
                </a>
            @endcan

            @can('basicdata.units.index')
                <a class="line-sub-item {{ Route::is('basicdata.units*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.units.index') }}" wire:navigate>
                    <i class="fa-solid fa-scale-balanced fs-8 me-2 text-info"></i>
                    <span>@lang('basicdata::models/db_units.plural')</span>
                </a>
            @endcan

            @can('basicdata.kitchens.index')
                <a class="line-sub-item {{ Route::is('basicdata.kitchens*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.kitchens.index') }}" wire:navigate>
                    <i class="fa-solid fa-utensils fs-8 me-2 text-danger"></i>
                    <span>@lang('basicdata::models/db_kitchens.plural')</span>
                </a>
            @endcan

            @can('basicdata.service_points.index')
                <a class="line-sub-item {{ Route::is('basicdata.service_points*') ? 'active-sub' : '' }}" 
                   href="{{ route('basicdata.service_points.index') }}" wire:navigate>
                    <i class="fa-solid fa-location-dot fs-8 me-2 text-primary"></i>
                    <span>@lang('basicdata::models/db_service_points.plural')</span>
                </a>
            @endcan
        </div>
    </div>

@endcanany
