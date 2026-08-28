<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->id }}</b>
    </div>
</div>


<!-- Trackable Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.trackable')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->trackable }}</b>
    </div>
</div>


<!-- User Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.user_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->user_id }}</b>
    </div>
</div>


<!-- Sort Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.sort')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->sort }}</b>
    </div>
</div>


<!-- Is Current Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.is_current')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->is_current }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_tracking_approvals.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $tracking_approval->updated_at }}</b>
    </div>
</div>