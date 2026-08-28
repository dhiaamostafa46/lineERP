<!-- Employee Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('employee_id', __('hr::models/hr_attendances.fields.employee_id') . ':') !!}
    <x-select2-input name="employee_id" :Attendanceholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($Attendance)->employee_id ?? 0)">
    </x-select2-input>
</div>

<!-- Day Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('day', __('hr::models/hr_attendances.fields.day') . ':') !!}
    <x-select2-input name="day" :Attendanceholder="__('hr::lang.day')" :list="$weekdays" :selected_id="old('day', @optional($Attendance)->day ?? 0)">
    </x-select2-input>
</div>

<!-- Distance Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('distance', __('hr::models/hr_attendances.fields.distance') . ':') !!}
    {!! Form::number('distance', old('distance', @optional($Attendance)->distance), [
        'class' => 'form-control',
        'Attendanceholder' => __('hr::models/hr_attendances.fields.distance'),
    ]) !!}
</div>

<!-- Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('name', __('hr::models/hr_attendances.fields.name') . ':') !!}
    {!! Form::text('name', old('name', @optional($Attendance)->name), [
        'class' => 'form-control',
        'Attendanceholder' => __('hr::models/hr_attendances.fields.name'),
    ]) !!}
</div>

<!-- Latitude Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('lat', __('hr::models/hr_attendances.fields.lat') . ':') !!}
    {!! Form::number('lat', old('lat', @optional($Attendance)->lat), [
        'class' => 'form-control',
        'id' => 'us3-lat',
        'readonly' => 'true',
        'Attendanceholder' => __('hr::models/hr_attendances.fields.lat'),
    ]) !!}
</div>

<!-- Longitude Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('lon', __('hr::models/hr_attendances.fields.lon') . ':') !!}
    {!! Form::number('lon', old('lon', @optional($Attendance)->lon), [
        'class' => 'form-control',
        'id' => 'us3-lon',
        'readonly' => 'true',
        'Attendanceholder' => __('hr::models/hr_attendances.fields.lon'),
    ]) !!}
</div>

<!-- Address Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('address', __('hr::models/hr_attendances.fields.address') . ':') !!}
    {!! Form::text('address', old('address', @optional($Attendance)->address), [
        'class' => 'form-control',
        'id' => 'us3-address',
        'Attendanceholder' => __('hr::models/hr_attendances.fields.address'),
    ]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_attendances.fields.status') . ':') !!}
    <x-select2-input name="status" :Attendanceholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($Attendance)->status ?? 0)">
    </x-select2-input>
</div>






<!-- Map Container -->
<div class="col-lg-6" id="us3" style="width: 100%;height:400px"></div>

@section('scripts')
    <script>
        $('#us3').locationpicker({
            location: {
                latitude: '{{ old('lat', optional($Attendance)->lat) ?? 24.711668300417863 }}',
                    longitude: '{{ old('lon', optional($Attendance)->lon) ?? 46.67555999755859 }}',
            },
            radius: 200,
            enableSearchBox: true,
            enableAutocomplete: true,
            inputBinding: {
                latitudeInput: $('#us3-lat'),
                longitudeInput: $('#us3-lon'),
                radiusInput: $('#us3-radius'), // Ensure this ID exists in your HTML
                locationNameInput: $('#us3-address')
            },
            markerDraggable: true,
            markerVisible: true,
            automaticallyAnimateToCurrentLocation: true,
            enableReverseGeocode: true,
            requiredGPS: true,

            onchanged: function(currentLocation, radius, isMarkerDropped) {
                // You can add functionality here if needed
            },
        });
    </script>
@endsection
