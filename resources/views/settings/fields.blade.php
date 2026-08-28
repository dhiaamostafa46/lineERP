<!-- Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('name', __('models/settings.fields.name') . ':') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-6 mb-3 d-flex align-items-center">
    <!-- Coming_soon Field -->
    <div class="form-group col-sm-6 my-3 d-flex align-items-center">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input name="coming_soon" class="form-check-input" type="checkbox" value=1
                {{ $setting->coming_soon ? 'checked' : '' }} />
            <label class="form-check-label" for="flexSwitchDefault">
                @lang('models/settings.fields.coming_soon')
            </label>
        </div>
    </div>
</div>

<div class="images my-5 d-flex">
    <!-- Logo Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('logo', __('models/settings.fields.logo') . ':') !!}
        <x-image-input name="logo" :value="$setting->logo_original_path ?? ''" />
    </div>
    <!-- Fav_icon Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('fav_icon', __('models/settings.fields.fav_icon') . ':') !!}
        <x-image-input name="fav_icon" :value="$setting->fav_icon_original_path ?? ''" />
    </div>
</div>
