<!-- Termination Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('termination_id', __('hr::models/hr_termination_contracts.fields.termination_id').':') !!}
    {!! Form::text('termination_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Contract Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('contract_id', __('hr::models/hr_termination_contracts.fields.contract_id').':') !!}
    {!! Form::text('contract_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Worked Days Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('worked_days', __('hr::models/hr_termination_contracts.fields.worked_days').':') !!}
    {!! Form::text('worked_days', null, ['class' => 'form-control']) !!}
</div>