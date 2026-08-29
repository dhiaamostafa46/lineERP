@extends('layouts.app')

@section('title', __('basicdata::models/db_kitchens.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    @include('basicdata::layouts.partials._page_toolbar', [
        'title' => __('basicdata::models/db_kitchens.plural'),
        'icon' => 'fa-solid fa-utensils',
        'permission' => 'basicdata.kitchens.create'
    ])

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_kitchens.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalKitchensCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">
                                <i class="fa-solid fa-utensils fs-9 me-1"></i> @lang('basicdata::lang.all')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::lang.active')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activeKitchensCount ?? 0 }}</span>
                            <span class="badge bg-light-success text-success front-stat-badge">
                                <span class="front-legend-indicator bg-success"></span> @lang('basicdata::lang.active')
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Front Card -->
            <div class="front-card">
                @include('basicdata::layouts.partials._table_header', [
                    'route' => 'basicdata.kitchens.index',
                    'title' => __('basicdata::models/db_kitchens.plural'),
                    'placeholder' => __('basicdata::models/db_kitchens.fields.name'),
                    'excelRoute' => 'basicdata.kitchens.excel',
                    'pdfRoute' => 'basicdata.kitchens.pdf',
                    'statuses' => $statuses ?? []
                ])

                <div class="front-card-body">
                    @include('basicdata::kitchens.table')
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Livewire Kitchen Modal -->
<livewire:basicdata::kitchens.kitchen-modal />
@endsection
