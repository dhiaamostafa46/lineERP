<!-- Type Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('type', __('hr::models/hr_trackers.fields.type') . ':') !!}
    {!! Form::select('type', $types, null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('status', __('hr::models/hr_trackers.fields.status') . ':') !!}
    {!! Form::select('status', $statuses, null, ['class' => 'form-control']) !!}
</div>


<!-- Name Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('name', __('hr::models/hr_trackers.fields.name') . ':') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>
@livewire('hr::trackers.department-job', [
'department_id' => $tracker->department_id??null,
'tracked_jobs' => isset($tracker) ? $tracker->jobs->pluck('id')->toArray():[]
], key('department-job'))
@include('hr::trackers.approvals')