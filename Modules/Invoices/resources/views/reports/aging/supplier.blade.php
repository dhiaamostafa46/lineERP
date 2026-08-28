@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.supplier_aging'))

@section('styles')
<style>
    @media print {
        .app-toolbar, #card-filter, .card-footer, .btn, .breadcrumb { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>
@endsection


@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('invoices::models/inv_reports.types.supplier_aging')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('invoices.reports.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/inv_reports.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.types.supplier_aging')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('invoices.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card shadow-sm my-3" id="card-filter">
                    {!! Form::open(['route' => 'invoices.reports.supplier_aging', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('supplier_id', __('invoices::models/inv_reports.filters.supplier') . ':') !!}
                                {!! Form::select('supplier_id', [], request('supplier_id'), ['class' => 'form-select select2-ajax-suppliers', 'data-selected' => request('supplier_id'), 'placeholder' => __('invoices::models/inv_reports.filters.all')]) !!}
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('store_id', __('lang.store') . ':') !!}
                                <x-select2-input name="store_id" :list="$stores" :selected_id="request('store_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                {!! Form::label('branch_id', __('lang.branch') . ':') !!}
                                <x-select2-input name="branch_id" :list="$branches" :selected_id="request('branch_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.from_date') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate'), ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.to_date') . ':') !!}
                                {!! Form::date('toDate', request('toDate'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                        <button type="button" class="btn btn-sm btn-btc" onclick="window.print()"><i class="fa-solid fa-print me-1"></i></button>
                        @if(isset($data))
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel"><i class="fa-regular fa-file-excel fs-5"></i></a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV"><i class="fa-solid fa-file-csv fs-5"></i></a>
                            <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF"><i class="fa-regular fa-file-pdf fs-5"></i></a>
                        @endif
                        <a href="{{ route('invoices.reports.supplier_aging') }}" class="btn btn-sm btn-btc"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h3 class="card-title">@lang('invoices::models/inv_reports.types.supplier_aging')</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center">
                            <thead class="bg-light-warning fw-bold text-gray-800">
                                <tr>
                                    <th rowspan="2" class="align-middle">@lang('invoices::models/inv_reports.columns.supplier')</th>
                                    <th colspan="2">@lang('invoices::models/inv_reports.columns.current')</th>
                                    <th colspan="2">@lang('invoices::models/inv_reports.columns.1_30')</th>
                                    <th colspan="2">@lang('invoices::models/inv_reports.columns.31_60')</th>
                                    <th colspan="2">@lang('invoices::models/inv_reports.columns.61_90')</th>
                                    <th colspan="2">@lang('invoices::models/inv_reports.columns.over_90')</th>
                                    <th colspan="2" class="bg-light-info">@lang('lang.total')</th>
                                    <th rowspan="2" class="align-middle bg-light-danger">@lang('invoices::models/inv_reports.columns.total_due')</th>
                                </tr>
                                <tr>
                                    <th class="text-end text-primary">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success">@lang('invoices::models/inv_reports.columns.credit')</th>
                                    <th class="text-end text-primary">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success">@lang('invoices::models/inv_reports.columns.credit')</th>
                                    <th class="text-end text-primary">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success">@lang('invoices::models/inv_reports.columns.credit')</th>
                                    <th class="text-end text-primary">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success">@lang('invoices::models/inv_reports.columns.credit')</th>
                                    <th class="text-end text-primary">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success">@lang('invoices::models/inv_reports.columns.credit')</th>
                                    <th class="text-end text-primary bg-light-info">@lang('invoices::models/inv_reports.columns.debit')</th>
                                    <th class="text-end text-success bg-light-info">@lang('invoices::models/inv_reports.columns.credit')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td class="text-start fw-bold">{{ $item->name }}</td>
                                        <td class="text-end">{{ number_format($item->aging['current']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['current']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['1_30']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['1_30']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['31_60']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['31_60']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['61_90']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['61_90']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['over_90']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->aging['over_90']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end fw-semibold text-primary bg-light-info">{{ number_format($item->aging['total']['debit'] ?? 0, 2) }}</td>
                                        <td class="text-end fw-semibold text-success bg-light-info">{{ number_format($item->aging['total']['credit'] ?? 0, 2) }}</td>
                                        <td class="text-end fw-bold text-danger bg-light-danger">{{ number_format($item->aging['total']['balance'] ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="14" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td class="text-start">@lang('lang.total'):</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['current']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['current']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['1_30']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['1_30']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['31_60']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['31_60']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['61_90']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['61_90']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['over_90']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end">{{ number_format($data->sum(fn($i) => $i->aging['over_90']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end text-primary bg-light-info">{{ number_format($data->sum(fn($i) => $i->aging['total']['debit'] ?? 0), 2) }}</td>
                                    <td class="text-end text-success bg-light-info">{{ number_format($data->sum(fn($i) => $i->aging['total']['credit'] ?? 0), 2) }}</td>
                                    <td class="text-end text-danger bg-light-danger">{{ number_format($data->sum(fn($i) => $i->aging['total']['balance'] ?? 0), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
