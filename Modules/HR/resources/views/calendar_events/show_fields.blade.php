<!-- Id Field -->
<div class="col-sm-12 row d-none">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->id }}</b>
    </div>
</div>

<!-- Name Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.name')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->name }}</b>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.type')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->type_text }}</b>
    </div>
</div>

<!-- Start Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.start_date')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->start_date ? $calendarEvent->start_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- End Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.end_date')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->end_date ? $calendarEvent->end_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.status')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->status_text }}</b>
    </div>
</div>

<!-- Description Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.description')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->description }}</b>
    </div>
</div>

<!-- Rules Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.rules')
        </p>
    </div>

    <div class="col-8">
        @if(!empty($calendarEvent->rules) && is_array($calendarEvent->rules))
            <div class="form-control h-auto">
                @foreach($calendarEvent->rules as $date => $detail)
                    <div class="d-flex justify-content-between border-bottom py-1">
                        <span class="fw-bold">{{ $date }}</span>
                        <span>{{ is_string($detail) ? $detail : json_encode($detail) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <b class="form-control"></b>
        @endif
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.created_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->created_at }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('hr::models/hr_calendar_events.fields.updated_at')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $calendarEvent->updated_at }}</b>
    </div>
</div>
