<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->status }}</b>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->type }}</b>
    </div>
</div>

<!-- Work Hours Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.work_hours')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->work_hours }}</b>
    </div>
</div>

<!-- From Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.from')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->from_text }}</b>
    </div>
</div>

<!-- To Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.to')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->to_text }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_shift_types.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $shift->updated_at }}</b>
    </div>
</div>
