<style>
    .zatca-env-card-mod {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: start;
        height: 100%;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    
    .zatca-env-card-mod::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 0;
        background: linear-gradient(90deg, var(--bs-secondary), var(--bs-primary));
        transition: height 0.3s ease;
    }
    
    .zatca-env-input-mod:checked + .zatca-env-card-mod::before {
        height: 3px;
    }
    
    .zatca-env-card-mod:hover {
        border-color: var(--bs-primary);
        box-shadow: 0 4px 15px rgba(106, 102, 157, 0.1);
    }
    
    .zatca-env-input-mod:checked + .zatca-env-card-mod {
        border-color: var(--bs-primary);
        background: linear-gradient(135deg, #fff 0%, rgba(106, 102, 157, 0.05) 100%);
        box-shadow: 0 4px 20px rgba(106, 102, 157, 0.15);
    }
    
    .zatca-env-input-mod {
        display: none;
    }
    
    .zatca-env-input-mod:checked + .zatca-env-card-mod .zatca-env-title-mod {
        color: var(--bs-primary);
    }
    
    .form-row-mod {
        transition: all 0.3s ease;
        padding: 0.75rem;
        margin: 0 -0.75rem;
        border-radius: 6px;
    }
    
    .form-row-mod:hover {
        background: rgba(106, 102, 157, 0.03);
    }
</style>

<!-- Section Badge -->
<div class="section-badge mb-4">
    <i class="bi bi-shield-check"></i>
    @lang('invoices::models/invoices_setting.fields.zatca_settings')
</div>

<div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-4 mb-4 border border-primary border-dashed align-items-center">
    <i class="fas fa-shield-alt fs-2hx text-primary me-4 mb-4 mb-sm-0"></i>
    <div class="d-flex flex-column pe-0 pe-sm-10 flex-grow-1">
        <h5 class="mb-1 text-primary">@lang('invoices::models/invoices_setting.fields.zatca_settings')</h5>
        <span class="mb-0">@lang('invoices::models/invoices_setting.fields.zatca_section_desc')</span>
    </div>
    <div class="d-flex align-items-center mt-4 mt-sm-0">
        @if(!empty($zatca_setting->binary_security_token))
            <div class="d-flex align-items-center">
                <span class="badge fs-6 fw-bold px-4 py-2 me-3" style="background: linear-gradient(135deg, var(--bs-secondary), var(--bs-primary)); color: #fff;">
                    <i class="fas fa-check-circle me-2"></i>
                    @lang('invoices::models/invoices_setting.fields.zatca_status_linked')
                    @if($zatca_setting->environment == 'production')
                        (@lang('invoices::models/invoices_setting.fields.zatca_env_prod_badge'))
                    @elseif($zatca_setting->environment == 'simulation')
                        (@lang('invoices::models/invoices_setting.fields.zatca_env_sim_badge'))
                    @elseif($zatca_setting->environment == 'sandbox')
                        (@lang('invoices::models/invoices_setting.fields.environment_sandbox'))
                    @endif
                </span>
                
                @if($zatca_setting->status == 'linked' && $zatca_setting->environment == 'production')
                    <form action="{{ route('invoices.Setting.zatca.production', $zatca_setting->id) }}" method="POST" class="ms-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-certificate me-2"></i>
                            @lang('invoices::models/invoices_setting.fields.zatca_request_production_btn')
                        </button>
                    </form>
                @endif
            </div>
        @else
            <span class="badge fs-6 fw-bold px-4 py-2 me-3" style="background: #dc3545; color: #fff;">
                <i class="fas fa-times-circle me-2"></i>
                @lang('invoices::models/invoices_setting.fields.zatca_status_not_linked')
            </span>
        @endif
    </div>
</div>

<form action="{{ route('invoices.Setting.zatcaStore', $zatca_setting->id ?? 0) }}" method="POST">
    @csrf
    <input type="hidden" name="zatca_isVatGroup" value="0">

    <div class="row g-4">
        <!-- Environment Selection -->
        <div class="col-12">
            <h5 class="fw-bold mb-4" style="color: var(--bs-primary-active);">
                <i class="bi bi-cloud"></i>
                @lang('invoices::models/invoices_setting.sections.zatca_environment_settings')
            </h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="w-100">
                        <input type="radio" name="zatca_environment" value="production" class="zatca-env-input-mod" {{ old('zatca_environment', $zatca_setting->environment ?? '') == 'production' ? 'checked' : '' }}>
                        <div class="zatca-env-card-mod">
                            <h6 class="zatca-env-title-mod fw-bold mb-2">@lang('invoices::models/invoices_setting.fields.zatca_env_production_title')</h6>
                            <p class="small text-muted mb-0">{!! __('invoices::models/invoices_setting.fields.zatca_env_production_desc') !!}</p>
                        </div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="w-100">
                        <input type="radio" name="zatca_environment" value="simulation" class="zatca-env-input-mod" {{ old('zatca_environment', $zatca_setting->environment ?? '') == 'simulation' ? 'checked' : '' }}>
                        <div class="zatca-env-card-mod">
                            <h6 class="zatca-env-title-mod fw-bold mb-2">@lang('invoices::models/invoices_setting.fields.zatca_env_simulation_title')</h6>
                            <p class="small text-muted mb-0">{!! __('invoices::models/invoices_setting.fields.zatca_env_simulation_desc') !!}</p>
                        </div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="w-100">
                        <input type="radio" name="zatca_environment" value="sandbox" class="zatca-env-input-mod" {{ old('zatca_environment', $zatca_setting->environment ?? '') == 'sandbox' ? 'checked' : '' }}>
                        <div class="zatca-env-card-mod">
                            <h6 class="zatca-env-title-mod fw-bold mb-2">@lang('invoices::models/invoices_setting.fields.environment_sandbox')</h6>
                            <p class="small text-muted mb-0">{!! __('invoices::models/invoices_setting.fields.zatca_env_sandbox_desc') !!}</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="col-12">
            <h5 class="fw-bold mb-4" style="color: var(--bs-primary-active);">
                <i class="bi bi-building"></i>
                @lang('invoices::models/invoices_setting.sections.zatca_basic_fields')
            </h5>
            <div class="row g-3">
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_organization_name')</label>
                    <input type="text" name="zatca_organization_name" class="mod-form-control" value="{{ old('zatca_organization_name', $zatca_setting->organization_name ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_vat_number')</label>
                    <input type="text" name="zatca_vat_number" class="mod-form-control" value="{{ old('zatca_vat_number', $zatca_setting->vat_number ?? '') }}" maxlength="15">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_organization_unit')</label>
                    <input type="text" name="zatca_organization_unit_name" class="mod-form-control" value="{{ old('zatca_organization_unit_name', $zatca_setting->organization_unit_name ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_registered_address')</label>
                    <input type="text" name="zatca_registered_address" class="mod-form-control" value="{{ old('zatca_registered_address', $zatca_setting->registered_address ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_activity_classification')</label>
                    <input type="text" name="zatca_activity_classification" class="mod-form-control" value="{{ old('zatca_activity_classification', $zatca_setting->activity_classification ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_invoice_type')</label>
                    <select name="zatca_inv_type" class="mod-form-control">
                        <option value="0100" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '') == '0100' ? 'selected' : '' }}>@lang('invoices::models/invoices_setting.fields.zatca_invoice_simple')</option>
                        <option value="1000" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '') == '1000' ? 'selected' : '' }}>@lang('invoices::models/invoices_setting.fields.zatca_invoice_standard')</option>
                        <option value="1100" {{ old('zatca_inv_type', $zatca_setting->inv_type ?? '1100') == '1100' ? 'selected' : '' }}>@lang('invoices::models/invoices_setting.fields.zatca_invoice_both')</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- OTP -->
        <div class="col-12">
            <h5 class="fw-bold mb-4" style="color: var(--bs-primary-active);">
                <i class="bi bi-key"></i>
                @lang('invoices::models/invoices_setting.fields.zatca_otp')
            </h5>
            <div class="row g-3">
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_otp')</label>
                    <input type="text" name="zatca_otp" class="mod-form-control" placeholder="Enter OTP" value="{{ old('zatca_otp', $zatca_setting->otp ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_otp_confirmation')</label>
                    <input type="text" name="zatca_otp_confirmation" class="mod-form-control" placeholder="Confirm OTP" value="{{ old('zatca_otp_confirmation', $zatca_setting->otp_confirmation ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="col-12">
            <h5 class="fw-bold mb-4" style="color: var(--bs-primary-active);">
                <i class="bi bi-info-circle"></i>
                @lang('invoices::models/invoices_setting.sections.zatca_additional_info')
            </h5>
            <div class="row g-3">
                <div class="form-group col-md-3 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_building_number')</label>
                    <input type="text" name="zatca_building_number" class="mod-form-control" value="{{ old('zatca_building_number', $zatca_setting->building_number ?? '') }}" maxlength="4">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_street_name')</label>
                    <input type="text" name="zatca_street_name" class="mod-form-control" value="{{ old('zatca_street_name', $zatca_setting->street_name ?? '') }}">
                </div>
                <div class="form-group col-md-3 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_status')</label>
                    <input type="text" name="zatca_status" class="mod-form-control" value="{{ old('zatca_status', $zatca_setting->status ?? '') }}" readonly>
                </div>
                <div class="form-group col-md-4 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_district_name')</label>
                    <input type="text" name="zatca_district_name" class="mod-form-control" value="{{ old('zatca_district_name', $zatca_setting->district_name ?? '') }}">
                </div>
                <div class="form-group col-md-4 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_city_name')</label>
                    <input type="text" name="zatca_city_name" class="mod-form-control" value="{{ old('zatca_city_name', $zatca_setting->city_name ?? '') }}">
                </div>
                <div class="form-group col-md-4 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_postal_code')</label>
                    <input type="text" name="zatca_postal_code" class="mod-form-control" value="{{ old('zatca_postal_code', $zatca_setting->postal_code ?? '') }}" maxlength="5">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_uuid')</label>
                    <input type="text" name="zatca_uuid" class="mod-form-control" value="{{ old('zatca_uuid', $zatca_setting->uuid ?? '') }}" readonly>
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_common_name')</label>
                    <input type="text" name="zatca_common_name" class="mod-form-control" value="{{ old('zatca_common_name', $zatca_setting->common_name ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_cv')</label>
                    <input type="text" name="zatca_cv" class="mod-form-control" value="{{ old('zatca_cv', $zatca_setting->cv ?? '') }}">
                </div>
                <div class="form-group col-md-6 form-row-mod">
                    <label class="mod-form-label">@lang('invoices::models/invoices_setting.fields.zatca_serial_number')</label>
                    <input type="text" name="zatca_serial_number" class="mod-form-control" value="{{ old('zatca_serial_number', $zatca_setting->serial_number ?? '') }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-6">
        <a href="{{ route('invoices.Setting.edit', $settinga->id ?? 1) }}" class="mod-btn-secondary">
            <i class="bi bi-x-lg me-1"></i>@lang('crud.cancel')
        </a>
        <button type="submit" class="mod-btn-primary">
            <i class="bi bi-save me-1"></i>@lang('crud.save')
        </button>
    </div>
</form>