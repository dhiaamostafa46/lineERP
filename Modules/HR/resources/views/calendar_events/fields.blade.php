<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_calendar_events.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($calendarEvent) ? $calendarEvent->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<!-- Type Field -->
<input type="hidden" name="type" value="1">
{{-- <div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_calendar_events.fields.type') . ':') !!}
    {!! Form::select('type', $types, null, [
    'class' => 'form-control', 'placeholder' => __('hr::lang.select_type')
    ]) !!}
</div> --}}

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_calendar_events.fields.status') . ':') !!}
    {!! Form::select('status', $statuses, null, [
    'class' => 'form-control',
    ]) !!}
</div>

<!-- Start Date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('start_date', __('hr::models/hr_calendar_events.fields.start_date') . ':') !!}
    {!! Form::date('start_date', @optional($calendarEvent)->start_date ? @optional($calendarEvent)->start_date->format('Y-m-d') : null, ['class' => 'form-control']) !!}
</div>

<!-- End Date Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('end_date', __('hr::models/hr_calendar_events.fields.end_date') . ':') !!}
    {!! Form::date('end_date', @optional($calendarEvent)->end_date ? @optional($calendarEvent)->end_date->format('Y-m-d') : null, ['class' => 'form-control']) !!}
</div>

<!-- Color Field -->
<div class="form-group col-sm-6 mb-3" style="display: none">
    {!! Form::label('color', __('hr::models/hr_calendar_events.fields.color') . ':') !!}
    {!! Form::color(
        'color',
        @optional($calendarEvent)->color ?? '#c6e2ff',
        ['class' => 'form-control form-control-color w-100']
    ) !!}
</div>


<!-- Is Recurring Field -->
<div class="form-group col-sm-6 mb-3">
    <div class="form-check form-switch form-check-custom form-check-solid mt-8">
        {!! Form::hidden('is_recurring', 0) !!}
        {!! Form::checkbox('is_recurring', 1, @optional($calendarEvent)->is_recurring ?? null, ['class' => 'form-check-input']) !!}
        {!! Form::label('is_recurring', __('hr::models/hr_calendar_events.fields.is_recurring'), ['class' => 'form-check-label']) !!}
    </div>
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('description', __('hr::models/hr_calendar_events.fields.description') . ':') !!}
    {!! Form::textarea('description', @optional($calendarEvent)->description ?? null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>

<!-- Rules Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('rules', __('hr::models/hr_calendar_events.fields.rules') . ':') !!}
    <div id="daily-rules-container" class="border p-3 rounded bg-light" style="max-height: 400px; overflow-y: auto;">
        <span class="text-muted">Please select start and end dates.</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const container = document.getElementById('daily-rules-container');

        // Prepare existing rules safely
        const existingRules = @json(old('rules', isset($calendarEvent) ? $calendarEvent->rules : []));

        function calculateDays() {
            const startVal = startDateInput.value;
            const endVal = endDateInput.value;

            if (!startVal || !endVal) {
                container.innerHTML = '<span class="text-muted">Please select start and end dates.</span>';
                return;
            }

            const sParts = startVal.split('-');
            const eParts = endVal.split('-');
            const start = new Date(sParts[0], sParts[1]-1, sParts[2]);
            const end = new Date(eParts[0], eParts[1]-1, eParts[2]);

            if (start > end) {
                container.innerHTML = '<span class="text-danger">Start date cannot be after end date.</span>';
                return;
            }

            container.innerHTML = ''; // Clear current

            let current = new Date(start);

            while (current <= end) {
                // Format YYYY-MM-DD manually to avoid timezone issues
                const year = current.getFullYear();
                const month = String(current.getMonth() + 1).padStart(2, '0');
                const day = String(current.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;

                const val = existingRules && existingRules[dateString] ? existingRules[dateString] : '';

                const row = `
                    <div class="form-group row mb-2">
                        <label class="col-sm-3 col-form-label">${dateString}</label>
                        <div class="col-sm-9">
                            <input type="text" name="rules[${dateString}]" class="form-control" value="${val}" placeholder="Details">
                        </div>
                    </div>`;

                container.insertAdjacentHTML('beforeend', row);

                current.setDate(current.getDate() + 1);
            }
        }

        startDateInput.addEventListener('change', calculateDays);
        endDateInput.addEventListener('change', calculateDays);

        if (startDateInput.value && endDateInput.value) {
            calculateDays();
        }
    });
</script>
