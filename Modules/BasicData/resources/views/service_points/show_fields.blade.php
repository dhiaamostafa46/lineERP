<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->status_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.code')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->code }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->type_text }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_service_points.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $servicePoint->updated_at }}</b>
    </div>
</div>
