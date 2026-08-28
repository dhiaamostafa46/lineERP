@extends('layouts.app')

@section('title', __('store::models/st_damageds.singular'))

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
                        @lang('store::models/st_damageds.singular') @lang('crud.detail')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('store.damaged.index') }}" class=" text-muted text-hover-primary">
                                @lang('store::models/st_damageds.plural')
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
                            @lang('crud.detail')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" onclick="window.print()" title="@lang('lang.print')">
                        <i class="fa-solid fa-print fs-5"></i>
                    </button>
                    <a class="btn btn-sm btn-secondary float-right" href="{{ route('store.damaged.index') }}">
                        @lang('crud.back')
                    </a>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body">
                        @php
                            $org = \App\Models\Organization::first();
                            $orgName = $org ? $org->name : 'AccuSoft ERP';
                            $branchName = '';
                            
                            $document = null;
                            foreach (get_defined_vars() as $key => $value) {
                                if (is_object($value) && in_array(class_basename($value), [
                                    'StDamaged', 'StDirectTransfer', 'StIssuing', 'StOpeningBalance', 
                                    'StReceiving', 'StReservation', 'StSettlement', 'Store'
                                ])) {
                                    $document = $value;
                                    break;
                                }
                            }
                            
                            if ($document) {
                                if (isset($document->branch_id)) {
                                    $branchName = \App\Models\Branch::find($document->branch_id)->name ?? '';
                                } elseif (isset($document->store) && isset($document->store->branch)) {
                                    $branchName = $document->store->branch->name ?? '';
                                }
                            }
                            
                            if (empty($branchName)) {
                                $branchName = \App\Models\Branch::find(auth()->user()->branch_id ?? 1)->name ?? \App\Models\Branch::first()->name ?? '';
                            }
                        @endphp
                        
                        <!-- Print-Only Header -->
                        <div class="d-none d-print-block mb-5 pb-5 border-bottom border-3 border-dark">
                            <div class="d-flex justify-content-between align-items-center w-100" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
                                <div style="text-align: right;">
                                    <h2 style="font-weight: 800; color: #1a1a1a; margin: 0 0 5px 0; font-size: 22px;">{{ $orgName }}</h2>
                                    <span style="color: #5e6278; font-size: 13px;">{{ $branchName }}</span>
                                </div>
                                <div style="text-align: left;">
                                    <h2 style="font-weight: 800; color: #6A669D; margin: 0 0 5px 0; font-size: 20px;">@yield('title')</h2>
                                    <span style="color: #5e6278; font-size: 13px;">@lang('lang.date'): {{ now()->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row gap-1">
                            @include('store::damageds.show_fields')
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>

<style>
@media print {
    #kt_app_header,
    #kt_app_sidebar,
    #kt_app_toolbar,
    #kt_app_footer,
    .btn,
    .btn-group,
    .icon-btn,
    .breadcrumb,
    .card-footer,
    #kt_scrolltop,
    #card-filter,
    #loader,
    .spinner-grow,
    .spinner-border,
    .no-print {
        display: none !important;
    }

    html, body, 
    #kt_app_root, 
    #kt_app_page, 
    #kt_app_wrapper, 
    #kt_app_main, 
    .app-main, 
    .app-wrapper, 
    .app-page, 
    .app-root, 
    .app-content, 
    .app-container,
    #kt_app_content, 
    #kt_app_content_container {
        position: static !important;
        display: block !important;
        float: none !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: transparent !important;
        background-color: #fff !important;
        box-shadow: none !important;
        border: none !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card-body {
        padding: 0 !important;
        margin: 0 !important;
    }

    b.form-control, .form-control {
        border: none !important;
        border-bottom: 1px dashed #ccc !important;
        background-color: transparent !important;
        padding: 4px 0 !important;
        font-weight: 700 !important;
        color: #000 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .col-4.my-auto p {
        font-weight: 600 !important;
        color: #333 !important;
        margin-bottom: 3px !important;
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 15px !important;
    }

    .table th {
        background-color: #f3f6f9 !important;
        color: #000 !important;
        font-weight: 800 !important;
        border: 1px solid #dbdfe9 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table td {
        border: 1px solid #dbdfe9 !important;
        color: #111 !important;
        font-weight: 500 !important;
    }

    .d-print-block {
        display: block !important;
    }

    .d-print-none {
        display: none !important;
    }
}
</style>

@if(request()->has('print'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var loader = document.getElementById('loader');
        if (loader) {
            loader.style.display = 'none';
        }

        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
                setTimeout(function() {
                    window.location.href = "{{ route('store.damaged.index') }}";
                }, 300);
            }, 100);
        };

        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>
@endif

@endsection




