<!-- Termination Type Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('termination_type_id', __('hr::models/hr_termination_type_rewards.fields.termination_type_id').':')
    !!}
    {!! Form::text('termination_type_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Percentage Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('percentage', __('hr::models/hr_termination_type_rewards.fields.percentage').':') !!}
    {!! Form::text('percentage', null, ['class' => 'form-control']) !!}
</div>


<!-- Worked Days Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('worked_days', __('hr::models/hr_termination_type_rewards.fields.worked_days').':') !!}
    {!! Form::text('worked_days', null, ['class' => 'form-control']) !!}
</div>


<!-- Fixed Amount Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('fixed_amount', __('hr::models/hr_termination_type_rewards.fields.fixed_amount').':') !!}
    {!! Form::text('fixed_amount', null, ['class' => 'form-control']) !!}
</div>