@extends('layouts.app')

@section('title', __('hr::models/hr_places.plural'))

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
                    <h1>@lang('hr::models/hr_places.plural')</h1>
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
                        @lang('hr::models/hr_places.plural')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a class="btn btn-sm btn-primary float-right" href="{{ route('hr.Place.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    @lang('crud.add_new')
                </a>
                <a class="btn btn-sm btn-secondary float-right" href="{{ route('hr.Place.export') }}">
                    <i class="fa-solid fa-file-export"></i>
                    @lang('crud.export')
                </a>
                <button type="button" class="btn btn-sm btn-secondary float-right" data-bs-toggle="modal"
                    data-bs-target="#import_modal">
                    <i class="fa-solid fa-file-import"></i>
                    @lang('crud.import')
                </button>
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
                <div id="kt_docs_card_collapsible" class="collapse {{ request()->has('pagination') ? 'show' : '' }}">
                    {!! Form::open(['route' => 'hr.Place.index', 'method' => 'GET']) !!}
                    <div class="card-body">
                        <div class="row">
                            <!-- Employee Field -->
                            {{-- <div class="form-group col-sm-4 mb-4">
                                {!! Form::label('employee_id', __('hr::models/hr_places.fields.employee_id') . ':') !!}
                                <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                    :selected_id="old('employee_id')">
                                </x-select2-input>
                            </div> --}}

                            <!-- Status Field -->
                            <div class="form-group col-sm-4 mb-4">
                                {!! Form::label('status', __('hr::models/hr_places.fields.status') . ':') !!}
                                {!! Form::select('status', $statuses, request('status'), ['class' => 'form-control',
                                'placeholder'
                                => __('hr::lang.select_status')]) !!}
                            </div>




                            <!-- pagination Field -->
                            <div class="form-group col-sm-4 mb-4">
                                {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination'),
                                ['class' =>
                                'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-4">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            @lang('crud.search')
                        </button>
                        <a class="btn btn-sm btn-danger float-right" href="{{ route('hr.Place.index') }}">
                            <i class="fa-solid fa-circle-xmark"></i>
                            @lang('crud.reset')
                        </a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            @endif
            <div class="card">
                @include('hr::Place.table')
            </div>
        </div>
    </div>
    <!--end::Content-->
    <div class="modal fade" tabindex="-1" id="import_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">@lang('crud.import') @lang('hr::models/hr_places.plural')</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'hr.Place.import', 'method' => 'POST', 'files' => true]) !!}
                    <div class="form-group mb-5">
                        {!! Form::label('file', __('crud.file') . ':') !!}
                        {!! Form::file('file', ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">@lang('crud.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('crud.import')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
