<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->name }}</b>
    </div>
</div>

<!-- Rate Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.rate')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->rate }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->status_text }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('models/tax_accounts.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $taxAccount->updated_at }}</b>
    </div>
</div>