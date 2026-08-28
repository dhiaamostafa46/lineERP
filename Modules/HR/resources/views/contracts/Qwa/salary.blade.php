<div class="row">
    <div class="col-6 Arabic_section">


        <h3 class="headtext">
            {{ trans('hr::models/hr_contracts.commitments_details.first_party_commitments', [], 'ar') }}</h3>
        <div class="lh-lg textprograf">
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.main', [], 'ar') }}</span>
                <span><strong> {{ $contract->employee->salary->basic ?? '' }} </strong></span>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.currency', [], 'ar') }}</span>
                <span><strong>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.month', [], 'ar') }}</strong></span>
            </p>

            @if (count($contract->employee->salary->salary_allowances) > 0)
                <p>{{ trans('hr::models/hr_contracts.commitments_details.allowances.commitment', [], 'ar') }}</p>
                <ol>
                    @foreach ($contract->employee->salary->salary_allowances as $item)
                        @if ($item->amount > 0)
                            <li>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.payment', [], 'ar') }}</span>
                                <span><strong> {{ $item->amount }}</strong></span>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.currency', [], 'ar') }}</span>
                                <span><strong> {{ $item->allowance->translate('ar')->name ?? '' }} </strong></span>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.month', [], 'ar') }}</span>
                            </li>
                        @endif
                    @endforeach
                </ol>
            @endif

            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.annual_leave.main', [], 'ar') }}</span>
                <span><strong> {{ $contract->employee->max_off_days ?? '' }} </strong></span>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.annual_leave.days', [], 'ar') }}</span>
            </p>
            <p>{{ trans('hr::models/hr_contracts.commitments_details.medical_care', [], 'ar') }}</p>
            <p>{{ trans('hr::models/hr_contracts.commitments_details.social_insurance', [], 'ar') }}</p>
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.recruitment_fees.main', [], 'ar') }}</span>
            </p>
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.funeral_expenses.main', [], 'ar') }}</span>
            </p>
        </div>

    </div>
    <div class="col-6 English_section">
        <h3 class="headtext">
            {{ trans('hr::models/hr_contracts.commitments_details.first_party_commitments', [], 'en') }}</h3>
        <div class="lh-lg textprograf">
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.main', [], 'en') }}</span>
                <span><strong> {{ $contract->employee->salary->basic ?? '' }} </strong></span>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.currency', [], 'en') }}</span>
                <span><strong>{{ trans('hr::models/hr_contracts.commitments_details.basic_salary.month', [], 'en') }}</strong></span>
            </p>

            @if (count($contract->employee->salary->salary_allowances) > 0)
                <p>{{ trans('hr::models/hr_contracts.commitments_details.allowances.commitment', [], 'en') }}</p>
                <ol>
                    @foreach ($contract->employee->salary->salary_allowances as $item)
                        @if ($item->amount > 0)
                            <li>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.payment', [], 'en') }}</span>
                                <span><strong> {{ $item->amount }}</strong></span>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.currency', [], 'en') }}</span>
                                <span><strong> {{ $item->allowance->translate('en')->name ?? '' }} </strong></span>
                                <span>{{ trans('hr::models/hr_contracts.commitments_details.allowances.month', [], 'en') }}</span>
                            </li>
                        @endif
                    @endforeach
                </ol>
            @endif

            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.annual_leave.main', [], 'en') }}</span>
                <span><strong> {{ $contract->employee->max_off_days ?? '' }} </strong></span>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.annual_leave.days', [], 'en') }}</span>
            </p>
            <p>{{ trans('hr::models/hr_contracts.commitments_details.medical_care', [], 'en') }}</p>
            <p>{{ trans('hr::models/hr_contracts.commitments_details.social_insurance', [], 'en') }}</p>
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.recruitment_fees.main', [], 'en') }}</span>
            </p>
            <p>
                <span>{{ trans('hr::models/hr_contracts.commitments_details.funeral_expenses.main', [], 'en') }}</span>
            </p>
        </div>

    </div>
</div>
