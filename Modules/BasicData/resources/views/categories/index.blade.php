@extends('layouts.app')

@section('title', __('basicdata::models/db_categories.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    @include('basicdata::layouts.partials._page_toolbar', [
        'title' => __('basicdata::models/db_categories.plural'),
        'icon' => 'fa-solid fa-folder-tree',
        'permission' => 'basicdata.categories.create'
    ])

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_categories.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">
                                <i class="fa-solid fa-folder-tree fs-9 me-1"></i> @lang('basicdata::lang.all')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::lang.active')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activeCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-success text-success front-stat-badge">
                                <span class="front-legend-indicator bg-success"></span> @lang('basicdata::lang.active')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_categories.fields.parent_id')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-primary">{{ $mainCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-info text-info front-stat-badge">رئيسية</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="front-stat-card">
                        <span class="front-stat-title">فرعية</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-dark">{{ $subCategoriesCount ?? 0 }}</span>
                            <span class="badge bg-light-dark text-gray-700 front-stat-badge">فرعية</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Front Card -->
            <div class="front-card">
                @include('basicdata::layouts.partials._table_header', [
                    'route' => 'basicdata.categories.index',
                    'title' => __('basicdata::models/db_categories.plural'),
                    'placeholder' => __('basicdata::models/db_categories.fields.name'),
                    'excelRoute' => 'basicdata.categories.excel',
                    'pdfRoute' => 'basicdata.categories.pdf',
                    'statuses' => $statuses ?? []
                ])

                <div class="front-card-body">
                    @include('basicdata::categories.table')
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Livewire Category Modal -->
<livewire:basicdata::categories.category-modal />
@endsection
