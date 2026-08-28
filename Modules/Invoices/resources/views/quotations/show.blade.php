@extends('layouts.app')

@section('title', __('invoices::models/quotations.singular'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('lang.details') @lang('invoices::models/quotations.singular')
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('invoices.quotations.index') }}" class="text-muted text-hover-primary">
                                @lang('invoices::models/quotations.plural')
                            </a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">@lang('lang.details')</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- Convert to Invoice Button --}}
                     @can('invoices.quotations.convert')  
                    @if ($quotation->status != 'converted')
                        <a href="{{ route('invoices.quotations.convert', $quotation->id) }}"
                            class='btn btn-sm btn-primary float-right mx-1' title="تحويل لفاتورة">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                    @endif
                  @endcan
                    {{-- Attachment Actions --}}
                    @if($quotation->file_url)
                        <a href="{{ $quotation->file_url }}" target="_blank" class="btn btn-sm btn-info" title="عرض المرفق">
                            <i class="fa-solid fa-eye me-1"></i> 
                        </a>
                        <a href="{{ $quotation->file_url }}" download class="btn btn-sm btn-success" title="تحميل المرفق">
                            <i class="fa-solid fa-download me-1"></i> 
                        </a>
                    @endif

                    {{-- Print Button --}}
                    <button type="button" onclick="window.print();" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-print"></i>
                    </button>

                    <a href="{{ route('invoices.quotations.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.back')
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card">
                    <div class="card-body">
                            {!! $renderedTemplate !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
