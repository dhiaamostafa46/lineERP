<h2 class="text-primary text-center mb-5">@lang('models/employees.fields.identity_details')</h2>

<!-- Identity Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('identity_type', __('models/employees.fields.identity_type') . ':',['class' => 'required']) !!}
    <x-select2-input name="identity_type" :placeholder="__('lang.select_identity_type')" :list="$identityTypes"
        :selected_id="old('identity_type', @optional($identity)->identity_type??2)">
    </x-select2-input>
</div>

<!-- Identity No Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('identity_no', __('models/employees.fields.identity_no') . ':',['class' => 'required']) !!}
    {!! Form::text('identity_no', isset($identity) ? $identity->identity_no : null, ['class' => 'form-control']) !!}
</div>

<!-- Identity Expiry Date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('identity_expired_at', __('models/employees.fields.identity_expired_at') . ':',['class' => 'required']) !!}
    {!! Form::date('identity_expired_at', isset($identity) ? $identity->identity_expired_at : null, [
    'class' => 'form-control',
    ]) !!}
</div>

<!-- Insurance No Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('insurance_no', __('models/employees.fields.insurance_no') . ':') !!}
    {!! Form::text('insurance_no', isset($identity) ? $identity->insurance_no : null, ['class' => 'form-control']) !!}
</div>

<!-- Insurance Expiry Date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('insurance_expired_at', __('models/employees.fields.insurance_expired_at') . ':') !!}
    {!! Form::date('insurance_expired_at', isset($identity) ? $identity->insurance_expired_at : null, [
    'class' => 'form-control',
    ]) !!}
</div>
