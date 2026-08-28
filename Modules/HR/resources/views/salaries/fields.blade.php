@isset ($employees)
<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_salaries.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($salary)->employee_id??0)">
    </x-select2-input>
</div>
@endisset

<!-- Basic Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('basic', __('hr::models/hr_salaries.fields.basic') . ':') !!}
    {!! Form::number('basic', null, ['class' => 'form-control']) !!}
</div>

<hr class="my-10">
@include('hr::salaries.allowance_fields')
<hr class="my-10">
@include('hr::salaries.deduct_fields')