@extends('layouts.app')

@section('title', __('accusoft::models/as_journal_entries.plural'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <h1>@lang('accusoft::models/as_journal_entries.plural')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted  text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('accusoft::models/as_journal_entries.plural')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->


                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('accusoft.JournalEntry.print')
                        <button type="button" class="icon-btn   btn-btc" onclick="window.print()">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('accusoft.JournalEntry.copy')
                        <button type="button" class="icon-btn   btn-btc copy-table" data-target="#AS-journalEntries-table">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('accusoft.JournalEntry.csv')
                        <a type="button" class="icon-btn   btn-btc" href="{{ route('accusoft.JournalEntry.csv') }}">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة Excel -->
                    @can('accusoft.JournalEntry.excel')
                        <a type="button" class="icon-btn   btn-btc" href="{{ route('accusoft.JournalEntry.excel') }}">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة النسخ -->
                    @can('accusoft.JournalEntry.pdf')
                        <a type="button" class="icon-btn   btn-btc" href="{{ route('accusoft.JournalEntry.pdf') }}">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('accusoft.JournalEntry.import')
                        <a class="icon-btn   btn-btc" href="{{ route('accusoft.JournalEntry.import') }}">
                            <i class="fa-solid fa-file-import"></i>
                        </a>
                    @endcan
                    @can('accusoft.JournalEntry.pending')
                        @php $pendingCount = \App\Models\AccuSoft\JournalEntry::where('status', \App\Models\AccuSoft\JournalEntry::STATUS_PENDING)->count(); @endphp
                        <a class="icon-btn btn-btc position-relative" href="{{ route('accusoft.JournalEntry.pending') }}"
                           data-bs-toggle="tooltip" data-bs-placement="bottom"
                           title="@lang('accusoft::lang.pending_journal_entries') ({{ $pendingCount }})">
                            <i class="fa-solid fa-hourglass-half {{ $pendingCount > 0 ? 'text-warning' : '' }}"></i>
                            @if($pendingCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.65rem; min-width: 18px; animation: pulse 1.5s infinite;">
                                    {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                                </span>
                            @endif
                        </a>
                        <style>
                            @keyframes pulse {
                                0%   { transform: translate(-50%, -50%) scale(1); }
                                50%  { transform: translate(-50%, -50%) scale(1.25); }
                                100% { transform: translate(-50%, -50%) scale(1); }
                            }
                        </style>
                    @endcan
                    @can('accusoft.JournalEntry.create')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('accusoft.JournalEntry.create') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('crud.add_new')
                        </a>
                    @endcan
                </div>





                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="clearfix"></div>
                @if (true)
                    <div class="card shadow-sm my-3 " id="card-filter">
                        <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse"
                            data-bs-target="#kt_docs_card_collapsible" aria-expanded="false">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible" class="collapse">
                            {!! Form::open(['route' => 'accusoft.JournalEntry.index', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <!-- Name Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('entry_number', __('accusoft::models/as_journal_entries.fields.entry_number') . ':') !!}
                                        {!! Form::text('entry_number', request('entry_number'), ['class' => 'form-control']) !!}
                                    </div>

                                     <div class="form-group col-md-4 mb-3">
                                        {!! Form::label('entry_type', __('accusoft::models/as_journal_entries.fields.entry_type') . ':') !!}
                                        <x-select2-input name="entry_type" :placeholder="__('hr::lang.select_status')" :list="$types"
                                            :selected_id="old('entry_type')">
                                        </x-select2-input>
                                    </div>

                                     <div class="form-group col-md-4 mb-3">
                                        {!! Form::label('status', __('accusoft::models/as_journal_entries.fields.status') . ':') !!}
                                        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
                                            :selected_id="request('status')">
                                        </x-select2-input>
                                    </div>

                                    <div class="form-group col-md-4 mb-3">
                                        {!! Form::label('source', __('accusoft::models/as_journal_entries.fields.source') . ':') !!}
                                        <x-select2-input name="source" :placeholder="__('accusoft::models/as_journal_entries.fields.source')" :list="$sources"
                                            :selected_id="request('source')">
                                        </x-select2-input>
                                    </div>

                                    <!-- pagination Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-primary float-right"
                                    href="{{ route('accusoft.JournalEntry.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('accusoft::journal_entries.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
