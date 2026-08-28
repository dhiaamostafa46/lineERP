@extends('layouts.app')

@section('title', __('basicdata::models/db_products.plural'))

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
                        <h1>@lang('basicdata::models/db_products.plural')</h1>
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
                            @lang('basicdata::models/db_products.plural')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->


                  <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('basicdata.products.print')
                        <button type="button" class="icon-btn"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;"
                            onclick="window.print()">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('basicdata.products.copy')
                        <button type="button" class="icon-btn copy-table" data-target="#db-products-table"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('basicdata.products.csv')
                        <a type="button" class="icon-btn" href="{{ route('basicdata.products.csv') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة Excel -->
                    @can('basicdata.products.excel')
                        <a type="button" class="icon-btn"   href="{{ route('basicdata.products.excel') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة النسخ -->
                    @can('basicdata.products.pdf')
                        <a type="button" class="icon-btn"  href="{{ route('basicdata.products.pdf') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('basicdata.products.import')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('basicdata.products.import') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('crud.import')
                        </a>
                    @endcan

                    @can('basicdata.products.create')
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-plus"></i>
                                @lang('crud.add_new')
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('basicdata.products.create', ['type' => 1]) }}">@lang('basicdata::models/db_products.fields.product')</a></li>
                                <li><a class="dropdown-item" href="{{ route('basicdata.products.create', ['type' => 2]) }}">@lang('basicdata::models/db_products.fields.service')</a></li>
                            </ul>
                        </div>
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
                    <div class="card shadow-xs my-3 border" id="card-filter">
                        <div class="card-header collapsible cursor-pointer rotate d-flex align-items-center justify-content-between py-3 px-5" 
                             data-bs-toggle="collapse"
                             data-bs-target="#kt_docs_card_collapsible" aria-expanded="true">
                            <h3 class="card-title d-flex align-items-center gap-2 m-0">
                                <i class="fa-solid fa-filter fs-5 text-primary"></i>
                                <span class="fw-bold fs-6 text-gray-900">@lang('crud.search')</span>
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-3 text-gray-600"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible" class="collapse show">
                            {!! Form::open(['route' => 'basicdata.products.index', 'method' => 'GET']) !!}
                            <div class="card-body p-5">
                                <div class="row g-3">
                                    <!-- Name Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('name', __('basicdata::models/db_products.fields.name') . ':', ['class' => 'fw-semibold text-gray-700 fs-7 mb-1']) !!}
                                        {!! Form::text('name', request('name'), ['class' => 'form-control fs-7', 'placeholder' => __('basicdata::models/db_products.fields.name')]) !!}
                                    </div>

                                    <div class="form-group col-md-4">
                                        {!! Form::label('status', __('basicdata::models/db_products.fields.status') . ':', ['class' => 'fw-semibold text-gray-700 fs-7 mb-1']) !!}
                                        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
                                            :selected_id="old('status')">
                                        </x-select2-input>
                                    </div>
                                    <!-- pagination Field -->
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('pagination', __('crud.pagination') . ':', ['class' => 'fw-semibold text-gray-700 fs-7 mb-1']) !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, [
                                            'class' => 'form-select fs-7',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-end gap-2 py-3 px-5 border-top">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-magnifying-glass fs-8"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-secondary"
                                    href="{{ route('basicdata.products.index') }}">
                                    <i class="fa-solid fa-rotate-left fs-8"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('basicdata::products.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
