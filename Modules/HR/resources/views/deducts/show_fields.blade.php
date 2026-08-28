<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_deducts.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $deduct->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_deducts.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $deduct->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_deducts.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $deduct->status }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_deducts.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $deduct->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_deducts.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $deduct->updated_at }}</b>
    </div>
</div>
