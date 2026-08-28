<!-- Type Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_documents.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($document)->employee_id??0)">
    </x-select2-input>
</div>
<!-- Type Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type_id', __('hr::models/hr_documents.fields.type_id') . ':') !!}
    <x-select2-input name="type_id" :placeholder="__('hr::lang.select_type')" :list="$types"
        :selected_id="old('type_id', @optional($document)->type_id ?? 0)">
    </x-select2-input>
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_documents.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
        :selected_id="old('status', @optional($document)->status ?? 0)">
    </x-select2-input>
</div>

<!-- File Field -->
<div class="form-group col-sm-6 mb-3 my-auto">
    {!! Form::label('file', __('hr::models/hr_documents.fields.file') . ':') !!}
    {!! Form::file('file', null, ['class' => 'form-control']) !!}
</div>
