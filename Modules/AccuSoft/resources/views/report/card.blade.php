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
    }

    .report-card:hover {
        transform: translateY(-5px);
        color: #2c5282;
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

    @can('accusoft.reports.accountstatement')
        <a href="{{ route('accusoft.reports.accountstatement') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.account_statement')
            </div>
        </a>
    @endcan




    @can('accusoft.reports.incomeStatement')
        <a href="{{ route('accusoft.reports.incomeStatement') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.income_statement')
            </div>
        </a>
    @endcan





    @can('accusoft.reports.trialBalance')
        <a href="{{ route('accusoft.reports.trialBalance') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.trial_balance')
            </div>
        </a>
    @endcan


    @can('accusoft.reports.assets')
        <a href="{{ route('accusoft.reports.assets') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.reports.assets')
            </div>
        </a>
    @endcan
    {{-- <a href="{{ route('accusoft.reports.generalLedger') }}" class="report-card">
        <div class="report-icon bg-primary">
            <i class="fas fa-book"></i>
        </div>
        <div class="report-title">
            @lang('accusoft::models/as_reports.types.general_ledger')
        </div>
    </a> --}}


    @can('accusoft.reports.balanceSheet')
        <a href="{{ route('accusoft.reports.balanceSheet') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-scale-balanced"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.balance_sheet')
            </div>
        </a>
    @endcan



    @can('accusoft.reports.cashFlow')
        <a href="{{ route('accusoft.reports.cashFlow') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.cash_flow_statement_direct')
            </div>
        </a>
    @endcan

    @can('accusoft.reports.journalEntries')
        <a href="{{ route('accusoft.reports.journalEntries') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-list-check"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.journal_entries')
            </div>
        </a>
    @endcan

    @can('accusoft.reports.costcenter')
        <a href="{{ route('accusoft.reports.costcenter') }}" class="report-card">
            <div class="report-icon bg-primary">
                <i class="fas fa-sitemap"></i>
            </div>
            <div class="report-title">
                @lang('accusoft::models/as_reports.types.cost_center')
            </div>
        </a>
    @endcan

</div>
