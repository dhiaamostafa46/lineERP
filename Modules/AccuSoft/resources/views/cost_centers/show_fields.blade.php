<!-- Id Field -->


<!-- Code Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.code')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->code }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->name }}</b>
    </div>
</div>

<!-- Parent Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.parent_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->parent ? $CostCenter->parent->name : '-' }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->status_text }}</b>
    </div>
</div>



<!-- Is Leaf Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.is_leaf')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->is_leaf ? __('lang.yes') : __('lang.no') }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_cost_centers.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $CostCenter->updated_at }}</b>
    </div>
</div>
