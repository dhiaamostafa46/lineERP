@canany([
    'basicdata.categories.index',
    'basicdata.units.index',
    'basicdata.products.index',
    'basicdata.kitchens.index',
    'basicdata.service_points.index'
])
<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-bullet">
            <i class="ki-duotone ki-data fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </span>
        <span class="menu-title">@lang('basicdata::lang.basicdata')</span>
        <span class="menu-arrow"></span>
    </span>

    <div class="menu-sub menu-sub-accordion">
        <!----------------------------------------------Start Basic Data------------------------------------------------------------------------------->

        @can('basicdata.categories.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('basicdata.categories*') ? 'active' : '' }}" href="{{ route('basicdata.categories.index') }}" wire:navigate>
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-tags"></i>
                    </span>
                    <span class="menu-title">@lang('basicdata::models/db_categories.plural')</span>
                </a>
            </div>
        @endcan
         @can('basicdata.units.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('basicdata.units*') ? 'active' : '' }}" href="{{ route('basicdata.units.index') }}" wire:navigate>
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-balance-scale-left"></i>
                    </span>
                    <span class="menu-title">@lang('basicdata::models/db_units.plural')</span>
                </a>
            </div>
        @endcan
         @can('basicdata.products.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('basicdata.products*') ? 'active' : '' }}" href="{{ route('basicdata.products.index') }}" wire:navigate>
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-box-open"></i>
                    </span>
                    <span class="menu-title">@lang('basicdata::models/db_products.plural')</span>
                </a>
            </div>
        @endcan

    </div>
</div>
@endcanany
