<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.employee') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->employee->username }}</b> <!-- عرض اسم المستخدم المرتبط بالمهمة -->
    </div>
</div>






<!-- End Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.end_date') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->end }}</b> <!-- عرض تاريخ الانتهاء -->
    </div>
</div>

<!-- Reason Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.reason') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->reason_text }}</b> <!-- عرض السبب -->
    </div>
</div>

<!-- Reward Amount Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.reward_amount') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->reward_amount }}</b> <!-- عرض قيمة المكافأة -->
    </div>
</div>


<!-- Description Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.description') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->description }}</b> <!-- عرض وصف المهمة -->
    </div>
</div>
<!-- Approved Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_end_service.fields.approved') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $EndService->approved ? __('hr::lang.yes') : __('hr::lang.no') }}</b> <!-- عرض الموافقة -->
    </div>
</div>
