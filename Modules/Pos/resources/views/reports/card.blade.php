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

    {{-- POS Sales --}}
    @can('pos.reports.sales')
        <a href="{{ route('pos.reports.sales') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.pos_sales')</div>
        </a>
    @endcan



    {{-- Category Sales --}}
    @can('pos.reports.category_sales')
        <a href="{{ route('pos.reports.category_sales') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-sitemap"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.category_sales')</div>
        </a>
    @endcan





    {{-- Product Sales --}}
    {{-- @can('pos.reports.product_sales')
        <a href="{{ route('pos.reports.product_sales') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.product_sales')</div>
        </a>
    @endcan --}}

    {{-- Session Sales --}}
    @can('pos.reports.sessions')
        <a href="{{ route('pos.reports.sessions') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.session_sales')</div>
        </a>
    @endcan

    {{-- Sessions Detailed --}}
    {{-- @can('pos.reports.sessions_detailed')
        <a href="{{ route('pos.reports.sessions_detailed') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.sessions_detailed')</div>
        </a>
    @endcan --}}

    
    {{-- Profit Sessions --}}
    @can('pos.reports.profit_sessions')
        <a href="{{ route('pos.reports.profit_sessions') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.profit_sessions')</div>
        </a>
    @endcan

    {{-- Profit Categories --}}
    @can('pos.reports.profit_categories')
        <a href="{{ route('pos.reports.profit_categories') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.profit_categories')</div>
        </a>
    @endcan



    {{-- Profit Products --}}
    @can('pos.reports.profit_products')
        <a href="{{ route('pos.reports.profit_products') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.profit_products')</div>
        </a>
    @endcan

    {{-- Payment Methods --}}
    @can('pos.reports.payment_methods')
        <a href="{{ route('pos.reports.payment_methods') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.payment_methods')</div>
        </a>
    @endcan

    {{-- Cash Movements --}}
    @can('pos.reports.cash_movements')
        <a href="{{ route('pos.reports.cash_movements') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.cash_movements')</div>
        </a>
    @endcan

    {{-- Returns --}}
    @can('pos.reports.returns')
        <a href="{{ route('pos.reports.returns') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-undo-alt"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.returns')</div>
        </a>
    @endcan

    {{-- Taxes --}}
    @can('pos.reports.taxes')
        <a href="{{ route('pos.reports.taxes') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="report-title">@lang('pos::reports.types.taxes')</div>
        </a>
    @endcan

</div>
