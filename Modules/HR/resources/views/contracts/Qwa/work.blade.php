<div class="row">
    <div class="col-6 Arabic_section">
        <h3 class="headtext">{{ trans('hr::models/hr_contracts.work_details.work_days_and_hours', [], 'ar') }}</h3>
        <div class="lh-lg textprograf">
            <span>{{ trans('hr::models/hr_contracts.work_details.normal_work_days', [], 'ar') }}</span>
            <span>
                <strong>
                    {{ is_array($contract->employee->shift->work_days) ? count($contract->employee->shift->work_days) : 7 }}
                    ايام
                </strong>
            </span>
            <span>{{ trans('hr::models/hr_contracts.work_details.work_hours', [], 'ar') }}</span>
            <span><strong> {{ $contract->employee->shift->work_hours ?? '' }}</strong></span>
            <span>{{ trans('hr::models/hr_contracts.work_details.daily_hours', [], 'ar') }}</span>

        </div>

    </div>
    <div class="col-6 English_section">


        <h3 class="headtext">{{ trans('hr::models/hr_contracts.work_details.work_days_and_hours', [], 'en') }}</h3>
        <div class="lh-lg textprograf">
            <span>{{ trans('hr::models/hr_contracts.work_details.normal_work_days', [], 'en') }}</span>
            <span>
                <strong>
                    {{ is_array($contract->employee->shift->work_days) ? count($contract->employee->shift->work_days) : 7 }}
                    Days
                </strong>
            </span>
            <span>{{ trans('hr::models/hr_contracts.work_details.work_hours', [], 'en') }}</span>
            <span><strong> {{ $contract->employee->shift->work_hours ?? '' }}</strong></span>
            <span>{{ trans('hr::models/hr_contracts.work_details.daily_hours', [], 'en') }}</span>

        </div>
    </div>
</div>
