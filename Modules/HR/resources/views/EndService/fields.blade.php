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
    {!! Form::date('end', old('end', @optional($EndService)->end), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_end_service.fields.end_date'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('reason', __('hr::models/hr_end_service.fields.reason') . ':') !!}
    <x-select2-input id="reasonSelect" name="reason" :placeholder="__('hr::lang.select_reason')" :list="$reasons" :selected_id="old('reason', @optional($EndService)->reason ?? 0)">
    </x-select2-input>
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('reward_amount', __('hr::models/hr_end_service.fields.reward_amount') . ':') !!}
    {!! Form::number('reward_amount', old('reward_amount', @optional($EndService)->reward_amount), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_end_service.fields.reward_amount'),
        'readonly' => 'readonly' // اجعل الحقل للعرض فقط
    ]) !!}
</div>

<!-- Total Penalties Field -->
<div class="form-group col-sm-6 mb-3">
    <label for="total_penalties">{{ __('hr::models/hr_end_service.fields.total_penalties') }}:</label>
    <input type="text" id="total_penalties" class="form-control" value="{{ @optional($EndService)->total_penalties }}" readonly>
</div>

<!-- Total Advances Field -->
<div class="form-group col-sm-6 mb-3">
    <label for="total_advances">{{ __('hr::models/hr_end_service.fields.total_advances') }}:</label>
    <input type="text" id="total_advances" class="form-control" value="{{ @optional($EndService)->total_advances }}" readonly>
</div>

<!-- Total Deducts Field -->
<div class="form-group col-sm-6 mb-3">
    <label for="total_deducts">{{ __('hr::models/hr_end_service.fields.total_deducts') }}:</label>
    <input type="text" id="total_deducts" class="form-control" value="{{ @optional($EndService)->total_deducts }}" readonly>
</div>

<!-- Net Reward Field -->
<div class="form-group col-sm-6 mb-3">
    <label for="net_reward">{{ __('hr::models/hr_end_service.fields.net_reward') }}:</label>
    <input type="text" id="net_reward" class="form-control fw-bold text-success" value="{{ @optional($EndService)->net_reward }}" readonly>
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
        
        function fetchEosbData() {
            var employeeId = $('#employeeSelect').val();
            var endDate = $('input[name="end"]').val();
            var reason = $('#reasonSelect').val();

            if (employeeId && endDate && reason) {
                $.ajax({
                    url: '{{ route("hr.calculate-eosb") }}',
                    type: 'POST',
                    data: {
                        employee_id: employeeId,
                        end_date: endDate,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            $('#duration').val(response.data.duration_text);
                            $('input[name="reward_amount"]').val(response.data.reward_amount);
                            $('#total_penalties').val(response.data.total_penalties);
                            $('#total_advances').val(response.data.total_advances);
                            $('#total_deducts').val(response.data.total_deducts);
                            $('#net_reward').val(response.data.net_reward);
                        } else {
                            $('#duration').val('');
                            $('input[name="reward_amount"]').val('');
                            $('#total_penalties').val('');
                            $('#total_advances').val('');
                            $('#total_deducts').val('');
                            $('#net_reward').val('');
                            if (response.message) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('تنبيه', response.message, 'warning');
                                } else {
                                    alert(response.message);
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error calculating EOSB:', xhr.responseText);
                    }
                });
            } else {
                $('#duration').val('');
                $('input[name="reward_amount"]').val('');
                $('#total_penalties').val('');
                $('#total_advances').val('');
                $('#total_deducts').val('');
                $('#net_reward').val('');
            }
        }

        // Trigger fetch when any of the crucial fields change
        $(document).on('change', '#employeeSelect, input[name="end"], #reasonSelect', function() {
            fetchEosbData();
        });

        // Fetch on initial load if all fields are filled (e.g. during validation failure redirect)
        fetchEosbData();
    });
</script>
@endsection
