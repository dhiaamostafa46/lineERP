<!-- Id Field -->


<!-- Code Field -->


<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_safe.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->name }}</b>
    </div>
</div>




<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('finance::models/fnc_safe.fields.status')
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
            @lang('finance::models/fnc_safe.fields.created_at')
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
            @lang('finance::models/fnc_safe.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $bank->updated_at }}</b>
    </div>
</div>
