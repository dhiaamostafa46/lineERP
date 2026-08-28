@extends('layouts.app')

@section('title', __('lang.details') . ' ' . __('invoices::models/purchase_return_invoices.singular'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('lang.details') @lang('invoices::models/purchase_return_invoices.singular') #{{ $purchaseReturn->invoice_number }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('invoices.purchase_return.index') }}" class="text-muted text-hover-primary">
                            @lang('invoices::models/purchase_return_invoices.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('lang.details')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if ($purchaseReturn->status == \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT)
                    @can('invoices.purchase_return.edit')
                        <a href="{{ route('invoices.purchase_return.edit', $purchaseReturn->id) }}" class="btn btn-sm btn-warning">
                            <i class="fa-solid fa-edit me-1"></i> تعديل المسودة
                        </a>
                    @endcan
                @endif
                <button type="button" onclick="window.print();" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-print me-1"></i>
                  
                </button>
                <a href="{{ route('invoices.purchase_return.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.back')
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @include('invoices::purchase_return_invoices.show_fields')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
