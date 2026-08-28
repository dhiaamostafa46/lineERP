@extends('layouts.app')

@section('title', __('pos::reports.detailed_sales_title'))

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
                    @lang('pos::reports.detailed_sales_title')
                    <span class="badge badge-light-primary ms-2 fs-7">@lang('pos::reports.shift_number') {{ $summary['session']->id }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.sessions') }}" class="text-muted text-hover-primary">@lang('pos::reports.types.session_sales')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.detailed_sales')</li>
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
            <!-- Header Summary Info -->
            <div class="card bg-light-primary border border-primary border-dashed p-4 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.cashier'):</span>
                        <span class="fw-bolder fs-6 text-gray-800">{{ $summary['session']->cashier->name ?? '---' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.pos_device'):</span>
                        <span class="fw-bolder fs-6 text-info"><i class="fa-solid fa-desktop me-1"></i> {{ $summary['session']->device->name ?? '---' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.items_count'):</span>
                        <span class="fw-bolder fs-6 text-primary">{{ $invoices->count() }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.post_tax'):</span>
                        <span class="fw-bolder fs-5 text-success">{{ number_format($invoices->sum('total_amount_inclusive'), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-file-invoice-dollar fs-2 me-2 text-primary"></i> @lang('pos::reports.invoices_list')
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover align-middle">
                            <thead class="bg-light-primary">
                                <tr class="fw-bold text-gray-800">
                                    <th class="text-center">#</th>
                                    <th>@lang('pos::reports.invoice_number')</th>
                                    <th class="text-center">@lang('pos::reports.entry_date')</th>
                                    <th>@lang('pos::reports.customer')</th>
                                    <th class="text-center">@lang('pos::reports.items_count')</th>
                                    <th class="text-end">@lang('pos::reports.pre_tax')</th>
                                    <th class="text-end">@lang('pos::reports.tax_amount')</th>
                                    <th class="text-end">@lang('pos::reports.post_tax')</th>
                                    <th class="text-center">@lang('pos::reports.payment_method')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td><span class="badge badge-light-primary fw-bolder fs-7">{{ $inv->invoice_number }}</span></td>
                                        <td class="text-center" dir="ltr">{{ $inv->created_at ? $inv->created_at->format('Y-m-d H:i') : '---' }}</td>
                                        <td class="fw-semibold">{{ $inv->customer->name ?? __('pos::reports.cash_customer') }}</td>
                                        <td class="text-center"><span class="badge badge-light-dark">{{ $inv->items->count() }}</span></td>
                                        <td class="text-end">{{ number_format($inv->total_amount_exclusive, 2) }}</td>
                                        <td class="text-end">{{ number_format($inv->total_vat_amount, 2) }}</td>
                                        <td class="text-end fw-bolder text-success">{{ number_format($inv->total_amount_inclusive, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light-info"><i class="fa-solid fa-wallet me-1"></i> {{ optional($inv->payments->first())->method_text ?? __('pos::reports.cash_type') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center py-10 text-muted">@lang('lang.no_data')</td></tr>
                                @endforelse
                            </tbody>
                            @if($invoices->count() > 0)
                                <tfoot class="fw-bold bg-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bolder">@lang('pos::reports.grand_total')</td>
                                        <td class="text-end fw-bold">{{ number_format($invoices->sum('total_amount_exclusive'), 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($invoices->sum('total_vat_amount'), 2) }}</td>
                                        <td class="text-end fw-bolder text-success fs-6">{{ number_format($invoices->sum('total_amount_inclusive'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
