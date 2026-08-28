<!-- Input Fields -->
<input type="hidden" id="salary" placeholder="الراتب" readonly>
<input type="hidden" id="start_date" placeholder="تاريخ البدء" readonly>

<!-- Employee Field -->
<div id="employee_field" class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_end_service.fields.employee') . ':') !!}
    <x-select2-input id="employeeSelect" name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($EndService)->employee_id ?? 0)">
    </x-select2-input>
</div>

<!-- Title Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('end', __('hr::models/hr_end_service.fields.end_date') . ':') !!}
    {!! Form::date('end', old('title', @optional($EndService)->end), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_end_service.fields.end_date'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('reason', __('hr::models/hr_end_service.fields.reason') . ':') !!}
    <x-select2-input name="reason" :placeholder="__('hr::lang.select_reason')" :list="$reasons" :selected_id="old('reason', @optional($EndService)->reason ?? 0)">
    </x-select2-input>
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('reward_amount', __('hr::models/hr_end_service.fields.reward_amount') . ':') !!}
    {!! Form::number('reward_amount', old('title', @optional($EndService)->reward_amount), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_end_service.fields.reward_amount'),
        'readonly' => 'readonly' // اجعل الحقل للعرض فقط
    ]) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_end_service.fields.description') . ':') !!}
    {!! Form::textarea('description', old('description', @optional($EndService)->description), [
        'class' => 'form-control',
        'id' => 'summernote',
        'col' => '5',
        'placeholder' => __('hr::models/hr_end_service.fields.description'),
    ]) !!}
</div>

<div class="form-group col-sm-12 mb-3">
    <label for="duration">{{ __('hr::models/hr_end_service.fields.duration') }}:</label>
    <input type="text" id="duration" class="form-control" readonly>
</div>
@section('scripts')
<script>
    $(document).ready(function() {
        // تحميل بيانات الموظف عند تحميل الصفحة
        var initialEmployeeId = $('#employeeSelect').val();
        if (initialEmployeeId) {
            loadEmployeeData(initialEmployeeId);
        }

        $('#employeeSelect').change(function() {
            var employeeId = $(this).val();
            loadEmployeeData(employeeId);
        });

        function loadEmployeeData(employeeId) {
            if (employeeId) {
                $.ajax({
                    url: '{{ route("hr.hr-get-Employees-salaries") }}',
                    type: 'POST',
                    data: {
                        employee_id: employeeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.original.success && response.original.data) {
                            $('#salary').val(response.original.data.salary);
                            $('#start_date').val(response.original.data.start);
                            calculateBonus(); // حساب المكافأة عند تغيير الموظف
                        } else {
                            $('#salary').val('');
                            $('#start_date').val('');
                            console.error('لا توجد بيانات للموظف المحدد.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#salary').val('');
                $('#start_date').val('');
            }
        }

        // حدث عند تغيير تاريخ الانتهاء
        $('input[name="end"]').on('change', function() {
            calculateDuration();
        });

        // حدث عند تغيير سبب الانتهاء
        $('select[name="reason"]').on('change', function() {
            calculateBonus(); // حساب المكافأة عند تغيير السبب
        });

        // دالة لحساب الفرق بين التاريخين
        function calculateDuration() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('input[name="end"]').val());

            if (startDate && endDate) {
                var years = endDate.getFullYear() - startDate.getFullYear();
                var months = endDate.getMonth() - startDate.getMonth();
                var days = endDate.getDate() - startDate.getDate();

                // ضبط القيم حسب الفرق
                if (days < 0) {
                    months--;
                    days += new Date(endDate.getFullYear(), endDate.getMonth(), 0).getDate();
                }

                if (months < 0) {
                    years--;
                    months += 12;
                }

                // عرض النتيجة
                $('#duration').val(years + ' سنوات، ' + months + ' شهور، ' + days + ' أيام');
            } else {
                $('#duration').val(''); // مسح الحقل إذا كان التاريخ غير صحيح
            }
        }

        // دالة لحساب مكافأة نهاية الخدمة
        function calculateBonus() {
            var salary = parseFloat($('#salary').val()) || 0;
            var duration = $('#duration').val();
            var years = parseInt(duration.split(' ')[0]) || 0; // الحصول على عدد السنوات
            var months = parseInt(duration.split(' ')[2]) || 0; // الحصول على عدد الشهور
            var terminationRea = $('select[name="reason"]').val();
            var totalYears = years + (months / 12);
            var bonus = 0;

            // حساب المكافأة بناءً على سبب إنهاء الخدمة
            if (terminationRea === '3' || terminationRea === '8') { // أسباب الاستقالة
                if (totalYears < 2) {
                    bonus = 0; // أقل من سنتين، لا توجد مكافأة
                } else if (totalYears <= 5) {
                    bonus = (salary / 6) * totalYears; // مكافأة للمدة بين 2 و 5 سنوات
                } else {
                    bonus = (((salary / 2) * 5) + (salary * (totalYears - 5))) / 3 * 2; // أكثر من 5 سنوات
                }
            } else { // أسباب إنهاء الخدمة الأخرى
                if (totalYears <= 5) {
                    bonus = (salary / 2) * totalYears; // مكافأة للمدة بين 0 و 5 سنوات
                } else {
                    bonus = (salary / 2) * 5 + (salary * (totalYears - 5)); // أكثر من 5 سنوات
                }
            }

            // عرض المكافأة في حقل reward_amount
            $('input[name="reward_amount"]').val(bonus.toFixed(2));
        }
    });
</script>
@endsection
