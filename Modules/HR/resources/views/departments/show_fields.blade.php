<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->status }}</b>
    </div>
</div>

<!-- Code Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.code')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->code }}</b>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->type }}</b>
    </div>
</div>

<!-- Parent Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.parent_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->parent_id }}</b>
    </div>
</div>

<!-- Owner Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.owner_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->owner_id }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_departments.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $department->updated_at }}</b>
    </div>
</div>
