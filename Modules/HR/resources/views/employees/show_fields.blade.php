@include('employees.show_fields', ['employee' => @optional($employee)->main_employee])
<hr class="my-10">
<h2 class="text-success text-center mb-5">@lang('hr::models/hr_employees.job_details')</h2>
<!-- Job Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.branches')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $employee->main_employee->Branch->name ?? '' }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.job_number')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $employee->job_number ?? '' }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.Direct_manager')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->DirectManager->username ?? '' }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.job_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->job->name ?? '' }}</b>
    </div>
</div>

<!-- Department Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.department_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->department->name ?? '' }}</b>
    </div>
</div>

<!-- Shift Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.shift_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->shift->name ?? '' }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.fingerprint_exempt')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $employee->fingerprint_exempt_text ?? '' }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.attendance_type')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $employee->attendance_type_text ?? '' }}</b>
    </div>
</div>



<!-- Max Off Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.max_off_days')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->max_off_days }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.vacation_balance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $employee->vacation_balance ?? '' }}</b>
    </div>
</div>

<!-- Max Advance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.max_advance')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->max_advance }}</b>
    </div>
</div>

<!-- Job Level Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.job_level')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->job_level }}</b>
    </div>
</div>

<!-- Specialty Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.specialty')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->specialty }}</b>
    </div>
</div>

<!-- Start At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.start_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->start_at }}</b>
    </div>
</div>

<!-- Start At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.license_expired_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control upComingCheck">{{ $employee->license_expired_at }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_employees.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->updated_at }}</b>
    </div>
</div>
