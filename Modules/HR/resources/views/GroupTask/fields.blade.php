{{-- <div class="form-group col-md-6 col-sm-12 mb-3">
    {!! Form::label('name', __('hr::models/hr_GroupTask.fields.name') . ':') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div> --}}


<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_departments.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($GroupTask) ? $GroupTask->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<div class="form-group col-md-12 col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_GroupTask.fields.description') . ':') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>
{{-- @livewire('hr::GroupTask.department-job', [
'department_id' => $tracker->department_id??null,
'tracked_jobs' => isset($tracker) ? $tracker->jobs->pluck('id')->toArray():[]
], key('department-job'))
 --}}
 @include('hr::GroupTask.approvals')
