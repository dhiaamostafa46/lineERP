<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_rewards.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
        :selected_id="old('employee_id', @optional($reward)->employee_id??0)">
    </x-select2-input>
</div>

<!-- Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_rewards.fields.type') . ':') !!}
    <x-select2-input name="type" :placeholder="__('hr::lang.select_type')" :list="$types"
        :selected_id="old('type', @optional($reward)->type??0)">
    </x-select2-input>
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('amount', __('hr::models/hr_rewards.fields.amount') . ':') !!}
    {!! Form::number('amount', @optional($reward)->amount ?? 0, ['class' => 'form-control']) !!}
</div>

<!-- due_date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('due_date', __('hr::models/hr_penalties.fields.due_date') . ':') !!}
    {!! Form::date('due_date', @optional($reward)->due_date ?? null, ['class' => 'form-control']) !!}
</div>


<!-- Over Time Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('over_time', __('hr::models/hr_rewards.fields.over_time') . ':') !!}
    {!! Form::number('over_time', @optional($reward)->over_time ?? 0, ['class' => 'form-control']) !!}
</div>

<!-- Days Off Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('days_off', __('hr::models/hr_rewards.fields.days_off') . ':') !!}
    {!! Form::number('days_off', @optional($reward)->days_off ?? 0, ['class' => 'form-control']) !!}
</div>

<!-- Start At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('start_at', __('hr::models/hr_rewards.fields.start_at') . ':') !!}
    {!! Form::date('start_at', @optional($reward)->start_at??null, ['class' => 'form-control']) !!}
</div>

<!-- End At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('end_at', __('hr::models/hr_rewards.fields.end_at') . ':') !!}
    {!! Form::date('end_at', @optional($reward)->end_at??null, ['class' => 'form-control']) !!}
</div>


<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_rewards.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
        :selected_id="old('status', @optional($reward)->status??0)">
    </x-select2-input>
</div>
