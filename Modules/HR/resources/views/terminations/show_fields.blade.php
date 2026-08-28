<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->id }}</b>
    </div>
</div>


<!-- Termination Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.termination_type_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->termination_type_id }}</b>
    </div>
</div>


<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->employee_id }}</b>
    </div>
</div>


<!-- Worked Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.worked_days')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->worked_days }}</b>
    </div>
</div>


<!-- Last Reward Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.last_reward')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->last_reward }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_terminations.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination->updated_at }}</b>
    </div>
</div>