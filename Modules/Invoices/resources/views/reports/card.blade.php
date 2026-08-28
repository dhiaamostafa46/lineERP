<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 2rem;
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .report-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #1e3a5f;
        transition: transform 0.2s ease, color 0.2s ease;
        border-radius: 8px;
        padding: 1rem;
    }

    .report-card:hover {
        transform: translateY(-5px);
        color: #2c5282;
        background-color: rgba(44, 82, 130, 0.05);
    }

    .report-icon {
        width: 150px;
        height: 150px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .report-card:hover .report-icon {
        filter: brightness(90%);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .report-icon i {
        font-size: 2.5rem;
        color: white;
    }

    .report-title {
        text-align: center;
        font-weight: bold;
        line-height: 1.3;
        max-width: 140px;
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        .report-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .report-icon {
            width: 75px;
            height: 75px;
        }

        .report-icon i {
            font-size: 2rem;
        }

        .report-title {
            font-size: 0.8rem;
        }
    }
</style>

<div class="report-grid">

    {{-- Sales Invoices --}}
    @can('invoices.reports.sales')
        <a href="{{ route('invoices.reports.sales') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.sales_invoices')</div>
        </a>
    @endcan



    {{-- Purchase Invoices --}}
    @can('invoices.reports.purchases')
        <a href="{{ route('invoices.reports.purchases') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.purchase_invoices')</div>
        </a>
    @endcan





    {{-- Customer Aging --}}
    @can('invoices.reports.customer_aging')
        <a href="{{ route('invoices.reports.customer_aging') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.customer_aging')</div>
        </a>
    @endcan

    {{-- Supplier Aging --}}
    @can('invoices.reports.supplier_aging')
        <a href="{{ route('invoices.reports.supplier_aging') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-truck-loading"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.supplier_aging')</div>
        </a>
    @endcan

    {{-- Product Profit --}}
    @can('invoices.reports.profit')
        <a href="{{ route('invoices.reports.profit') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.product_profit')</div>
        </a>
    @endcan

    {{-- Daily Summary --}}
    @can('invoices.reports.daily')
        <a href="{{ route('invoices.reports.daily') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.daily_summary')</div>
        </a>
    @endcan

    {{-- Tax Report --}}
    @can('invoices.reports.tax')
        <a href="{{ route('invoices.reports.tax') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-percent"></i>
            </div>
            <div class="report-title">@lang('invoices::models/inv_reports.types.tax_report')</div>
        </a>
    @endcan



</div>
