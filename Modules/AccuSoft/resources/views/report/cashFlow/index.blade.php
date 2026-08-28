@extends('layouts.app')

@section('title', __('accusoft::models/as_reports.types.cash_flow_statement_indirect'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 d-print-none">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('accusoft::models/as_reports.types.cash_flow_statement_indirect')
                    </h1>
                    <!--end::Title-->
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
                            <a href="{{ route('accusoft.reports.index') }}" class="text-muted text-hover-primary">
                                @lang('accusoft::models/as_reports.plural')
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            @lang('accusoft::models/as_reports.types.cash_flow_statement_indirect')
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('accusoft.reports.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel')
                    </a>
                </div>
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                {{-- Filter Card --}}
                <div class="card shadow-sm my-3 d-print-none">
                    <div class="card-header collapsible cursor-pointer rotate collapsed" data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible">
                        <h3 class="card-title">
                            <i class="fa-solid fa-filter fs-2 me-2"></i>
                            @lang('crud.search')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_docs_card_collapsible" class="collapse show">
                        {!! Form::open(['route' => 'accusoft.reports.cashFlow', 'method' => 'GET']) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('branchId', __('accusoft::models/as_reports.filters.branchId') . ':') !!}
                                    <x-select2-input name="branchId" :placeholder="__('accusoft::models/as_reports.filters.all_branch')" :list="$branchs"
                                        :selected_id="old('branchId', request('branchId'))">
                                    </x-select2-input>
                                </div>

                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('fromDate', __('accusoft::models/as_reports.filters.date_from') . ':') !!}
                                    <input type="date" name="fromDate" class="form-control form-control-solid" value="{{ request('fromDate', $fromDate) }}" />
                                </div>

                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('toDate', __('accusoft::models/as_reports.filters.date_to') . ':') !!}
                                    <input type="date" name="toDate" class="form-control form-control-solid" value="{{ request('toDate', $toDate) }}" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-4">
                            <button type="submit" class="btn btn-sm btn-btc" name="export" value="RPT">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <button type="button" class="icon-btn btn-btc" onclick="window.print()">
                                <i class="fa-solid fa-print"></i>
                            </button>
                            <button type="submit" name="export" value="excel" class="icon-btn btn-btc">
                                <i class="fa-solid fa-file-excel"></i>
                            </button>
                            <button type="submit" name="export" value="pdf" class="icon-btn btn-btc">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                            <a class="btn btn-sm float-right btn-btc" href="{{ route('accusoft.reports.cashFlow') }}">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                @if($cashFlow)
                    @include('accusoft::report.cashFlow.table')
                @endif

                @include('accusoft::layouts._style')
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
