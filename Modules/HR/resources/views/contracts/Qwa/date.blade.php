<div class="row">
    <div class="col-6 Arabic_section"  >

        <h3 class="headtext">
            {{ trans('hr::models/hr_contracts.contracts.contract_number', [], 'ar') }}: {{ $contract->qiwa_no }}
        </h3>

        <p class="lh-lg textprograf ">
            {{ trans('hr::models/hr_contracts.contracts.contract_text', [], 'ar') }}

            <strong>  {{  $Organization->translate('ar')->name}} </strong>

            {{ trans('hr::models/hr_contracts.contracts.saudi_arabia', [], 'ar') }}
            {{ trans('hr::models/hr_contracts.contracts.on_day', [], 'ar') }}

            <strong>  {{ date('Y-m-d') }}</strong>
           {{ trans('hr::models/hr_contracts.contracts.m', [], 'ar') }}

        </p>

    </div>
    <div class="col-6 English_section"  >
        <h3 class="headtext">
            {{ trans('hr::models/hr_contracts.contracts.contract_number', [], 'en') }}: {{ $contract->qiwa_no }}
        </h3>

        <p class="lh-lg textprograf ">
            {{ trans('hr::models/hr_contracts.contracts.contract_text', [], 'en') }}

            <strong>  {{  $Organization->translate('en')->name}} </strong>

            {{ trans('hr::models/hr_contracts.contracts.saudi_arabia', [], 'en') }}
            {{ trans('hr::models/hr_contracts.contracts.on_day', [], 'en') }}

            <strong>  {{ date('Y-m-d') }}</strong>
            {{ trans('hr::models/hr_contracts.contracts.m', [], 'en') }}
        </p>

    </div>
</div>
