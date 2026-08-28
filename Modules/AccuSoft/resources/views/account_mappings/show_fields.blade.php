<!-- Name Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_account_mappings.fields.name')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $accountMapping->name }}</span>
    </div>
</div>

<!-- Key Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_account_mappings.fields.mapping_key')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $accountMapping->mapping_key }}</span>
    </div>
</div>

<!-- Tree Account Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_account_mappings.fields.account_id')</label>
    </div>
    <div class="col-8">
        <span >{{ $accountMapping->account->name ?? '-' }}</span>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('crud.created_at')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $accountMapping->created_at }}</span>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('crud.updated_at')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $accountMapping->updated_at }}</span>
    </div>
</div>
