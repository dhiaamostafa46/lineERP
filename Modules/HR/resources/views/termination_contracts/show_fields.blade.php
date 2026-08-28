<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->id }}</b>
    </div>
</div>


<!-- Termination Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.termination_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->termination_id }}</b>
    </div>
</div>


<!-- Contract Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.contract_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->contract_id }}</b>
    </div>
</div>


<!-- Worked Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.worked_days')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->worked_days }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_termination_contracts.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $termination_contract->updated_at }}</b>
    </div>
</div>