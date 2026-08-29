@extends('layouts.app')

@php
    $isService = ($type ?? request('type', 1)) == 2;
    $pageTitle = $isService ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.products');
    $singleTitle = $isService ? __('basicdata::models/db_products.service') : __('basicdata::models/db_products.product');
@endphp

@section('title', $pageTitle)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0 d-flex align-items-center gap-2">
                    @if($isService)
                        <i class="fa-solid fa-bell-concierge text-success fs-4"></i>
                    @else
                        <i class="fa-solid fa-boxes-stacked text-primary fs-4"></i>
                    @endif
                    <span>{{ $pageTitle }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('basicdata.products.index', ['type' => $type]) }}" class="text-muted text-hover-primary">
                            @lang('basicdata::lang.basicdata')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">{{ $pageTitle }}</li>
                </ul>
            </div>

            <!-- Header Actions: Single dedicated Icon-Only Add Button -->
            <div class="d-flex align-items-center gap-2">
                @can('basicdata.products.import')
                    <a class="btn btn-sm btn-icon btn-light rounded-circle shadow-xs" 
                       href="{{ route('basicdata.products.import') }}" 
                       title="@lang('crud.import')" 
                       data-bs-toggle="tooltip" 
                       wire:navigate>
                        <i class="fa-solid fa-file-import fs-7 text-muted"></i>
                    </a>
                @endcan

                @can('basicdata.products.create')
                    @if($isService)
                        <!-- Add Service Button (Icon Only) -->
                        <button type="button" 
                                class="btn btn-sm btn-icon btn-success rounded-circle shadow-xs" 
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #fff;"
                                title="@lang('crud.add_new') {{ $singleTitle }}" 
                                data-bs-toggle="tooltip"
                                x-on:click="$dispatch('openCreateModal', { type: 2 })" 
                                onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 2 })">
                            <i class="fa-solid fa-plus fs-7"></i>
                        </button>
                    @else
                        <!-- Add Product Button (Icon Only) -->
                        <button type="button" 
                                class="btn btn-sm btn-icon btn-primary front-btn-primary rounded-circle shadow-xs" 
                                title="@lang('crud.add_new') {{ $singleTitle }}" 
                                data-bs-toggle="tooltip"
                                x-on:click="$dispatch('openCreateModal', { type: 1 })" 
                                onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 1 })">
                            <i class="fa-solid fa-plus fs-7"></i>
                        </button>
                    @endif
                @endcan
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards Row (Fully Isolated) -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">
                            {{ $isService ? 'إجمالي الخدمات' : 'إجمالي المنتجات' }}
                        </span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value {{ $isService ? 'text-success' : 'text-primary' }}">{{ $totalCount ?? 0 }}</span>
                            <span class="badge {{ $isService ? 'bg-light-success text-success' : 'bg-light-primary text-primary' }} front-stat-badge">
                                @if($isService)
                                    <i class="fa-solid fa-bell-concierge fs-9 me-1"></i> خدمة
                                @else
                                    <i class="fa-solid fa-boxes-stacked fs-9 me-1"></i> منتج
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">
                            {{ $isService ? 'الخدمات النشطة' : 'المنتجات النشطة' }}
                        </span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activeCount ?? 0 }}</span>
                            <span class="badge bg-light-success text-success front-stat-badge">
                                <span class="front-legend-indicator bg-success"></span> @lang('basicdata::lang.active')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">
                            {{ $isService ? 'الخدمات غير النشطة' : 'المنتجات غير النشطة' }}
                        </span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-danger">{{ $inactiveCount ?? 0 }}</span>
                            <span class="badge bg-light-danger text-danger front-stat-badge">
                                <span class="front-legend-indicator bg-danger"></span> @lang('basicdata::lang.inactive')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_categories.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-dark">{{ $totalCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-dark text-gray-700 front-stat-badge">
                                <i class="fa-solid fa-tags fs-9 me-1"></i> @lang('basicdata::models/db_categories.singular')
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Front Card Table -->
            <div class="front-card">
                @include('basicdata::layouts.partials._table_header', [
                    'route' => 'basicdata.products.index',
                    'title' => $pageTitle,
                    'placeholder' => $isService ? 'البحث في الخدمات...' : 'البحث في المنتجات والباركود...',
                    'excelRoute' => 'basicdata.products.excel',
                    'pdfRoute' => 'basicdata.products.pdf',
                    'hiddenInputs' => ['type' => $type],
                    'statuses' => $statuses ?? [],
                    'categoriesList' => $categories ?? []
                ])

                <div class="front-card-body">
                    @include('basicdata::products.table')
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Livewire Product Modal -->
<livewire:basicdata::products.product-modal />
@endsection
