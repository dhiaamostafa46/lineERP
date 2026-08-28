<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.id')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.name')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->name }}</b>
    </div>
</div>

<!-- Locale Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.locale')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->locale }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.status')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->status }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.created_at')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/languages.fields.updated_at')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $language->updated_at }}</b>
    </div>
</div>

