@canany([
    'fnc.banks.index',
    'fnc.safes.index',
    'fnc.bonds.index'
])
<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-bullet">
            <i class="ki-duotone ki-bank fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </span>
        <span class="menu-title">@lang('finance::lang.finance')</span>
        <span class="menu-arrow"></span>
    </span>




    <div class="menu-sub menu-sub-accordion">
        <!----------------------------------------------Start Basic Data------------------------------------------------------------------------------->



        @can('fnc.banks.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('fnc.banks*') ? 'active' : '' }}" href="{{ route('fnc.banks.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-university"></i>
                    </span>
                    <span class="menu-title">@lang('finance::models/fnc_bank.plural')</span>
                </a>
            </div>
        @endcan

         @can('fnc.safes.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('fnc.safes*') ? 'active' : '' }}" href="{{ route('fnc.safes.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-vault"></i>
                    </span>
                    <span class="menu-title">@lang('finance::models/fnc_safe.plural')</span>
                </a>
            </div>
        @endcan

          @can('fnc.bonds.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('fnc.bonds*') ? 'active' : '' }}" href="{{ route('fnc.bonds.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    </span>
                    <span class="menu-title">@lang('finance::models/fnc_bond.plural')</span>
                </a>
            </div>
        @endcan


    </div>
</div>
@endcanany
