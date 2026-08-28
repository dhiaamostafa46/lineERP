<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->employee->username }}</b>
    </div>
</div>

<!-- Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.type_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->type->name }}</b>
    </div>
</div>

<!-- File Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.file')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            <a href="{{ $contract->file_original_path }}" target="_blank">
                <i class="fa-solid fa-file fs-2 text-primary"></i>
            </a>
        </b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            <span class="{{ $contract->status_badge }}">{{ $contract->status_text }}</span>
        </b>
    </div>
</div>

<!-- Qwa No Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.qiwa_no')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->qiwa_no }}</b>
    </div>
</div>

<!-- Start At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.start_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->start_at }}</b>
    </div>
</div>

<!-- End At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.end_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->end_at }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_contracts.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $contract->updated_at }}</b>
    </div>
</div>
