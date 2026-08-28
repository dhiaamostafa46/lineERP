<!-- Id Field -->


<!-- Code Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.code')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->code }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->name }}</b>
    </div>
</div>

<!-- Parent Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.parent_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->parent ? $TreeAccount->parent->name : '-' }}</b>
    </div>
</div>

<!-- Account Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.account_type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->account_type_text }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->status_text }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->type_text }}</b>
    </div>
</div>

<!-- Is Leaf Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.is_leaf')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->is_leaf ? __('lang.yes') : __('lang.no') }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('accusoft::models/as_tree_account.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $TreeAccount->updated_at }}</b>
    </div>
</div>
