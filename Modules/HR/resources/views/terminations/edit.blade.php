@extends('layouts.app')

@section('title', __('hr::models/hrTerminations.singular'))

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
                    @lang('crud.edit') @lang('hr::models/hrTerminations.singular')
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted
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
                        <a href="{{ route('hr.terminations.index') }}" class="text-muted text-hover-primary">
                            @lang('hr::models/hr_terminations.plural')
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
                        @lang('crud.edit')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('hrTerminations.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
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
            @include('adminlte-templates::common.errors')
            <div class="clearfix"></div>

            <div class="card">

                {!! Form::model($termination, ['route' => ['hr.terminations.update', $termination->id], 'method' =>
                'patch','files' => true]) !!}

                <div class="card-body">
                    <div class="row">
                        @include('hr::terminations.fields')
                    </div>
                </div>

                <div class="card-footer py-4 text-end">
                    <a href="{{ route('hr.terminations.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel') </a>
                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary']) !!}
                </div>

                {!! Form::close() !!}

            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
@endsection