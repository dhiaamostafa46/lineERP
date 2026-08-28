@extends('layouts.app')

@section('title', __('accusoft::models/as_asset.plural'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('accusoft::models/as_asset.plural')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('accusoft::models/as_asset.plural')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->

                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('accusoft.assets.print')
                        <button type="button" class="icon-btn btn-btc" onclick="window.print()">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('accusoft.assets.copy')
                        <button type="button" class="icon-btn btn-btc copy-table" data-target="#assets-table">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('accusoft.assets.csv')
                        <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assets.csv') }}">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('accusoft.assets.excel')
                        <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assets.excel') }}">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('accusoft.assets.pdf')
                        <a type="button" class="icon-btn btn-btc" href="{{ route('accusoft.assets.pdf') }}">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                     @can('accusoft.assets.unactivated')
                        @php
                            $unactivatedCount = \Illuminate\Support\Facades\Cache::remember('accusoft_menu_unactivated_assets', 60, function() {
                                $count = 0;
                                if (class_exists(\Modules\HR\App\Models\HrAsset::class)) {
                                    $count += \Modules\HR\App\Models\HrAsset::doesntHave('financialAsset')->count();
                                }
                                return $count;
                            });
                        @endphp
                        <a type="button" class="icon-btn btn-btc position-relative" href="{{ route('accusoft.assets.unactivated') }}"
                           data-bs-toggle="tooltip" data-bs-placement="bottom"
                           title="@lang('accusoft::models/as_asset.unactivated_assets') ({{ $unactivatedCount }})">
                            <i class="fa-solid fa-link" style="font-size: 14px;"></i>
                            @if($unactivatedCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      style="font-size: 0.65rem; min-width: 18px; padding: 2px 4px; animation: pulseBadge 1.5s infinite;">
                                    {{ $unactivatedCount > 99 ? '99+' : $unactivatedCount }}
                                </span>
                                <style>
                                    @keyframes pulseBadge {
                                        0%   { transform: translate(-50%, -50%) scale(1); }
                                        50%  { transform: translate(-50%, -50%) scale(1.25); }
                                        100% { transform: translate(-50%, -50%) scale(1); }
                                    }
                                </style>
                            @endif
                        </a>
                    @endcan
                    @can('accusoft.assets.create')
                        
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('accusoft.assets.create') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('crud.add_new')
                        </a>
                    @endcan
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="clearfix"></div>
                @include('flash::message')

                @if (true)
                    <div class="card shadow-sm my-3 " id="card-filter">
                        <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse"
                            data-bs-target="#kt_docs_card_collapsible" aria-expanded="false">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible" class="collapse">
                            {!! Form::open(['route' => 'accusoft.assets.index', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        {!! Form::label('code', __('accusoft::models/as_asset.fields.code') . ':') !!}
                                        {!! Form::text('code', request('code'), ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-4">
                                        {!! Form::label('name', __('accusoft::models/as_asset.fields.name') . ':') !!}
                                        {!! Form::text('name', request('name'), ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-4">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination', [10 => 10, 25 => 25, 50 => 50, 100 => 100]), request('pagination') ?? null, [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-primary float-right" href="{{ route('accusoft.assets.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('accusoft::assets.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
