@extends('layouts.app')

@section('title', __('pos::reports.types.session_sales'))

@section('styles')
<style>
    .table-responsive {
        min-height: 280px !important;
        overflow: visible !important;
    }
    .card, .card-body {
        overflow: visible !important;
    }
    .dropdown-menu, .menu-sub-dropdown {
        z-index: 999999 !important;
    }
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
                    @lang('pos::reports.types.session_sales')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.index') }}" class="text-muted text-hover-primary">@lang('pos::reports.title')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.types.session_sales')</li>
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
                    {!! Form::open(['route' => 'pos.reports.sessions', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('device_id', __('pos::lang.device') . ':') !!}
                                <x-select2-input name="device_id" :list="$devices" :selected_id="request('device_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('user_id', __('pos::lang.cashier') . ':') !!}
                                <x-select2-input name="user_id" :list="$employees" :selected_id="request('user_id')" :placeholder="__('invoices::models/inv_reports.filters.all')"></x-select2-input>
                            </div>
                            
                            <div class="form-group col-md-3 mb-3">
                                {!! Form::label('status', __('pos::lang.status') . ':') !!}
                                <select name="status" class="form-select form-select-solid">
                                    <option value="">@lang('invoices::models/inv_reports.filters.all')</option>
                                    <option value="opened" {{ request('status') == 'opened' ? 'selected' : '' }}>@lang('pos::lang.active')</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>@lang('pos::lang.closed')</option>
                                </select>
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
                        @if($sessions !== null)
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel"><i class="fa-regular fa-file-excel fs-5"></i></a>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV"><i class="fa-solid fa-file-csv fs-5"></i></a>
                            <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF"><i class="fa-regular fa-file-pdf fs-5"></i></a>
                        @endif
                        <a href="{{ route('pos.reports.sessions') }}" class="btn btn-sm btn-btc float-right"><i class="fa-solid fa-circle-xmark me-1"></i></a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($sessions !== null)
                <div class="card shadow-sm">
                    <div class="card-header"><h3 class="card-title">@lang('pos::reports.types.session_sales') <span class="badge badge-light-primary ms-2">{{ $sessions->total() }} @lang('lang.record')</span></h3></div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="bg-light-primary">
                                    <tr class="fw-bold text-gray-800">
                                        <th class="text-center">#</th>
                                        <th>@lang('pos::lang.device')</th>
                                        <th>@lang('pos::lang.cashier')</th>
                                        <th>@lang('pos::lang.opened_at')</th>
                                        <th>@lang('pos::lang.closed_at')</th>
                                        <th>@lang('pos::lang.difference')</th>
                                        <th>@lang('pos::lang.status')</th>
                                        <th class="text-center">@lang('pos::reports.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sessions as $session)
                                        <tr>
                                            <td class="text-center">{{ ($sessions->currentPage()-1) * $sessions->perPage() + $loop->iteration }}</td>
                                            <td>{{ $session->device?->name }}</td>
                                            <td>{{ $session->cashier?->name }}</td>
                                            <td>{{ $session->opened_at ? $session->opened_at->format('Y-m-d H:i') : '---' }}</td>
                                            <td>{{ $session->closed_at ? $session->closed_at->format('Y-m-d H:i') : '---' }}</td>
                                            <td class="text-end">
                                                @if($session->difference > 0)
                                                    <span class="text-success">+{{ number_format($session->difference, 2) }}</span>
                                                @elseif($session->difference < 0)
                                                    <span class="text-danger">{{ number_format($session->difference, 2) }}</span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($session->status == 'closed')
                                                    <span class="badge badge-secondary">{{ __('pos::lang.closed') }}</span>
                                                @else
                                                    <span class="badge badge-success">{{ __('pos::lang.active') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <a href="{{ route('pos.reports.shift.z_report', $session->id) }}" target="_blank" class="btn btn-sm btn-icon btn-light-primary" title="{{ __('pos::reports.z_report') }}"><i class="fas fa-file-invoice-dollar fs-6"></i></a>
                                                    <a href="{{ route('pos.reports.shift.cash_ledger', $session->id) }}" target="_blank" class="btn btn-sm btn-icon btn-light-success" title="{{ __('pos::reports.cash_ledger') }}"><i class="fas fa-list fs-6"></i></a>
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                                            @lang('pos::reports.actions')
                                                        </button>
                                                        <ul class="dropdown-menu shadow-lg" style="z-index: 999999 !important;">
                                                            <li><a class="dropdown-item py-2" href="{{ route('pos.reports.shift.z_report', $session->id) }}" target="_blank"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> @lang('pos::reports.z_report')</a></li>
                                                            <li><a class="dropdown-item py-2" href="{{ route('pos.reports.shift.cash_ledger', $session->id) }}" target="_blank"><i class="fas fa-list text-success me-2"></i> @lang('pos::reports.cash_ledger')</a></li>
                                                            <li><a class="dropdown-item py-2" href="{{ route('pos.reports.shift.detailed_sales', $session->id) }}" target="_blank"><i class="fas fa-shopping-cart text-info me-2"></i> @lang('pos::reports.detailed_sales')</a></li>
                                                            <li><a class="dropdown-item py-2" href="{{ route('pos.reports.shift.journal_entries', $session->id) }}" target="_blank"><i class="fas fa-book text-warning me-2"></i> @lang('pos::reports.journal_entries')</a></li>
                                                            <li><a class="dropdown-item py-2" href="{{ route('pos.reports.shift.sold_items', $session->id) }}" target="_blank"><i class="fas fa-box text-danger me-2"></i> @lang('pos::reports.sold_items')</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-10">@lang('lang.no_data')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $sessions->appends(request()->all())->links() }}</div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm"><div class="card-body text-center py-20"><i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i><h3 class="text-muted">@lang('invoices::models/inv_reports.messages.select_filters')</h3></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
