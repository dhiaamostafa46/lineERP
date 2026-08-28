<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $reward->employee->username ?? '' }}</b>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $reward->type_text }}</b>
    </div>
</div>

<!-- Amount Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.value')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $reward->value_text }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            @livewire('hr::trackers.get-status', ['model' => $reward], key('trackers_get_status'))
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
        <b class="form-control">{{ $reward->approver->name ?? '' }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $reward->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_rewards.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $reward->updated_at }}</b>
    </div>
</div>
