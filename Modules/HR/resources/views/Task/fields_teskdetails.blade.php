

<!-- Title Field -->
<div class="form-group col-sm-6 mb-3 d-none">
    {!! Form::label('hr_task_id', __('hr::models/hr_tasks.fields.title') . ':') !!}
    {!! Form::text('hr_task_id', old('hr_task_id', @optional($Tasts)->id), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_tasks.fields.title'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3 d-none">
    {!! Form::label('employee_id', __('hr::models/hr_tasks.fields.title') . ':') !!}
    {!! Form::text('employee_id', old('hr_task_id', @optional($Tasts)->employee_id), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_tasks.fields.title'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3 d-none">
    {!! Form::label('userID', __('hr::models/hr_tasks.fields.title') . ':') !!}
    {!! Form::text('userID', old('hr_task_id', auth()->user()->id), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_tasks.fields.title'),
    ]) !!}
</div>



<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_tasks.fields.description') . ':') !!}
    {!! Form::textarea('description',null, [
        'class' => 'form-control',
        'id'    => 'summernote',
        'placeholder' => __('hr::models/hr_tasks.fields.description'),
    ]) !!}
</div>



