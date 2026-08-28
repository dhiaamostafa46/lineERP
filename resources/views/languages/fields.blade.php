<!-- Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('name', __('models/languages.fields.name').':') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Locale Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('locale', __('models/languages.fields.locale').':') !!}
    {!! Form::text('locale', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('models/languages.fields.status').':') !!}
    {!! Form::text('status', null, ['class' => 'form-control']) !!}
</div>