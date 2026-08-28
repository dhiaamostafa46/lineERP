@extends('layouts.app')

@section('title', __('invoices::models/invoices_setting.sections.taxes_and_zakat'))

@section('content')
<style>
    /* ── ZATCA Settings Custom Modern Design System ────────────────────────────── */
    .zatca-page-card {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .zatca-page-header {
        background: linear-gradient(135deg, rgba(106, 102, 157, 0.08) 0%, rgba(27, 50, 91, 0.05) 100%);
        border-bottom: 1px solid #edf2f7;
        padding: 1.5rem 2rem;
    }

    .zatca-section-heading {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--bs-primary-active, #1B325B);
        margin-bottom: 1.25rem;
        margin-top: 1.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px dashed #edf2f7;
    }

    .zatca-section-heading i {
        font-size: 1.25rem;
        color: var(--bs-primary, #6A669D);
    }

    .zatca-section-heading:first-of-type {
        margin-top: 0;
    }

    /* Environment Selection Cards */
    .env-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .env-card-label {
        cursor: pointer;
        display: block;
        margin: 0;
    }

    .env-radio-input {
        display: none;
    }

    .env-card-box {
        position: relative;
        padding: 1.25rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.85rem;
        background: #ffffff;
        transition: all 0.25s ease-in-out;
        height: 100%;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .env-card-box:hover {
        border-color: var(--bs-primary, #6A669D);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(106, 102, 157, 0.08);
    }

    .env-radio-input:checked + .env-card-box {
        border-color: var(--bs-primary, #6A669D);
        background: linear-gradient(135deg, #ffffff 0%, rgba(106, 102, 157, 0.05) 100%);
        box-shadow: 0 8px 25px rgba(106, 102, 157, 0.15);
    }

    .env-card-box .env-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.25s ease;
    }

    .env-radio-input:checked + .env-card-box .env-icon {
        background: var(--bs-primary, #6A669D);
        color: #ffffff;
    }

    .env-card-box .env-check {
        position: absolute;
        top: 1rem;
        inset-inline-end: 1rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .env-radio-input:checked + .env-card-box .env-check {
        border-color: var(--bs-primary, #6A669D);
        background: var(--bs-primary, #6A669D);
    }

    .env-card-box .env-check::after {
        content: '';
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
        display: none;
    }

    .env-radio-input:checked + .env-card-box .env-check::after {
        display: block;
    }

    /* Form Fields & Labels */
    .zatca-form-label {
        font-weight: 600;
        font-size: 0.92rem;
        color: #1e293b;
        margin-bottom: 0.45rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .zatca-form-label.required::after {
        content: '*';
        color: #dc2626;
        font-weight: bold;
    }

    .zatca-custom-input {
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.2s ease;
        width: 100%;
    }

    .zatca-custom-input:focus {
        background-color: #ffffff;
        border-color: var(--bs-primary, #6A669D);
        outline: none;
        box-shadow: 0 0 0 3px rgba(106, 102, 157, 0.15);
    }

    /* Mode Navigation Tabs */
    .zatca-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 0.75rem;
    }

    .zatca-nav-tabs .nav-link {
        border: none;
        border-radius: 0.6rem 0.6rem 0 0;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #64748b;
        position: relative;
        background: transparent;
        transition: all 0.2s ease;
    }

    .zatca-nav-tabs .nav-link:hover {
        color: var(--bs-primary, #6A669D);
        background: rgba(106, 102, 157, 0.04);
    }

    .zatca-nav-tabs .nav-link.active {
        color: var(--bs-primary-active, #1B325B);
        background: #ffffff;
        font-weight: 700;
    }

    .zatca-nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--bs-primary, #6A669D);
        border-radius: 3px 3px 0 0;
    }

    /* Status Alerts */
    .zatca-status-card {
        border-radius: 0.85rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .zatca-status-card.is-linked {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.12) 0%, rgba(39, 174, 96, 0.05) 100%);
        border: 1.5px solid #a7f3d0;
    }

    .zatca-status-card.not-linked {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(217, 119, 6, 0.05) 100%);
        border: 1.5px solid #fde68a;
    }
</style>

<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-primary fs-2"></i>
                        @lang('invoices::models/invoices_setting.sections.taxes_and_zakat')
                    </span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('invoices.Setting.index') }}" class="text-muted text-hover-primary">@lang('invoices::models/invoices_setting.plural')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('invoices::models/invoices_setting.fields.zatca_settings')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('invoices.Setting.index') }}" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="bi bi-arrow-right"></i> @lang('crud.back')
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="zatca-page-card">
                <!-- Card Header -->
                <div class="zatca-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary p-2">
                            <i class="bi bi-shield-lock-fill fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-gray-900">@lang('invoices::models/invoices_setting.fields.zatca_settings')</h4>
                            <span class="text-muted fs-7">@lang('invoices::models/invoices_setting.fields.zatca_section_desc')</span>
                        </div>
                    </div>

                    <!-- Current Mode Badge -->
                    <div>
                        <span class="badge bg-light-primary text-primary border border-primary border-opacity-25 px-4 py-3 fs-7 fw-bold">
                            <i class="bi bi-diagram-3-fill me-1 text-primary"></i>
                            @lang('invoices::models/invoices_setting.fields.current_organization_mode'): 
                            {{ @$organization->tax_registration_type === 'branches' ? __('models/Organization.fields.tax_registration_type_branches') : __('models/Organization.fields.tax_registration_type_unified') }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-8 p-lg-10">
                    <!-- Status Banner -->
                    @if (!empty($zatca_setting->binary_security_token))
                        <div class="zatca-status-card is-linked mb-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-45px symbol-circle bg-success bg-opacity-20 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-patch-check-fill fs-2 text-success"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h5 class="fw-bold text-success mb-0">@lang('invoices::models/invoices_setting.fields.zatca_status_linked')</h5>
                                        <span class="badge bg-success text-white fs-8 px-2 py-1">
                                            {{ $zatca_setting->environment == 'production' ? __('invoices::models/invoices_setting.fields.zatca_env_prod_badge') : __('invoices::models/invoices_setting.fields.environment_sandbox') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 fs-7 mb-0">@lang('invoices::models/invoices_setting.fields.zatca_section_desc')</p>
                                </div>
                            </div>
                            @if ($zatca_setting->status == 'linked' && $zatca_setting->environment == 'production')
                                <form action="{{ route('invoices.Setting.zatca.production', $zatca_setting->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $branch_id ?? '' }}">
                                    <button type="submit" class="btn btn-sm btn-primary shadow-sm px-5">
                                        <i class="bi bi-award-fill me-1"></i> @lang('invoices::models/invoices_setting.fields.zatca_request_production_btn')
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="zatca-status-card not-linked mb-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-45px symbol-circle bg-warning bg-opacity-20 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-warning mb-1">@lang('invoices::models/invoices_setting.fields.zatca_status_not_linked')</h5>
                                    <p class="text-gray-700 fs-7 mb-0">@lang('invoices::models/invoices_setting.fields.zatca_section_desc')</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tax Mode Selection Tabs -->
                    <ul class="nav zatca-nav-tabs mb-8">
                        <li class="nav-item">
                            <a class="nav-link {{ empty($branch_id) ? 'active' : '' }}" 
                               href="{{ route('invoices.Setting.zatca', ['tax_type' => 'unified']) }}">
                                <i class="bi bi-building me-2"></i>
                                @lang('models/Organization.fields.tax_registration_type_unified')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ !empty($branch_id) ? 'active' : '' }}" 
                               href="{{ route('invoices.Setting.zatca', ['branch_id' => $branches->first()->id ?? 0, 'tax_type' => 'branches']) }}">
                                <i class="bi bi-diagram-3 me-2"></i>
                                @lang('models/Organization.fields.tax_registration_type_branches')
                            </a>
                        </li>
                    </ul>

                    @if(!empty($branch_id))
                        <!-- Branch Selector -->
                        <div class="card bg-light-subtle border border-dashed border-gray-300 p-4 rounded-3 mb-8">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="zatca-form-label required mb-0">
                                        <i class="bi bi-geo-alt-fill text-primary"></i>
                                        @lang('invoices::models/invoices_setting.fields.selected_branch_for_zatca')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-select form-select-solid" onchange="window.location.href=this.value">
                                        @foreach($branches as $b)
                                            <option value="{{ route('invoices.Setting.zatca', ['branch_id' => $b->id, 'tax_type' => 'branches']) }}"
                                                {{ $branch_id == $b->id ? 'selected' : '' }}>
                                                {{ $b->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Main Form -->
                    <form action="{{ route('invoices.Setting.zatcaStore', $zatca_setting->id ?? 0) }}" method="POST">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branch_id ?? '' }}">
                        <!-- Hidden Technical Fields -->
                        <input type="hidden" name="zatca_isVatGroup" value="0">
                        <input type="hidden" name="zatca_status" value="{{ $zatca_setting->status ?? '' }}">
                        <input type="hidden" name="zatca_uuid" value="{{ $zatca_setting->uuid ?? '' }}">
                        <input type="hidden" name="zatca_serial_number" value="{{ $zatca_setting->serial_number ?? '' }}">

                        <!-- 1. Environment Selection -->
                        <div class="zatca-section-heading">
                            <i class="bi bi-hdd-network-fill"></i>
                            @lang('invoices::models/invoices_setting.sections.zatca_environment_settings')
                        </div>

                        <div class="env-grid mb-8">
                            <!-- Production Card -->
                            <label class="env-card-label">
                                <input type="radio" name="zatca_environment" value="production" class="env-radio-input"
                                    {{ old('zatca_environment', $zatca_setting->environment ?? '') == 'production' ? 'checked' : '' }}>
                                <div class="env-card-box">
                                    <div class="env-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-4">
                                        <div class="fw-bold fs-6 text-gray-900 mb-1">
                                            @lang('invoices::models/invoices_setting.fields.zatca_env_production_title')
                                        </div>
                                        <div class="text-muted fs-8">
                                            {!! __('invoices::models/invoices_setting.fields.zatca_env_production_desc') !!}
                                        </div>
                                    </div>
                                    <div class="env-check"></div>
                                </div>
                            </label>

                            <!-- Sandbox Card -->
                            <label class="env-card-label">
                                <input type="radio" name="zatca_environment" value="sandbox" class="env-radio-input"
                                    {{ old('zatca_environment', $zatca_setting->environment ?? 'sandbox') == 'sandbox' ? 'checked' : '' }}>
                                <div class="env-card-box">
                                    <div class="env-icon">
                                        <i class="bi bi-code-slash"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-4">
                                        <div class="fw-bold fs-6 text-gray-900 mb-1">
                                            @lang('invoices::models/invoices_setting.fields.environment_sandbox')
                                        </div>
                                        <div class="text-muted fs-8">
                                            {!! __('invoices::models/invoices_setting.fields.zatca_env_sandbox_desc') !!}
                                        </div>
                                    </div>
                                    <div class="env-check"></div>
                                </div>
                            </label>
                        </div>

                        <!-- 2. Basic Info -->
                        <div class="zatca-section-heading">
                            <i class="bi bi-card-text"></i>
                            @lang('invoices::models/invoices_setting.sections.zatca_basic_fields')
                        </div>

                        <div class="row g-4 mb-6">
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_common_name')</label>
                                <input type="text" name="zatca_common_name" class="zatca-custom-input"
                                    placeholder="e.g. My Company HQ"
                                    value="{{ old('zatca_common_name', $zatca_setting->common_name ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_organization_name')</label>
                                <input type="text" name="zatca_organization_name" class="zatca-custom-input"
                                    placeholder="اسم المنشأة كما في السجل"
                                    value="{{ old('zatca_organization_name', $zatca_setting->organization_name ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_vat_number')</label>
                                <input type="text" name="zatca_vat_number" class="zatca-custom-input"
                                    placeholder="300000000000003"
                                    value="{{ old('zatca_vat_number', $zatca_setting->vat_number ?? '') }}"
                                    maxlength="15" required>
                            </div>

                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_cv')</label>
                                <input type="text" name="zatca_cv" class="zatca-custom-input"
                                    value="{{ old('zatca_cv', $zatca_setting->cv ?? '') }}"
                                    placeholder="رقم السجل التجاري (10 أرقام)" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_organization_unit')</label>
                                <input type="text" name="zatca_organization_unit_name" class="zatca-custom-input"
                                    placeholder="الفرع الرئيسي / RIYADH"
                                    value="{{ old('zatca_organization_unit_name', $zatca_setting->organization_unit_name ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_activity_classification')</label>
                                <input type="text" name="zatca_activity_classification" class="zatca-custom-input"
                                    placeholder="e.g. Retail / IT Services"
                                    value="{{ old('zatca_activity_classification', $zatca_setting->activity_classification ?? '') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_invoice_type')</label>
                                <select name="zatca_inv_type" class="zatca-custom-input form-select">
                                    <option value="0100" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '') == '0100' ? 'selected' : '' }}>
                                        @lang('invoices::models/invoices_setting.fields.zatca_invoice_simple')
                                    </option>
                                    <option value="1000" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '') == '1000' ? 'selected' : '' }}>
                                        @lang('invoices::models/invoices_setting.fields.zatca_invoice_standard')
                                    </option>
                                    <option value="1100" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '1100') == '1100' ? 'selected' : '' }}>
                                        @lang('invoices::models/invoices_setting.fields.zatca_invoice_both')
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_registered_address')</label>
                                <input type="text" name="zatca_registered_address" class="zatca-custom-input"
                                    placeholder="العنوان المسجل رسمياً"
                                    value="{{ old('zatca_registered_address', $zatca_setting->registered_address ?? '') }}" required>
                            </div>
                        </div>

                        <!-- 3. OTP Section -->
                        <div class="zatca-section-heading">
                            <i class="bi bi-key-fill"></i>
                            @lang('invoices::models/invoices_setting.fields.zatca_otp')
                        </div>

                        <div class="alert alert-dismissible bg-light-primary d-flex align-items-center p-4 mb-6 border border-primary border-dashed rounded-3">
                            <i class="bi bi-info-circle-fill fs-2 text-primary me-3"></i>
                            <div class="fs-7 text-gray-800">
                                أدخل رمز التفعيل (OTP) المستخرج من منصة فاتورة لإتمام عملية الربط وإصدار شهادة الامتثال الفنية مباشرة.
                            </div>
                        </div>

                        <div class="row g-4 mb-6">
                            <div class="col-md-6">
                                <label class="zatca-form-label">@lang('invoices::models/invoices_setting.fields.zatca_otp')</label>
                                <input type="text" name="zatca_otp" class="zatca-custom-input font-monospace"
                                    placeholder="123456"
                                    value="{{ old('zatca_otp', $zatca_setting->otp ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="zatca-form-label">@lang('invoices::models/invoices_setting.fields.zatca_otp_confirmation')</label>
                                <input type="text" name="zatca_otp_confirmation" class="zatca-custom-input font-monospace"
                                    placeholder="123456"
                                    value="{{ old('zatca_otp_confirmation', $zatca_setting->otp_confirmation ?? '') }}">
                            </div>
                        </div>

                        <!-- 4. Location & Address Info -->
                        <div class="zatca-section-heading">
                            <i class="bi bi-geo-alt-fill"></i>
                            @lang('invoices::models/invoices_setting.sections.zatca_additional_info')
                        </div>

                        <div class="row g-4 mb-8">
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_building_number')</label>
                                <input type="text" name="zatca_building_number" class="zatca-custom-input"
                                    placeholder="1234"
                                    value="{{ old('zatca_building_number', $zatca_setting->building_number ?? '') }}"
                                    maxlength="4" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_street_name')</label>
                                <input type="text" name="zatca_street_name" class="zatca-custom-input"
                                    placeholder="اسم الشارع"
                                    value="{{ old('zatca_street_name', $zatca_setting->street_name ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_district_name')</label>
                                <input type="text" name="zatca_district_name" class="zatca-custom-input"
                                    placeholder="اسم الحي"
                                    value="{{ old('zatca_district_name', $zatca_setting->district_name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_city_name')</label>
                                <input type="text" name="zatca_city_name" class="zatca-custom-input"
                                    placeholder="الرياض / جدة / ..."
                                    value="{{ old('zatca_city_name', $zatca_setting->city_name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="zatca-form-label required">@lang('invoices::models/invoices_setting.fields.zatca_postal_code')</label>
                                <input type="text" name="zatca_postal_code" class="zatca-custom-input"
                                    placeholder="12345"
                                    value="{{ old('zatca_postal_code', $zatca_setting->postal_code ?? '') }}"
                                    maxlength="5" required>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end align-items-center gap-3 pt-6 border-top">
                            <a href="{{ route('invoices.Setting.index') }}" class="btn btn-light px-8 py-3 fw-semibold">
                                @lang('crud.cancel')
                            </a>
                            <button type="submit" class="btn btn-primary px-10 py-3 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> @lang('crud.save')
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
