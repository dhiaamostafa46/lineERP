@extends('layouts.app')

@section('title', __('hr::models/hr_report_types.LeaveHoldaybalance'))

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

                        <h1> @lang('hr::models/hr_report_types.LeaveHoldaybalance')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a  href="{{ route('hr.report_types.index') }}" class=" text-muted text-hover-primary">
                                @lang('hr::models/hr_report_types.plural')
                            </a>

                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('hr::models/hr_report_types.LeaveHoldaybalance')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a class="btn btn-sm btn-primary float-right" onclick="window.print();">
                        <i class="fa-solid fa fa-print"></i>
                        @lang('crud.print')
                    </a>
                    <a class="btn btn-sm btn-primary float-right" onclick="downloadExcel();">
                        <i class="fa-solid fa fa-file-excel"></i>
                        @lang('crud.export')
                    </a>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container">

                <div class="clearfix"></div>
                <div class="card">
                    <div class="card-body">
                        @include('hr::report_types.LeaveHoldaybalance.table')
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
