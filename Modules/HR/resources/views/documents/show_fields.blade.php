<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document->employee->username }}</b>
    </div>
</div>

<!-- Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.type_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document->type->name }}</b>
    </div>
</div>

<!-- File Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.file')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">
            <a href="{{ $document->file_original_path }}" target="_blank">
                <i class="fa-solid fa-file fs-2 text-primary"></i>
            </a>
        </span>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            <span class="{{ $document->status_badge }}">{{ $document->status_text }}</span>
        </b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_documents.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document->updated_at }}</b>
    </div>
</div>
