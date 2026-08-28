<!-- Employee Id Field -->

<!-- Flage Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label(
        'flage',
        __('hr::models/hr_places.fields.department_id') . '/' . __('hr::models/hr_places.fields.employee_id') . ':'. __('hr::models/hr_places.fields.branch_id') ,
    ) !!}
    <x-select2-input id="flage" name="flage" :placeholder="__('hr::lang.select_status')" :list="$flages" :selected_id="old('flage', @optional($Place)->flage ?? 1)">
    </x-select2-input>
</div>


<div id="employee_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_places.fields.employee_id') . ':') !!}
    <x-select2multi-input id="employee_id" name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($Place)->employee_id ?? 0)" />
</div>




<div id="department_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('department_id', __('hr::models/hr_places.fields.department_id') . ':') !!}
    <x-select2multi-input id="department_id" name="department_id" :placeholder="__('hr::lang.select_department')" :list="$Department"
        :selected_id="old('department_id', @optional($Place)->department_id ?? 0)" />
</div>




<div id="branches_field" style="display:none;" class="form-group col-sm-6 mb-3">
    {!! Form::label('branch_id', __('hr::models/hr_places.fields.branch_id') . ':') !!}
    <x-select2multi-input id="branch_id" name="branch_id" :placeholder="__('hr::lang.branches')" :list="$Branches" :selected_id="old('branch_id', @optional($Place)->branch_id ?? 0)" />
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('day', __('hr::models/hr_places.fields.day') . ':') !!}
    <x-select2multi-input id="day" name="day" :placeholder="__('hr::lang.day')" :list="$weekdays" :selected_id="old('day', @optional($Place)->day ?? 0)" />
</div>


<div class="form-group col-sm-6 mb-3"style="display: none;">
    <div class="form-check">
        <input class="form-check-input"   type="checkbox"    id="enable_daterange"
            name="enable_daterange"
            value="1"
            {{ (isset($Place) && $Place->enable_daterange == 1) || old('enable_daterange') ? 'checked' : '' }}
        >
        <label class="form-check-label" for="enable_daterange">
            {{ __('hr::models/hr_places.fields.active_date') }}
        </label>
    </div>
</div>


<div id="daterangepicker_container" class="form-group col-sm-6 mb-3" style="display: none;">
    {!! Form::label('daterangepicker', __('hr::models/hr_places.fields.start_date') . ' / ' . __('hr::models/hr_places.fields.end_date') . ':') !!}
    @php
        $dateRangeValue = '';
        if (isset($Place) && $Place->start_date && $Place->end_date) {
            $dateRangeValue = $Place->start_date->format('m/d/Y h:i A') . ' - ' . $Place->end_date->format('m/d/Y h:i A');
        }
    @endphp
    <input class="form-control form-control-solid" name="daterangepicker" placeholder="Pick date range" id="kt_daterangepicker_1" value="{{ old('daterangepicker', $dateRangeValue) }}" />
     {!! Form::label('daterangepicker',  __('hr::models/hr_places.fields.dateinfo') ) !!}
