<h2 class="text-primary text-center mb-5">@lang('hr::models/hr_employees.job_details')</h2>
<!-- Job Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('job_number', __('hr::models/hr_employees.fields.job_number') . ':') !!}
    {!! Form::number('job_number', @optional($employee)->job_number ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('job_id', __('hr::models/hr_employees.fields.job_id') . ':',['class' => 'required']) !!}
    <x-select2-input name="job_id" :placeholder="__('hr::lang.select_job')" :list="$jobs" :selected_id="old('job_id', @optional($employee)->job_id ?? 0)">
    </x-select2-input>
</div>

<!-- Department Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('department_id', __('hr::models/hr_employees.fields.department_id') . ':',['class' => 'required']) !!}
    <x-select2-input name="department_id" :placeholder="__('hr::lang.select_department')" :list="$departments" :selected_id="old('department_id', @optional($employee)->department_id ?? 0)">
    </x-select2-input>
</div>
<!-- Shift Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('shift_id', __('hr::models/hr_employees.fields.shift_id') . ':',['class' => 'required']) !!}
    <x-select2-input name="shift_id" :placeholder="__('hr::lang.select_shift')" :list="$shifts" :selected_id="old('shift_id', @optional($employee)->shift_id ?? 0)">
    </x-select2-input>
</div>
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('fingerprint_exempt', __('hr::models/hr_employees.fields.fingerprint_exempt') . ':',['class' => 'required']) !!}
    <x-select2-input name="fingerprint_exempt" :placeholder="__('hr::lang.select_job')" :list="$fingerprint_exempts" :selected_id="old('fingerprint_exempt', @optional($employee)->fingerprint_exempt ?? 0)">
    </x-select2-input>
</div>
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('attendance_type', __('hr::models/hr_employees.fields.attendance_type') . ':',['class' => 'required']) !!}
    <x-select2-input name="attendance_type" :placeholder="__('hr::lang.select_job')" :list="$attendance_types" :selected_id="old('attendance_type', @optional($employee)->attendance_type ?? 0)">
    </x-select2-input>
</div>

<!-- Max Off Days Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('max_off_days', __('hr::models/hr_employees.fields.max_off_days') . ':',['class' => 'required']) !!}
    {!! Form::number('max_off_days', @optional($employee)->max_off_days ?? null, ['class' => 'form-control']) !!}
</div>
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('vacation_balance', __('hr::models/hr_employees.fields.vacation_balance') . ':') !!}
    {!! Form::number('vacation_balance',  @optional($employee)->vacation_balance ?? null, ['class' => 'form-control']) !!}
</div>

<!-- Max Advance Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('max_advance', __('hr::models/hr_employees.fields.max_advance') . ':',['class' => 'required']) !!}
    {!! Form::number('max_advance',  @optional($employee)->max_advance ?? null, ['class' => 'form-control']) !!}
</div>

<!-- Job Level Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('job_level', __('hr::models/hr_employees.fields.job_level') . ':') !!}
    {!! Form::text('job_level',  @optional($employee)->job_level ?? null, ['class' => 'form-control']) !!}
</div>

<!-- Specialty Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('specialty', __('hr::models/hr_employees.fields.specialty') . ':') !!}
    {!! Form::text('specialty',  @optional($employee)->specialty ?? null, ['class' => 'form-control']) !!}
</div>

<!-- Start At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('start_at', __('hr::models/hr_employees.fields.start_at') . ':',['class' => 'required']) !!}
    {!! Form::date('start_at', @optional($employee)->start_at ?? null, ['class' => 'form-control']) !!}
</div>

<!-- License Expired At Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('license_expired_at', __('hr::models/hr_employees.fields.license_expired_at') . ':') !!}
    {!! Form::date('license_expired_at', @optional($employee)->license_expired_at ?? null, [
        'class' => 'form-control',
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('Direct_manager', __('hr::models/hr_employees.fields.Direct_manager') . ':') !!}
    <x-select2-input name="Direct_manager" :placeholder="__('hr::lang.Direct_manager')" :list="$employees" :selected_id="old('Direct_manager', @optional($employee)->Direct_manager ?? 0)">
    </x-select2-input>
</div>
