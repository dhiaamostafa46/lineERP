<div class="row">
    <div class="col-6">
        <div class="lh-lg textprograf text-center">
            <p class="lh-lg textprograf text-center">
                <strong>{{ trans('hr::models/hr_contracts.contract_parties.first_party') }}</strong>
            </p>
            <p class="lh-lg textprograf text-center">
                <strong>{{ trans('hr::models/hr_contracts.contract_parties.first_party_company') }}</strong>
                {{ $Organization->name }}
            </p>
            <div class="d-flex justify-content-center" style="height: 200px">
                <img src="{{ $Organization->signature_original_path }}"
                     alt="{{ $Organization->translate('en')->name }}"
                     style="max-height: 100%; max-width: 100%;">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="lh-lg textprograf text-center">
            <p class="lh-lg textprograf text-center">
                <strong>{{ trans('hr::models/hr_contracts.contract_parties.second_party') }}</strong>
            </p>
            <p class="lh-lg textprograf text-center">
                <strong>{{ trans('hr::models/hr_contracts.contract_parties.second_party_employee') }}</strong>
                {{ $contract->employee->main_employee->full_name ?? '' }}
            </p>
            <div style="width: 100%; height: 200px">
                <!-- يمكن إضافة صورة هنا في المستقبل إذا لزم الأمر -->
            </div>
        </div>
    </div>
</div>
