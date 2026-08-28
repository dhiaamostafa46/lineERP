<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('company_id', __('models/CompanyContracts.fields.company') . ':') !!}
        {!! Form::select(
            'company_id',
            ['' => __('models/CompanyContracts.placeholders.select_company')]
                + $companies->mapWithKeys(fn ($c) => [$c->id => $c->code . ' — ' . $c->name])->all(),
            old('company_id', @optional($CompanyContract)->company_id),
            ['class' => 'form-control', 'required' => true],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('models/CompanyContracts.fields.status') . ':') !!}
        {!! Form::select(
            'status',
            $statuses,
            old('status', @optional($CompanyContract)->status ?? \App\Models\CompanyContract::STATUS_ACTIVE),
            ['class' => 'form-control', 'placeholder' => __('hr::lang.select_status')],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('company_pricing_type', __('models/CompanyContracts.fields.company_pricing_type') . ':') !!}
        {!! Form::select(
            'company_pricing_type',
            $companyPricingTypes,
            old('company_pricing_type', @optional($CompanyContract)->company_pricing_type),
            ['class' => 'form-control', 'required' => true],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('company_pricing_value', __('models/CompanyContracts.fields.company_pricing_value') . ':') !!}
        {!! Form::number(
            'company_pricing_value',
            old('company_pricing_value', @optional($CompanyContract)->company_pricing_value ?? 0),
            ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required' => true],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('driver_payment_type', __('models/CompanyContracts.fields.driver_payment_type') . ':') !!}
        {!! Form::select(
            'driver_payment_type',
            $driverPaymentTypes,
            old('driver_payment_type', @optional($CompanyContract)->driver_payment_type),
            ['class' => 'form-control', 'required' => true],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('driver_payment_value', __('models/CompanyContracts.fields.driver_payment_value') . ':') !!}
        {!! Form::number(
            'driver_payment_value',
            old('driver_payment_value', @optional($CompanyContract)->driver_payment_value ?? 0),
            ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required' => true],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('settlement_cycle', __('models/CompanyContracts.fields.settlement_cycle') . ':') !!}
        {!! Form::select(
            'settlement_cycle',
            $settlementCycles,
            old('settlement_cycle', @optional($CompanyContract)->settlement_cycle),
            ['class' => 'form-control', 'required' => true],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('start_date', __('models/CompanyContracts.fields.start_date') . ':') !!}
        {!! Form::date(
            'start_date',
            old(
                'start_date',
                isset($CompanyContract) && $CompanyContract->start_date
                    ? $CompanyContract->start_date->format('Y-m-d')
                    : null,
            ),
            ['class' => 'form-control'],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('end_date', __('models/CompanyContracts.fields.end_date') . ':') !!}
        {!! Form::date(
            'end_date',
            old(
                'end_date',
                isset($CompanyContract) && $CompanyContract->end_date
                    ? $CompanyContract->end_date->format('Y-m-d')
                    : null,
            ),
            ['class' => 'form-control'],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-12 mb-3">
        {!! Form::label('notes', __('models/CompanyContracts.fields.notes') . ':') !!}
        {!! Form::textarea('notes', old('notes', @optional($CompanyContract)->notes), ['class' => 'form-control', 'rows' => 3]) !!}
    </div>
</div>
