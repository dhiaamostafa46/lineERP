<!-- Employee Id Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_custodies.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($custody)->employee_id??0)">
    </x-select2-input>
</div>


<!-- Asset Id Field -->
<div class="form-group col-sm-4 mb-3">
    {!! Form::label('asset_id', __('hr::models/hr_custodies.fields.asset_id') . ':') !!}
    <x-select2-input name="asset_id" :placeholder="__('hr::lang.select_asset')" :list="$assets"
        :selected_id="old('asset_id', @optional($custody)->asset_id??0)">
    </x-select2-input>
</div>

<!-- File Field -->
<div class="form-group col-sm-4 mb-3 my-auto">
    {!! Form::label('file', __('hr::models/hr_custodies.fields.file') . ':') !!}
    {!! Form::file('file', null, ['class' => 'form-control']) !!}
</div>

<!-- Details Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('details', __('hr::models/hr_custodies.fields.details') . ':') !!}
    {!! Form::textarea('details', null, ['class' => 'form-control']) !!}
</div>
