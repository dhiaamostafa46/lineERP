<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->name }}</b>
    </div>
</div>

<!-- Parent Category Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.parent_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->parent?->name ?? '-' }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->status_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->type_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.sort')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->sort }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.img')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            @if (isset($category) && $category->img)
                <div class="mt-2">
                    <img src="{{ $category->imgThumbPath }}" alt="Category Image" style="max-height:100px;">
                </div>
            @endif
        </b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('basicdata::models/db_categories.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $category->updated_at }}</b>
    </div>
</div>
