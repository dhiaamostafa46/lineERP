@extends('layouts.app')

@section('title', __('hr::models/hr_salaries.plural'))

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
                    <h1>@lang('hr::models/hr_salaries.plural')</h1>
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
                        @lang('hr::models/hr_salaries.plural')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a class="btn btn-sm btn-primary float-right" href="{{ route('hr.salaries.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    @lang('crud.add_new')
                </a>
                 <button type="button" class="btn btn-sm btn-secondary float-right" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_1">
                    <i class="fa-solid fa-file-import"></i>
                    @lang('crud.import')
                </button>

                  <div class="modal fade" tabindex="-1" id="kt_modal_1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="row mb-5">
                                    <div class="form-group col-md-8 col-sm-12 my-auto">
                                        <h2>@lang('hr::models/hr_employees.fields.template')</h2>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <a href="{{ asset('uploads/files/evix_salaries_employee.xlsx') }}"
                                            class="btn btn-sm btn-primary" download>
                                            <i class="fa-solid fa-file"></i>
                                            @lang('crud.download')
                                        </a>
                                    </div>
                                </div>
                                <hr class="mb-5">
                                {!! Form::open(['route' => 'hr.salaries.import', 'class' => 'row', 'files' => true]) !!}
                                <div class="form-group col-sm-8">
                                    {!! Form::label('file', __('hr::models/hr_employees.fields.file') . ':') !!}
                                    {!! Form::file('file', null, ['class' => 'form-control d-none']) !!}
                                </div>
                                <div class="form-group col-sm-4">
                                    {!! Form::button('Import', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-success',
                                    ]) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

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
            <div class="card shadow-sm my-3 ">
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
                    {!! Form::open(['route' => 'hr.salaries.index', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            @isset ($employees)
                            <!-- Employee Id Field -->
                            <div class="form-group col-sm-6 mb-3">
                                {!! Form::label('employee_id', __('hr::models/hr_salaries.fields.employee_id') . ':') !!}
                                <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                    :selected_id="request('employee_id')">
                                </x-select2-input>
                            </div>
                            @endisset

                            <!-- Basic Field -->
                            <div class="form-group col-sm-6 mb-3">
                                {!! Form::label('basic', __('hr::models/hr_salaries.fields.basic') . ':') !!}
                                {!! Form::number('basic', request('basic'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            @lang('crud.search')
                        </button>
                        <a class="btn btn-sm btn-danger float-right" href="{{ route('hr.salaries.index') }}">
                            <i class="fa-solid fa-circle-xmark"></i>
                            @lang('crud.reset')
                        </a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            @endif
            <div class="card">
                @include('hr::salaries.table')
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
