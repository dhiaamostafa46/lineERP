@extends('layouts.app')

@section('title', __('accusoft::models/as_journal_entries.singular'))

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
                        @lang('crud.detail') @lang('accusoft::models/as_journal_entries.singular')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('accusoft.JournalEntry.index') }}" class=" text-muted text-hover-primary">
                                @lang('accusoft::models/as_journal_entries.plural')
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
                            @lang('crud.back')
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
                        <button type="button" class="icon-btn   btn-btc copy-table"
                            data-target="#AS-journalEntriesdetails-table">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan

                    <!-- أيقونة النسخ -->
                    @can('accusoft.JournalEntry.pdfdetails')
                        <a type="button" class="icon-btn   btn-btc"
                            href="{{ route('accusoft.JournalEntry.pdfdetails', $journalEntry->id) }}">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a class="btn btn-sm btn-secondary float-right" href="{{ route('accusoft.JournalEntry.index') }}">
                            @lang('crud.back')
                        </a>
                    </div>
                </div>
                {{--  --}}
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body">
                        <div class="row gap-1" id="AS-journalEntriesdetails-table">
                            @include('accusoft::journal_entries.show_fields')
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
@endsection
