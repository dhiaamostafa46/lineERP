<!-- Name Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.name')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $fiscalYear->name }}</span>
    </div>
</div>

<!-- Start Date Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.start_date')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $fiscalYear->start_date }}</span>
    </div>
</div>

<!-- End Date Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.end_date')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $fiscalYear->end_date }}</span>
    </div>
</div>

<!-- Notes Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.notes')</label>
    </div>
    <div class="col-8">
        <span class="fs-6 text-gray-600">{{ $fiscalYear->notes }}</span>
    </div>
</div>



<!-- Is Current Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.is_current')</label>
    </div>
    <div class="col-8">
        @if ($fiscalYear->is_current)
            <span class="badge badge-light-success fs-base">@lang('lang.yes')</span>
        @else
            <span class="badge badge-light-danger fs-base">@lang('lang.no')</span>
        @endif
    </div>
</div>

<!-- Is Closed Field -->
<div class="col-sm-12 row mb-5">
    <div class="col-4 my-auto">
        <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.is_closed')</label>
    </div>
    <div class="col-8">
        @if ($fiscalYear->is_closed)
            <span class="badge badge-light-danger fs-base">@lang('lang.closed')</span>
        @else
            <span class="badge badge-light-success fs-base">@lang('lang.open')</span>
        @endif
    </div>
</div>

@if($fiscalYear->is_closed)
    <!-- Closed At Field -->
    <div class="col-sm-12 row mb-5">
        <div class="col-4 my-auto">
            <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.closed_at')</label>
        </div>
        <div class="col-8">
            <span class="fs-6 text-gray-600">{{ $fiscalYear->closed_at }}</span>
        </div>
    </div>

    <!-- Closed By Field -->
    <div class="col-sm-12 row mb-5">
        <div class="col-4 my-auto">
            <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.closed_by')</label>
        </div>
        <div class="col-8">
            <span class="fs-6 text-gray-600">{{ optional($fiscalYear->closedBy)->name }}</span>
        </div>
    </div>

    <!-- Closed Periods Field -->
    @if(isset($fiscalYear->closed_periods) && is_array($fiscalYear->closed_periods))
        <div class="col-sm-12 row mb-5">
            <div class="col-4 my-auto">
                <label class="fs-5 fw-bold text-gray-800">@lang('accusoft::models/as_fiscal_years.fields.closed_periods')</label>
            </div>
            <div class="col-8">
                <span class="fs-6 text-gray-600">
                    <strong>@lang('accusoft::models/as_fiscal_years.fields.start_date'):</strong> {{ $fiscalYear->closed_periods['from'] ?? 'N/A' }} <br>
                    <strong>@lang('accusoft::models/as_fiscal_years.fields.end_date'):</strong> {{ $fiscalYear->closed_periods['to'] ?? 'N/A' }}
                </span>
            </div>
        </div>
    @endif
@endif
