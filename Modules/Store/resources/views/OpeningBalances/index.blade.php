@extends('layouts.app')

@section('title', __('store::models/st_opening_balances.plural') )

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
                        <h1>@lang('store::models/st_opening_balances.plural')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}"
                                class="text-muted
                            text-hover-primary">
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
                            @lang('store::models/st_opening_balances.plural')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>


                
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('store.openingbalance.print')
                        <button type="button" class="icon-btn"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;"
                            onclick="window.print()">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('store.openingbalance.copy')
                        <button type="button" class="icon-btn copy-table" data-target="#db-categories-table"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('store.openingbalance.csv')
                        <a type="button" class="icon-btn" href="{{ route('store.openingbalance.csv') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة Excel -->
                    @can('store.openingbalance.excel')
                        <a type="button" class="icon-btn" href="{{ route('store.openingbalance.excel') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة النسخ -->
                    @can('store.openingbalance.pdf')
                        <a type="button" class="icon-btn" href="{{ route('store.openingbalance.pdf') }}" target="_blank"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('store.openingbalance.import')
                        <a type="button" class="icon-btn" href="{{ route('store.openingbalance.import') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-import" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('store.openingbalance.create')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('store.openingbalance.create') }}">
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
                            {!! Form::open(['route' => 'store.openingbalance.index', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <!-- Document Number Field -->
                                    <div class="form-group col-md-3">
                                        {!! Form::label('document_number', __('store::models/st_opening_balances.fields.document_number') . ':') !!}
                                        {!! Form::text('document_number', request('document_number'), ['class' => 'form-control form-control', 'placeholder' => __('lang.search')]) !!}
                                    </div>




                                    <!-- Store Field -->
                                    <div class="form-group col-md-3">
                                        {!! Form::label('store_id', __('store::models/st_opening_balances.fields.store_id') . ':') !!}
                                        <x-select2-input name="store_id" id="store_id" :placeholder="__('lang.select')" :list="$stores" :selected_id="request('store_id')">
                                        </x-select2-input>
                                    </div>

                                    <!-- Status Field -->
                                    <div class="form-group col-md-3">
                                        {!! Form::label('status', __('store::models/st_opening_balances.fields.status') . ':') !!}
                                        <x-select2-input name="status" id="status" :placeholder="__('lang.select')" :list="$statuses" :selected_id="request('status')">
                                        </x-select2-input>
                                    </div>

                                    <!-- pagination Field -->
                                    <div class="form-group col-md-3">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, [
                                            'class' => 'form-control form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-secondary" href="{{ route('store.openingbalance.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('store::OpeningBalances.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>

<style>
@media print {
    #kt_app_header,
    #kt_app_sidebar,
    #kt_app_toolbar,
    #kt_app_footer,
    #card-filter,
    .btn,
    .btn-group,
    .icon-btn,
    .breadcrumb,
    .card-footer,
    .table-action,
    #kt_scrolltop,
    .no-print {
        display: none !important;
    }

    html, body, 
    #kt_app_root, 
    #kt_app_page, 
    #kt_app_wrapper, 
    #kt_app_main, 
    .app-main, 
    .app-wrapper, 
    .app-page, 
    .app-root, 
    .app-content, 
    .app-container,
    #kt_app_content, 
    #kt_app_content_container {
        position: static !important;
        display: block !important;
        float: none !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: transparent !important;
        background-color: #fff !important;
        box-shadow: none !important;
        border: none !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card-body {
        padding: 0 !important;
        margin: 0 !important;
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .table th {
        background-color: #f3f6f9 !important;
        color: #000 !important;
        font-weight: 800 !important;
        border: 1px solid #dbdfe9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table td {
        border: 1px solid #dbdfe9 !important;
        color: #111 !important;
        font-weight: 500 !important;
    }
}
</style>
@endsection


