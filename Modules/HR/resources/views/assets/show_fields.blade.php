<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->id }}</b>
    </div>
</div>


<!-- Department Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.department_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->department->name ?? '' }}</b>
    </div>
</div>


<!-- Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.type_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->type->name ?? '' }}</b>
    </div>
</div>


<!-- Is New Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.is_new')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->is_new ? __('lang.yes') : __('lang.no') }}</b>
    </div>
</div>


<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->name }}</b>
    </div>
</div>


<!-- Note Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.note')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->note }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->status_text }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_assets.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $asset->updated_at }}</b>
    </div>
</div>