<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->id }}</b>
    </div>
</div>


<!-- Department Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.department_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->department->name ?? '' }}</b>
    </div>
</div>


<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->type_text }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->status_text }}</b>
    </div>
</div>


<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->name }}</b>
    </div>
</div>


{{-- <!-- Tracker Approvals Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.tracker_approvals')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ json_encode($tracker->tracker_approvals) }}</b>
    </div>
</div> --}}


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_trackers.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracker->updated_at }}</b>
    </div>
</div>
