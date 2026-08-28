@extends('layouts.app')

@section('title', __('pos::reports.types.pos_sales'))

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
                    @lang('pos::reports.types.pos_sales')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.index') }}" class="text-muted text-hover-primary">@lang('pos::reports.title')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.types.pos_sales')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('pos.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
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
                    {!! Form::open(['route' => 'pos.reports.sales', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('customer_id', __('invoices::models/inv_reports.filters.customer') . ':') !!}
                                <x-select2-input name="customer_id" :list="$customers" :selected_id="request('customer_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('store_id', __('lang.store') . ':') !!}
                                <x-select2-input name="store_id" :list="$stores" :selected_id="request('store_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('branch_id', __('lang.branch') . ':') !!}
                                <x-select2-input name="branch_id" :list="$branches" :selected_id="request('branch_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('user_id', __('invoices::models/inv_reports.filters.employee') . ':') !!}
                                <x-select2-input name="user_id" :list="$employees" :selected_id="request('user_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('device_id', __('pos::lang.device') . ':') !!}
                                <x-select2-input name="device_id" :list="$devices" :selected_id="request('device_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('pos_session_id', __('pos::lang.session') . ':') !!}
                                {!! Form::text('pos_session_id', request('pos_session_id'), ['class' => 'form-control form-control-solid', 'placeholder' => __('pos::lang.session')]) !!}
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('fromDate', __('invoices::models/inv_reports.filters.date_from') . ':') !!}
                                {!! Form::date('fromDate', request('fromDate', $fromDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('toDate', __('invoices::models/inv_reports.filters.date_to') . ':') !!}
                                {!! Form::date('toDate', request('toDate', $toDate), ['class' => 'form-control form-control-solid']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" name="search" value="1" class="btn btn-sm btn-btc"><i class="fa-solid fa-magnifying-glass me-1"></i> @lang('crud.search')</button>
                        <button type="button" class="btn btn-sm btn-btc" onclick="window.print()"><i class="fa-solid fa-print me-1"></i></button>
                        @if($sales !== null)
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel"><i class="fa-regular fa-file-excel fs-5"></i></a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV"><i class="fa-solid fa-file-csv fs-5"></i></a>
                            <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF"><i class="fa-regular fa-file-pdf fs-5"></i></a>
                        @endif
                        <a href="{{ route('pos.reports.sales') }}" class="btn btn-sm btn-btc float-right"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($sales !== null)
                @php $accTotals = $sales->accountingTotals ?? null; @endphp
                @if($accTotals)
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light-primary border border-primary border-dashed p-3 text-center">
                                <span class="fs-7 text-muted fw-bold">@lang('invoices::models/inv_reports.accounting_totals.gross_sales')</span>
                                <span class="fs-4 fw-bolder text-primary" dir="ltr">{{ number_format($accTotals['gross_inclusive_vat'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-danger border border-danger border-dashed p-3 text-center">
                                <span class="fs-7 text-muted fw-bold">@lang('invoices::models/inv_reports.accounting_totals.sales_returns')</span>
                                <span class="fs-4 fw-bolder text-danger" dir="ltr">-{{ number_format($accTotals['return_inclusive_vat'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-warning border border-warning border-dashed p-3 text-center">
                                <span class="fs-7 text-muted fw-bold">@lang('invoices::models/inv_reports.accounting_totals.net_vat')</span>
                                <span class="fs-4 fw-bolder text-warning" dir="ltr">{{ number_format($accTotals['net_vat'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-success border border-success border-dashed p-3 text-center">
                                <span class="fs-7 text-muted fw-bold">@lang('invoices::models/inv_reports.accounting_totals.net_final')</span>
                                <span class="fs-3 fw-bolder text-success" dir="ltr">{{ number_format($accTotals['net_inclusive_vat'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">@lang('pos::reports.types.pos_sales') <span class="badge badge-light-primary ms-2">{{ $sales->total() }} @lang('lang.record')</span></h3></div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th class="text-center">#</th>
                                        <th>@lang('invoices::models/inv_reports.columns.invoice_number')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.issue_date')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.customer')</th>
                                        <th>@lang('invoices::models/inv_reports.columns.employee')</th>
                                        <th>@lang('pos::lang.device')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.total_exclusive_vat')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.total_vat')</th>
                                        <th class="text-end">@lang('invoices::models/inv_reports.columns.total_inclusive_vat')</th>
                                        <th class="text-center">@lang('pos::lang.session')</th>
                                        <th class="text-center">@lang('invoices::models/inv_reports.columns.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $inv)
                                        @php $isReturn = $inv->type_inv == \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS; @endphp
                                        <tr class="{{ $isReturn ? 'table-light-danger' : '' }}">
                                            <td class="text-center">{{ ($sales->currentPage()-1) * $sales->perPage() + $loop->iteration }}</td>
                                            <td>{{ $inv->invoice_number }}</td>
                                            <td>{{ $inv->issue_date->format('Y-m-d') }}</td>
                                            <td>{{ $inv->customer?->name }}</td>
                                            <td>{{ $inv->user?->name }}</td>
                                            <td><span class="badge badge-light-info">{{ $inv->posSession?->device?->name ?? '---' }}</span></td>
                                            <td class="text-end">{{ number_format($inv->total_exclusive_vat, 2) }}</td>
                                            <td class="text-end">{{ number_format($inv->total_vat, 2) }}</td>
                                            <td class="text-end fw-bold {{ $isReturn ? 'text-danger' : 'text-primary' }}">{{ number_format($inv->total_inclusive_vat, 2) }}</td>
                                            <td class="text-center">{{ $inv->pos_session_id ?? '---' }}</td>
                                            <td class="text-center"><span class="{{ $inv->status_badge }}">{{ $inv->status_text }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="11" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                    @endforelse
                                </tbody>
                                @if($accTotals)
                                    <tfoot class="fw-bold bg-light">
                                        <tr>
                                            <td colspan="6" class="text-end fw-bold">@lang('invoices::models/inv_reports.accounting_totals.gross_total'):</td>
                                            <td class="text-end">{{ number_format($accTotals['gross_exclusive_vat'], 2) }}</td>
                                            <td class="text-end">{{ number_format($accTotals['gross_vat'], 2) }}</td>
                                            <td class="text-end text-primary">{{ number_format($accTotals['gross_inclusive_vat'], 2) }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                        @if($accTotals['return_inclusive_vat'] > 0)
                                            <tr class="text-danger">
                                                <td colspan="6" class="text-end fw-bold">@lang('invoices::models/inv_reports.accounting_totals.total_returns'):</td>
                                                <td class="text-end">-{{ number_format($accTotals['return_exclusive_vat'], 2) }}</td>
                                                <td class="text-end">-{{ number_format($accTotals['return_vat'], 2) }}</td>
                                                <td class="text-end">-{{ number_format($accTotals['return_inclusive_vat'], 2) }}</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        @endif
                                        <tr class="table-success fw-bolder fs-6">
                                            <td colspan="6" class="text-end">@lang('invoices::models/inv_reports.accounting_totals.final_net'):</td>
                                            <td class="text-end">{{ number_format($accTotals['net_exclusive_vat'], 2) }}</td>
                                            <td class="text-end">{{ number_format($accTotals['net_vat'], 2) }}</td>
                                            <td class="text-end text-success">{{ number_format($accTotals['net_inclusive_vat'], 2) }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        <div class="mt-4">{{ $sales->appends(request()->all())->links() }}</div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm"><div class="card-body text-center py-20"><i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i><h3 class="text-muted">@lang('invoices::models/inv_reports.messages.select_filters')</h3></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
