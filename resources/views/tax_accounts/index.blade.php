@extends('layouts.app')

@section('title', __('models/tax_accounts.plural'))

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
                        <h1>@lang('models/tax_accounts.plural')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
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
                            @lang('models/tax_accounts.plural')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->

                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @can('taxaccounts.print')
                        <button type="button" class="icon-btn"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;"
                            onclick="window.print()">
                            <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('taxaccounts.copy')
                        <button type="button" class="icon-btn copy-table" data-target="#tax_accounts-table"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-copy" style="font-size: 14px;"></i>
                        </button>
                    @endcan
                    @can('taxaccounts.csv')
                        <a type="button" class="icon-btn" href="{{ route('taxaccounts.csv') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-csv" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة Excel -->
                    @can('taxaccounts.excel')
                        <a type="button" class="icon-btn" href="{{ route('taxaccounts.excel') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    <!-- أيقونة النسخ -->
                    @can('taxaccounts.pdf')
                        <a type="button" class="icon-btn" href="{{ route('taxaccounts.pdf') }}"
                            style="background-color: transparent; border: 1px solid #cbd5e0; color: #4a5568; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-file-pdf" style="font-size: 14px;"></i>
                        </a>
                    @endcan
                    @can('taxaccounts.create')
                        <a class="btn btn-sm btn-primary float-right" href="{{ route('taxaccounts.create') }}">
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
                @if (true)
                    <div class="card shadow-sm my-3 " id="card-filter">
                        <div class="card-header collapsible cursor-pointer rotate {{ request()->has('pagination') ? 'active' : 'collapsed' }}"
                            data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible"
                            aria-expanded="{{ request()->has('pagination') ? 'true' : 'false' }}">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible"
                            class="collapse {{ request()->has('pagination') ? 'show' : '' }}">
                            {!! Form::open(['route' => 'taxaccounts.index', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">
                                    <!-- Name Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('name', __('models/tax_accounts.fields.name') . ':') !!}
                                        {!! Form::text('name', request('name'), ['class' => 'form-control']) !!}
                                    </div>

                                    <!-- Status Field -->
                                    {{-- <div class="form-group col-sm-4 mb-4">
                                {!! Form::label('status', __('models/tax_accounts.fields.status') . ':') !!}
                                {!! Form::select('status', $statuses, request('status'), ['class' => 'form-control',
                                'placeholder' => __('hr::lang.select_status')]) !!}
                            </div> --}}


                                    <!-- pagination Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('pagination', __('crud.pagination') . ':') !!}
                                        {!! Form::select('pagination', config('statusSystem.pagination'), request('pagination'), [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm   btn-search">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-primary float-right" href="{{ route('taxaccounts.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif
                <div class="card">
                    @include('tax_accounts.table')
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
