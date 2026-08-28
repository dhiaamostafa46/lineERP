@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.zatca_report'))

@section('styles')
<style>
    @media print {
        .app-toolbar, .app-container > .card:first-child, #kt_docs_card_collapsible, .card-footer, .btn, .breadcrumb { display: none !important; }
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>@lang('invoices::models/inv_reports.types.zatca_report')</h1>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('invoices.reports.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/inv_reports.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.types.zatca_report')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('invoices.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-md-2">
                    <div class="card shadow-sm text-center p-5">
                        <span class="text-muted fw-bold">@lang('invoices::models/inv_reports.summary.total_invoices')</span>
                        <span class="fs-2hx fw-bolder">{{ $summary['total_invoices'] }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card shadow-sm text-center p-5 border-success border-dashed">
                        <span class="text-success fw-bold">@lang('invoices::models/inv_reports.summary.reported')</span>
                        <span class="fs-2hx fw-bolder text-success">{{ $summary['reported'] }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card shadow-sm text-center p-5 border-info border-dashed">
                        <span class="text-info fw-bold">@lang('invoices::models/inv_reports.summary.pending')</span>
                        <span class="fs-2hx fw-bolder text-info">{{ $summary['pending'] }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card shadow-sm text-center p-5 border-danger border-dashed">
                        <span class="text-danger fw-bold">@lang('invoices::models/inv_reports.summary.rejected')</span>
                        <span class="fs-2hx fw-bolder text-danger">{{ $summary['rejected'] }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card shadow-sm text-center p-5 border-secondary border-dashed">
                        <span class="text-muted fw-bold">@lang('invoices::models/inv_reports.summary.draft')</span>
                        <span class="fs-2hx fw-bolder text-muted">{{ $summary['draft'] }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm my-3" id="card-filter">
                <div id="kt_docs_card_collapsible" class="collapse show">
                    {!! Form::open(['route' => 'invoices.reports.zatca', 'method' => 'GET']) !!}
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
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" name="search" value="1" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($invoices !== null)
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th>@lang('invoices::models/inv_reports.columns.invoice_number')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.issue_date')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.total_inclusive_vat')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.zatca_status')</th>
                                        <th class="text-center">@lang('lang.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $inv)
                                        <tr>
                                            <td>{{ $inv->invoice_number }}</td>
                                            <td>{{ $inv->issue_date->format('Y-m-d H:i') }}</td>
                                            <td class="fw-bold">{{ number_format($inv->total_inclusive_vat, 2) }}</td>
                                            <td><span class="{{ $inv->status_badge }}">{{ $inv->status_text }}</span></td>
                                            <td class="text-center">
                                                <a href="{{ route('invoices.sales.show', $inv->id) }}" class="btn btn-icon btn-sm btn-light-primary"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $invoices->appends(request()->all())->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
