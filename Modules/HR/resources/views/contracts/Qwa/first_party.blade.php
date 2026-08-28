<div class="row">
    <div class="col-6 Arabic_section">
        <h3 class="headtext">{{ trans('hr::models/hr_contracts.first_party', [], 'ar') }}:</h3>
        <div>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.company_name', [], 'ar') }}:</strong> {{  $Organization->translate('ar')->name}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.unified_national_number', [], 'ar') }}:</strong> {{$Organization->chamber_no}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.facility_number', [], 'ar') }}:</strong> {{$Organization->organization_number}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.commercial_register', [], 'ar') }}:</strong> {{$Organization->CR}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.address', [], 'ar') }}:</strong> {{$Organization->national_address}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.workplace', [], 'ar') }}:</strong>  {{$Organization->national_address}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.email', [], 'ar') }}:</strong> {{auth()->user()->email ??''}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.represented_by', [], 'ar') }}:</strong> {{auth()->user()->employee->full_name ??''}}

                {{ trans('hr::models/hr_contracts.first_party_table.development_team', [], 'ar') }}
            </p>
        </div>

    </div>




    <div class="col-6  English_section">

        <h3 class="headtext">{{ trans('hr::models/hr_contracts.first_party', [], 'en') }}:</h3>
        <div>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.company_name', [], 'en') }}:</strong> {{  $Organization->translate('en')->name}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.unified_national_number', [], 'en') }}:</strong> {{$Organization->chamber_no}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.facility_number', [], 'en') }}:</strong> {{$Organization->organization_number}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.commercial_register', [], 'en') }}:</strong> {{$Organization->CR}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.address', [], 'en') }}:</strong> {{$Organization->national_address}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.workplace', [], 'en') }}:</strong>  {{$Organization->national_address}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.email', [], 'en') }}:</strong> {{auth()->user()->email ??''}}
            </p>
            <p class="lh-lg textprograf">
                <strong>{{ trans('hr::models/hr_contracts.first_party_table.represented_by', [], 'en') }}:</strong>{{auth()->user()->employee->full_name ??''}}
                {{ trans('hr::models/hr_contracts.first_party_table.development_team', [], 'en') }}
            </p>
        </div>

    </div>

</div>

