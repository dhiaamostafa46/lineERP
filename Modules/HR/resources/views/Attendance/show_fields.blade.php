<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.employee_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->employee->username }}</b>
    </div>
</div>

<!-- Day Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.day')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->weekdays_text }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.name')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->name }}</b>
    </div>
</div>

<!-- Latitude Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.lat')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->lat }}</b>
    </div>
</div>

<!-- Longitude Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.lon')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->lon }}</b>
    </div>
</div>

<!-- Address Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.address')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->address }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            <span class="{{ $Attendance->status_badge }}">{{ $Attendance->status_text }}</span>
        </b>
    </div>
</div>

<!-- Distance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.distance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->distance }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.created_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.updated_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Attendance->updated_at }}</b>
    </div>
</div>

<!-- Map Display -->



<!-- Map Display -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_attendances.fields.map')
        </p>
    </div>
    <div class="col-8">
        <!-- Google Maps with Location Picker -->
        <div id="location-picker" style="width: 100%; height: 400px;"></div>
    </div>
</div>

@section('scripts')

<script>
    $(document).ready(function() {
        // Initialize the location picker
        $('#location-picker').locationpicker({
            location: {
                latitude: {{ $Attendance->lat }},
                longitude: {{ $Attendance->lon }}
            },
            radius: 0,

            enableAutocomplete: false,
            onchanged: function(currentLocation, radius, isMarkerDropped) {

            },
            mapOptions: {
                zoom: 14
            }
        });
    });
</script>
@endsection
