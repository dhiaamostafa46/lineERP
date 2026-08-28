@extends('layouts.app')

@section('title', 'تقرير بنود القيد')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <h1>@lang('accusoft::models/as_reports.reports.journal_entries')</h1>
                    </h1>
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
                            @lang('accusoft::models/as_reports.reports.journal_entries')
                        </li>
                    </ul>
                </div>
                <!--end::Page title-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('accusoft.reports.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel')
                    </a>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="card shadow-sm my-3" id="card-filter">
                    <div class="card-header collapsible cursor-pointer rotate collapsed active"
                        data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible" aria-expanded="false">
                        <h3 class="card-title">
                            <i class="fa-solid fa-filter fs-2 me-2"></i>
                            @lang('crud.search')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_docs_card_collapsible" class="collapse show">
                        {!! Form::open(['route' => ['accusoft.reports.journalEntries', 'RPT'], 'method' => 'GET']) !!}
                        <input type="hidden" name="search" value="1">
                        <div class="card-body">
                            <div class="row">
                                {{-- الحساب (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('accountId', __('accusoft::models/as_reports.filters.account') . ':') !!}
                                    <x-select2-input name="accountId" :placeholder="__('accusoft::models/as_reports.filters.select_account')" :list="$accounts"
                                        :selected_id="old('accountId', request('accountId'))">
                                    </x-select2-input>
                                </div>

                                {{-- مركز التكلفة (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('costCenterId', __('accusoft::models/as_reports.filters.cost_center') . ':') !!}
                                    <x-select2-input name="costCenterId" :placeholder="__('accusoft::models/as_reports.filters.all_cost_centers')" :list="$costCenters"
                                        :selected_id="old('costCenterId', request('costCenterId'))">
                                    </x-select2-input>
                                </div>

                                {{-- الفرع (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('branchId', __('accusoft::models/as_reports.filters.branchId') . ':') !!}
                                    <x-select2-input name="branchId" :placeholder="__('accusoft::models/as_reports.filters.all_branch')" :list="$branchs"
                                        :selected_id="old('branchId', request('branchId'))">
                                    </x-select2-input>
                                </div>

                                {{-- نوع القيد (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('entryType', __('accusoft::models/as_reports.filters.entry_type') . ':') !!}
                                    <x-select2-input name="entryType" :placeholder="__('accusoft::models/as_reports.filters.all_types')" :list="$journalTypes"
                                        :selected_id="old('entryType', request('entryType'))">
                                    </x-select2-input>
                                </div>

                                {{-- تاريخ البداية (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('fromDate', __('accusoft::models/as_reports.filters.date_from') . ':') !!}
                                    <div class="input-group" id="kt_td_picker_from" data-td-target-input="nearest"
                                        data-td-target-toggle="nearest">
                                        <input id="kt_td_picker_from_input" type="text" name="fromDate"
                                            class="form-control form-control-solid"
                                            data-td-target="#kt_td_picker_from_input"
                                            value="{{ old('fromDate', request('fromDate')) ?? $fromDate }}" />
                                        <span class="input-group-text" data-td-target="#kt_td_picker_from_input"
                                            data-td-toggle="datetimepicker">
                                            <i class="ki-duotone ki-calendar fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                </div>

                                {{-- تاريخ النهاية (اختياري) --}}
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('toDate', __('accusoft::models/as_reports.filters.date_to') . ':') !!}
                                    <div class="input-group" id="kt_td_picker_to" data-td-target-input="nearest"
                                        data-td-target-toggle="nearest">
                                        <input id="kt_td_picker_to_input" type="text" name="toDate"
                                            class="form-control form-control-solid"
                                            data-td-target="#kt_td_picker_to_input"
                                            value="{{ old('toDate', request('toDate')) ?? $toDate }}" />
                                        <span class="input-group-text" data-td-target="#kt_td_picker_to_input"
                                            data-td-toggle="datetimepicker">
                                            <i class="ki-duotone ki-calendar fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer py-4">
                            <button type="submit" class="btn btn-sm btn-btc" name="export" value="RPT">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <button type="button" class="icon-btn btn-btc" onclick="window.print()">
                                <i class="fa-solid fa-print" style="font-size: 14px;"></i>
                            </button>
                            <button type="submit" name="export" value="csv" class="icon-btn btn-btc">
                                <i class="fa-solid fa-file-csv"></i>
                            </button>
                            <button type="submit" name="export" value="excel" class="icon-btn btn-btc">
                                <i class="fa-solid fa-file-excel"></i>
                            </button>
                            <button type="submit" name="export" value="pdf" class="icon-btn btn-btc">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                            <a class="btn btn-sm float-right  btn-btc" href="{{ route('accusoft.reports.journalEntries') }}">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                @if(isset($reportData))
                    @include('accusoft::report.journalEntries.table')
                @endif
                
                @include('accusoft::layouts._style')
                @include('accusoft::layouts._script')

            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
