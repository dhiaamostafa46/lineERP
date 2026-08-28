<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.department_id') - @lang('hr::models/hr_places.fields.employee_id') - @lang('hr::models/hr_places.fields.branch_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            @switch($Place->flage)
                @case(\Modules\HR\App\Models\HrPlace::FLAG_EMPLOYEES)
                    {{ $Place->employees()->pluck('username')->implode(', ') }}
                @break

                @case(\Modules\HR\App\Models\HrPlace::FLAG_DEPARTMENT)
                    {{ $Place->departments()->pluck('name')->implode(', ') }}
                @break

                @case(\Modules\HR\App\Models\HrPlace::FLAG_BRANCHES)
                    {{ $Place->branches()->pluck('name')->implode(', ') }}
                @break

                @default
                    {{ __('hr::models/hr_places.flages.all') }}
            @endswitch

        </b>
    </div>
</div>

<!-- Day Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.day')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->weekdays_text }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.name')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->name }}</b>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4 d-flex align-items-center">
        <label class="fs-5 mb-0">
            {{ __('hr::models/hr_places.fields.date') }}
        </label>
    </div>

    <div class="col-md-8">
        <div class="form-control bg-light fw-bold">
            @if ($Place->enable_daterange == 1)
                {{ $Place->start_date ?? '—' }} &nbsp; — &nbsp; {{ $Place->end_date ?? '—' }}
            @endif

        </div>
    </div>
</div>

<!-- Latitude Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.lat')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->lat }}</b>
    </div>
</div>

<!-- Longitude Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.lon')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->lon }}</b>
    </div>
</div>

<!-- Address Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.address')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->address }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            <span class="{{ $Place->status_badge }}">{{ $Place->status_text }}</span>
        </b>
    </div>
</div>

<!-- Distance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.distance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->distance }}</b>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.created_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.updated_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Place->updated_at }}</b>
    </div>
</div>

<!-- Map Display -->



<!-- Map Display -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_places.fields.map')
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
                    latitude: {{ $Place->lat }},
                    longitude: {{ $Place->lon }}
                },
                radius: {{ $Place->distance }},

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
