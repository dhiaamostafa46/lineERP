@canany(['invoices.purchase.index', 'invoices.purchase_orders.index', 'invoices.purchase_return.index',
    'invoices.sales.index', 'invoices.sales_return.index', 'invoices.sales_debit.index',
    'invoices.quotations.index', 'invoices.customers.index', 'invoices.suppliers.index',
    'invoices.Setting.index'])
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is('invoices.*') ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-bullet">
                <i class="ki-duotone ki-bill fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </span>
            <span class="menu-title">@lang('invoices::lang.invoices')</span>
            <span class="menu-arrow"></span>
        </span>

        <div class="menu-sub menu-sub-accordion">
            <!----------------------------------------------Start Basic Data------------------------------------------------------------------------------->

            @canany(['invoices.sales.index', 'invoices.sales_return.index', 'invoices.sales_debit.index', 'invoices.quotations.index'])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is(['invoices.sales.*', 'invoices.sales_return.*', 'invoices.sales_debit.*', 'invoices.quotations.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-landmark-flag"></i>
                        </span>
                        <span class="menu-title">@lang('invoices::lang.sales')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                          @can('invoices.quotations.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.quotations.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.quotations.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-file-contract"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/quotations.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('invoices.sales.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.sales.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.sales.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/sales_invoices.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('invoices.sales_return.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.sales_return.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.sales_return.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/sales_return_invoices.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('invoices.sales_debit.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.sales_debit.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.sales_debit.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/sales_debit_notes.plural')</span>
                                </a>
                            </div>
                        @endcan

                    </div>
                </div>
            @endcanany

            @canany(['invoices.purchase.index', 'invoices.purchase_orders.index'])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is(['invoices.purchase.*', 'invoices.purchase_orders.*', 'invoices.purchase_return.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-landmark-flag"></i>
                        </span>
                        <span class="menu-title">@lang('invoices::lang.purchase')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('invoices.purchase_orders.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.purchase_orders.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.purchase_orders.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/purchase_orders.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('invoices.purchase.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.purchase.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.purchase.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/purchase_invoices.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('invoices.purchase_return.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.purchase_return.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.purchase_return.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/purchase_return_invoices.plural')</span>
                                </a>
                            </div>
                        @endcan

                    </div>
                </div>
            @endcanany

            @canany(['invoices.customers.index', 'invoices.suppliers.index'])
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is(['invoices.customers.*', 'invoices.suppliers.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-landmark-flag"></i>
                        </span>
                        <span class="menu-title">@lang('invoices::lang.customersandsuppliers')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('invoices.customers.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.customers.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.customers.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/inv_customers.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('invoices.suppliers.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('invoices.suppliers.*') ? 'active' : '' }}"
                                    href="{{ route('invoices.suppliers.index') }}" wire:navigate>
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('invoices::models/inv_suppliers.plural')</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endcanany

            @can('invoices.reports.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('invoices.reports.*') ? 'active' : '' }}"
                        href="{{ route('invoices.reports.index') }}" wire:navigate>
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-chart-line"></i>
                        </span>
                        <span class="menu-title">@lang('invoices::models/inv_reports.plural')</span>
                    </a>
                </div>
            @endcan

            @can('invoices.Setting.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('invoices.Setting*') ? 'active' : '' }}"
                        href="{{ route('invoices.Setting.index') }}" wire:navigate>
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-diamond"></i>
                        </span>
                        <span class="menu-title">@lang('invoices::models/invoices_setting.plural')</span>
                    </a>
                </div>
            @endcan

        </div>
    </div>
@endcanany
