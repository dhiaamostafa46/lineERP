@extends('layouts.app')

@section('title', __('store::models/st_reports.types.pending_stock'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
    {{-- Toolbar --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('store::models/st_reports.types.pending_stock')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('store.reports.index') }}" class="text-muted text-hover-primary">@lang('store::models/st_reports.plural')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('store::models/st_reports.types.pending_stock')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('store.reports.index') }}" class="btn btn-sm btn-secondary">@lang('crud.cancel')</a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            {{-- Filter --}}
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
                    <form method="GET" action="{{ route('store.reports.pendingStock') }}" id="filterForm">
                        <input type="hidden" name="search" value="1">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-3 mb-3">
                                    <label class="form-label">@lang('store::models/st_reports.columns.from_store')</label>
                                    <x-select2-input name="from_store_id" id="from_store_id" :placeholder="__('lang.all')" :list="$stores" :selected_id="request('from_store_id')">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-3 mb-3">
                                    <label class="form-label">@lang('store::models/st_reports.columns.to_store')</label>
                                    <x-select2-input name="to_store_id" id="to_store_id" :placeholder="__('lang.all')" :list="$stores" :selected_id="request('to_store_id')">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-3 mb-3">
                                    <label class="form-label">@lang('store::models/st_reports.columns.product')</label>
                                    <x-select2-input name="product_id" id="product_id" :placeholder="__('lang.all')" :list="$products" :selected_id="request('product_id')">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-3 mb-3">
                                    <label class="form-label">@lang('store::models/st_reports.columns.to_date')</label>
                                    <div class="input-group" id="kt_td_picker_to" data-td-target-input="nearest" data-td-target-toggle="nearest">
                                        <input id="kt_td_picker_to_input" type="text" name="toDate"
                                            class="form-control form-control-solid"
                                            data-td-target="#kt_td_picker_to"
                                            value="{{ $toDate }}" />
                                        <span class="input-group-text" data-td-target="#kt_td_picker_to" data-td-toggle="datetimepicker">
                                            <i class="ki-duotone ki-calendar fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-4">
                            <button type="submit" class="btn btn-sm btn-btc">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-btc" onclick="window.print()">
                                <i class="fa-solid fa-print me-1"></i> 
                            </button>
                            @if($stocks !== null)
                                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-btc" title="Excel">
                                    <i class="fa-regular fa-file-excel fs-5"></i>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-btc" title="CSV">
                                    <i class="fa-solid fa-file-csv fs-5"></i>
                                </a>
                                <a target="_blank" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-btc" title="PDF">
                                    <i class="fa-regular fa-file-pdf fs-5"></i>
                                </a>
                            @endif
                            <a href="{{ route('store.reports.pendingStock') }}" class="btn btn-sm btn-btc">
                                <i class="fa-solid fa-circle-xmark me-1"></i> 
                            </a>
                        </div>
                    </form>
                </div>
            </div>
                <!--end::Filter Card-->

                @if($stocks)
                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <h3 class="card-title">
                                @lang('store::models/st_reports.types.pending_stock')
                                <span class="badge badge-light-warning ms-2">{{ $stocks->total() }} @lang('lang.record')</span>
                            </h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover align-middle">
                                    <thead class="bg-light-warning">
                                        <tr class="fw-bold text-gray-800">
                                            <th class="text-center">@lang('store::models/st_reports.columns.id')</th>
                                            <th>@lang('lang.type')</th>
                                            <th>@lang('store::models/st_reports.columns.movement_number')</th>
                                            <th>@lang('store::models/st_reports.columns.date')</th>
                                            <th>@lang('store::models/st_reports.columns.product')</th>
                                            <th>@lang('store::models/st_reports.columns.from_store')</th>
                                            <th>@lang('store::models/st_reports.columns.to_store')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.quantity')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.pending_quantity')</th>
                                            <th>@lang('store::models/st_reports.columns.unit')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stocks as $i => $item)
                                            <tr>
                                                <td class="text-center">{{ ($stocks->currentPage()-1) * $stocks->perPage() + $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge badge-light-{{ $item->report_type == __('store::models/st_reservations.singular') ? 'primary' : 'warning' }}">
                                                        {{ $item->report_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-dark">{{ $item->doc_number }}</span>
                                                </td>
                                                <td>{{ $item->doc_date?->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $item->product_name }}</span>
                                                        <span class="text-muted fs-8">{{ $item->category_name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $item->source_store }}</td>
                                                <td>{{ $item->destination_store }}</td>
                                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="text-center fw-bolder text-danger">{{ number_format($item->pending_qty, 2) }}</td>
                                                <td>{{ $item->unit_name }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-light-info">{{ $item->status_label }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-10">
                                                    <p class="text-gray-400 fw-semibold">@lang('lang.no_data_found')</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                {{ $stocks->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                @else
                    <div class="card">
                        <div class="card-body text-center py-20">
                            <i class="fas fa-search fa-4x text-light-primary mb-5"></i>
                            <h4 class="text-gray-600">@lang('lang.please_select_filters_and_search')</h4>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
    @include('store::components.store_scripts')
    <script>
        $(document).ready(function() {
            if (typeof tempusDominus !== 'undefined') {
                const pickerTo = document.getElementById('kt_td_picker_to');
                if (pickerTo) {
                    new tempusDominus.TempusDominus(pickerTo, {
                        display: {
                            components: {
                                calendar: true,
                                clock: false
                            },
                            buttons: {
                                today: true,
                                clear: true,
                                close: true
                            }
                        },
                        localization: {
                            format: 'yyyy-MM-dd'
                        }
                    });
                }
            }
        });
    </script>
@endpush