</div>
<!-- Additional Fields -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('distance', __('hr::models/hr_places.fields.distance') . ':') !!}
    {!! Form::number('distance', old('distance', @optional($Place)->distance ?? 100), [
        'class' => 'form-control',
        'id' => 'distance',
        'min' => '0',
        'step' => '1',
        'max'  =>'100',
        'placeholder' => __('hr::models/hr_places.fields.distance'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('name', __('hr::models/hr_places.fields.name') . ':') !!}
    {!! Form::text('name', old('name', @optional($Place)->name), [
        'class' => 'form-control',
        'placeholder' => __('hr::models/hr_places.fields.name'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('lat', __('hr::models/hr_places.fields.lat') . ':') !!}
    {!! Form::number('lat', old('lat', @optional($Place)->lat ?? 24.711668300417863), [
        'class' => 'form-control',
        'id' => 'us3-lat',
        'step' => 'any',

        'placeholder' => __('hr::models/hr_places.fields.lat'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('lon', __('hr::models/hr_places.fields.lon') . ':') !!}
    {!! Form::number('lon', old('lon', @optional($Place)->lon ?? 46.67529838422555), [
        'class' => 'form-control',
        'id' => 'us3-lon',
        'step' => 'any',

        'placeholder' => __('hr::models/hr_places.fields.lon'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('address', __('hr::models/hr_places.fields.address') . ':') !!}
    {!! Form::text('address', old('address', @optional($Place)->address), [
        'class' => 'form-control',
        'id' => 'us3-address',
        'placeholder' => __('hr::models/hr_places.fields.address'),
    ]) !!}
</div>

<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_places.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($Place)->status ?? 0)">
    </x-select2-input>
</div>

<!-- Map Container -->
<div class="form-group col-sm-12 mb-3">
    <label>{{ __('hr::models/hr_places.fields.map_location') }}</label>
    <div id="us3" style="width: 100%; height: 400px; border: 1px solid #ddd; border-radius: 4px;"></div>
</div>

<!-- Hidden Fields for Default Values -->
<input type="hidden" id="default-lat" value="{{ @optional($Place)->lat ?? '24.711668300417863' }}">
<input type="hidden" id="default-lon" value="{{ @optional($Place)->lon ?? '46.67529838422555' }}">
<input type="hidden" id="default-distance" value="{{ @optional($Place)->distance ?? '100' }}">

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize default values

            function toggleDateRangePicker() {
                if ($('#enable_daterange').is(':checked')) {
                    $('#daterangepicker_container').show();
                } else {
                    $('#daterangepicker_container').hide();
                }
            }

            $('#enable_daterange').on('change', function() {
                toggleDateRangePicker();
            });

            // Initialize on page load
            toggleDateRangePicker();


            $('#kt_daterangepicker_1').daterangepicker({
                timePicker: true,
                startDate: moment(),
                endDate: moment(),
                locale: {
                    format: "YYYY-MM-DD",
                    applyLabel: "تطبيق",
                    cancelLabel: "إلغاء"
                }
            }, function(start, end) {
                // عند اختيار تاريخ جديد، نحذف أي تلوين قديم
                $('.daterangepicker td').removeClass('custom-highlight');

                // نحسب الأيام الواقعة بين التاريخين
                $('.daterangepicker td.available').each(function() {
                    var cellDate = moment($(this).attr('data-title'), 'L');
                    if (cellDate.isBetween(start, end, 'day', '[]')) {
                        $(this).addClass('custom-highlight');
                    }
                });
            });

            // نضيف CSS للتلوين
            $('<style>')
                .prop('type', 'text/css')
                .html(`
        .daterangepicker td.custom-highlight {
            background-color: #007bff !important;
            color: white !important;
            border-radius: 50%;
        }
    `)
                .appendTo('head');



            var defaultLat = parseFloat($('#default-lat').val()) || 24.711668300417863;
            var defaultLon = parseFloat($('#default-lon').val()) || 46.67529838422555;
            var defaultDistance = parseInt($('#default-distance').val()) || 100;

            // Initialize the map with locationpicker
            $('#us3').locationpicker({
                location: {
                    latitude: defaultLat,
                    longitude: defaultLon
                },
                radius: defaultDistance,
                enableSearchBox: true,
                enableAutocomplete: true,
                inputBinding: {
                    latitudeInput: $('#us3-lat'),
                    longitudeInput: $('#us3-lon'),
                    radiusInput: $('#distance'),
                    locationNameInput: $('#us3-address')
                },
                markerDraggable: true,
                markerVisible: true,
                circleVisible: true,
                automaticallyAnimateToCurrentLocation: false,
                enableReverseGeocode: true,
                onchanged: function(currentLocation, radius, isMarkerDropped) {
                    // Update distance field when circle is resized
                    if (radius) {
                        $('#distance').val(Math.round(radius));
                    }
                }
            });

            // Update map radius when distance input changes
            $('#distance').on('input change keyup', function() {
                var newRadius = parseInt($(this).val());
                if (newRadius && newRadius > 0) {
                    $('#us3').locationpicker('radius', newRadius);
                }
            });

            // Initialize summernote if exists
            if ($('#summernote').length) {
                $('#summernote').summernote({
                    height: 200
                });
            }

            // Toggle fields based on the flage value
            function toggleFields(flageValue) {
                // Hide all conditional fields by default
                $('#employee_field, #department_field, #branches_field').hide();

                // Show relevant field based on flage value
                if (flageValue == 2) {
                    $('#employee_field').show(); // Show employee field
                } else if (flageValue == 3) {
                    $('#department_field').show(); // Show department field
                } else if (flageValue == 4) {
                    $('#branches_field').show(); // Show branches field
                }
            }

            // Initialize fields on page load and listen for changes in the flage dropdown
            $('#flage').on('change', function() {
                var selectedValue = $(this).val();
                toggleFields(selectedValue);
            }).trigger('change');

            // Form validation before submit (optional)
            $('form').on('submit', function(e) {
                var flageValue = $('#flage').val();
                var isValid = true;
                var message = '';

                const isMultiSelectEmpty = (selector) => {
                    const value = $(selector).val();
                    return !value || value.length === 0;
                };

                switch (flageValue) {
                    case '2':
                        if (isMultiSelectEmpty('#employee_id')) {
                            isValid = false;
                            message = '{{ __('hr::lang.please_select_employee') }}';
                        }
                        break;
                    case '3':
                        if (isMultiSelectEmpty('#department_id')) {
                            isValid = false;
                            message = '{{ __('hr::lang.please_select_department') }}';
                        }
                        break;
                    case '4':
                        if (isMultiSelectEmpty('#branch_id')) {
                            isValid = false;
                            message = '{{ __('hr::lang.please_select_branch') }}';
                        }
                        break;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(message);
                    return false;
                }
            });
        });
    </script>
@endsection
