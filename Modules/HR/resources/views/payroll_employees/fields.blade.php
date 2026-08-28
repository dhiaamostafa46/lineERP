<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_payroll_employees.fields.employee_id').':') !!}
    {!! Form::text('employee_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Payroll Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('payroll_id', __('hr::models/hr_payroll_employees.fields.payroll_id').':') !!}
    {!! Form::text('payroll_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Salary Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('salary_id', __('hr::models/hr_payroll_employees.fields.salary_id').':') !!}
    {!! Form::text('salary_id', null, ['class' => 'form-control']) !!}
</div>


<!-- Total Allowances Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('total_allowances', __('hr::models/hr_payroll_employees.fields.total_allowances').':') !!}
    {!! Form::text('total_allowances', null, ['class' => 'form-control']) !!}
</div>


<!-- Total Deducts Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('total_deducts', __('hr::models/hr_payroll_employees.fields.total_deducts').':') !!}
    {!! Form::text('total_deducts', null, ['class' => 'form-control']) !!}
</div>


<!-- Basic Salary Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('basic_salary', __('hr::models/hr_payroll_employees.fields.basic_salary').':') !!}
    {!! Form::text('basic_salary', null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_payroll_employees.fields.status').':') !!}
    {!! Form::text('status', null, ['class' => 'form-control']) !!}
</div>