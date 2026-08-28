<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.company')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($CompanyContract->company)->name ?? '—' }}
            @if ($CompanyContract->company)
                ({{ $CompanyContract->company->code }})
            @endif
        </b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.company_pricing_type')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ \App\Models\CompanyContract::companyPricingTypes()[$CompanyContract->company_pricing_type] ?? $CompanyContract->company_pricing_type }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.company_pricing_value')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $CompanyContract->company_pricing_value }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.driver_payment_type')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ \App\Models\CompanyContract::driverPaymentTypes()[$CompanyContract->driver_payment_type] ?? $CompanyContract->driver_payment_type }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.driver_payment_value')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $CompanyContract->driver_payment_value }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.settlement_cycle')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ \App\Models\CompanyContract::settlementCycles()[$CompanyContract->settlement_cycle] ?? $CompanyContract->settlement_cycle }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.start_date')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($CompanyContract->start_date)->format('Y-m-d') ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.end_date')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($CompanyContract->end_date)->format('Y-m-d') ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.status')</p>
    </div>
    <div class="col-8">
        <b class="form-control"><span class="{{ $CompanyContract->status_badge }}">{{ $CompanyContract->status_text }}</span></b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.notes')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $CompanyContract->notes ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/CompanyContracts.fields.created_at')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $CompanyContract->created_at }}</b>
    </div>
</div>
