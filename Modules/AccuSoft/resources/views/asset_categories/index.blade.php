@extends('layouts.app')

@section('title', __('accusoft::models/as_asset_categories.plural'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('accusoft::models/as_asset_categories.plural')
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">@lang('accusoft::models/as_asset_categories.plural')</li>
                    </ul>
                </div>
                <!--end::Page title-->

                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button type="button" class="icon-btn btn-btc" onclick="window.print()">
                        <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                    </button>
                    @can('accusoft.AssetCategory.copy')
                        <button type="button" class="icon-btn btn-btc copy-table" data-target="#assetCategories-table">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assetcategories.csv') }}">
                        <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                    </a>
                    <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assetcategories.excel') }}">
                        <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                    </a>
                    <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assetcategories.pdf') }}">
                        <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                    </a>
                    <a class="btn btn-sm btn-primary float-right" href="{{ route('accusoft.assetcategories.create') }}">
                        <i class="fa-solid fa-plus"></i>
                        @lang('crud.add_new')
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

                <div class="clearfix"></div>
                
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
                        {!! Form::open(['route' => 'accusoft.assetcategories.index', 'method' => 'GET']) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {!! Form::label('name', __('accusoft::models/as_asset_categories.fields.name') . ':') !!}
                                    {!! Form::text('name', request('name'), ['class' => 'form-control', 'placeholder' => __('crud.search')]) !!}
                                </div>
                                
                                <div class="form-group col-md-3">
                                    {!! Form::label('default_depreciation_method', __('accusoft::models/as_asset_categories.fields.default_depreciation_method') . ':') !!}
                                    {!! Form::select('default_depreciation_method', ['' => __('lang.all')] + $depreciationMethods, request('default_depreciation_method'), ['class' => 'form-select', 'data-control' => 'select2']) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    {!! Form::label('has_accounting_effect', __('accusoft::models/as_asset_categories.fields.has_accounting_effect') . ':') !!}
                                    {!! Form::select('has_accounting_effect', ['' => __('lang.all'), '1' => __('lang.yes'), '0' => __('lang.no')], request('has_accounting_effect'), ['class' => 'form-select', 'data-control' => 'select2']) !!}
                                </div>

                                <div class="form-group col-md-3">
                                    {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                    {!! Form::select('pagination', config('statusSystem.pagination', [10 => 10, 25 => 25, 50 => 50, 100 => 100]), request('pagination') ?? null, [
                                        'class' => 'form-select',
                                        'data-control' => 'select2'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-4">
                            <button type="submit" class="btn btn-sm btn-search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                @lang('crud.search')
                            </button>
                            <a class="btn btn-sm btn-primary float-right"
                                href="{{ route('accusoft.assetcategories.index') }}">
                                <i class="fa-solid fa-circle-xmark"></i>
                                @lang('crud.reset')
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <div class="card">
                    @include('accusoft::asset_categories.table')
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
@endsection
