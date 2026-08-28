<!-- Photo Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.photo')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control"><img src="{{ $user->photo_original_path }}" alt="{{ $user->name }}"
                class="rounded-circle" width="100"></b>
    </div>
</div>

<!-- Id Field -->
<div class="col-sm-12 row d-none">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->name }}</b>
    </div>
</div>

<!-- Email Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.email')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->email }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->status_text }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/users.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $user->updated_at }}</b>
    </div>
</div>
