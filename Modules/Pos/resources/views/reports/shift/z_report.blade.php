@extends('layouts.app')

@section('title', __('pos::reports.z_report'))

@section('styles')
<style>
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            background-color: #fff !important;
        }
        .btn-print, #kt_app_sidebar, #kt_app_header, #kt_app_footer, #kt_app_toolbar {
            display: none !important;
        }
        .app-content, .app-container {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .card {
            box-shadow: none !important;
        }
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
        }
        .col-md-3, .col-3 {
            flex: 0 0 auto !important;
            width: 25% !important;
        }
        .col-md-4, .col-4 {
            flex: 0 0 auto !important;
            width: 33.333333% !important;
        }
        .col-md-6, .col-6 {
            flex: 0 0 auto !important;
            width: 50% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('pos::reports.z_report')
                    <span class="badge badge-light-primary ms-2 fs-7">@lang('pos::reports.shift_number') {{ $summary['session']->id }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.sessions') }}" class="text-muted text-hover-primary">@lang('pos::reports.types.session_sales')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">Z-Report</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button onclick="window.print()" class="btn btn-sm btn-primary btn-print"><i class="fa-solid fa-print me-1"></i> @lang('pos::reports.print_report')</button>
                <a href="{{ route('pos.reports.sessions') }}" class="btn btn-sm btn-secondary btn-print"><i class="fa-solid fa-arrow-right me-1"></i> @lang('pos::reports.back_to_sessions')</a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!-- 1. KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light-primary border border-primary border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1">@lang('pos::reports.net_sales')</span>
                        <span class="fs-2x fw-bolder text-primary" dir="ltr">{{ number_format($summary['revenue']['net_sales'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-danger border border-danger border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1">@lang('pos::reports.total_returns')</span>
                        <span class="fs-2x fw-bolder text-danger" dir="ltr">-{{ number_format($summary['revenue']['returns_post_tax'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-info border border-info border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1">@lang('pos::reports.expected_cash')</span>
                        <span class="fs-2x fw-bolder text-info" dir="ltr">{{ number_format($summary['drawer']['expected_cash'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    @php
                        $varType = $summary['drawer']['variance_type'];
                        $varBg = $varType == 'shortage' ? 'bg-light-danger border-danger' : ($varType == 'overage' ? 'bg-light-success border-success' : 'bg-light-secondary border-secondary');
                        $varColor = $varType == 'shortage' ? 'text-danger' : ($varType == 'overage' ? 'text-success' : 'text-gray-700');
                    @endphp
                    <div class="card {{ $varBg }} border border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1">@lang('pos::reports.variance')</span>
                        <span class="fs-2x fw-bolder {{ $varColor }}" dir="ltr">
                            {{ number_format($summary['drawer']['variance'], 2) }}
                        </span>
                        <span class="badge badge-light-dark mx-auto mt-1 fs-8">
                            {{ $varType == 'shortage' ? __('pos::reports.shortage') : ($varType == 'overage' ? __('pos::reports.overage') : __('pos::reports.exact_match')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Z-Report Card -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-receipt fs-2 me-2 text-primary"></i> @lang('pos::reports.z_report_details')
                    </h3>
                    <div class="card-toolbar">
                        <span class="text-muted fs-7"><i class="fa-regular fa-clock me-1"></i> @lang('pos::reports.print_date'): {{ date('Y-m-d H:i') }}</span>
                    </div>
                </div>

                <div class="card-body p-6">
                    <!-- Section 1: Shift Info -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-primary fs-6 me-2">1</span>
                        <h4 class="fw-bold text-gray-800 m-0">@lang('pos::reports.shift_info')</h4>
                    </div>
                    <div class="table-responsive mb-5">
                        <table class="table table-bordered align-middle gs-4 gy-3">
                            <tbody>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700 w-200px">@lang('pos::reports.shift_id')</td>
                                    <td><span class="fw-bolder fs-6 text-primary">#{{ $summary['session']->id }}</span></td>
                                    <td class="bg-light fw-bold text-gray-700 w-200px">@lang('pos::reports.shift_status')</td>
                                    <td>
                                        @if($summary['session']->closed_at)
                                            <span class="badge badge-light-secondary fw-bold">@lang('pos::reports.closed_status')</span>
                                        @else
                                            <span class="badge badge-light-success fw-bold">@lang('pos::reports.active_status')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.cashier')</td>
                                    <td class="fw-bold">{{ $summary['session']->cashier->name ?? '---' }}</td>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.pos_device')</td>
                                    <td class="fw-bold text-info"><i class="fa-solid fa-desktop me-1"></i> {{ $summary['session']->device->name ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.opened_at')</td>
                                    <td dir="ltr" class="text-end fw-semibold">{{ $summary['session']->opened_at ? $summary['session']->opened_at->format('Y-m-d H:i:s') : '---' }}</td>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.closed_at')</td>
                                    <td dir="ltr" class="text-end fw-semibold">{{ $summary['session']->closed_at ? $summary['session']->closed_at->format('Y-m-d H:i:s') : '---' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <!-- Section 2: Revenue Summary -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-primary fs-6 me-2">2</span>
                        <h4 class="fw-bold text-gray-800 m-0">@lang('pos::reports.revenue_summary')</h4>
                    </div>
                    <div class="table-responsive mb-5">
                        <table class="table table-striped table-bordered align-middle gs-4 gy-3">
                            <thead class="bg-light-primary text-gray-800 fw-bold">
                                <tr>
                                    <th>@lang('pos::reports.description')</th>
                                    <th class="text-end">@lang('pos::reports.pre_tax')</th>
                                    <th class="text-end">@lang('pos::reports.tax_amount')</th>
                                    <th class="text-end">@lang('pos::reports.post_tax')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">@lang('pos::reports.total_sales')</td>
                                    <td class="text-end">{{ number_format($summary['revenue']['sales_pre_tax'], 2) }}</td>
                                    <td class="text-end">{{ number_format($summary['revenue']['sales_tax'], 2) }}</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($summary['revenue']['sales_post_tax'], 2) }}</td>
                                </tr>
                                <tr class="table-light-danger">
                                    <td class="fw-semibold text-danger">@lang('pos::reports.total_returns')</td>
                                    <td class="text-end text-danger">-{{ number_format($summary['revenue']['returns_pre_tax'], 2) }}</td>
                                    <td class="text-end text-danger">-{{ number_format($summary['revenue']['returns_tax'], 2) }}</td>
                                    <td class="text-end fw-bold text-danger">-{{ number_format($summary['revenue']['returns_post_tax'], 2) }}</td>
                                </tr>
                                <tr class="table-success fw-bolder fs-6">
                                    <td>@lang('pos::reports.final_net_sales')</td>
                                    <td class="text-end">{{ number_format($summary['revenue']['sales_pre_tax'] - $summary['revenue']['returns_pre_tax'], 2) }}</td>
                                    <td class="text-end">{{ number_format($summary['revenue']['sales_tax'] - $summary['revenue']['returns_tax'], 2) }}</td>
                                    <td class="text-end text-success">{{ number_format($summary['revenue']['net_sales'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <!-- Section 3: Collection by Payment Methods -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-primary fs-6 me-2">3</span>
                        <h4 class="fw-bold text-gray-800 m-0">@lang('pos::reports.collections_by_method')</h4>
                    </div>
                    <div class="table-responsive mb-5">
                        <table class="table table-striped table-bordered align-middle gs-4 gy-3">
                            <thead class="bg-light-info text-gray-800 fw-bold">
                                <tr>
                                    <th>@lang('pos::reports.payment_method')</th>
                                    <th>@lang('pos::reports.type')</th>
                                    <th class="text-end">@lang('pos::reports.collected')</th>
                                    <th class="text-end">@lang('pos::reports.returned')</th>
                                    <th class="text-end">@lang('pos::reports.net_collected')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['collections'] as $method)
                                    <tr>
                                        <td class="fw-bold">{{ $method['name'] }}</td>
                                        <td>
                                            @if($method['type'] == 'cash')
                                                <span class="badge badge-light-success"><i class="fa-solid fa-money-bill-wave me-1"></i> @lang('pos::reports.cash_type')</span>
                                            @elseif($method['type'] == 'card')
                                                <span class="badge badge-light-info"><i class="fa-solid fa-credit-card me-1"></i> @lang('pos::reports.card_type')</span>
                                            @else
                                                <span class="badge badge-light-primary">@lang('pos::reports.other_type')</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-success fw-semibold">{{ number_format($method['collected'], 2) }}</td>
                                        <td class="text-end text-danger fw-semibold">{{ number_format($method['returned'], 2) }}</td>
                                        <td class="text-end fw-bolder text-gray-900">{{ number_format($method['net'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <!-- Section 4: Cash Drawer Summary -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-primary fs-6 me-2">4</span>
                        <h4 class="fw-bold text-gray-800 m-0">@lang('pos::reports.cash_drawer_summary')</h4>
                    </div>
                    <div class="table-responsive mb-5">
                        <table class="table table-bordered align-middle gs-4 gy-3">
                            <tbody>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700 w-250px">@lang('pos::reports.opening_balance')</td>
                                    <td colspan="3" class="fw-bolder fs-6 text-primary">{{ number_format($summary['drawer']['opening_balance'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.cash_sales')</td>
                                    <td class="text-success fw-bold">{{ number_format($summary['drawer']['cash_sales'], 2) }}</td>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.cash_returns')</td>
                                    <td class="text-danger fw-bold">{{ number_format($summary['drawer']['cash_returns'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.cash_deposits')</td>
                                    <td class="text-success fw-bold">{{ number_format($summary['drawer']['deposits'], 2) }}</td>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.cash_withdrawals')</td>
                                    <td class="text-danger fw-bold">{{ number_format($summary['drawer']['withdrawals'], 2) }}</td>
                                </tr>
                                <tr class="table-light-info">
                                    <td class="fw-bold">@lang('pos::reports.expected_cash')</td>
                                    <td class="fw-bolder text-info fs-6">{{ number_format($summary['drawer']['expected_cash'], 2) }}</td>
                                    <td class="fw-bold">@lang('pos::reports.actual_cash')</td>
                                    <td class="fw-bolder text-dark fs-6">{{ number_format($summary['drawer']['actual_cash'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-light fw-bold text-gray-700">@lang('pos::reports.variance_result')</td>
                                    <td colspan="3" class="fw-bolder fs-5 {{ $varColor }}">
                                        {{ number_format(abs($summary['drawer']['variance']), 2) }}
                                        <span class="fs-7 fw-normal ms-2">({{ $varType == 'shortage' ? __('pos::reports.shortage') : ($varType == 'overage' ? __('pos::reports.overage') : __('pos::reports.exact_match')) }})</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <!-- Section 5: Accounting Journal Entries -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-primary fs-6 me-2">5</span>
                        <h4 class="fw-bold text-gray-800 m-0">@lang('pos::reports.shift_journal_entries')</h4>
                    </div>
                    <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-4 mb-4">
                        <i class="fa-solid fa-circle-info fs-2 text-primary me-4 mb-2 mb-sm-0"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-primary fs-6">@lang('pos::reports.posting_policy'): {{ $summary['policy']['policy_name'] }}</span>
                            <span class="text-gray-700 fs-7">
                                {{ $summary['policy']['auto_journal_entry'] ? __('pos::reports.auto_entry_desc') : __('pos::reports.consolidated_entry_desc') }}
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle gs-4 gy-3">
                            <thead class="bg-light-dark text-gray-800 fw-bold">
                                <tr>
                                    <th>@lang('pos::reports.entry_number')</th>
                                    <th>@lang('pos::reports.entry_date')</th>
                                    <th>@lang('pos::reports.entry_type')</th>
                                    <th class="text-end">@lang('pos::reports.entry_total')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary['journal_entries'] as $je)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $je['number'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($je['date'])->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge badge-light-info">{{ $je['type'] }}</span></td>
                                        <td class="text-end fw-bold">{{ number_format($je['total'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">@lang('lang.no_data')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-10 pt-5 border-top text-muted fs-7">
                        <p class="mb-1">@lang('pos::reports.end_of_report')</p>
                        <p class="mb-0">@lang('pos::reports.printed_by'): <span class="fw-bold text-gray-800">{{ auth()->user()->name }}</span> | {{ date('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
