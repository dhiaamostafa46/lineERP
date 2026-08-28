<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->id }}</b>
    </div>
</div>


<!-- Delivery Payroll At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.delivery_payroll_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->delivery_at }}</b>
    </div>
</div>


<!-- Preparing Payroll At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.preparing_payroll_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->preparing_at }}</b>
    </div>
</div>


<!-- Min Salary Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.min_salary')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->min_salary }}</b>
    </div>
</div>


<!-- Max Off Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.max_off_days')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->max_off_days }}</b>
    </div>
</div>


<!-- Currency Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.currency')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->currency }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_settings.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $setting->updated_at }}</b>
    </div>
</div>
