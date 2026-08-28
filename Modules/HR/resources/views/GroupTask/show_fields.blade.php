<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->id }}</b>
    </div>
</div>
<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->name }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->status_text }}</b>
    </div>
</div>


<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.description')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->description }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $GroupTask->updated_at }}</b>
    </div>
</div>


<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_GroupTask.employee')
        </p>
    </div>

    <div class="col-8">

    </div>
</div>

@if (count($GroupTask->details) > 0)
    @foreach ($GroupTask->details as $item)
        <div class="col-sm-12 row">
            <div class="col-4 my-auto">
                <p class="fs-5 ">

                </p>
            </div>

            <div class="col-8">
                <b class="form-control"> {{ $item->employee->username ??''}}</b>

            </div>
        </div>
    @endforeach

@endif






