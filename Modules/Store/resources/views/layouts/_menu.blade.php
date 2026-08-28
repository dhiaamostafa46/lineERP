@canany(['store.stores.index', 'store.openingbalance.index', 'store.damaged.index', 'store.transfer_out.index',
    'store.settlement.index', 'store.reports.index', 'store.setting.index', 'store.pending.index',
    'store.transfer_in.index', 'store.inventory_orders.index', 'store.receiving.index', 'store.issuing.index',
    'store.direct_transfer.index', 'store.reservation.index'])
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
        <span class="menu-link">
            <span class="menu-bullet">
                <i class="ki-duotone ki-shop fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </span>
            <span class="menu-title">@lang('store::lang.store')</span>
            <span class="menu-arrow"></span>
        </span>




        <div class="menu-sub menu-sub-accordion">
            <!----------------------------------------------Start Basic Data------------------------------------------------------------------------------->



            @can('store.stores.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.stores*') ? 'active' : '' }}"
                        href="{{ route('store.stores.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-warehouse"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_stores.plural')</span>
                    </a>
                </div>
            @endcan


            @can('store.openingbalance.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.openingbalance*') ? 'active' : '' }}"
                        href="{{ route('store.openingbalance.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-coins"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_opening_balances.plural')</span>
                    </a>
                </div>
            @endcan

            @can('store.damaged.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.damaged*') ? 'active' : '' }}"
                        href="{{ route('store.damaged.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-trash-alt"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_damageds.plural')</span>
                    </a>
                </div>
            @endcan

            @can('store.reservation.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.reservation*') ? 'active' : '' }}"
                        href="{{ route('store.reservation.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-bookmark"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_reservations.plural')</span>
                    </a>
                </div>
            @endcan

            @can('store.receiving.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.receiving*') ? 'active' : '' }}"
                        href="{{ route('store.receiving.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-file-import"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_receivings.plural')</span>
                    </a>
                </div>
            @endcan

            @can('store.issuing.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.issuing*') ? 'active' : '' }}"
                        href="{{ route('store.issuing.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-file-export"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_issuings.plural')</span>
                    </a>
                </div>
            @endcan

            @can('store.direct_transfer.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.direct_transfer*') ? 'active' : '' }}"
                        href="{{ route('store.direct_transfer.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_direct_transfers.plural')</span>
                    </a>
                </div>
            @endcan





            {{-- @can('store.transfer_out.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('store.transfer_out*') ? 'active' : '' }}"
                    href="{{ route('store.transfer_out.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-arrow-circle-right"></i>
                    </span>
                    <span class="menu-title"> @lang('store::models/st_transfer_outs.plural') </span>
                </a>
            </div>
        @endcan


        @can('store.transfer_in.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('store.transfer_in*') ? 'active' : '' }}"
                    href="{{ route('store.transfer_in.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-arrow-circle-left"></i>
                    </span>
                    <span class="menu-title">@lang('store::models/st_transfer_ins.plural')</span>
                </a>
            </div>
        @endcan --}}






            @can('store.settlement.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.settlement*') ? 'active' : '' }}"
                        href="{{ route('store.settlement.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-balance-scale"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_adjustment.list_types.settlement')</span>
                    </a>
                </div>
            @endcan


            @can('store.reports.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.reports*') ? 'active' : '' }}"
                        href="{{ route('store.reports.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-chart-line"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_reports.plural')</span>
                    </a>
                </div>
            @endcan


            @can('store.setting.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('store.setting*') ? 'active' : '' }}"
                        href="{{ route('store.setting.edit', 1) }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-cogs"></i>
                        </span>
                        <span class="menu-title">@lang('store::models/st_setting.plural')</span>
                    </a>
                </div>
            @endcan




        </div>
    </div>
@endcanany
