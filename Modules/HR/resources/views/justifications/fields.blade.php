<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_justifications.fields.employee_id') . ':') !!}
    <x-select2-input id="employee_id_select" name="employee_id" :list="$employees" :placeholder="__('hr::lang.select_employee')" />
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_justifications.fields.type') . ':') !!}
    <x-select2-input id="type_justification" name="type" :list="$types" :placeholder="__('hr::lang.select_type')" />
</div>
<!-- Shift Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('shift_id', __('hr::models/hr_justifications.fields.shift_id') . ':') !!}
    <x-select2-input id="shift_id" name="shift_id" :list="[]" :placeholder="__('hr::lang.select_shift')" />
</div>

<!-- Attachment Field -->


<!-- Request Date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('request_date', __('hr::models/hr_justifications.fields.request_date') . ':') !!}
    {!! Form::date('request_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
</div>


<div class="form-group from_to_container col-sm-6 mb-3" style="display: none">
    {!! Form::label('from_time', __('hr::models/hr_justifications.fields.from') . ':') !!}
    {!! Form::time('from_time', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group from_to_container col-sm-6 mb-3" style="display: none">
    {!! Form::label('to_time', __('hr::models/hr_justifications.fields.to') . ':') !!}
    {!! Form::time('to_time', null, ['class' => 'form-control']) !!}
</div>


<!-- Type Field -->

<div class="form-group col-sm-6 col-lg-6 mb-3">
    {!! Form::label('attachment', __('hr::models/hr_justifications.fields.attachment') . ':') !!}
    {!! Form::file('attachment', ['class' => 'form-control']) !!}
</div>


<!-- Reason Field -->
<div class="form-group col-sm-12 col-lg-12 mb-3">
    {!! Form::label('reason', __('hr::models/hr_justifications.fields.reason') . ':') !!}
    {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const employeeSelect = $('#employee_id_select');
            const shiftSelect = $('#shift_id');
             const type_justification = $('#type_justification');




            const fromToContainer = $('.from_to_container');

            type_justification.on('change', function() {

                console.log('====================================');
                console.log($(this).val() );
                console.log('====================================');
                if ($(this).val() == '4') {
                    fromToContainer.show();
                } else {
                    fromToContainer.hide();
                }
            });


            employeeSelect.on('change', function() {
                const employeeId = $(this).val();

                shiftSelect.empty().trigger('change');

                if (employeeId) {
                    // Add loading option
                    shiftSelect.append(new Option('@lang('lang.loading')', '')).trigger('change');

                    $.ajax({
                        url: '{{ route('hr.justifications.getAttendancesForEmployee') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: employeeId
                        },
                        success: function(data) {


                            // console.log('====================================');
                            // console.log(data);
                            // console.log('====================================');
                            shiftSelect.empty();
                            shiftSelect.append(new Option('@lang('hr::lang.select_shift')', ''));

                            $.each(data, function(shiftId, shiftText) {
                                shiftSelect.append(new Option(shiftText, shiftId));
                            });

                            shiftSelect.trigger('change');
                        },
                        error: function() {
                            shiftSelect.empty();
                            shiftSelect.append(new Option('@lang('lang.error_on_load')', '')).trigger(
                                'change');
                        }
                    });
                }
            });
        });
    </script>
@endpush
