<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_asset_types.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($asset_type) ? $asset_type->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_jobs.fields.status') . ':') !!}
    {!! Form::select('status', $statuses, null, [
    'class' => 'form-control',
    'placeholder' => __('hr::lang.select_status'),
    ]) !!}
</div>
