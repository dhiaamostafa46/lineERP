@extends('layouts.app')

@section('title', __('basicdata::models/db_products.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bolder fs-4 my-0">
                    @lang('basicdata::models/db_products.plural')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary" wire:navigate>
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('basicdata::models/db_products.plural')</li>
                </ul>
            </div>
            <!--end::Page title-->

            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2">
                @can('basicdata.products.import')
                    <a class="btn btn-sm btn-light-primary fw-bold" href="{{ route('basicdata.products.import') }}" wire:navigate>
                        <i class="fa-solid fa-file-import fs-8"></i>
                        @lang('crud.import')
                    </a>
                @endcan

                @can('basicdata.products.create')
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary fw-bold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-plus fs-8"></i>
                            @lang('crud.add_new')
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2 rounded-2">
                            <li><a class="dropdown-item fs-7 py-2" href="{{ route('basicdata.products.create', ['type' => 1]) }}" wire:navigate>@lang('basicdata::models/db_products.fields.product')</a></li>
                            <li><a class="dropdown-item fs-7 py-2" href="{{ route('basicdata.products.create', ['type' => 2]) }}" wire:navigate>@lang('basicdata::models/db_products.fields.service')</a></li>
                        </ul>
                    </div>
                @endcan
            </div>
            <!--end::Actions-->

        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- ============================================================== -->
            <!-- Unified Smart Datatable Card (البطاقة الذكية المدمجة المتكاملة) -->
            <!-- ============================================================== -->
            <div class="card shadow-xs border" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
                
                <!-- 1. Card Top Toolbar: Search + Filter Toggle + Export Tools -->
                <div class="card-header border-0 pt-4 pb-3 px-5 d-flex align-items-center justify-content-between gap-3 flex-wrap bg-white">
                    
                    <!-- Left: Quick Search & Filter Trigger -->
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 480px;">
                        {!! Form::open(['route' => 'basicdata.products.index', 'method' => 'GET', 'class' => 'd-flex align-items-center gap-2 w-100']) !!}
                            <div class="position-relative flex-grow-1">
                                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted fs-7"></i>
                                <input type="text" 
                                       name="name" 
                                       value="{{ request('name') }}" 
                                       class="form-control form-control-sm rounded-2 ps-9 fs-7" 
                                       placeholder="@lang('crud.search') بالاسم أو الكود..." 
                                       style="background: #f8fafc; border: 1px solid #e2e8f0; height: 38px;" />
                            </div>

                            <button type="button" 
                                    class="btn btn-sm btn-light border d-flex align-items-center gap-2 text-nowrap" 
                                    style="height: 38px; background: #f8fafc; border-color: #e2e8f0 !important;"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#advancedFilterPanel" 
                                    aria-expanded="{{ request()->hasAny(['status', 'pagination']) ? 'true' : 'false' }}">
                                <i class="fa-solid fa-sliders text-primary fs-7"></i>
                                <span class="fs-7 fw-semibold">الفلاتر</span>
                                @if(request()->hasAny(['name', 'status']))
                                    <span class="badge badge-circle badge-primary w-15px h-15px fs-9 p-0 d-flex align-items-center justify-content-center">✓</span>
                                @endif
                            </button>
                        {!! Form::close() !!}
                    </div>

                    <!-- Right: Quick Export Buttons (Print, Copy, Excel, PDF) -->
                    <div class="d-flex align-items-center gap-1">
                        @can('basicdata.products.print')
                            <button type="button" 
                                    class="btn btn-icon btn-sm btn-light border w-34px h-34px rounded-2" 
                                    onclick="window.print()" 
                                    title="@lang('crud.print')">
                                <i class="fa-solid fa-print text-gray-600 fs-7"></i>
                            </button>
                        @endcan

                        @can('basicdata.products.copy')
                            <button type="button" 
                                    class="btn btn-icon btn-sm btn-light border w-34px h-34px rounded-2 copy-table" 
                                    data-target="#db-products-table" 
                                    title="نسخ">
                                <i class="fa-solid fa-copy text-gray-600 fs-7"></i>
                            </button>
                        @endcan

                        @can('basicdata.products.excel')
                            <a href="{{ route('basicdata.products.excel') }}" 
                               class="btn btn-icon btn-sm btn-light border w-34px h-34px rounded-2" 
                               title="Excel">
                                <i class="fa-solid fa-file-excel text-success fs-7"></i>
                            </a>
                        @endcan

                        @can('basicdata.products.pdf')
                            <a href="{{ route('basicdata.products.pdf') }}" 
                               class="btn btn-icon btn-sm btn-light border w-34px h-34px rounded-2" 
                               title="PDF">
                                <i class="fa-solid fa-file-pdf text-danger fs-7"></i>
                            </a>
                        @endcan
                    </div>

                </div>

                <!-- 2. Collapsible Advanced Filter Drawer inside the Card -->
                <div class="collapse {{ request()->hasAny(['status', 'pagination']) ? 'show' : '' }}" id="advancedFilterPanel">
                    <div class="p-4 border-top border-bottom" style="background: #f8fafc;">
                        {!! Form::open(['route' => 'basicdata.products.index', 'method' => 'GET']) !!}
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fs-8 fw-bold text-gray-700">@lang('basicdata::models/db_products.fields.name')</label>
                                    <input type="text" name="name" value="{{ request('name') }}" class="form-control form-control-sm fs-7" placeholder="اسم الصنف أو الكود" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-8 fw-bold text-gray-700">@lang('basicdata::models/db_products.fields.status')</label>
                                    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="request('status')"></x-select2-input>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fs-8 fw-bold text-gray-700">@lang('crud.pagination')</label>
                                    {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, ['class' => 'form-select form-select-sm fs-7']) !!}
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                                        <i class="fa-solid fa-filter fs-8"></i> تطبيق
                                    </button>
                                    <a href="{{ route('basicdata.products.index') }}" class="btn btn-sm btn-light border" title="إعادة تعيين">
                                        <i class="fa-solid fa-rotate-left fs-8"></i>
                                    </a>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <!-- 3. Integrated Table Component -->
                @include('basicdata::products.table')

            </div>
            <!-- ============================================================== -->
            <!-- End Unified Smart Datatable Card -->
            <!-- ============================================================== -->

        </div>
    </div>
    <!--end::Content-->

</div>
@endsection
