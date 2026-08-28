{{-- <div class="row">
    <div class="col-6 Arabic_section"  >
    </div>
    <div class="col-6 English_section"  >
    </div>
</div> --}}


<div class="row">
    <div class="col-6 Arabic_section">

        <h3 class="headtext">{{ trans('hr::models/hr_contracts.second_party', [], 'ar') }}:</h3>

        <div class="lh-lg textprograf">
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.name', [], 'ar') }}:</strong>
                {{ $contract->employee->main_employee->full_name ?? '' }} </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.job', [], 'ar') }}:</strong>
                {{ $contract->employee->job->name ?? '' }} </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.employee_number', [], 'ar') }}:
                </strong>{{ $contract->employee->id ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.nationality', [], 'ar') }}:</strong>
                {{ $contract->employee->main_employee->nationality ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.birth_date', [], 'ar') }}:</strong>{{ $contract->employee->main_employee->dob ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.id_number', [], 'ar') }}:</strong>{{ $contract->employee->main_employee->identity->identity_no ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.id_type', [], 'ar') }}:</strong>{{ $contract->employee->main_employee->identity->type_text ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.gender', [], 'ar') }}:</strong>
                {{ $contract->employee->main_employee->gender_text ?? '' }}</p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.religion', [], 'ar') }}:
                    {{ $contract->employee->main_employee->religion ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.end_date', [], 'ar') }}:
                    {{ $contract->employee->main_employee->identity->identity_expired_at ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.marital_status', [], 'ar') }}:
                    {{ $contract->employee->main_employee->marital_status_text ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.education_level', [], 'ar') }}:
                    {{ $contract->employee->specialty ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.specialization', [], 'ar') }}:
                    {{ $contract->employee->job_level ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.iban_number', [], 'ar') }}:
                    {{ $contract->employee->main_employee->bank->iban ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.email', [], 'ar') }}:</strong>
                {{ $contract->employee->main_employee->email ?? '' }}</p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.phone_number', [], 'ar') }}:
                    {{ $contract->employee->main_employee->phone ?? '' }}</strong>
            </p>
            <p>{{ trans('hr::models/hr_contracts.second_party_table.second_party_reference', [], 'ar') }}</p>
        </div>
    </div>
    <div class="col-6 English_section">


        <h3 class="headtext">{{ trans('hr::models/hr_contracts.second_party', [], 'en') }}:</h3>

        <div class="lh-lg textprograf">
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.name', [], 'en') }}:</strong>
                {{ $contract->employee->main_employee->full_name ?? '' }} </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.job', [], 'en') }}:</strong>
                {{ $contract->employee->job->name ?? '' }} </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.employee_number', [], 'en') }}:
                </strong>{{ $contract->employee->id ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.nationality', [], 'en') }}:</strong>
                {{ $contract->employee->main_employee->nationality ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.birth_date', [], 'en') }}:</strong>{{ $contract->employee->main_employee->dob ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.id_number', [], 'en') }}:</strong>{{ $contract->employee->main_employee->identity->identity_no ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.id_type', [], 'en') }}:</strong>{{ $contract->employee->main_employee->identity->type_text ?? '' }}
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.gender', [], 'en') }}:</strong>
                {{ $contract->employee->main_employee->gender_text ?? '' }}</p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.religion', [], 'en') }}:
                    {{ $contract->employee->main_employee->religion ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.end_date', [], 'en') }}:
                    {{ $contract->employee->main_employee->identity->identity_expired_at ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.marital_status', [], 'en') }}:
                    {{ $contract->employee->main_employee->marital_status_text ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.education_level', [], 'en') }}:
                    {{ $contract->employee->specialty ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.specialization', [], 'en') }}:
                    {{ $contract->employee->job_level ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.iban_number', [], 'en') }}:
                    {{ $contract->employee->main_employee->bank->iban ?? '' }}</strong>
            </p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.email', [], 'en') }}:</strong>
                {{ $contract->employee->main_employee->email ?? '' }}</p>
            <p><strong>{{ trans('hr::models/hr_contracts.second_party_table.phone_number', [], 'en') }}:
                    {{ $contract->employee->main_employee->phone ?? '' }}</strong>
            </p>
            <p>{{ trans('hr::models/hr_contracts.second_party_table.second_party_reference', [], 'en') }}</p>
        </div>
    </div>
</div>
