<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_tasks.fields.employee_id') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Tasts->employee->username }}</b> <!-- عرض اسم المستخدم المرتبط بالمهمة -->
    </div>
</div>

<!-- Title Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_tasks.fields.title') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Tasts->title }}</b> <!-- عرض عنوان المهمة -->
    </div>
</div>

<!-- Description Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_tasks.fields.description') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Tasts->description }}</b> <!-- عرض وصف المهمة -->
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_tasks.fields.status') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Tasts->status_text }}</b> <!-- عرض حالة المهمة -->
    </div>
</div>

<!-- Done Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_tasks.fields.done') <!-- مطابق للمصفوفة -->
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Tasts->done }}</b> <!-- عرض تاريخ الإنجاز -->
    </div>
</div>
