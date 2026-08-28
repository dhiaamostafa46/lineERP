@extends('layouts.app')

@section('title', __('basicdata::models/db_products.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar / Page Header-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0">
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

            <!-- Header Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                @can('basicdata.products.import')
                    <a class="btn btn-sm front-btn-filter" href="{{ route('basicdata.products.import') }}" wire:navigate>
                        <i class="fa-solid fa-file-import fs-8 text-primary"></i>
                        @lang('crud.import')
                    </a>
                @endcan

                @can('basicdata.products.create')
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm front-btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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

        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- ============================================================== -->
            <!-- Front Dashboard Exact Card Layout -->
            <!-- ============================================================== -->
            <div class="front-card">
                
                <!-- Front Card Header: Search + Export + Filter Dropdown -->
                <div class="front-card-header">
                    
                    <!-- Search Input -->
                    <div class="position-relative flex-grow-1" style="max-width: 320px;">
                        {!! Form::open(['route' => 'basicdata.products.index', 'method' => 'GET', 'id' => 'frontSearchForm']) !!}
                            <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted fs-7"></i>
                            <input type="text" 
                                   name="name" 
                                   value="{{ request('name') }}" 
                                   class="form-control front-search-input" 
                                   placeholder="@lang('crud.search') بالاسم أو الكود..." 
                                   onchange="document.getElementById('frontSearchForm').submit()" />
                        {!! Form::close() !!}
                    </div>

                    <!-- Right Toolbar Controls: Export + Filter -->
                    <div class="d-flex align-items-center gap-2">
                        
                        <!-- 1. Export Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="front-btn-export dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-arrow-down-to-bracket fs-8"></i>
                                <span>تصدير (Export)</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2 rounded-2" style="font-size: 13px;">
                                @can('basicdata.products.print')
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="window.print(); return false;">
                                            <i class="fa-solid fa-print text-muted fs-7"></i>
                                            <span>@lang('crud.print')</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('basicdata.products.copy')
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 copy-table" href="#" data-target="#db-products-table">
                                            <i class="fa-solid fa-copy text-muted fs-7"></i>
                                            <span>نسخ (Copy)</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('basicdata.products.excel')
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('basicdata.products.excel') }}">
                                            <i class="fa-solid fa-file-excel text-success fs-7"></i>
                                            <span>Excel</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('basicdata.products.pdf')
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('basicdata.products.pdf') }}">
                                            <i class="fa-solid fa-file-pdf text-danger fs-7"></i>
                                            <span>PDF</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>

                        <!-- 2. Front Filter Dropdown (Exact Popover Modal) -->
                        <div class="dropdown">
                            @php
                                $activeFiltersCount = (request('name') ? 1 : 0) + (request('status') ? 1 : 0) + (request('type') ? 1 : 0);
                            @endphp
                            <button type="button" 
                                    class="front-btn-filter dropdown-toggle {{ $activeFiltersCount > 0 ? 'show text-primary border-primary' : '' }}" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside"
                                    aria-expanded="false">
                                <i class="fa-solid fa-sliders fs-8"></i>
                                <span>فلترة (Filter)</span>
                                @if($activeFiltersCount > 0)
                                    <span class="badge rounded-pill bg-primary text-white fs-9 px-2 py-0">{{ $activeFiltersCount }}</span>
                                @endif
                            </button>

                            <!-- Front Filter Dropdown Card Body -->
                            <div class="dropdown-menu dropdown-menu-end front-filter-dropdown">
                                {!! Form::open(['route' => 'basicdata.products.index', 'method' => 'GET']) !!}
                                    <!-- Filter Header -->
                                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                        <h5 class="front-filter-title">فلترة المنتجات (Filter)</h5>
                                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary p-0" onclick="this.closest('.dropdown-menu').classList.remove('show')">
                                            <i class="fa-solid fa-xmark text-muted fs-6"></i>
                                        </button>
                                    </div>

                                    <!-- Filter: Type Radio/Check -->
                                    <div class="mb-3">
                                        <span class="front-filter-section-title">نوع الصنف (TYPE)</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input" type="radio" name="type" id="typeAll" value="" {{ !request('type') ? 'checked' : '' }}>
                                                <label class="form-check-label fs-7 text-gray-700" for="typeAll">الكل</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input" type="radio" name="type" id="typeProd" value="1" {{ request('type') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label fs-7 text-gray-700" for="typeProd">منتج</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input" type="radio" name="type" id="typeServ" value="2" {{ request('type') == '2' ? 'checked' : '' }}>
                                                <label class="form-check-label fs-7 text-gray-700" for="typeServ">خدمة</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Filter: Status Select -->
                                    <div class="mb-3">
                                        <span class="front-filter-section-title">الحالة (STATUS)</span>
                                        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="request('status')"></x-select2-input>
                                    </div>

                                    <!-- Filter: Pagination Select -->
                                    <div class="mb-4">
                                        <span class="front-filter-section-title">عدد الصفوف (PAGINATION)</span>
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, ['class' => 'form-select form-select-sm fs-7']) !!}
                                    </div>

                                    <!-- Filter Actions -->
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary front-btn-primary w-100 justify-content-center">
                                            تطبيق (Apply)
                                        </button>
                                        @if($activeFiltersCount > 0)
                                            <a href="{{ route('basicdata.products.index') }}" class="btn btn-light front-btn-filter" title="إعادة تعيين">
                                                <i class="fa-solid fa-rotate-left fs-8"></i>
                                            </a>
                                        @endif
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Front Table Body -->
                @include('basicdata::products.table')

            </div>
            <!-- ============================================================== -->

        </div>
    </div>
    <!--end::Content-->

</div>
@endsection
