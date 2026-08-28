@extends('layouts.app')

@section('title', __('crud.edit') . ' ' . __('accusoft::models/as_asset_categories.singular'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('crud.edit') @lang('accusoft::models/as_asset_categories.singular')
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
                    <li class="breadcrumb-item text-muted">@lang('crud.edit')</li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.assetcategories.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('adminlte-templates::common.errors')
            <div class="clearfix"></div>
            
            <div class="card">
                <form method="POST" action="{{ route('accusoft.assetcategories.update', $category->id) }}" id="assetCategoryForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            @include('accusoft::asset_categories.fields')
                        </div>
                    </div>
                    <div class="card-footer py-4 text-end">
                        <a href="{{ route('accusoft.assetcategories.index') }}" class="btn btn-sm btn-secondary">
                            @lang('crud.cancel')
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary">@lang('crud.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
