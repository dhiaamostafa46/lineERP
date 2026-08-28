<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->name }}</b>
    </div>
</div>

<!-- BranchId Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.branch_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->branch->name }}</b>
    </div>
</div>

<!-- Manager User Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.manager_user_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ optional($store->managerUser)->name ?? '-' }}</b>
    </div>
</div>

<!-- Address Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.address')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->address }}</b>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.type')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $store->type_text }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->status_text }}</b>
    </div>
</div>



<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_stores.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $store->updated_at }}</b>
    </div>
</div>
