@canany(['pos.index', 'pos.devices.index', 'pos.reports.index'])
<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is('pos.*') ? 'here show' : '' }}">
    <span class="menu-link">
        <span class="menu-bullet">
            <i class="ki-duotone ki-shop fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
        </span>
        <span class="menu-title">@lang('pos::lang.pos')</span>
        <span class="menu-arrow"></span>
    </span>

    <div class="menu-sub menu-sub-accordion">
        
        @can('pos.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('pos.select_device') || Route::is('pos.terminal') ? 'active' : '' }}" href="{{ route('pos.select_device') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-cash-register"></i>
                    </span>
                    <span class="menu-title">@lang('pos::lang.pos_screen')</span>
                </a>
            </div>
        @endcan
        
        @can('pos.devices.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('pos.devices.*') ? 'active' : '' }}" href="{{ route('pos.devices.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-tablet-alt"></i>
                    </span>
                    <span class="menu-title">@lang('pos::models/devices.plural')</span>
                </a>
            </div>
        @endcan
        
        @can('pos.reports.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('pos.reports.*') ? 'active' : '' }}" href="{{ route('pos.reports.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-chart-line"></i>
                    </span>
                    <span class="menu-title">@lang('pos::lang.reports')</span>
                </a>
            </div>
        @endcan
        
    </div>
</div>
@endcanany
