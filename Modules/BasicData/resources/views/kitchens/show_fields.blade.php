<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->status_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.barcode')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->barcode }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_kitchens.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $kitchen->updated_at }}</b>
    </div>
</div>
