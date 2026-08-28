<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->employee->username ?? '' }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            @livewire('hr::trackers.get-status', ['model' => $holiday], key('trackers_get_status'))
        </b>
    </div>
</div>


<!-- Approver Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::lang.approver')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->approver->name ?? '' }}</b>
    </div>
</div>


<!-- Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.type_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->type->name ?? '' }}</b>
    </div>
</div>


<!-- From At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.from_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->from_at->format('Y-m-d h:i a') }}</b>
    </div>
</div>


<!-- End At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.end_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->end_at->format('Y-m-d h:i a') }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_holidays.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $holiday->updated_at }}</b>
    </div>
</div>
