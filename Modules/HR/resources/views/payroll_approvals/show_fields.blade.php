<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->id }}</b>
    </div>
</div>


<!-- Payroll Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.payroll_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->payroll_id }}</b>
    </div>
</div>


<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->employee_id }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->status }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payroll_approvals.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll_approval->updated_at }}</b>
    </div>
</div>