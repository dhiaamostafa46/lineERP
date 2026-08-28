<div class="row">
    <!-- Employee Id Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('employee_id', __('hr::models/hr_holidays.fields.employee_id') . ':*') !!}
        <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($holiday)->employee_id ?? 0)" :id="'employee_id'">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type_id', __('hr::models/hr_holidays.fields.type_id') . ':*') !!}
        <x-select2-input name="type_id" :placeholder="__('hr::lang.select_type')" :list="$types" :selected_id="old('type_id', @optional($holiday)->type_id ?? 0)" :id="'type_id'">
        </x-select2-input>
    </div>
</div>

<div class="row">
    <!-- From At Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('from_at', __('hr::models/hr_holidays.fields.from_at') . ':*') !!}
        {!! Form::text(
            'from_at',
            old('from_at', isset($holiday) ? optional($holiday->from_at)->format('Y-m-d') : now()->format('Y-m-d')),
            ['class' => 'form-control', 'id' => 'from_at'],
        ) !!}
    </div>

    <!-- End At Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('end_at', __('hr::models/hr_holidays.fields.end_at') . ':*') !!}
        {!! Form::text('end_at', old('end_at', isset($holiday) ? $holiday->end_at : null), [
            'class' => 'form-control',
            'id' => 'end_at',
        ]) !!}
    </div>
</div>

<div class="row">
    <!-- Required Days Field -->
    <div class="form-group col-sm-4 mb-3">
        {!! Form::label('required_days', __('hr::models/hr_holidays.fields.required_days') . ':') !!}
        {!! Form::text('required_days', null, ['class' => 'form-control', 'readonly', 'id' => 'required_days']) !!}
    </div>
    <!-- Remaining Balance Field -->
    <div class="form-group col-sm-4 mb-3">
        {!! Form::label('remaining_balance', __('hr::models/hr_holidays.fields.remaining_balance') . ':') !!}
        {!! Form::text('remaining_balance', null, ['class' => 'form-control', 'readonly', 'id' => 'remaining_balance']) !!}
    </div>
    <!-- Future Balance Field -->
    <div class="form-group col-sm-4 mb-3">
        {!! Form::label('allowed', __('hr::models/hr_holidays.fields.allowed') . ':') !!}
        {!! Form::text('allowed', null, ['class' => 'form-control', 'readonly', 'id' => 'allowed']) !!}
    </div>
</div>

<div class="row">
    <!-- Attachment Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('attachment', __('hr::models/hr_holidays.fields.attachment') . ':') !!}
        <div class="input-group">
            <div class="custom-file">
                {!! Form::file('attachment', ['class' => 'custom-file-input']) !!}
                {!! Form::label('attachment', __('hr::lang.no_file_chosen'), ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12 mb-3">
    {!! Form::label('comments', __('hr::models/hr_holidays.fields.comments') . ':*') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>



@push('scripts')
    <script>
        let $day = [];

        // Initialize date pickers
        $("#request_date").flatpickr({
            dateFormat: "Y-m-d",
        });

        $("#from_at").flatpickr({
            dateFormat: "Y-m-d",
            onChange: calculateDays,
        });

        $("#end_at").flatpickr({
            dateFormat: "Y-m-d",
            onChange: calculateDays,
        });

        // Calculate days between two dates
        function calculateDays() {
            const fromDate = $("#from_at").val();
            const toDate = $("#end_at").val();

            // If dates are not selected
            if (!fromDate || !toDate) {
                $('#required_days').val(0);
                return;
            }

            const start = new Date(fromDate);
            const end = new Date(toDate);

            // If end date is before start date
            if (end < start) {
                $('#required_days').val(0);
                return;
            }

            // If shift days are not defined, count all days
            if ($day.length === 0) {
                const diffTime = Math.abs(end - start);
                // Add 1 to include both start and end days
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                $('#required_days').val(diffDays);
                return;
            }

            // If shift days are defined, count only working days
            const dayMapping = {
                'sunday': 0,
                'monday': 1,
                'tuesday': 2,
                'wednesday': 3,
                'thursday': 4,
                'friday': 5,
                'saturday': 6
            };

            const workWeek = $day.map(d => dayMapping[d.toLowerCase()]);
            let workingDaysCount = 0;
            let current = new Date(start);

            // Loop through all days and count working days
            while (current <= end) {
                if (workWeek.includes(current.getDay())) {
                    workingDaysCount++;
                }
                current.setDate(current.getDate() + 1);
            }

            $('#required_days').val(workingDaysCount);
        }

        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        // Fetch employee details and leave balance when employee or leave type changes
        $('#employee_id, #type_id').on('change', function() {
            fetchEmployeeAndLeaveData();
        });

        // Fetch employee and leave data from API
        function fetchEmployeeAndLeaveData() {
            const employeeId = $('#employee_id').val();
            const typeId = $('#type_id').val();

            if (employeeId && typeId) {
                const url = `/api/v1/holidays/balance/${employeeId}/${typeId}`;
                console.log('Calling URL:', url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        console.log('Success Response:', response);

                        // Update remaining balance
                        if (response.success === true) {
                            var remaining = response.total_remaining !== undefined ? response.total_remaining : Math.max((response.annual_balance - response.balance), 0);
                            $('#remaining_balance').val(remaining || 0);
                            $('#allowed').val(response.allowed || 0);

                            // Update shift days if available
                            if (response.shift && Array.isArray(response.shift) && response.shift.length > 0) {
                                $day = response.shift;
                            } else {
                                $day = [];
                            }
                        } else {
                            $('#remaining_balance').val(0);
                            $('#allowed').val(0);
                            $day = [];
                        }

                        // Recalculate days after updating shift data
                        calculateDays();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error:", textStatus, errorThrown);
                        console.error("Response Text:", jqXHR.responseText);
                        console.error("Status Code:", jqXHR.status);

                        // Reset fields on error
                        $('#remaining_balance').val(0);
                        $('#allowed').val(0);
                        $day = [];
                        calculateDays();
                    }
                });
            } else {
                // If employee or type is not selected, reset fields
                $('#remaining_balance').val('');
                $('#allowed').val('');
                $day = [];
                calculateDays();
            }
        }

        // Initialize data on page load
        $(document).ready(function() {
            fetchEmployeeAndLeaveData();
        });
    </script>
@endpush
