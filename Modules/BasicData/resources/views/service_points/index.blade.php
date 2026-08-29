@extends('layouts.app')

@section('title', __('basicdata::models/db_service_points.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    @include('basicdata::layouts.partials._page_toolbar', [
        'title' => __('basicdata::models/db_service_points.plural'),
        'permission' => 'basicdata.service_points.create'
    ])

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_service_points.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalPointsCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">@lang('basicdata::lang.all')</span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::lang.active')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activePointsCount ?? 0 }}</span>
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
                    'route' => 'basicdata.service_points.index',
                    'title' => __('basicdata::models/db_service_points.plural'),
                    'placeholder' => __('basicdata::models/db_service_points.fields.name'),
                    'excelRoute' => 'basicdata.service_points.excel',
                    'pdfRoute' => 'basicdata.service_points.pdf',
                    'statuses' => $statuses ?? []
                ])

                <div class="front-card-body">
                    @include('basicdata::service_points.table')
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Livewire ServicePoint Modal -->
<livewire:basicdata::service-points.service-point-modal />
@endsection
