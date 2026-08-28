@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.tax_report'))

@section('styles')
<style>
    @media print {
        * { margin: 0 !important; padding: 0 !important; }
        body { margin: 0; padding: 10px !important; font-size: 11px; }
        .app-toolbar, .app-container > .card:first-child, #kt_docs_card_collapsible, .card-footer, .btn, .breadcrumb { display: none !important; }
        .app-content, .app-content-container { padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #999 !important; page-break-inside: avoid; }
        .card-header { background-color: #f0f0f0 !important; border-bottom: 2px solid #333 !important; padding: 8px !important; }
        .card-title { font-size: 12px !important; font-weight: bold !important; }
        .table { font-size: 10px; width: 100%; border-collapse: collapse; }
        .table thead th { background-color: #e8e8e8 !important; border: 1px solid #999 !important; padding: 4px !important; }
        .table td { padding: 3px !important; border: 1px solid #999 !important; }
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('invoices::models/inv_reports.types.tax_report')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('invoices.reports.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/inv_reports.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.types.tax_report')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('invoices.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            {{-- Filter Card --}}
            <div class="card shadow-sm my-3" id="card-filter">
                <div class="card-header collapsible cursor-pointer rotate collapsed active" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible">
                    <h3 class="card-title"><i class="fa-solid fa-filter fs-2 me-2"></i> @lang('crud.search')</h3>
                    <div class="card-toolbar rotate-180"><i class="ki-duotone ki-down fs-1"></i></div>
                </div>
                <div id="kt_docs_card_collapsible" class="collapse show">
                    {!! Form::open(['route' => 'invoices.reports.tax', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':') !!}
                                {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('branch_id', __('lang.branch') . ':') !!}
                                <x-select2-input name="branch_id" :list="$branches" :selected_id="request('branch_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                        <button type="button" class="btn btn-sm btn-btc" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> </button>
                        @if(isset($data))
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel"><i class="fa-regular fa-file-excel fs-5"></i></a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV"><i class="fa-solid fa-file-csv fs-5"></i></a>
                            <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF"><i class="fa-regular fa-file-pdf fs-5"></i></a>
                        @endif
                        <a href="{{ route('invoices.reports.tax') }}" class="btn btn-sm btn-btc"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if(isset($data))
                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">@lang('invoices::models/inv_reports.types.tax_report')</h3></div>
                    <div class="card-body pt-3">
                        {{-- Sales Table --}}
                        <div class="table-responsive mb-10">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th style="width: 40%">@lang('invoices::models/inv_reports.tax.row_description')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.amount')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.adjustment')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.vat_amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-light"><td colspan="4" class="fw-bolder">@lang('invoices::models/inv_reports.tax.sales')</td></tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_standard')</td>
                                        <td class="text-end">{{ number_format($data['sales']['standard']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['sales']['standard']['adj'], 2) }}</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($data['sales']['standard']['vat'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_zero')</td>
                                        <td class="text-end">{{ number_format($data['sales']['zero']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['sales']['zero']['adj'], 2) }}</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_exempt')</td>
                                        <td class="text-end">{{ number_format($data['sales']['exempt']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['sales']['exempt']['adj'], 2) }}</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr class="fw-bold bg-light">
                                        <td>@lang('invoices::models/inv_reports.tax.total')</td>
                                        <td class="text-end">{{ number_format($data['sales']['standard']['amount'] + $data['sales']['zero']['amount'] + $data['sales']['exempt']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['sales']['standard']['adj'] + $data['sales']['zero']['adj'] + $data['sales']['exempt']['adj'], 2) }}</td>
                                        <td class="text-end text-primary">{{ number_format($data['sales']['standard']['vat'] + $data['sales']['zero']['vat'] + $data['sales']['exempt']['vat'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Purchases Table --}}
                        <div class="table-responsive mb-10">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th style="width: 40%">@lang('invoices::models/inv_reports.tax.row_description')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.amount')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.adjustment')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.tax.vat_amount_purchases')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-light"><td colspan="4" class="fw-bolder">@lang('invoices::models/inv_reports.tax.purchases')</td></tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_standard')</td>
                                        <td class="text-end">{{ number_format($data['purchases']['standard']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['purchases']['standard']['adj'], 2) }}</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($data['purchases']['standard']['vat'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_zero')</td>
                                        <td class="text-end">{{ number_format($data['purchases']['zero']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['purchases']['zero']['adj'], 2) }}</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-8">@lang('invoices::models/inv_reports.tax.vat_exempt')</td>
                                        <td class="text-end">{{ number_format($data['purchases']['exempt']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['purchases']['exempt']['adj'], 2) }}</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr class="fw-bold bg-light">
                                        <td>@lang('invoices::models/inv_reports.tax.total')</td>
                                        <td class="text-end">{{ number_format($data['purchases']['standard']['amount'] + $data['purchases']['zero']['amount'] + $data['purchases']['exempt']['amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($data['purchases']['standard']['adj'] + $data['purchases']['zero']['adj'] + $data['purchases']['exempt']['adj'], 2) }}</td>
                                        <td class="text-end text-danger">{{ number_format($data['purchases']['standard']['vat'] + $data['purchases']['zero']['vat'] + $data['purchases']['exempt']['vat'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary Section --}}
                        <div class="row justify-content-end mt-10">
                            <div class="col-md-6">
                                <table class="table table-bordered align-middle">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold bg-light" style="width: 60%">@lang('invoices::models/inv_reports.tax.summary_rows.total_due')</td>
                                            <td class="text-end">{{ number_format($data['summary']['total_due'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">@lang('invoices::models/inv_reports.tax.summary_rows.carried_forward')</td>
                                            <td class="text-end">{{ number_format($data['summary']['carried_forward'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold bg-light">@lang('invoices::models/inv_reports.tax.summary_rows.corrections')</td>
                                            <td class="text-end">{{ number_format($data['summary']['corrections'], 2) }}</td>
                                        </tr>
                                        <tr class="bg-light-primary">
                                            <td class="fw-bolder fs-4">@lang('invoices::models/inv_reports.tax.summary_rows.net_final')</td>
                                            <td class="text-end fw-bolder fs-4 text-primary">{{ number_format($data['summary']['net_final'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
