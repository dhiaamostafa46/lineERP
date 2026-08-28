@extends('layouts.app')

@section('title', __('hr::models/hr_contracts.singular'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column no-print justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('hr::models/hr_contracts.singular') @lang('crud.detail')
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class=" text-muted
                            text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('hr.contracts.index') }}" class=" text-muted text-hover-primary">
                            @lang('hr::models/hr_contracts.plural')
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
                <a class="btn btn-sm btn-secondary float-right" href="{{ route('hr.contracts.index') }}">
                    @lang('crud.back')
                </a>
                {{-- <a class="btn btn-sm btn-primary float-right"  href="{{ $contract->file_original_path }}" target="_blank" >
                    <i class="fa fa-file"></i>
                    @lang('hr::models/hr_contracts.qiwa')
                </a> --}}
                <a class="btn btn-sm btn-primary float-right" onclick="window.print();">
                    <i class="fa-solid fa fa-print"></i>
                    @lang('crud.print')
                </a>

                {{-- <a class="btn btn-sm btn-primary float-right" id="download-pdf" >
                    <i class="fa fa-file-pdf"></i>
                    @lang('crud.pdf')
                </a> --}}
                {{-- <button type="button" class=" btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#kt_modal_1">
                    {{ trans('hr::models/hr_contract_items.singular') }}
                </button> --}}
            </div>

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
                    <div class="row gap-1" id="content_QWA">
                        {{-- @include('hr::contracts.show_fields') --}}
                        @include('hr::contracts.templete')
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>


@endsection



