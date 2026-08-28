@extends('layouts.app')

@section('title', __('basicdata::models/db_categories.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0">
                    @lang('basicdata::models/db_categories.plural')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary" wire:navigate>
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('basicdata::models/db_categories.plural')</li>
                </ul>
            </div>

            <!-- Header Actions -->
            <div class="d-flex align-items-center gap-2">
                @can('basicdata.categories.create')
                    <a class="btn btn-sm front-btn-primary" href="{{ route('basicdata.categories.create') }}" wire:navigate>
                        <i class="fa-solid fa-plus fs-8"></i>
                        @lang('crud.add_new')
                    </a>
                @endcan
            </div>

        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- 1. Top KPI Stat Cards -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">إجمالي الفئات (TOTAL CATEGORIES)</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">الكل</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">الفئات النشطة (ACTIVE)</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activeCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-success text-success front-stat-badge">
                                <span class="front-legend-indicator bg-success"></span> نشط
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">الفئات الرئيسية (MAIN)</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-primary">{{ $mainCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-info text-info front-stat-badge">رئيسية</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">الفئات الفرعية (SUB)</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-dark">{{ $subCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-dark text-gray-700 front-stat-badge">فرعية</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Front Card -->
            <div class="front-card">
                <div class="front-card-header">
                    
                    <!-- Search Input -->
                    <div class="position-relative flex-grow-1" style="max-width: 340px;">
                        {!! Form::open(['route' => 'basicdata.categories.index', 'method' => 'GET', 'id' => 'frontSearchCatForm']) !!}
                            <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted fs-7"></i>
                            <input type="text" 
                                   name="name" 
                                   value="{{ request('name') }}" 
                                   class="form-control front-search-input" 
                                   placeholder="البحث باسم الفئة..." 
                                   onchange="document.getElementById('frontSearchCatForm').submit()" />
                        {!! Form::close() !!}
                    </div>

                    <!-- Right Controls: Export & Filter -->
                    <div class="d-flex align-items-center gap-2">
                        
                        <!-- Export Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="front-btn-export dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-arrow-down-to-bracket fs-8"></i>
                                <span>Export</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2 rounded-2" style="font-size: 13px;">
                                @can('basicdata.categories.print')
                                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="window.print(); return false;"><i class="fa-solid fa-print text-muted fs-7"></i><span>@lang('crud.print')</span></a></li>
                                @endcan
                                @can('basicdata.categories.copy')
                                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2 copy-table" href="#" data-target="#db-categories-table"><i class="fa-solid fa-copy text-muted fs-7"></i><span>Copy Table</span></a></li>
                                @endcan
                                @can('basicdata.categories.excel')
                                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('basicdata.categories.excel') }}"><i class="fa-solid fa-file-excel text-success fs-7"></i><span>Excel</span></a></li>
                                @endcan
                                @can('basicdata.categories.pdf')
                                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('basicdata.categories.pdf') }}"><i class="fa-solid fa-file-pdf text-danger fs-7"></i><span>PDF</span></a></li>
                                @endcan
                            </ul>
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="dropdown">
                            @php
                                $activeCatFilters = (request('name') ? 1 : 0) + (request('status') ? 1 : 0);
                            @endphp
                            <button type="button" 
                                    class="front-btn-filter dropdown-toggle {{ $activeCatFilters > 0 ? 'show text-primary border-primary' : '' }}" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside"
                                    aria-expanded="false">
                                <i class="fa-solid fa-sliders fs-8"></i>
                                <span>Filter</span>
                                @if($activeCatFilters > 0)
                                    <span class="badge rounded-pill bg-primary text-white fs-9 px-2 py-0 ms-1">{{ $activeCatFilters }}</span>
                                @endif
                            </button>

                            <div class="dropdown-menu dropdown-menu-end front-filter-dropdown shadow-lg">
                                {!! Form::open(['route' => 'basicdata.categories.index', 'method' => 'GET']) !!}
                                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                        <h5 class="front-filter-title">Filter categories</h5>
                                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary p-0" onclick="this.closest('.dropdown-menu').classList.remove('show')">
                                            <i class="fa-solid fa-xmark text-muted fs-6"></i>
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <span class="front-filter-section-title">STATUS</span>
                                        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="request('status')"></x-select2-input>
                                    </div>

                                    <div class="mb-4">
                                        <span class="front-filter-section-title">PAGINATION</span>
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, ['class' => 'form-select form-select-sm fs-7']) !!}
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary front-btn-primary w-100 justify-content-center">Apply</button>
                                        @if($activeCatFilters > 0)
                                            <a href="{{ route('basicdata.categories.index') }}" class="btn btn-light front-btn-filter" title="Reset"><i class="fa-solid fa-rotate-left fs-8"></i></a>
                                        @endif
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Table Component -->
                @include('basicdata::categories.table')
            </div>

        </div>
    </div>

    <!-- Floating Bulk Actions Bar -->
    <x-bulk-action-bar route="{{ route('basicdata.categories.bulkDelete') }}" />

</div>
@endsection
