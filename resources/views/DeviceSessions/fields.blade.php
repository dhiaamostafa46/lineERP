<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label($locale . '[name]', $language .' '. __('models/Branches.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($Branch) ? $Branch->translate($locale)->name : null, [
            'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>









<!-- Phone Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('phone', __('models/Branches.fields.phone') . ':') !!}
    {!! Form::text('phone', old('phone', @optional($Branch)->phone), ['class' => 'form-control']) !!}
</div>
<!-- Area Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('area', __('models/Branches.fields.area') . ':') !!}
    {!! Form::text('area', old('area', @optional($Branch)->area), ['class' => 'form-control']) !!}
</div>
<!-- City Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('city', __('models/Branches.fields.city') . ':') !!}
    {!! Form::text('city', old('city', @optional($Branch)->city), ['class' => 'form-control']) !!}
</div>

<!-- District Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('district', __('models/Branches.fields.district') . ':') !!}
    {!! Form::text('district', old('district', @optional($Branch)->district), ['class' => 'form-control']) !!}
</div>

<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label($locale . '[address]', $language .' '. __('models/Branches.fields.address') . ':') !!}
        {!! Form::text($locale . '[address]', isset($Branch) ? $Branch->translate($locale)->address : null, [
            'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>




{{-- <!-- Longitude Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('long', __('models/Branches.fields.long') . ':') !!}
    {!! Form::text('long', old('long', @optional($Branch)->long), ['class' => 'form-control']) !!}
</div>

<!-- Latitude Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('lat', __('models/Branches.fields.lat') . ':') !!}
    {!! Form::text('lat', old('lat', @optional($Branch)->lat), ['class' => 'form-control']) !!}
</div>

<!-- Distance Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('distance', __('models/Branches.fields.distance') . ':') !!}
    {!! Form::number('distance', old('distance', @optional($Branch)->distance), ['class' => 'form-control', 'step' => '0.01']) !!}
</div> --}}

<!-- Manager Field -->
{{-- <div class="form-group col-sm-6 mb-3">
    {!! Form::label('manager', __('models/Branches.fields.manager') . ':') !!}
    {!! Form::text('manager', old('manager', @optional($Branch)->manager), ['class' => 'form-control']) !!}
</div> --}}

<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('models/Branches.fields.description') . ':') !!}
    {!! Form::textarea('description', old('description', @optional($Branch)->description), ['class' => 'form-control', 'rows' => 3]) !!}
</div>

