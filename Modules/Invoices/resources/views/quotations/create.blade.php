@extends('layouts.app')

@section('title', __('crud.add_new') . ' ' . __('invoices::models/quotations.singular'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('crud.add_new') @lang('invoices::models/quotations.singular')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('invoices.quotations.index') }}" class="text-muted text-hover-primary">
                            @lang('invoices::models/quotations.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('crud.add_new')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('invoices.quotations.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('adminlte-templates::common.errors')
            <div class="clearfix"></div>
            <div class="card">
                {!! Form::open(['route' => 'invoices.quotations.store', 'files' => true]) !!}
                <div class="card-body">
                    <div class="row">
                        @include('invoices::quotations.fields')
                    </div>
                </div>
                <div class="card-footer py-4 text-end">
                    <a href="{{ route('invoices.quotations.index') }}" class="btn btn-sm btn-secondary me-2">
                        @lang('crud.cancel')
                    </a>
                    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
