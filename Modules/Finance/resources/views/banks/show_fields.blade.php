<!-- Id Field -->


<!-- Code Field -->


<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->name }}</b>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.account_number')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->account_number }}</b>
    </div>
</div>


<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.iban')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->iban }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->status_text }}</b>
    </div>
</div>





<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_bank.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->updated_at }}</b>
    </div>
</div>
