<!-- Employee Id Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_penalties.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($penalty)->employee_id??0)">
    </x-select2-input>
</div>

<!-- Amount Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('amount', __('hr::models/hr_penalties.fields.amount') . ':') !!}
    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
</div>


<!-- Due Date Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('due_date', __('hr::models/hr_penalties.fields.due_date') . ':') !!}
    {!! Form::date('due_date', @optional($penalty)->due_date??null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_penalties.fields.description') . ':') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>