<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->status_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.conversion_factor')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->conversion_factor }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_units.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $unit->updated_at }}</b>
    </div>
</div>
