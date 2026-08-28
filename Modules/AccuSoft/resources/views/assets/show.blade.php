@extends('layouts.app')

@section('title', __('accusoft::models/as_asset.details') . ' - ' . $asset->name)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('accusoft::models/as_asset.details'): {{ $asset->name }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('accusoft.assets.index') }}" class="text-muted text-hover-primary">@lang('accusoft::models/as_asset.plural')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('accusoft::models/as_asset.details')</li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @can('accusoft.assets.print')
                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" onclick="window.print()" title="@lang('accusoft::models/as_asset.print')">
                        <i class="fa-solid fa-print"></i>
                    </button>
                @endcan

                @if($asset->status != \Modules\AccuSoft\App\Models\Asset::STATUS_DISPOSED)
                    @if($asset->calculation_type === 'manual' && $asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE)
                    <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#depreciateModal">
                        <i class="fa-solid fa-calculator"></i> @lang('accusoft::models/as_asset.manual_depreciation')
                    </button>
                    @endif
                    @if($asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE)
                    <button type="button" class="btn btn-sm btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#disposeModal">
                        <i class="fa-solid fa-trash-arrow-up"></i> @lang('accusoft::models/as_asset.dispose_asset')
                    </button>
                    @endif
                @endif
                <a class="btn btn-sm btn-secondary fw-bold" href="{{ route('accusoft.assets.index') }}">
                    <i class="fa-solid fa-arrow-right"></i> @lang('crud.back')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('flash::message')
            @include('adminlte-templates::common.errors')

            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-{{ $asset->depreciation_status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE ? '12' : '4' }}">
                    @include('accusoft::assets.partials.profile')
                </div>
                <!--end::Col-->

                @if($asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE)
                <!--begin::Col-->
                <div class="col-xl-8">
                    @include('accusoft::assets.partials.financial_dashboard')
                    @include('accusoft::assets.partials.tabs')
                </div>
                <!--end::Col-->
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            margin: 1cm;
            size: A4 portrait;
        }

        /* Hide Layout Elements */
        #kt_app_header, 
        #kt_app_sidebar, 
        #kt_app_toolbar, 
        .app-sidebar, 
        .app-header, 
        .footer, 
        #kt_scrolltop,
        .btn {
            display: none !important;
        }

        /* Adjust Content Wrapper for Full Page */
        .app-wrapper, 
        .app-main, 
        .app-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* Prevent Tabs Menu from Printing but keep active content */
        .nav-tabs {
            display: none !important;
        }
        .tab-content > .tab-pane {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Force colors to print (backgrounds, badges, etc.) */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Fix column breaking on print */
        .col-xl-4, .col-xl-8 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        
        .card {
            page-break-inside: avoid;
        }
    }
</style>

@include('accusoft::assets.partials.modals')

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 with AJAX for account fields in Modal
        $('.account-select').select2({
            dropdownParent: $("#disposeModal"),
            ajax: {
                url: '{{ route("Lookup.TreeAccounts") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                        lang: '{{ app()->getLocale() }}',
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination?.more || false
                        }
                    };
                },
                cache: true
            },
            allowClear: true,
            minimumInputLength: 0,
            dir: 'rtl',
            width: '100%',
            placeholder: '@lang('accusoft::models/as_asset.select_account')',
            language: {
                searching: function() { return '@lang('accusoft::models/as_asset.searching')'; },
                noResults: function() { return '@lang('accusoft::models/as_asset.no_results')'; }
            }
        });
    });
</script>
@endpush
