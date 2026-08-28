<!-- Payroll Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('payroll_id', __('hr::models/hr_payroll_approvals.fields.payroll_id').':') !!}
    {!! Form::text('payroll_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_payroll_approvals.fields.employee_id').':') !!}
    {!! Form::text('employee_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_payroll_approvals.fields.status').':') !!}
    {!! Form::text('status', null, ['class' => 'form-control']) !!}
</div>