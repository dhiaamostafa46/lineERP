<div class="row">
    <div class="col-6 Arabic_section">
        <div class="lh-lg textprograf">
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.agreement.part_1', [], 'ar') }}
                <strong>{{ $contract->employee->job_level ?? '' }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.agreement.part_2', [], 'ar') }}
            </p>
            <p>
               
                {{ trans('hr::models/hr_contracts.contract_details.duration.main', [], 'ar') }}
                <strong> سنة </strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.start', [], 'ar') }}
                <strong>{{ $contract->start_at }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.end', [], 'ar') }}
                <strong>{{ $contract->end_at }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.duration_2', [], 'ar') }}
                <strong>{{ $contract->start_at }}</strong>
            </p>
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.renewal.main', [], 'ar') }}
                <strong>30</strong>
                {{ trans('hr::models/hr_contracts.contract_details.renewal.renewal_2', [], 'ar') }}
            </p>
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.probation_period.main', [], 'ar') }}
                <strong>90</strong>
                {{ trans('hr::models/hr_contracts.contract_details.probation_period.probation_period_2', [], 'ar') }}
            </p>
        </div>
    </div>

    <div class="col-6 English_section">
        <div class="lh-lg textprograf">
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.agreement.part_1', [], 'en') }}
                <strong>{{ $contract->employee->job_level ?? '' }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.agreement.part_2', [], 'en') }}
            </p>
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.duration.main', [], 'en') }}
                <strong>one year</strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.start', [], 'en') }}
                <strong>{{ $contract->start_at }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.end', [], 'en') }}
                <strong>{{ $contract->end_at }}</strong>
                {{ trans('hr::models/hr_contracts.contract_details.duration.duration_2', [], 'en') }}
                <strong>{{ $contract->start_at }}</strong>
            </p>
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.renewal.main', [], 'en') }}
                <strong>30</strong>
                {{ trans('hr::models/hr_contracts.contract_details.renewal.renewal_2', [], 'en') }}
            </p>
            <p>
                {{ trans('hr::models/hr_contracts.contract_details.probation_period.main', [], 'en') }}
                <strong>90</strong>
                {{ trans('hr::models/hr_contracts.contract_details.probation_period.probation_period_2', [], 'en') }}
            </p>
        </div>
    </div>
</div>
