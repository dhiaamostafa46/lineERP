@extends('layouts.app')

@section('title', __('invoices::models/invoices_setting.plural'))

@section('content')
    <style>
        .nav-pills-custom .nav-link {
            border: 1px solid transparent;
            transition: all 0.3s ease;
            color: #7E8299;
            font-weight: 600;
        }

        .nav-pills-custom .nav-link:hover {
            background-color: rgba(0, 158, 247, 0.05);
        }

        .nav-pills-custom .nav-link.active {
            background-color: var(--bs-primary) !important;
            border: 1px solid var(--bs-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }

        .nav-pills-custom .nav-link.active i {
            color: #ffffff !important;
        }

        .setting-card {
            border: 0;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            background: #fff;
            margin-bottom: 1.5rem;
        }
    </style>






    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('invoices::models/invoices_setting.plural')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('invoices.Setting.index') }}" class="text-muted text-hover-primary">
                                @lang('invoices::models/invoices_setting.plural')
                            </a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">@lang('crud.edit')</li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('invoices.Setting.index') }}" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel')
                    </a>
                </div>
                <!--end::Actions-->
            </div>
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="clearfix"></div>

                <form action="{{ route('invoices.Setting.update', $settinga->id ?? 1) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card setting-card">
                        <!-- Tabs Header -->
                        <div class="card-header p-0 bg-transparent border-bottom-0" style="min-height: 0">
                            <ul class="nav nav-pills nav-pills-custom gap-3 p-4 pb-0" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active d-flex align-items-center py-3 px-4 fs-6" data-bs-toggle="tab"
                                        href="#tab_sales">
                                        <i class="bi bi-receipt fs-4 me-2"></i>
                                        @lang('invoices::models/invoices_setting.sections.sales_settings')
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex align-items-center py-3 px-4 fs-6" data-bs-toggle="tab"
                                        href="#tab_general">
                                        <i class="bi bi-gear fs-4 me-2"></i>
                                        @lang('invoices::models/invoices_setting.sections.general_settings')
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex align-items-center py-3 px-4 fs-6" data-bs-toggle="tab"
                                        href="#tab_taxes">
                                        <i class="bi bi-percent fs-4 me-2"></i>
                                        @lang('invoices::models/invoices_setting.sections.taxes_settings')
                                    </a>
                                </li>
                            </ul>
                            <hr class="text-muted opacity-25 mx-4 mb-0 mt-3">
                        </div>

                        <!-- Tabs Content -->
                        <div class="card-body p-4">
                            <div class="tab-content" id="myTabContent">

                                {{-- ================= TAB 1: Sales, Purchase, Quotation ================= --}}
                                <div class="tab-pane fade show active" id="tab_sales" role="tabpanel">
                                    <div class="row g-4">
                                        <!-- Sales Box -->
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100 bg-light-light">
                                                <h5 class="fw-bold mb-4 text-primary"><i
                                                        class="bi bi-shop me-2"></i>@lang('invoices::models/invoices_setting.sections.sales_settings')</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted required">@lang('invoices::models/invoices_setting.fields.sales_prefix')</label>
                                                        <input type="text" name="sales_prefix" id="sales_prefix"
                                                            class="form-control"
                                                            value="{{ old('sales_prefix', $settinga->sales_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted required">@lang('invoices::models/invoices_setting.fields.sales_next_number')</label>
                                                        <input type="number" name="sales_next_number" class="form-control"
                                                            value="{{ old('sales_next_number', $settinga->sales_next_number) }}">
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <div class="form-check form-switch">
                                                            <input type="hidden" name="sales_auto_post" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="sales_auto_post" value="1" id="sales_auto_post"
                                                                {{ old('sales_auto_post', $settinga->sales_auto_post) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold small ms-2"
                                                                for="sales_auto_post">@lang('invoices::models/invoices_setting.fields.sales_auto_post')</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.sales_terms')</label>
                                                        <textarea name="sales_terms" class="form-control" rows="3">{{ old('sales_terms', $settinga->sales_terms) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Purchase Box -->
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100 bg-light-light">
                                                <h5 class="fw-bold mb-4 text-success"><i
                                                        class="bi bi-cart me-2"></i>@lang('invoices::models/invoices_setting.sections.purchase_settings')</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted required">@lang('invoices::models/invoices_setting.fields.purchase_prefix')</label>
                                                        <input type="text" name="purchase_prefix" id="purchase_prefix"
                                                            class="form-control"
                                                            value="{{ old('purchase_prefix', $settinga->purchase_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted required">@lang('invoices::models/invoices_setting.fields.purchase_next_number')</label>
                                                        <input type="number" name="purchase_next_number"
                                                            class="form-control"
                                                            value="{{ old('purchase_next_number', $settinga->purchase_next_number) }}">
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <div class="form-check form-switch">
                                                            <input type="hidden" name="purchase_auto_post"
                                                                value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="purchase_auto_post" value="1"
                                                                id="purchase_auto_post"
                                                                {{ old('purchase_auto_post', $settinga->purchase_auto_post) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold small ms-2"
                                                                for="purchase_auto_post">@lang('invoices::models/invoices_setting.fields.purchase_auto_post')</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.purchase_terms')</label>
                                                        <textarea name="purchase_terms" class="form-control" rows="3">{{ old('purchase_terms', $settinga->purchase_terms) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Returns Box -->
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100 bg-light-light">
                                                <h5 class="fw-bold mb-4 text-danger"><i
                                                        class="bi bi-arrow-counterclockwise me-2"></i>@lang('crud.returns')
                                                </h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.sales_return_prefix')</label>
                                                        <input type="text" name="sales_return_prefix"
                                                            id="sales_return_prefix" class="form-control"
                                                            value="{{ old('sales_return_prefix', $settinga->sales_return_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.sales_return_next_number')</label>
                                                        <input type="number" name="sales_return_next_number"
                                                            class="form-control"
                                                            value="{{ old('sales_return_next_number', $settinga->sales_return_next_number) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.purchase_return_prefix')</label>
                                                        <input type="text" name="purchase_return_prefix"
                                                            id="purchase_return_prefix" class="form-control"
                                                            value="{{ old('purchase_return_prefix', $settinga->purchase_return_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.purchase_return_next_number')</label>
                                                        <input type="number" name="purchase_return_next_number"
                                                            class="form-control"
                                                            value="{{ old('purchase_return_next_number', $settinga->purchase_return_next_number) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.sales_debit_prefix')</label>
                                                        <input type="text" name="sales_debit_prefix"
                                                            id="sales_debit_prefix" class="form-control"
                                                            value="{{ old('sales_debit_prefix', $settinga->sales_debit_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.sales_debit_next_number')</label>
                                                        <input type="number" name="sales_debit_next_number"
                                                            class="form-control"
                                                            value="{{ old('sales_debit_next_number', $settinga->sales_debit_next_number) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quotation Box -->
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100 bg-light-light">
                                                <h5 class="fw-bold mb-4 text-info"><i
                                                        class="bi bi-file-earmark-text me-2"></i>@lang('invoices::models/invoices_setting.sections.quotation_settings')</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.quotation_prefix')</label>
                                                        <input type="text" name="quotation_prefix"
                                                            id="quotation_prefix" class="form-control"
                                                            value="{{ old('quotation_prefix', $settinga->quotation_prefix) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.quotation_next_number')</label>
                                                        <input type="number" name="quotation_next_number"
                                                            class="form-control"
                                                            value="{{ old('quotation_next_number', $settinga->quotation_next_number) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.quotation_validity_days')</label>
                                                        <input type="number" name="quotation_validity_days"
                                                            class="form-control"
                                                            value="{{ old('quotation_validity_days', $settinga->quotation_validity_days) }}">
                                                    </div>
                                                    <div class="col-12 mt-4">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.quotation_terms')</label>
                                                        <textarea name="quotation_terms" class="form-control" rows="3">{{ old('quotation_terms', $settinga->quotation_terms) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Purchase Order Box -->
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100 bg-light-light">
                                                <h5 class="fw-bold mb-4 text-dark"><i
                                                        class="bi bi-file-earmark-check me-2"></i>@lang('invoices::models/purchase_orders.plural')</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.purchase_order_prefix')</label>
                                                        <input type="text" name="purchase_order_prefix"
                                                            id="purchase_order_prefix" class="form-control"
                                                            value="{{ old('purchase_order_prefix', $settinga->purchase_order_prefix) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.purchase_order_next_number')</label>
                                                        <input type="number" name="purchase_order_next_number"
                                                            class="form-control"
                                                            value="{{ old('purchase_order_next_number', $settinga->purchase_order_next_number) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                {{-- ================= TAB 2: General Settings ================= --}}
                                <div class="tab-pane fade" id="tab_general" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 h-100">
                                                <div class="form-check form-switch mb-3">
                                                    <input type="hidden" name="show_logo_in_print" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="show_logo_in_print" value="1" id="show_logo_in_print"
                                                        {{ old('show_logo_in_print', $settinga->show_logo_in_print) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="show_logo_in_print">@lang('invoices::models/invoices_setting.fields.show_logo_in_print')</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input type="hidden" name="show_customer_balance" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="show_customer_balance" value="1"
                                                        id="show_customer_balance"
                                                        {{ old('show_customer_balance', $settinga->show_customer_balance) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="show_customer_balance">@lang('invoices::models/invoices_setting.fields.show_customer_balance')</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input type="hidden" name="show_product_image" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="show_product_image" value="1" id="show_product_image"
                                                        {{ old('show_product_image', $settinga->show_product_image) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="show_product_image">@lang('invoices::models/invoices_setting.fields.show_product_image')</label>
                                                </div>

                                                <div class="form-check form-switch mb-3">
                                                    <input type="hidden" name="allow_negative_stock" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="allow_negative_stock" value="1"
                                                        id="allow_negative_stock"
                                                        {{ old('allow_negative_stock', $settinga->allow_negative_stock) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="allow_negative_stock">@lang('invoices::models/invoices_setting.fields.allow_negative_stock')</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input type="hidden" name="require_cost_center" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="require_cost_center" value="1"
                                                        id="require_cost_center"
                                                        {{ old('require_cost_center', $settinga->require_cost_center) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="require_cost_center">@lang('invoices::models/invoices_setting.fields.require_cost_center')</label>
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.invoice_footer_text')</label>
                                                    <input type="text" name="invoice_footer_text" class="form-control"
                                                        value="{{ old('invoice_footer_text', $settinga->invoice_footer_text) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= TAB 3: Taxes and Zakat ================= --}}
                                <div class="tab-pane fade" id="tab_taxes" role="tabpanel">
                                    <div class="row g-4">
                                        <!-- VAT Box -->
                                        <div class="col-md-12">
                                            <div class="border rounded-3 p-4 h-100">
                                                <h5 class="fw-bold mb-4 text-dark"><i
                                                        class="bi bi-percent me-2"></i>@lang('invoices::models/invoices_setting.fields.vat_title')</h5>
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label fw-bold small text-muted">@lang('invoices::models/invoices_setting.fields.default_vat_rate')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" min="0"
                                                            max="100" name="default_vat_rate" class="form-control"
                                                            value="{{ old('default_vat_rate', $settinga->default_vat_rate) }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted mt-1 d-block">@lang('invoices::models/invoices_setting.help.default_vat_rate')</small>
                                                </div>
                                                <div class="form-check form-switch mt-4">
                                                    <input type="hidden" name="prices_include_vat" value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="prices_include_vat" value="1" id="prices_include_vat"
                                                        {{ old('prices_include_vat', $settinga->prices_include_vat) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold ms-2"
                                                        for="prices_include_vat">@lang('invoices::models/invoices_setting.fields.prices_include_vat')</label>
                                                </div>
                                                <div class="alert alert-info mt-3 py-2 px-3" id="vat_hint_included"
                                                    style="display: {{ old('prices_include_vat', $settinga->prices_include_vat) ? 'block' : 'none' }}">
                                                    <small><i class="bi bi-info-circle me-1"></i>
                                                        @lang('invoices::models/invoices_setting.fields.vat_hint_included')</small>
                                                </div>
                                                <div class="alert alert-warning mt-3 py-2 px-3" id="vat_hint_excluded"
                                                    style="display: {{ old('prices_include_vat', $settinga->prices_include_vat) ? 'none' : 'block' }}">
                                                    <small><i class="bi bi-info-circle me-1"></i>
                                                        @lang('invoices::models/invoices_setting.fields.vat_hint_excluded')</small>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="card-footer bg-transparent border-top p-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('invoices.Setting.index') }}"
                                    class="btn btn-light">@lang('crud.cancel')</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> @lang('crud.save')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle VAT hint on checkbox change
            const vatChk = document.getElementById('prices_include_vat');
            const hintOn = document.getElementById('vat_hint_included');
            const hintOff = document.getElementById('vat_hint_excluded');

            function updateVatHint() {
                if (vatChk.checked) {
                    hintOn.style.display = 'block';
                    hintOff.style.display = 'none';
                } else {
                    hintOn.style.display = 'none';
                    hintOff.style.display = 'block';
                }
            }

            if (vatChk) {
                vatChk.addEventListener('change', updateVatHint);
            }

            // Prevent spaces in prefix fields
            const prefixIds = [
                'sales_prefix', 'sales_return_prefix', 'sales_debit_prefix',
                'purchase_prefix', 'purchase_return_prefix',
                'quotation_prefix'
            ];

            prefixIds.forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() {
                        this.value = this.value.replace(/\s+/g, '');
                    });
                    el.addEventListener('keydown', function(e) {
                        if (e.key === ' ') e.preventDefault();
                    });
                }
            });
        });
    </script>
@endpush
