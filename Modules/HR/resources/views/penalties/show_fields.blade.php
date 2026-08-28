<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->id }}</b>
    </div>
</div>


<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->employee->username ?? '' }}</b>
    </div>
</div>


<!-- Description Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.description')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->description }}</b>
    </div>
</div>


<!-- Amount Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.amount')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->amount }}</b>
    </div>
</div>


<!-- Due Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.due_date')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->due_date }}</b>
    </div>
</div>
<!-- Due Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            @livewire('hr::trackers.get-status', ['model' => $penalty], key('trackers_get_status'))
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
        <b class="form-control">{{ $penalty->approver->name ?? '' }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_penalties.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $penalty->updated_at }}</b>
    </div>
</div>
