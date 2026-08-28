@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.product_profit'))

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
                    @lang('invoices::models/inv_reports.types.product_profit')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('invoices.reports.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/inv_reports.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.types.product_profit')</li>
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
                    {!! Form::open(['route' => 'invoices.reports.profit', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':') !!}
                                {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('store_id', __('lang.store') . ':') !!}
                                <x-select2-input name="store_id" :list="$stores" :selected_id="request('store_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('branch_id', __('lang.branch') . ':') !!}
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
                        <a href="{{ route('invoices.reports.profit') }}" class="btn btn-sm btn-btc"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h3 class="card-title">@lang('invoices::models/inv_reports.types.product_profit')</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="bg-light-success">
                                <tr class="fw-bold text-gray-800">
                                     <th>@lang('invoices::models/inv_reports.columns.product')</th>
                                     <th class="text-center">@lang('invoices::models/inv_reports.columns.quantity') (@lang('invoices::models/inv_reports.filters.base_unit'))</th>
                                     <th class="text-end">@lang('invoices::models/inv_reports.columns.sales_total')</th>
                                     <th class="text-end">@lang('invoices::models/inv_reports.columns.cost_total')</th>
                                     <th class="text-end text-white bg-success">@lang('invoices::models/inv_reports.columns.profit')</th>
                                     <th class="text-center">@lang('invoices::models/inv_reports.columns.margin') %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalProfit = 0; @endphp
                                @forelse($data as $item)
                                    @php $totalProfit += $item->total_profit; @endphp
                                     <tr>
                                         <td>{{ $item->product_name }}</td>
                                         <td class="text-center fw-bold text-gray-700">{{ number_format($item->total_qty, 2) }}</td>
                                         <td class="text-end">{{ number_format($item->total_sales, 2) }}</td>
                                         <td class="text-end">{{ number_format($item->total_cost, 2) }}</td>
                                         <td class="text-end fw-bold {{ $item->total_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                             {{ number_format($item->total_profit, 2) }}
                                         </td>
                                         <td class="text-center">
                                             @php 
                                                 $margin = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales) * 100 : 0;
                                             @endphp
                                             <span class="badge {{ $margin >= 20 ? 'badge-light-success' : ($margin > 0 ? 'badge-light-warning' : 'badge-light-danger') }}">
                                                 {{ number_format($margin, 1) }}%
                                             </span>
                                         </td>
                                     </tr>
                                 @empty
                                     <tr><td colspan="6" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                 @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                     <td class="text-end">@lang('lang.total'):</td>
                                     <td class="text-center">{{ number_format($data->sum('total_qty'), 2) }}</td>
                                     <td class="text-end">{{ number_format($data->sum('total_sales'), 2) }}</td>
                                     <td class="text-end">{{ number_format($data->sum('total_cost'), 2) }}</td>
                                     <td class="text-end text-success fs-4">{{ number_format($totalProfit, 2) }}</td>
                                     <td class="text-center">
                                         @php 
                                             $totalSales = $data->sum('total_sales');
                                             $totalMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;
                                         @endphp
                                         <span class="badge badge-success">{{ number_format($totalMargin, 1) }}%</span>
                                     </td>
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
