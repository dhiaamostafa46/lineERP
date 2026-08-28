@extends('layouts.app')

@section('title', __('store::models/st_reports.types.stock_movement'))

@section('styles')
    <style>
        @media print {
            * {
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                margin: 0;
                padding: 10px !important;
                font-size: 11px;
            }

            .app-toolbar,
            .app-container>.card:first-child,
            #filter_collapse,
            .my-3,
            .card-footer,
            .btn,
            a.btn,
            button.btn,
            .breadcrumb {
                display: none !important;
            }

            .app-content,
            .app-content-container {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #999 !important;
                page-break-inside: avoid;
                margin-bottom: 0 !important;
                margin-top: 0 !important;
            }

            .card-header {
                background-color: #f0f0f0 !important;
                border-bottom: 2px solid #333 !important;
                padding: 8px !important;
            }

            .card-title {
                font-size: 12px !important;
                font-weight: bold !important;
                margin: 0 !important;
            }

            .card-body {
                padding: 5px !important;
            }

            .table {
                font-size: 10px;
                margin-bottom: 0;
                width: 100%;
                border-collapse: collapse;
            }

            .table thead {
                display: table-header-group;
            }

            .table thead th {
                background-color: #e8e8e8 !important;
                border: 1px solid #999 !important;
                padding: 4px !important;
                font-weight: bold;
                text-align: center;
            }

            .table tbody tr {
                page-break-inside: avoid;
            }

            .table td {
                padding: 3px !important;
                border: 1px solid #999 !important;
            }

            .table tfoot tr {
                display: table-row;
                font-weight: bold;
                background-color: #f5f5f5 !important;
            }

            .badge {
                background-color: #f0f0f0 !important;
                border: 1px solid #999 !important;
                color: #000 !important;
                padding: 2px 4px !important;
                font-size: 9px !important;
            }

            .text-center {
                text-align: center;
            }

            .text-end {
                text-align: right;
            }

            .fw-bold {
                font-weight: bold;
            }

            .fw-bolder {
                font-weight: 900;
            }

            .text-success {
                color: #28a745;
            }

            .text-danger {
                color: #dc3545;
            }

            .text-primary {
                color: #6A669D;
            }

            .page-break {
                page-break-after: always;
            }

            .report-header {
                text-align: center;
                margin-bottom: 10px;
                border-bottom: 2px solid #333;
                padding-bottom: 5px;
            }

            .report-header h2 {
                font-size: 14px;
                margin: 2px 0;
                font-weight: bold;
            }

            .report-info {
                font-size: 9px;
                margin-bottom: 10px;
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 10px;
            }

            .report-info-item {
                text-align: left;
            }

            .report-info-label {
                font-weight: bold;
            }
        }

        .d-none-print {
            display: block;
        }

        @media print {
            .d-none-print {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-column flex-column-fluid">

        {{-- Toolbar --}}
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('store::models/st_reports.types.stock_movement')
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('store.reports.index') }}"
                                class="text-muted text-hover-primary">@lang('store::models/st_reports.plural')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">@lang('store::models/st_reports.types.stock_movement')</li>
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
                    <div class="card-header collapsible cursor-pointer rotate collapsed active" data-bs-toggle="collapse"
                        data-bs-target="#kt_docs_card_collapsible" aria-expanded="false">
                        <h3 class="card-title">
                            <i class="fa-solid fa-filter fs-2 me-2"></i>
                            @lang('crud.search')
                        </h3>
                        <div class="card-toolbar rotate-180">
                            <i class="ki-duotone ki-down fs-1"></i>
                        </div>
                    </div>
                    <div id="kt_docs_card_collapsible" class="collapse show">
                        {!! Form::open(['route' => 'store.reports.stockMovement', 'method' => 'GET']) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-3 mb-3">
                                    {!! Form::label('branch_id', __('models/branches.singular') . ':') !!}
                                    <x-select2-input name="branch_id" :placeholder="__('lang.all')" :list="$branches" :selected_id="old('branch_id', request('branch_id'))">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('store_id', __('store::models/st_reports.filters.store') . ':') !!}
                                    <x-select2-input name="store_id" :placeholder="__('store::models/st_reports.filters.all_stores')" :list="$stores" :selected_id="old('store_id', request('store_id'))">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('category_id', __('store::models/st_reports.filters.category') . ':') !!}
                                    <x-select2-input name="category_id" :placeholder="__('store::models/st_reports.filters.all_categories')" :list="$categories"
                                        :selected_id="old('category_id', request('category_id'))">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('product_id', __('store::models/st_reports.filters.product') . ':') !!}
                                    <x-select2-input name="product_id" id="product_search_ajax" :placeholder="__('store::models/st_reports.filters.all_products')"
                                        :list="$products" :selected_id="old('product_id', request('product_id'))"
                                        data-ajax--url="{{ route('Lookup.getproducts') }}" data-ajax--cache="true">
                                    </x-select2-input>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('movement_type', __('store::models/st_reports.filters.movement_type') . ':') !!}
                                    <select name="movement_type" class="form-select form-select-solid">
                                        <option value="">{{ __('store::models/st_reports.filters.all_types') }}
                                        </option>
                                        @foreach ($movementTypes as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ request('movement_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('fromDate', __('store::models/st_reports.filters.date_from') . ':') !!}
                                    <div class="input-group" id="kt_td_picker_from" data-td-target-input="nearest"
                                        data-td-target-toggle="nearest">
                                        <input id="kt_td_picker_from_input" type="text" name="fromDate"
                                            class="form-control form-control-solid"
                                            data-td-target="#kt_td_picker_from"
                                            value="{{ old('fromDate', request('fromDate', $fromDate)) }}" />
                                        <span class="input-group-text" data-td-target="#kt_td_picker_from"
                                            data-td-toggle="datetimepicker">
                                            <i class="ki-duotone ki-calendar fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                    {!! Form::label('toDate', __('store::models/st_reports.filters.date_to') . ':') !!}
                                    <div class="input-group" id="kt_td_picker_to" data-td-target-input="nearest"
                                        data-td-target-toggle="nearest">
                                        <input id="kt_td_picker_to_input" type="text" name="toDate"
                                            class="form-control form-control-solid" data-td-target="#kt_td_picker_to"
                                            value="{{ old('toDate', request('toDate', $toDate)) }}" />
                                        <span class="input-group-text" data-td-target="#kt_td_picker_to"
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
                            <button type="submit" class="btn btn-sm btn-btc">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-btc" onclick="window.print()">
                                <i class="fa-solid fa-print me-1"></i> 
                            </button>
                            @if($movements !== null)
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
                            <a href="{{ route('store.reports.stockMovement') }}" class="btn btn-sm btn-btc">
                                <i class="fa-solid fa-circle-xmark me-1"></i> 
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                {{-- Results --}}
                @if ($movements !== null)
                    <!-- Print Header -->
                    <div class="report-header d-none-print" style="display: none;">
                        <h2>@lang('store::models/st_reports.types.stock_movement')</h2>
                        <div class="report-info" style="display: none;">
                            <div class="report-info-item">
                                <span class="report-info-label">@lang('store::models/st_reports.filters.store'):</span>
                                <span>{{ request('store_id') ? $stores->where('id', request('store_id'))->first()?->name ?? '-' : __('store::models/st_reports.filters.all_stores') }}</span>
                            </div>
                            <div class="report-info-item">
                                <span class="report-info-label">@lang('store::models/st_reports.filters.date_from'):</span>
                                <span>{{ request('fromDate') ?? '-' }}</span>
                            </div>
                            <div class="report-info-item">
                                <span class="report-info-label">@lang('store::models/st_reports.filters.date_to'):</span>
                                <span>{{ request('toDate') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                @lang('store::models/st_reports.types.stock_movement')
                                <span class="badge badge-light-primary ms-2">{{ $movements->total() }}
                                    @lang('lang.record')</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover align-middle">
                                    <thead class="bg-light-primary">
                                        <tr class="fw-bold text-gray-800">
                                            <th class="text-center">@lang('store::models/st_reports.columns.id')</th>
                                            <th>@lang('store::models/st_reports.columns.movement_number')</th>
                                            <th>@lang('store::models/st_reports.columns.date')</th>
                                            <th>@lang('store::models/st_reports.columns.movement_type')</th>
                                            <th>@lang('store::models/st_reports.columns.category')</th>
                                            <th>@lang('store::models/st_reports.columns.product')</th>
                                            <th>@lang('store::models/st_reports.columns.store')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.quantity')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.balance')</th>
                                            <th>@lang('store::models/st_reports.columns.unit')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.unit_cost')</th>
                                            <th class="text-center">@lang('store::models/st_reports.columns.total_cost')</th>
                                            <th>@lang('store::models/st_reports.columns.reference')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Opening Balance Row --}}
                                        <tr class="bg-light">
                                            <td class="text-center">-</td>
                                            <td colspan="7" class="fw-bold">@lang('store::models/st_reports.columns.opening_balance')</td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ number_format($openingBalance, 4) }}</td>
                                            <td colspan="4"></td>
                                        </tr>

                                        @forelse($movements as $i => $mv)
                                            <tr>
                                                <td class="text-center">
                                                    {{ ($movements->currentPage() - 1) * $movements->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $mv->movement_number }}</td>
                                                <td>{{ $mv->movement_date?->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge badge-light-info">
                                                        {{ $mv->movement_type_name }}
                                                    </span>
                                                </td>
                                                <td>{{ $mv->is_size ? $mv->productSize?->product?->category?->name : $mv->product?->category?->name }}
                                                </td>
                                                <td>{{ $mv->product_name }}</td>
                                                <td>{{ $mv->store?->name }}</td>
                                                <td
                                                    class="text-center fw-bold
                                                {{ $mv->stock_type === 'in' ? 'text-success' : 'text-danger' }}">
                                                    {{ $mv->stock_type === 'in' ? '+' : '-' }}{{ abs($mv->quantity) }}
                                                </td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ number_format($mv->running_balance, 4) }}
                                                </td>
                                                <td>{{ $mv->unit?->name }}</td>
                                                <td class="text-center">{{ number_format($mv->unit_cost, 2) }}</td>
                                                <td class="text-center fw-bold">{{ number_format($mv->total_cost, 2) }}
                                                </td>
                                                <td>{{ $mv->reference_number }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center text-muted py-10">
                                                    <i class="fa-solid fa-inbox fs-3x mb-3 d-block"></i>
                                                    @lang('lang.no_data')
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($movements->count() > 0)
                                        <tfoot>
                                            <tr class="fw-bold bg-light">
                                                <td colspan="11" class="text-end">@lang('lang.total'):</td>
                                                <td class="text-center fw-bolder text-primary">
                                                    {{ number_format($movements->sum('total_cost'), 2) }}
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $movements->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-20">
                            <i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i>
                            <h3 class="text-muted">@lang('store::models/st_reports.messages.select_filters')</h3>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if (typeof tempusDominus !== 'undefined') {
                const pickerFrom = document.getElementById('kt_td_picker_from');
                if (pickerFrom) {
                    new tempusDominus.TempusDominus(pickerFrom, {
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

            if ($('#product_search_ajax').length) {
                $('#product_search_ajax').select2({
                    placeholder: "@lang('store::models/st_reports.filters.all_products')",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('Lookup.getproducts') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            let results = data.results.map(function(item) {
                                return {
                                    id: (item.is_size ? 'size_' : 'prod_') + item.id,
                                    text: item.text
                                };
                            });
                            return {
                                results: results,
                                pagination: {
                                    more: data.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }
        });
    </script>
@endpush
