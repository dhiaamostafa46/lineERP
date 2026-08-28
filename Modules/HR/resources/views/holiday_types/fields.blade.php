<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_holiday_types.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($holiday_type) ? $holiday_type->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_holiday_types.fields.type') . ':') !!}
    {!! Form::select('type', $types, null, [
    'class' => 'form-control',
    'placeholder' => __('hr::lang.select_status'),
    ]) !!}
</div>


<!-- Off Days Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('off_days', __('hr::models/hr_holiday_types.fields.off_days') . ':') !!}
    {!! Form::text('off_days', null, ['class' => 'form-control']) !!}
</div>
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_jobs.fields.status') . ':') !!}
    {!! Form::select('status', $statuses, null, [
    'class' => 'form-control',
    'placeholder' => __('hr::lang.select_status'),
    ]) !!}
</div>

