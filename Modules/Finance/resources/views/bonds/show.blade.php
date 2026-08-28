@extends('layouts.app')

@section('title', __('finance::models/fnc_bond.singular') . ' #' . $bond->voucher_number)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('crud.detail') @lang('finance::models/fnc_bond.singular')
                    <span class="text-muted fs-7 fw-semibold ms-2">#{{ $bond->voucher_number }}</span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('fnc.bonds.index') }}" class="text-muted text-hover-primary">@lang('finance::models/fnc_bond.plural')</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">@lang('crud.detail')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-light-primary" onclick="window.print()">
                    <i class="ki-outline ki-printer fs-2"></i>
                </button>

                <a href="{{ route('fnc.bonds.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.back')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card shadow-sm mb-5">
                <div class="card-body p-lg-20">
                    <!--begin::Details-->
                    <div class="d-flex flex-column gap-7 gap-lg-10">
                        <!--begin::Order summary-->
                        @include('finance::bonds.show_fields')
                        
                        <!--end::Order summary-->
                    </div>
                    <!--end::Details-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');
@media print {
    .app-toolbar, .app-sidebar, .app-header, .btn, .breadcrumb {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .container-xxl {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    body { background: #fff !important; }
}
</style>
@endsection
