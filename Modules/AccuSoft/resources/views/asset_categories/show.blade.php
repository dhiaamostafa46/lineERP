@extends('layouts.app')

@section('title', __('lang.show') . ' ' . __('accusoft::models/as_asset_categories.singular'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('crud.detail') @lang('accusoft::models/as_asset_categories.singular')
                </h1>
                
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('accusoft.assetcategories.index') }}" class="text-muted text-hover-primary">
                            @lang('accusoft::models/as_asset_categories.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('crud.back')</li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.assetcategories.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.back')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body">
                        <div class="row gap-1">
                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.name')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->name }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">{{ __('accusoft::models/as_asset_categories.fields.has_accounting_effect') }}</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">
                                        @if($category->has_accounting_effect)
                                            <span class="badge badge-light-success">نعم</span>
                                        @else
                                            <span class="badge badge-light-danger">لا</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5" style="{{ $category->has_accounting_effect ? '' : 'display: none;' }}">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.asset_account_id')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->assetAccount->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5" style="{{ $category->has_accounting_effect ? '' : 'display: none;' }}">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.accumulated_depreciation_account_id')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->accumulatedDepreciationAccount->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5" style="{{ $category->has_accounting_effect ? '' : 'display: none;' }}">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.depreciation_expense_account_id')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->depreciationExpenseAccount->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.default_depreciation_method')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ __('accusoft::models/as_asset_categories.methods.' . $category->default_depreciation_method) }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.default_useful_life')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->default_useful_life ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.calculation_type')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->calculation_type == 'automatic' ? (__('accusoft::models/as_asset.automatic') ?? 'تلقائي') : (__('accusoft::models/as_asset.manual') ?? 'يدوي') }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_asset_categories.fields.useful_life_type')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">{{ $category->useful_life_type == 'monthly' ? (__('accusoft::models/as_asset.monthly') ?? 'شهري') : (__('accusoft::models/as_asset.yearly') ?? 'سنوي') }}</span>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-5">
                                <div class="col-4 my-auto">
                                    <label class="fs-5 fw-bold text-gray-800">@lang('lang.status')</label>
                                </div>
                                <div class="col-8">
                                    <span class="fs-6 text-gray-600">
                                        @if($category->status == 1)
                                            <span class="badge badge-light-success">{{ __('lang.active') }}</span>
                                        @else
                                            <span class="badge badge-light-danger">{{ __('lang.inactive') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
        <!--end::Content-->
    </div>
@endsection
