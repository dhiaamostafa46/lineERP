@extends('layouts.app')

@section('title', __('pos::reports.journal_entries_title'))

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
                    @lang('pos::reports.journal_entries_title')
                    <span class="badge badge-light-primary ms-2 fs-7">@lang('pos::reports.shift_number') {{ $summary['session']->id }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('pos.reports.sessions') }}" class="text-muted text-hover-primary">@lang('pos::reports.types.session_sales')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('pos::reports.journal_entries')</li>
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
            <!-- Policy Alert Box -->
            <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-4">
                <i class="fa-solid fa-scale-balanced fs-2x text-primary me-4 mb-3 mb-sm-0"></i>
                <div class="d-flex flex-column">
                    <h4 class="fw-bold text-primary fs-5 mb-1">@lang('pos::reports.posting_policy'): {{ $summary['policy']['policy_name'] }}</h4>
                    <span class="text-gray-700 fs-7">
                        {{ $summary['policy']['auto_journal_entry'] ? __('pos::reports.auto_entry_desc') : __('pos::reports.consolidated_entry_desc') }}
                    </span>
                </div>
            </div>

            <!-- Journal Entries Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-book fs-2 me-2 text-primary"></i> @lang('pos::reports.journal_entries_list')
                    </h3>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover align-middle">
                            <thead class="bg-light-primary">
                                <tr class="fw-bold text-gray-800">
                                    <th>@lang('pos::reports.entry_number')</th>
                                    <th class="text-center">@lang('pos::reports.entry_date')</th>
                                    <th>@lang('pos::reports.entry_type')</th>
                                    <th class="text-end">@lang('pos::reports.entry_total')</th>
                                    <th class="text-center">@lang('pos::reports.shift_status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary['journal_entries'] as $je)
                                    <tr>
                                        <td><span class="badge badge-light-primary fs-7 fw-bolder">{{ $je['number'] }}</span></td>
                                        <td class="text-center" dir="ltr">{{ \Carbon\Carbon::parse($je['date'])->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge badge-light-info"><i class="fa-solid fa-tag me-1"></i> {{ $je['type'] }}</span></td>
                                        <td class="text-end fw-bolder text-gray-900">{{ number_format($je['total'], 2) }}</td>
                                        <td class="text-center">
                                            @if(($je['status'] ?? '') == 'posted')
                                                <span class="badge badge-light-success"><i class="fa-solid fa-check me-1"></i> @lang('pos::reports.posted')</span>
                                            @else
                                                <span class="badge badge-light-warning"><i class="fa-solid fa-clock me-1"></i> @lang('pos::reports.draft')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-10 text-muted">@lang('lang.no_data')</td></tr>
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
