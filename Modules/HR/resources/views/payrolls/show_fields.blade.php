<!-- Total Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.total')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            {{ $payroll->total_text }}
        </b>
    </div>
</div>


<!-- Payroll Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.payroll_date')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll->payroll_date_text }}</b>
    </div>
</div>


<!-- Delivery At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.delivery_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll->delivery_at_text }}</b>
    </div>
</div>


<!-- Preparingl At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.preparing_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll->preparing_at_text }}</b>
    </div>
</div>


<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">
            <span class="{{ $payroll->status_badge }}">
                {{ $payroll->status_text }}
            </span>
        </b>
    </div>
</div>


<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll->created_at }}</b>
    </div>
</div>


<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_payrolls.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $payroll->updated_at }}</b>
    </div>
</div>


<!-- Cancel Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('crud.cancel')
        </p>
    </div>

    <div class="col-8">
        {!! Form::open(['route' => ['hr.payrolls.destroy', $payroll->id], 'method' => 'delete']) !!}
        @if (in_array($payroll->status, [1, 2]))
            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                'type' => 'submit',
                'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                'onclick' => "return confirm('Are you sure?')",
            ]) !!}
        @endif

        {!! Form::close() !!}
    </div>
</div>
