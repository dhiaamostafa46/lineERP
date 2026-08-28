@extends('layouts.app')

@section('title', __('crud.add_new') . ' ' . __('invoices::models/sales_return_invoices.singular'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('crud.add_new') @lang('invoices::models/sales_return_invoices.singular')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('invoices.sales_return.index') }}" class="text-muted text-hover-primary">
                            @lang('invoices::models/sales_return_invoices.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('crud.add_new')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('invoices.sales_return.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('adminlte-templates::common.errors')
            <div class="clearfix"></div>
            <div class="card">
                {!! Form::open(['route' => 'invoices.sales_return.store', 'files' => true, 'id' => 'zatca-invoice-form']) !!}
                <div class="card-body">
                    <div class="row">
                        @include('invoices::sales_return_invoices.fields')
                    </div>
                </div>
                <div class="card-footer py-4 text-end">
                    <a href="{{ route('invoices.sales_return.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel')
                    </a>
                    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(session('confirm_simplified'))
<script>
    Swal.fire({
        title: 'بيانات العميل غير مكتملة',
        text: "{!! session('confirm_simplified') !!}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'إرسال كفاتورة مبسطة',
        cancelButtonText: 'تعديل البيانات'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById('zatca-invoice-form');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'force_simplified';
            input.value = 'true';
            form.appendChild(input);
            form.submit();
        }
    });
</script>
@endif
@endpush
@endsection
