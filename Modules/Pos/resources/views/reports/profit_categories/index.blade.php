@extends('layouts.app')
@section('title', __('pos::reports.types.profit_categories'))
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
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">@lang('pos::reports.types.profit_categories')</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.index') }}" class="text-muted text-hover-primary">@lang('pos::reports.title')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.types.profit_categories')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3"><a href="{{ route('pos.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a></div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card shadow-sm my-3" id="card-filter">
                <div class="card-header collapsible cursor-pointer rotate collapsed active" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible">
                    <h3 class="card-title"><i class="fa-solid fa-filter fs-2 me-2"></i> @lang('crud.search')</h3>
                    <div class="card-toolbar rotate-180"><i class="ki-duotone ki-down fs-1"></i></div>
                </div>
                <div id="kt_docs_card_collapsible" class="collapse show">
                    {!! Form::open(['route' => 'pos.reports.profit_categories', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':') !!}
                                {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" name="search" value="1" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                        <button type="button" class="btn btn-sm btn-btc" onclick="window.print()"><i class="fa-solid fa-print me-1"></i></button>
                        @if($reports !== null)
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel"><i class="fa-regular fa-file-excel fs-5"></i></a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV"><i class="fa-solid fa-file-csv fs-5"></i></a>
                            <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF"><i class="fa-regular fa-file-pdf fs-5"></i></a>
                        @endif
                        <a href="{{ route('pos.reports.profit_categories') }}" class="btn btn-sm btn-btc float-right"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            @if($reports !== null)
                <div class="row mb-5">
                    <div class="col-md-3">
                        <div class="card bg-light-primary shadow-sm">
                            <div class="card-body p-4 text-center">
                                <h4 class="text-primary mb-2">@lang('pos::reports.total_net_quantity')</h4>
                                <h2 class="fw-bolder">{{ number_format($reports->grand_totals->grand_quantity ?? 0, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-success shadow-sm">
                            <div class="card-body p-4 text-center">
                                <h4 class="text-success mb-2">@lang('pos::reports.total_net_revenue')</h4>
                                <h2 class="fw-bolder">{{ number_format($reports->grand_totals->grand_revenue ?? 0, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-danger shadow-sm">
                            <div class="card-body p-4 text-center">
                                <h4 class="text-danger mb-2">@lang('pos::reports.total_net_cost')</h4>
                                <h2 class="fw-bolder">{{ number_format($reports->grand_totals->grand_cost ?? 0, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-info shadow-sm">
                            <div class="card-body p-4 text-center">
                                <h4 class="text-info mb-2">@lang('pos::reports.total_net_profit')</h4>
                                <h2 class="fw-bolder">{{ number_format($reports->grand_totals->grand_profit ?? 0, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">@lang('pos::reports.types.profit_categories') <span class="badge badge-light-primary ms-2">{{ $reports->total() }} @lang('lang.record')</span></h3></div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary"><tr class="fw-bold text-gray-800">
                                    <th class="text-center">#</th>
                                    <th>@lang('basicdata::models/db_products.fields.category_id')</th>
                                    <th class="text-center">@lang('pos::reports.net_qty_short')</th>
                                    <th class="text-end">@lang('pos::reports.net_revenue_short')</th>
                                    <th class="text-end">@lang('pos::reports.net_cost_short')</th>
                                    <th class="text-end">@lang('pos::reports.net_profit_short')</th>
                                </tr></thead>
                                <tbody>
                                    @forelse($reports as $report)
                                        <tr>
                                            <td class="text-center">{{ ($reports->currentPage()-1) * $reports->perPage() + $loop->iteration }}</td>
                                            <td>{{ $report->category_name }}</td>
                                            <td class="text-center">{{ $report->total_quantity }}</td>
                                            <td class="text-end text-success">{{ number_format($report->total_revenue, 2) }}</td>
                                            <td class="text-end text-danger">{{ number_format($report->total_cost, 2) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($report->total_profit, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $reports->appends(request()->all())->links() }}</div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm"><div class="card-body text-center py-20"><i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i><h3 class="text-muted">@lang('invoices::models/inv_reports.messages.select_filters')</h3></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
