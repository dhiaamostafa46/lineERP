@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.daily_summary'))

@section('styles')
<style>
    @media print {
        .app-toolbar, #card-filter, .btn, .breadcrumb { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        .table-responsive { overflow: visible !important; }
    }
    .report-header-badge {
        padding: 1rem;
        border-radius: 8px;
        background: #f8f9fa;
        border: 1px solid #ebedf3;
    }
    .metric-label { color: #7e8299; font-weight: 600; font-size: 0.9rem; }
    .metric-value { font-weight: 700; font-size: 1.4rem; color: #181c32; }
    .section-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #181c32;
        border-bottom: 2px solid #eff2f5;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
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
                    @lang('invoices::models/inv_reports.summary.operations_summary')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.summary.operations_summary')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('invoices.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            {{-- Search Card --}}
            <div class="card shadow-sm mb-6" id="card-filter">
                <div class="card-body py-4">
                    {!! Form::open(['route' => 'invoices.reports.daily', 'method' => 'GET']) !!}
                    <div class="row">
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':', ['class' => 'fw-bold mb-1']) !!}
                            {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':', ['class' => 'fw-bold mb-1']) !!}
                            {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('user_id', __('invoices::models/inv_reports.filters.employee') . ':', ['class' => 'fw-bold mb-1']) !!}
                            <x-select2-input name="user_id" :list="$employees" :selected_id="request('user_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('store_id', __('lang.store') . ':', ['class' => 'fw-bold mb-1']) !!}
                            <x-select2-input name="store_id" :list="$stores" :selected_id="request('store_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('branch_id', __('lang.branch') . ':', ['class' => 'fw-bold mb-1']) !!}
                            <x-select2-input name="branch_id" :list="$branches" :selected_id="request('branch_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
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
                    <a href="{{ route('invoices.reports.daily') }}" class="btn btn-sm btn-btc"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                </div>
                {!! Form::close() !!}
            </div>

            {{-- Main Unified Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-8">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 text-gray-800">@lang('invoices::models/inv_reports.summary.operations_summary')</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ \Carbon\Carbon::parse($fromDate)->locale('en')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($toDate)->locale('en')->translatedFormat('d F Y') }}</span>
                    </h3>
                </div>
                
                <div class="card-body">
                  

                    <div class="row g-10">
                        {{-- Section 2: Detailed Table --}}
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light px-3">
                                            <th class="ps-3">@lang('lang.description')</th>
                                            <th class="text-center">@lang('invoices::models/inv_reports.columns.sales_count')</th>
                                            <th class="text-end pe-3">@lang('lang.total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-3 fw-bold">@lang('invoices::models/inv_reports.types.sales_invoices')</td>
                                            <td class="text-center text-dark">{{ $data['sales']->count }}</td>
                                            <td class="text-end pe-3 text-dark fw-bold">{{ number_format($data['sales']->total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 fw-bold">@lang('invoices::models/inv_reports.types.sales_return_invoices')</td>
                                            <td class="text-center text-dark">{{ $data['sales_returns']->count }}</td>
                                            <td class="text-end pe-3 text-dark fw-bold">{{ number_format($data['sales_returns']->total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 fw-bold">@lang('invoices::models/inv_reports.types.purchase_invoices')</td>
                                            <td class="text-center text-dark">{{ $data['purchases']->count }}</td>
                                            <td class="text-end pe-3 text-dark fw-bold">{{ number_format($data['purchases']->total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 fw-bold">@lang('invoices::models/inv_reports.types.purchase_return_invoices')</td>
                                            <td class="text-center text-dark">{{ $data['purchase_returns']->count }}</td>
                                            <td class="text-end pe-3 text-dark fw-bold">{{ number_format($data['purchase_returns']->total, 2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="border-top border-2 border-gray-200">
                                        <tr>
                                            <td class="ps-3 fw-bold fs-5 text-gray-800">@lang('invoices::models/inv_reports.columns.net_total') (@lang('lang.sales'))</td>
                                            <td></td>
                                            <td class="text-end pe-3 fs-4 fw-bold text-success">{{ number_format($data['net_sales'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-3 fw-bold fs-5 text-gray-800">@lang('invoices::models/inv_reports.columns.net_total') (@lang('lang.purchase'))</td>
                                            <td></td>
                                            <td class="text-end pe-3 fs-4 fw-bold text-info">{{ number_format($data['net_purchases'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
