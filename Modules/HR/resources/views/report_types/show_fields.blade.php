<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_report_types.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $report_type->id }}</b>
    </div>
</div>

@foreach (config('langs') as $locale => $language)
<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            {{ $language }} @lang('hr::models/hr_report_types.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $report_type->translate($locale)->name }}</b>
    </div>
</div>
@endforeach

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_report_types.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $report_type->status }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_report_types.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $report_type->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_report_types.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $report_type->updated_at }}</b>
    </div>
</div>
