<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->id }}</b>
    </div>
</div>


<!-- Tracker Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.tracker_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->tracker_id }}</b>
    </div>
</div>


<!-- Job Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.job_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->job_id }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->status }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracker_jobs.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker_job->updated_at }}</b>
    </div>
</div>