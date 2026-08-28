<!-- Flage Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label(
        'flage',
        __('hr::models/hr_tasks.fields.department') . '/' . __('hr::models/hr_tasks.fields.employee_id') . ':',
    ) !!}
    <x-select2-input id="flage" name="flage" :placeholder="__('hr::lang.select_status')" :list="$flages" :selected_id="old('flage', @optional($task)->flage ?? 0)">
    </x-select2-input>
</div>
<!-- Employee Field -->
<div id="employee_field" class="form-group col-sm-6 mb-3" style="display:none;">
    {!! Form::label('employee_id', __('hr::models/hr_tasks.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($task)->employee_id ?? 0)">
    </x-select2-input>
</div>

<!-- Department Field -->
<div id="department_field" class="form-group col-sm-6 mb-3" style="display:none;">
    {!! Form::label('department_id', __('hr::models/hr_tasks.fields.department') . ':') !!}
    <x-select2-input name="department_id" :placeholder="__('hr::lang.select_department')" :list="$Department" :selected_id="old('department_id', @optional($task)->department_id ?? 0)">
    </x-select2-input>
</div>

<!-- Department Field -->
<div id="group_field" class="form-group col-sm-6 mb-3" style="display:none;">
    {!! Form::label('group_id', __('hr::models/hr_tasks.fields.Group') . ':') !!}
    <x-select2-input name="group_id" :placeholder="__('hr::lang.group_id')" :list="$Group" :selected_id="old('group_id', @optional($task)->group_id ?? 0)">
    </x-select2-input>
</div>

<!-- Title Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('title', __('hr::models/hr_tasks.fields.title') . ':') !!}
    {!! Form::text('title', old('title', @optional($task)->title), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_tasks.fields.title'),
    ]) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_tasks.fields.description') . ':') !!}
    {!! Form::textarea('description', old('description', @optional($task)->description), [
        'class' => 'form-control',
        'id' => 'summernote',
        'col' => '5',
        'placeholder' => __('hr::models/hr_tasks.fields.description'),
    ]) !!}
</div>

<!-- Status Field -->


<!-- Status Field -->
@if (!Route::is('hr.Task.create'))
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('hr::models/hr_tasks.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($task)->status ?? 0)">
        </x-select2-input>
    </div>
@endif

<!-- File Field (Uncomment if needed) -->
{{--
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('file', __('hr::models/hr_tasks.fields.file') . ':') !!}
    {!! Form::file('file', [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_tasks.fields.file'),
    ]) !!}
</div>
--}}

@section('scripts')
    <script>
        $(document).ready(function() {

            $('#summernote').summernote({
                height: 200
            });

            // Function to toggle fields based on flage value
            function toggleFields(flageValue) {
                if (flageValue == 1) {
                    // إخفاء حقل الموظفين وإظهار حقل القسم
                    $('#employee_field').hide();
                    $('#department_field').show();
                    $('#group_field').hide();
                } else if (flageValue == 2) {
                    // إظهار حقل الموظفين وإخفاء حقل القسم
                    $('#employee_field').show();
                    $('#department_field').hide();
                    $('#group_field').hide();
                } else if (flageValue == 3) {
                    // إظهار حقل المجموعة وإخفاء الحقول الأخرى
                    $('#employee_field').hide();
                    $('#department_field').hide();
                    $('#group_field').show();
                } else {
                    // إخفاء كافة الحقول إذا كانت القيمة غير معروفة
                    $('#employee_field').hide();
                    $('#department_field').hide();
                    $('#group_field').hide();
                }
            }

            // Listen for changes in the flage dropdown
            $('#flage').on('change', function() {
                toggleFields($(this).val());
            });

            // Initialize on page load
            toggleFields($('#flage').val()); // Make sure the toggle function is applied on page load
        });
    </script>
@endsection

