<!-- Termination Type Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('termination_type_id', __('hr::models/hr_terminations.fields.termination_type_id').':') !!}
    {!! Form::text('termination_type_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_terminations.fields.employee_id').':') !!}
    {!! Form::text('employee_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Worked Days Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('worked_days', __('hr::models/hr_terminations.fields.worked_days').':') !!}
    {!! Form::text('worked_days', null, ['class' => 'form-control']) !!}
</div>


<!-- Last Reward Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('last_reward', __('hr::models/hr_terminations.fields.last_reward').':') !!}
    {!! Form::text('last_reward', null, ['class' => 'form-control']) !!}
</div>