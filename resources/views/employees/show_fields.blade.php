<!-- Full Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.full_name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->full_name }}</b>
    </div>
</div>

<!-- Username Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.username')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->username }}</b>
    </div>
</div>

<!-- Phone Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.phone')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->phone }}</b>
    </div>
</div>

<!-- Email Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.email')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->email }}</b>
    </div>
</div>

<!-- Dob Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.dob')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->dob }}</b>
    </div>
</div>

<!-- Address Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.address')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->address }}</b>
    </div>
</div>

<!-- National Address Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.national_address')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->national_address }}</b>
    </div>
</div>

<!-- Religion Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.religion')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->religion }}</b>
    </div>
</div>

<!-- Gender Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.gender')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->gender_text }}</b>
    </div>
</div>

<!-- Marital Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.marital_status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->marital_status_text }}</b>
    </div>
</div>

<!-- Number Of Children Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.number_of_children')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->number_of_children }}</b>
    </div>
</div>

<!-- Nationality Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.nationality')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->nationality }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->updated_at }}</b>
    </div>
</div>

<h2 class="my-10 text-center text-success">@lang('models/employees.fields.bank_details')</h2>

<!-- bank name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.bank_name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->bank->bank_name ?? '' }}</b>
    </div>
</div>

<!-- iban Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.iban')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->bank->iban ?? '' }}</b>
    </div>
</div>

<h2 class="my-10 text-center text-success">@lang('models/employees.fields.identity_details')</h2>

<!-- identity type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.identity_type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->identity->identity_type ?? '' }}</b>
    </div>
</div>

<!-- identity no Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.identity_no')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->identity->identity_no ?? '' }}</b>
    </div>
</div>

<!-- identity expired at Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.identity_expired_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control upComingCheck">{{ $employee->identity->identity_expired_at ?? '' }}</b>
    </div>
</div>

<!-- insurance no Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.insurance_no')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $employee->identity->insurance_no ?? '' }}</b>
    </div>
</div>

<!-- insurance expired at Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('models/employees.fields.insurance_expired_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control upComingCheck">{{ $employee->identity->insurance_expired_at ?? '' }}</b>
    </div>
</div>
