@extends('layouts.app')

@section('title', __('invoices::models/inv_reports.types.purchase_analysis'))

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
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>@lang('invoices::models/inv_reports.types.purchase_analysis')</h1>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('invoices.reports.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/inv_reports.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/inv_reports.types.purchase_analysis')</li>
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
                <div class="card-header collapsible cursor-pointer rotate collapsed active" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible">
                    <h3 class="card-title"><i class="fa-solid fa-filter fs-2 me-2"></i> @lang('crud.search')</h3>
                    <div class="card-toolbar rotate-180"><i class="ki-duotone ki-down fs-1"></i></div>
                </div>
                <div id="kt_docs_card_collapsible" class="collapse show">
                    {!! Form::open(['route' => 'invoices.reports.purchase_analysis', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('supplier_id', __('invoices::models/inv_reports.filters.supplier') . ':') !!}
                                {!! Form::select('supplier_id', [], request('supplier_id'), ['class' => 'form-select select2-ajax-suppliers', 'data-selected' => request('supplier_id'), 'placeholder' => __('invoices::models/inv_reports.filters.all')]) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('product_id', __('invoices::models/inv_reports.filters.product') . ':') !!}
                                <x-select2-input name="product_id" :list="$products" :selected_id="request('product_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':') !!}
                                {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('type_inv', __('invoices::models/inv_reports.filters.type_inv') . ':') !!}
                                <select name="type_inv" class="form-select form-select-solid">
                                    <option value="">@lang('invoices::models/inv_reports.filters.all')</option>
                                    <option value="1" {{ request('type_inv') == '1' ? 'selected' : '' }}>@lang('invoices::models/inv_reports.filters.invoice')</option>
                                    <option value="2" {{ request('type_inv') == '2' ? 'selected' : '' }}>@lang('invoices::models/inv_reports.filters.return')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" name="search" value="1" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                        <button type="button" class="btn btn-sm btn-btc" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> @lang('lang.print')</button>
                        <a href="{{ route('invoices.reports.purchase_analysis') }}" class="btn btn-sm btn-btc float-right"><i class="fa-solid fa-circle-xmark me-1"></i> @lang('crud.reset')</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($data !== null)
                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">@lang('invoices::models/inv_reports.types.purchase_analysis') <span class="badge badge-light-primary ms-2">{{ $data->total() }} @lang('lang.record')</span></h3></div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th class="text-center">#</th>
                                        <th>@lang('invoices::models/inv_reports.columns.invoice_number')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.issue_date')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.supplier')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.product')</th>
                                        <th class="text-center">@lang('invoices::models/inv_reports.columns.quantity')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.unit_price')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.total_vat')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.total_inclusive_vat')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                        <tr>
                                            <td class="text-center">{{ ($data->currentPage()-1) * $data->perPage() + $loop->iteration }}</td>
                                            <td>{{ $item->invoice?->invoice_number }} {!! $item->invoice?->type_badge !!}</td>
                                            <td>{{ $item->invoice?->issue_date->format('Y-m-d') }}</td>
                                            <td>{{ $item->invoice?->supplier?->name }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->vat_amount, 2) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($item->subtotal_with_vat, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold bg-light">
                                        <td colspan="5" class="text-end">@lang('lang.total'):</td>
                                        <td class="text-center">{{ number_format($data->sum('quantity'), 2) }}</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($data->sum('vat_amount'), 2) }}</td>
                                        <td class="text-end text-primary">{{ number_format($data->sum('subtotal_with_vat'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-4">{{ $data->appends(request()->all())->links() }}</div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm"><div class="card-body text-center py-20"><i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i><h3 class="text-muted">@lang('invoices::models/inv_reports.messages.select_filters')</h3></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
