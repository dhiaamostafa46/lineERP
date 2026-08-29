@extends('layouts.app')

@section('title', __('basicdata::models/db_units.plural'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    @include('basicdata::layouts.partials._page_toolbar', [
        'title' => __('basicdata::models/db_units.plural'),
        'icon' => 'fa-solid fa-scale-balanced',
        'permission' => 'basicdata.units.create'
    ])

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- KPI Stat Cards -->
            <div class="row g-3 g-lg-4 mb-4">
                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::models/db_units.plural')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value">{{ $totalUnitsCount ?? 0 }}</span>
                            <span class="badge bg-light-primary text-primary front-stat-badge">
                                <i class="fa-solid fa-scale-balanced fs-9 me-1"></i> @lang('basicdata::lang.all')
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="front-stat-card">
                        <span class="front-stat-title">@lang('basicdata::lang.active')</span>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="front-stat-value text-success">{{ $activeUnitsCount ?? 0 }}</span>
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
                    'route' => 'basicdata.units.index',
                    'title' => __('basicdata::models/db_units.plural'),
                    'placeholder' => __('basicdata::models/db_units.fields.name'),
                    'excelRoute' => 'basicdata.units.excel',
                    'pdfRoute' => 'basicdata.units.pdf',
                    'statuses' => $statuses ?? []
                ])

                <div class="front-card-body">
                    @include('basicdata::units.table')
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Livewire Unit Modal -->
<livewire:basicdata::units.unit-modal />
@endsection
