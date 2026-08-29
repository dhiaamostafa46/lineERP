@extends('layouts.app')

@section('title', __('basicdata::models/db_products.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
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
                    <li class="breadcrumb-item text-muted">@lang('basicdata::lang.basicdata')</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('basicdata::models/db_products.plural')</li>
                </ul>
            </div>

            <!-- Header Actions -->
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
                            <li>
                                <a class="dropdown-item fs-7 py-2 d-flex align-items-center gap-2" 
                                   href="javascript:void(0)" 
                                   x-on:click="$dispatch('openCreateModal', { type: 1 })" 
                                   onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 1 })">
                                    <i class="fa-solid fa-box text-primary fs-8"></i>
                                    <span>@lang('basicdata::models/db_products.product')</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item fs-7 py-2 d-flex align-items-center gap-2" 
                                   href="javascript:void(0)" 
                                   x-on:click="$dispatch('openCreateModal', { type: 2 })" 
                                   onclick="if(window.Livewire) Livewire.dispatch('openCreateModal', { type: 2 })">
                                    <i class="fa-solid fa-bell-concierge text-success fs-8"></i>
                                    <span>@lang('basicdata::models/db_products.service')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
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
                    'title' => __('basicdata::models/db_products.plural'),
                    'placeholder' => __('basicdata::models/db_products.placeholders.name'),
                    'excelRoute' => 'basicdata.products.excel',
                    'pdfRoute' => 'basicdata.products.pdf',
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
