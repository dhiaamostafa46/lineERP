<!-- Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->id }}</b>
    </div>
</div>

<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->employee->username  ?? ''}}</b>
    </div>
</div>

<!-- Basic Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.basic')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->basic }}</b>
    </div>
</div>

<!-- total allowance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.total_allowance')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->totalAllowance() }}</b>
    </div>
</div>

<!-- total deducts Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.total_deduct')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->totalDeduct() }}</b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_salaries.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $salary->updated_at }}</b>
    </div>
</div>

{{-- salary_allowances --}}

<div class="salary_allowances">
    <hr class="my-5">
    <h2 class="text-success text-center mb-5">@lang('hr::models/hr_allowances.plural')</h2>
    @foreach ($salary->salary_allowances as $salary_allowance)
        <div class="col-sm-12 row">
            <div class="col-4 my-auto">
                <p class="fs-5 ">
                    {{ $salary_allowance->allowance->name  ?? ''}}
                </p>
            </div>

            <div class="col-8">
                <b class="form-control">{{ $salary_allowance->amount }}</b>
            </div>
        </div>
    @endforeach
</div>

{{-- salary_deducts --}}

<div class="salary_deducts">
    <hr class="my-5">
    <h2 class="text-danger text-center mb-5">@lang('hr::models/hr_deducts.plural')</h2>
    @foreach ($salary->salary_deducts as $salary_deduct)
        <div class="col-sm-12 row">
            <div class="col-4 my-auto">
                <p class="fs-5 ">
                    {{ $salary_deduct->deduct->name    ?? ""}}
                </p>
            </div>

            <div class="col-8">
                <b class="form-control">{{ $salary_deduct->amount }}</b>
            </div>
        </div>
    @endforeach
</div>
