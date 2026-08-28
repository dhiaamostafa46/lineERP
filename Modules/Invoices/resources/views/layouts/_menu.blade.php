@canany(['invoices.purchase.index', 'invoices.purchase_orders.index', 'invoices.purchase_return.index',
    'invoices.sales.index', 'invoices.sales_return.index', 'invoices.sales_debit.index',
    'invoices.quotations.index', 'invoices.customers.index', 'invoices.suppliers.index',
    'invoices.Setting.index'])

    @php
        $isInvoicesActive = Route::is('invoices.*');
    @endphp

    <div class="line-section-header">
        <span>@lang('invoices::lang.invoices')</span>
    </div>

    <div x-data="{ open: {{ $isInvoicesActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isInvoicesActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge icon-invoices">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span class="line-menu-title">@lang('invoices::lang.invoices')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            
            {{-- Quotations --}}
            @can('invoices.quotations.index')
                <a class="line-sub-item {{ Route::is('invoices.quotations.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.quotations.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/quotations.plural')</span>
                </a>
            @endcan

            {{-- Sales Invoices --}}
            @can('invoices.sales.index')
                <a class="line-sub-item {{ Route::is('invoices.sales.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.sales.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/sales_invoices.plural')</span>
                </a>
            @endcan

            {{-- Sales Return --}}
            @can('invoices.sales_return.index')
                <a class="line-sub-item {{ Route::is('invoices.sales_return.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.sales_return.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/sales_return_invoices.plural')</span>
                </a>
            @endcan

            {{-- Sales Debit Notes --}}
            @can('invoices.sales_debit.index')
                <a class="line-sub-item {{ Route::is('invoices.sales_debit.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.sales_debit.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/sales_debit_notes.plural')</span>
                </a>
            @endcan

            {{-- Purchase Orders --}}
            @can('invoices.purchase_orders.index')
                <a class="line-sub-item {{ Route::is('invoices.purchase_orders.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.purchase_orders.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/purchase_orders.plural')</span>
                </a>
            @endcan

            {{-- Purchase Invoices --}}
            @can('invoices.purchase.index')
                <a class="line-sub-item {{ Route::is('invoices.purchase.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.purchase.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/purchase_invoices.plural')</span>
                </a>
            @endcan

            {{-- Purchase Returns --}}
            @can('invoices.purchase_return.index')
                <a class="line-sub-item {{ Route::is('invoices.purchase_return.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.purchase_return.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/purchase_return_invoices.plural')</span>
                </a>
            @endcan

            {{-- Customers --}}
            @can('invoices.customers.index')
                <a class="line-sub-item {{ Route::is('invoices.customers.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.customers.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/inv_customers.plural')</span>
                </a>
            @endcan

            {{-- Suppliers --}}
            @can('invoices.suppliers.index')
                <a class="line-sub-item {{ Route::is('invoices.suppliers.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.suppliers.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/inv_suppliers.plural')</span>
                </a>
            @endcan

            {{-- Reports --}}
            @can('invoices.reports.index')
                <a class="line-sub-item {{ Route::is('invoices.reports.*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.reports.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/inv_reports.plural')</span>
                </a>
            @endcan

            {{-- Settings & ZATCA --}}
            @can('invoices.Setting.index')
                <a class="line-sub-item {{ Route::is('invoices.Setting*') ? 'active-sub' : '' }}"
                    href="{{ route('invoices.Setting.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('invoices::models/invoices_setting.plural')</span>
                </a>
            @endcan

        </div>
    </div>
@endcanany
