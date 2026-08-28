<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->id }}</b>
    </div>
</div>


<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->employee->username ?? '' }}</b>
    </div>
</div>


<!-- Asset Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.asset_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->asset->name ?? '' }}</b>
    </div>
</div>


<!-- Details Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.details')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->details }}</b>
    </div>
</div>


<!-- File Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.file')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">
            <a target="_blank" href="{{ $custody->file_path }}" class="">
                <i class="fa-solid fa-file fs-2 text-primary"></i>
            </a>
        </span>
    </div>
</div>

@if ($custody->receive_id)
<!-- Received Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.received_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->received_id }}</b>
    </div>
</div>


<!-- Received At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.received_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->received_at }}</b>
    </div>
</div>
@endif


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            <span class="{{ $custody->status_badge }}">
                {{ $custody->status_text }}
            </span>
        </b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_custodies.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $custody->updated_at }}</b>
    </div>
</div>

{{-- @if (!$custody->receive_id)
<hr class="my-3">
<div class="actions btn-group">
    <a href="{{ route('hr.custodies.receive', $custody->id) }}"
        class="btn btn-primary btn-sm mt-2">@lang('lang.receive')</a>
</div>
@endif --}}
