<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_jobs.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($job) ? $job->translate($locale)->name : null, [
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
    ]) !!}
</div>


<!-- License Required Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('license_required', __('hr::models/hr_jobs.fields.license_required') . ':') !!}
    {!! Form::select('license_required', $licenses, isset($job) ?
    $job->license_required : null, [
    'class' => 'form-control',
    ]) !!}
</div>
