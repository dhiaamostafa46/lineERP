

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

    {{-- Stock Movement Report - تقرير حركة المخزون --}}
    @can('store.reports.stockMovement')
        <a href="{{ route('store.reports.stockMovement') }}" class="report-card" title="@lang('store::models/st_reports.types.stock_movement')">
            <div class="report-icon bg-primary">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.stock_movement')
            </div>
        </a>
    @endcan

    {{-- Stock Balance Report - تقرير رصيد المخزون --}}
    @can('store.reports.stockBalance')
        <a href="{{ route('store.reports.stockBalance') }}" class="report-card" title="@lang('store::models/st_reports.types.stock_balance')">
            <div class="report-icon bg-primary">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.stock_balance')
            </div>
        </a>
    @endcan

    {{-- Inventory Valuation Report - تقرير تقييم المخزون --}}
    @can('store.reports.inventoryValuation')
        <a href="{{ route('store.reports.inventoryValuation') }}" class="report-card" title="@lang('store::models/st_reports.types.inventory_valuation')">
            <div class="report-icon bg-primary">
                <i class="fas fa-coins"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.inventory_valuation')
            </div>
        </a>
    @endcan

    {{-- Low Stock Report - تقرير المخزون المنخفض --}}
    @can('store.reports.lowStock')
        <a href="{{ route('store.reports.lowStock') }}" class="report-card" title="@lang('store::models/st_reports.types.low_stock')">
            <div class="report-icon bg-primary">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.low_stock')
            </div>
        </a>
    @endcan

    {{-- Inventory Count Report - تقرير جرد المخزون --}}
    @can('store.reports.inventoryCount')
        <a href="{{ route('store.reports.inventoryCount') }}" class="report-card" title="@lang('store::models/st_reports.types.inventory_count')">
            <div class="report-icon bg-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.inventory_count')
            </div>
        </a>
    @endcan

    {{-- Pending Stock Report - تقرير المخزون المعلق --}}
    @can('store.reports.pendingStock')
        <a href="{{ route('store.reports.pendingStock') }}" class="report-card" title="@lang('store::models/st_reports.types.pending_stock')">
            <div class="report-icon bg-primary">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="report-title">
                @lang('store::models/st_reports.types.pending_stock')
            </div>
        </a>
    @endcan



</div>
