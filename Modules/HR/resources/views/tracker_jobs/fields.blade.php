<!-- Tracker Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('tracker_id', __('hr::models/hr_tracker_jobs.fields.tracker_id').':') !!}
    {!! Form::text('tracker_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Job Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('job_id', __('hr::models/hr_tracker_jobs.fields.job_id').':') !!}
    {!! Form::text('job_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_tracker_jobs.fields.status').':') !!}
    {!! Form::text('status', null, ['class' => 'form-control']) !!}
</div>