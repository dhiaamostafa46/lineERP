@php
    $isCreate = !isset($fiscalYear);
    $lastDateValue = isset($lastdate) ? $lastdate : null;
    $startDateValue = old('start_date', @optional($fiscalYear)->start_date ? @optional($fiscalYear)->start_date->format('Y-m-d') : null);
    $readOnly = false;

    if ($isCreate && $lastDateValue) {
        $startDateValue = $lastDateValue;
        $readOnly = true;
    }
@endphp

<div class="row">
    <!-- Start Date Field -->
    <div class="form-group col-sm-6 mb-5">
        {!! Form::label('start_date', __('accusoft::models/as_fiscal_years.fields.start_date') . ':') !!}
        <div class="input-group" id="kt_td_picker_start_date" data-td-target-input="nearest"
            data-td-target-toggle="nearest">
            {!! Form::text(
                'start_date',
                $startDateValue,
                [
                    'class' => 'form-control' . ($readOnly ? ' form-control-solid' : ''),
                    'data-td-target' => '#kt_td_picker_start_date',
                    'required',
                    'autocomplete' => 'off',
                    
                    'placeholder' => __('accusoft::models/as_fiscal_years.fields.start_date'),
                ],
            ) !!}
            <span class="input-group-text" data-td-target="#kt_td_picker_start_date" data-td-toggle="datetimepicker">
                <i class="ki-duotone ki-calendar fs-2"><span class="path1"></span><span class="path2"></span></i>
            </span>
        </div>
    </div>

    <!-- End Date Field -->
    <div class="form-group col-sm-6 mb-5">
        {!! Form::label('end_date', __('accusoft::models/as_fiscal_years.fields.end_date') . ':') !!}
        <div class="input-group" id="kt_td_picker_end_date" data-td-target-input="nearest" data-td-target-toggle="nearest">
            {!! Form::text('end_date', old('end_date', @optional($fiscalYear)->end_date ? @optional($fiscalYear)->end_date->format('Y-m-d') : null), [
                'class' => 'form-control',
                'data-td-target' => '#kt_td_picker_end_date',
                'required',
                'autocomplete' => 'off',
                'placeholder' => __('accusoft::models/as_fiscal_years.fields.end_date'),
            ]) !!}
            <span class="input-group-text" data-td-target="#kt_td_picker_end_date" data-td-toggle="datetimepicker">
                <i class="ki-duotone ki-calendar fs-2"><span class="path1"></span><span class="path2"></span></i>
            </span>
        </div>
    </div>

    <!-- Notes Field -->
    <div class="form-group col-sm-12 mb-5">
        {!! Form::label('notes', __('accusoft::models/as_fiscal_years.fields.notes') . ':') !!}
        {!! Form::textarea('notes', old('notes', @optional($fiscalYear)->notes), ['class' => 'form-control', 'rows' => 3]) !!}
    </div>

</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateElement = document.getElementById("kt_td_picker_start_date");
            const endDateElement = document.getElementById("kt_td_picker_end_date");
            const startDateInput = document.querySelector('input[name="start_date"]');

            if (startDateElement && endDateElement) {
                const isReadOnly = startDateInput.hasAttribute('readonly');

                const endDatePicker = new tempusDominus.TempusDominus(endDateElement, {
                    useCurrent: false,
                    localization: {
                        locale: "{{ app()->getLocale() }}",
                        format: 'yyyy-MM-dd',
                    }
                });

                if (isReadOnly) {
                    if (startDateInput.value) {
                        endDatePicker.updateOptions({
                            restrictions: {
                                minDate: startDateInput.value,
                            },
                        });
                    }
                } else {
                    const startDatePicker = new tempusDominus.TempusDominus(startDateElement, {
                        localization: {
                            locale: "{{ app()->getLocale() }}",
                            format: 'yyyy-MM-dd',
                        }
                    });

                    const setInitialRestrictions = () => {
                        const startDate = startDatePicker.dates.lastPicked;
                        const endDate = endDatePicker.dates.lastPicked;

                        if (startDate) {
                            endDatePicker.updateOptions({
                                restrictions: {
                                    minDate: startDate,
                                },
                            });
                        }
                        if (endDate) {
                            startDatePicker.updateOptions({
                                restrictions: {
                                    maxDate: endDate,
                                },
                            });
                        }
                    };

                    startDateElement.addEventListener(tempusDominus.Namespace.events.change, (e) => {
                        endDatePicker.updateOptions({
                            restrictions: {
                                minDate: e.detail.date,
                            },
                        });
                    });

                    endDateElement.addEventListener(tempusDominus.Namespace.events.change, (e) => {
                        startDatePicker.updateOptions({
                            restrictions: {
                                maxDate: e.detail.date,
                            },
                        });
                    });

                    setInitialRestrictions();
                }
            }
        });
    </script>
@endpush
