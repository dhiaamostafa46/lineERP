@extends('layouts.app')

@section('title', __('hr::models/hr_attendances.attendance_movement'))

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
                        <h1>@lang('hr::models/hr_attendances.attendance_movement')</h1>
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
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('hr::models/hr_attendances.attendance_movement')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- <a class="btn btn-sm btn-primary float-right" href="{{ route('hr.Attendance.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    @lang('crud.add_new')
                </a> --}}
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>


        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card shadow-sm my-3">
                    <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse" data-bs-target="#kt_import_card_collapsible" aria-expanded="false">
                        <h3 class="card-title">
                            <i class="fa-solid fa-file-import fs-2 me-2"></i>
                            @lang('crud.import') @lang('hr::models/hr_attendances.plural')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_import_card_collapsible" class="collapse">
                        {!! Form::open(['route' => 'hr.attendance.import', 'class' => 'form', 'files' => true, 'id' => 'import_form']) !!}
                        <div class="card-body">
                            <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-information-5 fs-2hx text-info me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">@lang('hr::models/hr_employees.fields.template')</h4>
                                    <span>يمكنك تحميل ملف النموذج وإدخال البيانات ثم رفعه مرة أخرى.</span>
                                </div>
                                <a href="{{ asset('uploads/files/AttendanceDemo.xlsx') }}" class="btn btn-sm btn-info ms-auto" download>
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    @lang('crud.downloadSample')
                                </a>
                            </div>

                            <div class="form-group">
                                {!! Form::label('file', __('hr::models/hr_employees.fields.file') . ':', ['class' => 'form-label required']) !!}
                                {!! Form::file('file', ['class' => 'form-control form-control-solid', 'required' => 'required', 'accept' => '.xlsx, .xls, .csv']) !!}
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="submit_button">
                                <span class="indicator-label">
                                    @lang('crud.import')
                                </span>
                                <span class="indicator-progress">
                                    @lang('crud.please_wait')
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                @if (true)
                    <div class="card shadow-sm my-3 no-print">
                        <div class="card-header collapsible cursor-pointer rotate {{ request()->has('pagination') ? 'active' : 'collapsed' }}"
                            data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible"
                            aria-expanded="{{ request()->has('pagination') ? 'true' : 'false' }}">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible"
                            class="collapse {{ request()->has('pagination') ? 'show' : '' }}">
                            {!! Form::open(['route' => 'hr.attendance.movement', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">

                                    <!-- Employee Id Field -->
                                    <div class="form-group col-sm-4 mb-3">
                                        {!! Form::label('employee_id', __('hr::models/hr_custodies.fields.employee_id') . ':') !!}
                                        <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                            :selected_id="request('employee_id')">
                                        </x-select2-input>
                                    </div>
                                    <!-- Start Date Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('start_date', __('Start Date') . ':') !!}
                                        {!! Form::date('start_date', request('start_date'), ['class' => 'form-control']) !!}
                                    </div>

                                    <!-- End Date Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('end_date', __('End Date') . ':') !!}
                                        {!! Form::date('end_date', request('end_date'), ['class' => 'form-control']) !!}
                                    </div>




                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-danger float-right" href="{{ route('hr.report_types.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>

                    </div>
                @endif
                <div class="card">
                    @include('hr::Attendance.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
    <script>
        const form = document.getElementById('import_form');
        const submitButton = document.getElementById('submit_button');

        if(form) {
            form.addEventListener('submit', function (e) {
                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;
            });
        }
    </script>
@endpush
