<!-- Id Field -->
<div class="col-sm-12 row d-none">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->status }}</b>
    </div>
</div>

<!-- License Required Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.license_required')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->license_required }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_jobs.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $job->updated_at }}</b>
    </div>
</div>
