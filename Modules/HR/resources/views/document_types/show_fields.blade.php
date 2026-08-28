<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_document_types.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document_type->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_document_types.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document_type->name }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_document_types.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document_type->status }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_document_types.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document_type->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_document_types.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $document_type->updated_at }}</b>
    </div>
</div>
