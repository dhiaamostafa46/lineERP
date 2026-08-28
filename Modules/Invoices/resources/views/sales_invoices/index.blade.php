@extends('layouts.app')

@section('title', __('invoices::models/sales_invoices.plural'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <!-- fixed line format -->
                        @lang('invoices::models/sales_invoices.plural')
                    </h1>
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            @lang('invoices::models/sales_invoices.plural')
                        </li>
                    </ul>
                </div>
                <!--end::Page title-->
                
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('invoices.sales.print')
                        <button type="button" class="icon-btn"
                            onclick="window.print()" style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('invoices.sales.copy')
                        <button type="button" class="icon-btn copy-table" data-target="#db-sales-invoices-table" style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('invoices.sales.csv')
                        <a type="button" class="icon-btn" href="{{ route('invoices.sales.csv') }}" style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('invoices.sales.excel')
                        <a type="button" class="icon-btn" href="{{ route('invoices.sales.excel') }}" style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('invoices.sales.pdf')
                        <a type="button" class="icon-btn" href="{{ route('invoices.sales.pdf') }}" style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;" target="_blank">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('invoices.sales.create')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('invoices.sales.create') }}">
                            <i class="fa-solid fa-plus"></i>
                            @lang('crud.add_new')
                        </a>
                    @endcan
                </div>
                <!--end::Actions-->
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                @include('flash::message')

                <div class="clearfix"></div>
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
                            {!! Form::open(['route' => 'invoices.sales.index', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('invoice_number', __('invoices::models/sales_invoices.fields.invoice_number') . ':') !!}
                                        {!! Form::text('invoice_number', request('invoice_number'), ['class' => 'form-control', 'placeholder' => __('crud.search')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('customer_invoice_number', __('invoices::models/sales_invoices.fields.customer_invoice_number') . ':') !!}
                                        {!! Form::text('customer_invoice_number', request('customer_invoice_number'), ['class' => 'form-control', 'placeholder' => __('crud.search')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('customer_id', __('invoices::models/sales_invoices.fields.customer_id') . ':') !!}
                                        {!! Form::select('customer_id', [], request('customer_id'), ['class' => 'form-select select2-ajax-customers', 'data-selected' => request('customer_id'), 'placeholder' => __('lang.all')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('status', __('invoices::models/sales_invoices.fields.status') . ':') !!}
                                        {!! Form::select('status', \App\Models\invApp\SalesInvoice::statuses(), request('status'), ['class' => 'form-select', 'placeholder' => __('lang.all')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('issue_date', __('invoices::models/sales_invoices.fields.issue_date') . ':') !!}
                                        {!! Form::date('issue_date', request('issue_date'), ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('store_id', __('invoices::models/sales_invoices.fields.store_id') . ':') !!}
                                        {!! Form::select('store_id', [], request('store_id'), ['class' => 'form-select select2-ajax-stores', 'data-selected' => request('store_id'), 'placeholder' => __('lang.all')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('created_by', __('invoices::models/sales_invoices.fields.created_by') . ':') !!}
                                        {!! Form::select('created_by', [], request('created_by'), ['class' => 'form-select select2-ajax-users', 'data-selected' => request('created_by'), 'placeholder' => __('lang.all')]) !!}
                                    </div>

                                    <div class="form-group col-md-2 col-sm-6">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination') ?? null, ['class' => 'form-select']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-primary float-right"
                                    href="{{ route('invoices.sales.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('invoices::sales_invoices.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection

