<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/roles.fields.id')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $role->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/roles.fields.name')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $role->name }}</b>
    </div>
</div>

<!-- Guard Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/roles.fields.guard_name')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $role->guard_name }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/roles.fields.created_at')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $role->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
                            @lang('models/roles.fields.updated_at')
                    </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $role->updated_at }}</b>
    </div>
</div>

