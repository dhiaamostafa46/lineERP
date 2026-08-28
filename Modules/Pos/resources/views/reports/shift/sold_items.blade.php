@extends('layouts.app')

@section('title', __('pos::reports.sold_items_title'))

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
                    @lang('pos::reports.sold_items_title')
                    <span class="badge badge-light-primary ms-2 fs-7">@lang('pos::reports.shift_number') {{ $summary['session']->id }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.sessions') }}" class="text-muted text-hover-primary">@lang('pos::reports.types.session_sales')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.sold_items')</li>
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
            <!-- Profitability Summary KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-light-primary border border-primary border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1"><i class="fa-solid fa-chart-line text-primary me-1"></i> @lang('pos::reports.net_sales')</span>
                        <span class="fs-2x fw-bolder text-primary" dir="ltr">{{ number_format($summary['revenue']['net_sales'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-danger border border-danger border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1"><i class="fa-solid fa-box-open text-danger me-1"></i> @lang('pos::reports.net_cost')</span>
                        <span class="fs-2x fw-bolder text-danger" dir="ltr">{{ number_format($summary['revenue']['net_cost'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-success border border-success border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1"><i class="fa-solid fa-sack-dollar text-success me-1"></i> @lang('pos::reports.net_profit')</span>
                        <span class="fs-2x fw-bolder text-success" dir="ltr">{{ number_format($summary['revenue']['net_profit'], 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    @php
                        $margin = $summary['revenue']['net_sales'] > 0 ? ($summary['revenue']['net_profit'] / $summary['revenue']['net_sales']) * 100 : 0;
                    @endphp
                    <div class="card bg-light-warning border border-warning border-dashed p-4 text-center">
                        <span class="fs-7 text-muted fw-bold mb-1"><i class="fa-solid fa-percent text-warning me-1"></i> @lang('pos::reports.profit_margin')</span>
                        <span class="fs-2x fw-bolder text-warning" dir="ltr">{{ number_format($margin, 2) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-boxes-stacked fs-2 me-2 text-primary"></i> @lang('pos::reports.items_profitability_list')
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover align-middle">
                            <thead class="bg-light-primary">
                                <tr class="fw-bold text-gray-800">
                                    <th>@lang('pos::reports.sku')</th>
                                    <th>@lang('pos::reports.product_name')</th>
                                    <th class="text-center">@lang('pos::reports.sold_qty')</th>
                                    <th class="text-center">@lang('pos::reports.returned_qty')</th>
                                    <th class="text-center">@lang('pos::reports.net_qty')</th>
                                    <th class="text-end">@lang('pos::reports.net_revenue')</th>
                                    <th class="text-end">@lang('pos::reports.net_cost')</th>
                                    <th class="text-end">@lang('pos::reports.profit')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td><span class="badge badge-light-primary font-monospace">{{ $item['sku'] ?? '---' }}</span></td>
                                        <td class="fw-bold text-gray-800">{{ $item['name'] }}</td>
                                        <td class="text-center text-success fw-bold">{{ $item['sold_qty'] }}</td>
                                        <td class="text-center text-danger fw-bold">{{ $item['returned_qty'] }}</td>
                                        <td class="text-center"><span class="badge badge-light-dark fs-7">{{ $item['net_qty'] }}</span></td>
                                        <td class="text-end fw-semibold">{{ number_format($item['net_revenue'], 2) }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($item['net_cost'], 2) }}</td>
                                        <td class="text-end fw-bolder fs-6 {{ $item['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($item['profit'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center py-10 text-muted">@lang('lang.no_data')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
