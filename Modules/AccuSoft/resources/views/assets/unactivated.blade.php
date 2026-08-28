@extends('layouts.app')

@section('title', __('accusoft::models/as_asset.unactivated_assets'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                      
                        @lang('accusoft::models/as_asset.unactivated_assets')
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}"
                                class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('accusoft.assets.index') }}"
                                class="text-muted text-hover-primary">@lang('accusoft::models/as_asset.plural')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">@lang('accusoft::models/as_asset.unactivated_assets')</li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a class="btn btn-sm btn-secondary fw-bold" href="{{ route('accusoft.assets.index') }}">
                        <i class="fa-solid fa-arrow-right"></i> @lang('accusoft::models/as_asset.back_to_assets')
                    </a>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                @include('flash::message')

                <div class="row g-5 g-xl-8">
                    <!-- HR Assets Column -->
                    <div class="col-xl-6">
                        <div class="card card-xl-stretch mb-5 mb-xl-8 shadow-sm border-0">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 mb-1 text-primary">
                                        <i class="fa-solid fa-users text-primary me-2"></i>
                                        @lang('accusoft::models/as_asset.hr_assets')
                                    </span>
                                    <span class="text-muted mt-1 fw-semibold fs-7">@lang('accusoft::models/as_asset.hr_assets_subtitle')</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                                <th class="ps-4 min-w-150px rounded-start">
                                                    @lang('accusoft::models/as_asset.fields.name')</th>
                                                <th class="min-w-100px">@lang('accusoft::models/as_asset.fields.code')</th>
                                                <th class="min-w-100px text-end rounded-end pe-4">@lang('crud.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($hrAssets as $hrAsset)
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-40px me-3">
                                                                <div class="symbol-label bg-light-primary text-primary">
                                                                    <i class="fa-solid fa-laptop fs-3"></i>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <span
                                                                    class="text-gray-900 fw-bold fs-6">{{ $hrAsset->name ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light fw-bold fs-7">{{ $hrAsset->code ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="{{ route('accusoft.assets.create', ['assetable_type' => get_class($hrAsset), 'assetable_id' => $hrAsset->id, 'name' => $hrAsset->name ?? '-']) }}"
                                                            class="btn btn-sm btn-light-primary fw-bold transition-all hover-scale">
                                                            <i class="fa-solid fa-bolt text-primary"></i>
                                                            @lang('accusoft::models/as_asset.financial_activation')
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-10">
                                                        <i class="fa-solid fa-check-circle fs-3x text-success mb-3 d-block"></i>
                                                        @lang('accusoft::models/as_asset.no_unactivated_hr_assets')
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles Column -->
                    <div class="col-xl-6">
                        <div class="card card-xl-stretch mb-5 mb-xl-8 shadow-sm border-0">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 mb-1 text-info">
                                        <i class="fa-solid fa-truck text-info me-2"></i>
                                        @lang('accusoft::models/as_asset.vehicle_assets')
                                    </span>
                                    <span class="text-muted mt-1 fw-semibold fs-7">@lang('accusoft::models/as_asset.vehicle_assets_subtitle')</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                                <th class="ps-4 min-w-150px rounded-start">
                                                    @lang('accusoft::models/as_asset.vehicle_name')</th>
                                                <th class="min-w-100px">@lang('accusoft::models/as_asset.plate_number')</th>
                                                <th class="min-w-100px text-end rounded-end pe-4">@lang('crud.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($vehicles as $vehicle)
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-40px me-3">
                                                                <div class="symbol-label bg-light-info text-info">
                                                                    <i class="fa-solid fa-car fs-3"></i>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <span
                                                                    class="text-gray-900 fw-bold fs-6">{{ $vehicle->main_vehicle->name ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light fw-bold fs-7">{{ $vehicle->main_vehicle->plate_arabic ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="{{ route('accusoft.assets.create', ['assetable_type' => get_class($vehicle), 'assetable_id' => $vehicle->id, 'name' => $vehicle->main_vehicle->name ?? '-']) }}"
                                                            class="btn btn-sm btn-light-info fw-bold transition-all hover-scale">
                                                            <i class="fa-solid fa-bolt text-info"></i>
                                                            @lang('accusoft::models/as_asset.financial_activation')
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-10">
                                                        <i class="fa-solid fa-check-circle fs-3x text-success mb-3 d-block"></i>
                                                        @lang('accusoft::models/as_asset.no_unactivated_vehicles')
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
@endsection