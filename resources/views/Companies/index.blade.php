@extends('layouts.app')

@section('title', __('models/Companies.plural'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <h1>@lang('models/Companies.plural')</h1>
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            @lang('models/Companies.plural')
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('Companies.create')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('Companies.create') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('crud.add_new')
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="clearfix"></div>
                <div class="card shadow-sm my-3 ">
                    <div class="card-header collapsible cursor-pointer rotate {{ request()->has('pagination') ? 'active' : 'collapsed' }}"
                        data-bs-toggle="collapse" data-bs-target="#kt_companies_filter"
                        aria-expanded="{{ request()->has('pagination') ? 'true' : 'false' }}">
                        <h3 class="card-title">
                            <i class="fa-solid fa-filter fs-2 me-2"></i>
                            @lang('crud.search')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_companies_filter" class="collapse {{ request()->has('pagination') ? 'show' : '' }}">
                        {!! Form::open(['route' => 'Companies.index', 'method' => 'GET']) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-4 mb-4">
                                    {!! Form::label('name', __('models/Companies.fields.name') . ':') !!}
                                    {!! Form::text('name', request('name'), ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-4 mb-4">
                                    {!! Form::label('code', __('models/Companies.fields.code') . ':') !!}
                                    {!! Form::text('code', request('code'), ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-4 mb-4">
                                    {!! Form::label('city_id', __('models/Companies.fields.city') . ':') !!}
                                    {!! Form::select(
                                        'city_id',
                                        ['' => __('models/Companies.placeholders.select_city')] + $cities->pluck('name', 'id')->all(),
                                        request('city_id'),
                                        ['class' => 'form-control'],
                                    ) !!}
                                </div>
                                <div class="form-group col-sm-4 mb-4">
                                    {!! Form::label('status', __('models/Companies.fields.status') . ':') !!}
                                    {!! Form::select('status', $statuses, request('status'), [
                                        'class' => 'form-control',
                                        'placeholder' => __('hr::lang.select_status'),
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-4 mb-4">
                                    {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                    {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination'), [
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
                            <a class="btn btn-sm btn-primary float-right" href="{{ route('Companies.index') }}">
                                <i class="fa-solid fa-circle-xmark"></i>
                                @lang('crud.reset')
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="card">
                    @include('Companies.table')
                </div>
            </div>
        </div>
    </div>
@endsection
