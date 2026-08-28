<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_allowances.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $allowance->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_allowances.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $allowance->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_allowances.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $allowance->status }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_allowances.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $allowance->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_allowances.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $allowance->updated_at }}</b>
    </div>
</div>
