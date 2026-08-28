<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="col-sm-6">
            <div class="d-flex flex-column mb-8 fv-row">
                {!! Form::label($locale . '[name]', $language . ' ' . __('models/Organization.fields.organization_name') . ':', [
                    'class' => 'form-label',
                ]) !!}
                {!! Form::text($locale . '[name]', isset($Organization) ? $Organization->translate($locale)->name : null, [
                    'class' => 'form-control form-control-solid',
                ]) !!}
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <!-- VAT Number Field -->
    <div class="col-sm-6" style="display: none">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('tax_number', __('models/Organization.fields.tax_number') . ':', ['class' => 'form-label']) !!}
            {!! Form::number('tax_number', old('tax_number', @optional($Organization)->tax_number), [
                'class' => 'form-control form-control-solid',
            ]) !!}
        </div>
    </div>

    <!-- Commercial Registration Field -->
    <div class="col-sm-6">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('CR', __('models/Organization.fields.commercial_registration_number') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::number('CR', old('CR', @optional($Organization)->CR), ['class' => 'form-control form-control-solid']) !!}
        </div>
    </div>

    <!-- Chamber Number Field -->
    <div class="col-sm-6">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('chamber_no', __('models/Organization.fields.chamber_number') . ':', ['class' => 'form-label']) !!}
            {!! Form::number('chamber_no', old('chamber_no', @optional($Organization)->chamber_no), [
                'class' => 'form-control form-control-solid',
            ]) !!}
        </div>
    </div>
</div>

<div class="row">
    <!-- Organization Number Field -->
    <div class="col-sm-6">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('organization_number', __('models/Organization.fields.organization_number') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::number(
                'organization_number',
                old('organization_number', @optional($Organization)->organization_number),
                ['class' => 'form-control form-control-solid'],
            ) !!}
        </div>
    </div>

    <!-- Insurance Subscription Number Field -->
    <div class="col-sm-6">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('insurance_sub_no', __('models/Organization.fields.insurance_subscription_number') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::number('insurance_sub_no', old('insurance_sub_no', @optional($Organization)->insurance_sub_no), [
                'class' => 'form-control form-control-solid',
            ]) !!}
        </div>
    </div>
</div>

<div class="row">
    <!-- National Address Field -->
    <div class="col-sm-4">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('national_address', __('models/Organization.fields.national_address') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::text('national_address', old('national_address', @optional($Organization)->national_address), [
                'class' => 'form-control form-control-solid',
            ]) !!}
        </div>
    </div>

    <!-- Tax Number Field -->
    <div class="col-sm-4">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('tax_number', __('models/Organization.fields.tax_number') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::text('tax_number', old('tax_number', @optional($Organization)->tax_number), [
                'class' => 'form-control form-control-solid',
            ]) !!}
        </div>
    </div>

    <!-- Tax Registration Type Field -->
    <div class="col-sm-4">
        <div class="d-flex flex-column mb-8 fv-row">
            {!! Form::label('tax_registration_type', __('models/Organization.fields.tax_registration_type') . ':', [
                'class' => 'form-label',
            ]) !!}
            {!! Form::select('tax_registration_type', [
                'unified' => __('models/Organization.fields.tax_registration_type_unified'),
                'branches' => __('models/Organization.fields.tax_registration_type_branches'),
            ], old('tax_registration_type', @optional($Organization)->tax_registration_type ?? 'unified'), [
                'class' => 'form-select form-select-solid',
            ]) !!}
        </div>
    </div>
</div>

<div class="row mt-5">
    <!-- Logo Field -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">{!! Form::label('logo', __('models/Organization.fields.logo')) !!}</h3>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="image-input image-input-outline" data-kt-image-input="true"
                    style="background-image: url('{{ asset('admin_assets/media/svg/avatars/blank.svg') }}')">
                    <div class="image-input-wrapper w-250px h-250px"
                        style="background-image: url('{{ @$Organization->logo_original_path }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                    </div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="@lang('crud.change')">
                        <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                        {!! Form::file('logo', ['accept' => '.png, .jpg, .jpeg']) !!}
                        <input type="hidden" name="logo_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="@lang('crud.cancel')">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Field -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">{!! Form::label('signature', __('models/Organization.fields.signature')) !!}</h3>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="image-input image-input-outline" data-kt-image-input="true"
                    style="background-image: url('{{ asset('admin_assets/media/svg/avatars/blank.svg') }}')">
                    <div class="image-input-wrapper w-250px h-250px"
                        style="background-image: url('{{ @$Organization->signature_original_path }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                    </div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="@lang('crud.change')">
                        <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                        {!! Form::file('signature', ['accept' => '.png, .jpg, .jpeg']) !!}
                        <input type="hidden" name="signature_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="@lang('crud.cancel')">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Seal Field -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">{!! Form::label('seal', __('models/Organization.fields.organization_stamp')) !!}</h3>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div class="image-input image-input-outline" data-kt-image-input="true"
                    style="background-image: url('{{ asset('admin_assets/media/svg/avatars/blank.svg') }}')">
                    <div class="image-input-wrapper w-250px h-250px"
                        style="background-image: url('{{ @$Organization->seal_original_path }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                    </div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="@lang('crud.change')">
                        <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                        {!! Form::file('seal', ['accept' => '.png, .jpg, .jpeg']) !!}
                        <input type="hidden" name="seal_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="@lang('crud.cancel')">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
