@extends('layouts.app')

@section('title', __('pos::reports.cash_ledger'))

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
                    @lang('pos::reports.cash_ledger')
                    <span class="badge badge-light-primary ms-2 fs-7">@lang('pos::reports.shift_number') {{ $summary['session']->id }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.sessions') }}" class="text-muted text-hover-primary">@lang('pos::reports.types.session_sales')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.cash_ledger')</li>
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
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.opened_at'):</span>
                        <span class="fw-bold text-gray-800" dir="ltr">{{ $summary['session']->opened_at ? $summary['session']->opened_at->format('Y-m-d H:i') : '---' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted fs-7 d-block">@lang('pos::reports.closed_at'):</span>
                        <span class="fw-bold text-gray-800" dir="ltr">{{ $summary['session']->closed_at ? $summary['session']->closed_at->format('Y-m-d H:i') : '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-list-check fs-2 me-2 text-primary"></i> @lang('pos::reports.ledger_subtitle')
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover align-middle">
                            <thead class="bg-light-primary">
                                <tr class="fw-bold text-gray-800">
                                    <th class="text-center">@lang('pos::reports.time')</th>
                                    <th>@lang('pos::reports.type')</th>
                                    <th>@lang('pos::reports.description')</th>
                                    <th class="text-end">@lang('pos::reports.receipts')</th>
                                    <th class="text-end">@lang('pos::reports.payments')</th>
                                    <th class="text-end">@lang('pos::reports.cumulative_balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ledger as $row)
                                    @php
                                        $typeStr = strtolower($row['type'] ?? '');
                                        $isOpening = str_contains($typeStr, 'opening');
                                        $isSale = str_contains($typeStr, 'sale');
                                        $isReturn = str_contains($typeStr, 'return');
                                        $isDeposit = str_contains($typeStr, 'deposit');
                                        $isWithdrawal = str_contains($typeStr, 'withdrawal');
                                        $inVal = $row['in'] ?? $row['debit'] ?? 0;
                                        $outVal = $row['out'] ?? $row['credit'] ?? 0;
                                    @endphp
                                    <tr class="{{ $isOpening ? 'table-light-primary fw-bold' : '' }}">
                                        <td class="text-center fw-semibold" dir="ltr">{{ \Carbon\Carbon::parse($row['time'])->format('H:i:s') }}</td>
                                        <td>
                                            @if($isOpening)
                                                <span class="badge badge-primary"><i class="fa-solid fa-vault me-1"></i> @lang('pos::reports.opening_float')</span>
                                            @elseif($isSale)
                                                <span class="badge badge-light-success"><i class="fa-solid fa-arrow-down-left me-1"></i> @lang('pos::reports.cash_sale_label')</span>
                                            @elseif($isReturn)
                                                <span class="badge badge-light-danger"><i class="fa-solid fa-arrow-up-right me-1"></i> @lang('pos::reports.cash_return_label')</span>
                                            @elseif($isDeposit)
                                                <span class="badge badge-light-info"><i class="fa-solid fa-plus me-1"></i> @lang('pos::reports.cash_deposit_label')</span>
                                            @elseif($isWithdrawal)
                                                <span class="badge badge-light-warning"><i class="fa-solid fa-minus me-1"></i> @lang('pos::reports.cash_withdrawal_label')</span>
                                            @else
                                                <span class="badge badge-light-dark">{{ $row['type'] }}</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-gray-700">{{ $row['description'] ?? '---' }}</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ $inVal > 0 ? number_format($inVal, 2) : '-' }}
                                        </td>
                                        <td class="text-end text-danger fw-bold">
                                            {{ $outVal > 0 ? '-' . number_format($outVal, 2) : '-' }}
                                        </td>
                                        <td class="text-end fw-bolder text-primary">
                                            {{ number_format($row['balance'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-10 text-muted">@lang('lang.no_data')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
