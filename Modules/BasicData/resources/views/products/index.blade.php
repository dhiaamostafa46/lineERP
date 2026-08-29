@extends('layouts.app')

@section('title', request('type') == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.products'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0">
                    {{ request('type') == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.products') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('basicdata.products.index') }}" class="text-muted text-hover-primary">
                            @lang('basicdata::lang.basicdata')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('basicdata.products.index', ['type' => request('type', 1)]) }}" class="text-muted text-hover-primary">
                            {{ request('type') == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.products') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Header Actions: Icon-Only Buttons with Tooltips -->
            <div class="d-flex align-items-center gap-2">
                @can('basicdata.products.import')
                    <a class="btn btn-sm btn-icon btn-light rounded-circle shadow-xs" 
                       href="{{ route('basicdata.products.import') }}" 
                       title="@lang('crud.import')" 
                       data-bs-toggle="tooltip" 
                       wire:navigate>
                        <i class="fa-solid fa-file-import fs-7 text-primary"></i>
                    </a>
                @endcan

                @can('basicdata.products.create')
                    <!-- Add Product Button (Icon Only) -->
                    <button type="button" 
                            class="btn btn-sm btn-icon btn-primary front-btn-primary rounded-circle shadow-xs" 
                            title="@lang('crud.add_new') - @lang('basicdata::models/db_products.product')" 
                            data-bs-toggle="tooltip"
                            x-on:click="$dispatch('openCreateModal', { type: 1 })" 
                            onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 1 })">
                        <i class="fa-solid fa-box-open fs-7"></i>
                    </button>

                    <!-- Add Service Button (Icon Only) -->
                    <button type="button" 
                            class="btn btn-sm btn-icon btn-success rounded-circle shadow-xs" 
                            style="background: #10b981; border: none; color: #fff;"
                            title="@lang('crud.add_new') - @lang('basicdata::models/db_products.service')" 
                            data-bs-toggle="tooltip"
                            x-on:click="$dispatch('openCreateModal', { type: 2 })" 
                            onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 2 })">
                        <i class="fa-solid fa-bell-concierge fs-7"></i>
                    </button>
                @endcan
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards Row -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_products.products')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalProductsCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">
                                <i class="fa-solid fa-boxes-stacked fs-9 me-1"></i> @lang('basicdata::lang.all')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::lang.active')</span>
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
                        <span class="front-stat-title">@lang('basicdata::models/db_products.services')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-primary">{{ $totalServicesCount ?? 0 }}</span>
                            <span class="badge bg-light-info text-info front-stat-badge">
                                <i class="fa-solid fa-handshake-angle fs-9 me-1"></i> @lang('basicdata::models/db_products.service')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_categories.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-dark">{{ count($categories ?? []) }}</span>
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
                    'title' => request('type') == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.products'),
                    'placeholder' => __('basicdata::models/db_products.placeholders.name'),
                    'excelRoute' => 'basicdata.products.excel',
                    'pdfRoute' => 'basicdata.products.pdf',
                    'hiddenInputs' => request('type') ? ['type' => request('type')] : [],
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
