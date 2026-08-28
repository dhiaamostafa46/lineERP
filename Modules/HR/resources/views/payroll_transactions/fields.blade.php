<!-- Payroll Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('payroll_employee_id', __('hr::models/hr_payroll_transactions.fields.payroll_employee_id').':') !!}
    {!! Form::text('payroll_employee_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Forable Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('forable_id', __('hr::models/hr_payroll_transactions.fields.forable_id').':') !!}
    {!! Form::text('forable_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Forable Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('forable_type', __('hr::models/hr_payroll_transactions.fields.forable_type').':') !!}
    {!! Form::text('forable_type', null, ['class' => 'form-control']) !!}
</div>


<!-- Amount Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('amount', __('hr::models/hr_payroll_transactions.fields.amount').':') !!}
    {!! Form::text('amount', null, ['class' => 'form-control']) !!}
</div>


<!-- Currency Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('currency', __('hr::models/hr_payroll_transactions.fields.currency').':') !!}
    {!! Form::text('currency', null, ['class' => 'form-control']) !!}
</div>


<!-- Is Deduct Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('is_deduct', __('hr::models/hr_payroll_transactions.fields.is_deduct').':') !!}
    {!! Form::text('is_deduct', null, ['class' => 'form-control']) !!}
</div>


<!-- Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_payroll_transactions.fields.type').':') !!}
    {!! Form::text('type', null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_payroll_transactions.fields.status').':') !!}
    {!! Form::text('status', null, ['class' => 'form-control']) !!}
</div>


<!-- Note Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('note', __('hr::models/hr_payroll_transactions.fields.note').':') !!}
    {!! Form::text('note', null, ['class' => 'form-control']) !!}
</div>