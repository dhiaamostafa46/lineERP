<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_contracts.fields.employee_id') . ':') !!}

    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($contract)->employee_id ?? 0)">
    </x-select2-input>
</div>


<div class="form-group col-sm-6 mb-3">
    {!! Form::label('qiwa', __('hr::models/hr_contracts.fields.qiwa_no') . ':') !!}
    <x-select2-input name="qiwa" :placeholder="__('hr::lang.select_status')" :list="$qiwas" :selected_id="old('qiwa', @optional($contract)->qiwa ?? 0)">
    </x-select2-input>
</div>

<!-- contract Number Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('contract_number', __('hr::models/hr_contracts.fields.contract_number') . ':') !!}
    {!! Form::text('contract_number', old('contract_number',  @optional($contract)->contract_number ), ['class' => 'form-control']) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type_id', __('hr::models/hr_contracts.fields.type_id') . ':') !!}
    <x-select2-input name="type_id" :placeholder="__('hr::lang.select_type')" :list="$types" :selected_id="old('type_id', @optional($contract)->type_id ?? 0)">
    </x-select2-input>
</div>

<!-- Start At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('start_date', __('hr::models/hr_contracts.fields.start_at') . ':') !!}
    {!! Form::date('start_date', old('start_date',  @optional($contract)->start_date ?  @optional($contract)->start_date->format('Y-m-d') : null), ['class' => 'form-control']) !!}
</div>

<!-- End At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('end_date', __('hr::models/hr_contracts.fields.end_at') . ':') !!}
    {!! Form::date('end_date', old('end_date',  @optional($contract)->end_date ?  @optional($contract)->end_date->format('Y-m-d') : null), ['class' => 'form-control']) !!}
</div>


<!-- Auto Renewable Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('auto_renewable', __('hr::models/hr_contracts.fields.auto_renewable') . ':') !!}
    <x-select2-input name="auto_renewable" :placeholder="__('hr::lang.select_status')" :list="[1 => __('hr::lang.yes'), 0 => __('hr::lang.no')]" :selected_id="old('auto_renewable', @optional($contract)->auto_renewable ?? 0)">
    </x-select2-input>
</div>

<!-- Location Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('location', __('hr::models/hr_contracts.fields.location') . ':') !!}
    {!! Form::text('location', old('location',  @optional($contract)->location), ['class' => 'form-control']) !!}
</div>



<div class="form-group col-sm-6 mb-3">
    {!! Form::label('file', __('hr::models/hr_contracts.fields.file') . ':') !!}
    <div class="custom-file">
        {!! Form::file('file', ['class' => 'custom-file-input', 'id' => 'file_input']) !!}
        {!! Form::label('file_input', 'Choose file', ['class' => 'custom-file-label']) !!}
    </div>
</div>
