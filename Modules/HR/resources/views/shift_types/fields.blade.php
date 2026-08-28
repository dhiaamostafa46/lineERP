<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_shift_types.fields.name') . ':') !!}
        {!! Form::text(
            $locale . '[name]',
            old($locale.'.name', isset($shift) ? $shift->translate($locale)->name : null),
            ['class' => 'form-control']
        ) !!}
    </div>
    @endforeach
</div>

<div class="row">
    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('hr::models/hr_jobs.fields.status') . ':') !!}
        {!! Form::select(
            'status',
            $statuses,
            old('status', $shift->status ?? null),
            ['class' => 'form-control']
        ) !!}
    </div>

    <!-- Type Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('hr::models/hr_shift_types.fields.type') . ':') !!}
        {!! Form::select(
            'type',
            $types,
            old('type', $shift->type ?? null),
            ['class' => 'form-control', 'id' => 'shift_type']
        ) !!}
    </div>

    <!-- Work Hours Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('work_hours', __('hr::models/hr_shift_types.fields.work_hours') . ':') !!}
        {!! Form::number(
            'work_hours',
            old('work_hours', $shift->work_hours ?? 0),
            ['class' => 'form-control', 'step' => '0.5', 'min' => '0']
        ) !!}
    </div>
</div>

<!-- Conditional Date Fields for Type 3 -->
<div class="row mt-4" id="date_period_section" style="display: none;">
    <div class="col-12">
        <h4 class="mb-3">@lang('hr::models/hr_shift_types.sections.Specificperiods')</h4>
        <div class="separator mb-4"></div>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('start_date', __('hr::models/hr_shift_types.fields.start_date') . ':') !!}
        {!! Form::date(
            'start_date',
            old('start_date', $shift->start_date ?? null),
            ['class' => 'form-control', 'id' => 'start_date']
        ) !!}
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('end_date', __('hr::models/hr_shift_types.fields.end_date') . ':') !!}
        {!! Form::date(
            'end_date',
            old('end_date', $shift->end_date ?? null),
            ['class' => 'form-control', 'id' => 'end_date']
        ) !!}
    </div>
</div>

<!-- Attendance Settings Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">@lang('hr::models/hr_shift_types.sections.attendance_settings')</h4>
        <div class="separator mb-4"></div>
    </div>

    <div class="form-group col-md-3 mb-3">
        {!! Form::label('early_entry', __('hr::models/hr_shift_types.fields.early_entry') . ':') !!}
        {!! Form::number(
            'early_entry',
            old('early_entry', $shift->early_entry ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>

    <div class="form-group col-md-3 mb-3">
        {!! Form::label('late_entry', __('hr::models/hr_shift_types.fields.late_entry') . ':') !!}
        {!! Form::number(
            'late_entry',
            old('late_entry', $shift->late_entry ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>

    <div class="form-group col-md-3 mb-3">
        {!! Form::label('early_exit', __('hr::models/hr_shift_types.fields.early_exit') . ':') !!}
        {!! Form::number(
            'early_exit',
            old('early_exit', $shift->early_exit ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>

    <div class="form-group col-md-3 mb-3">
        {!! Form::label('late_exit', __('hr::models/hr_shift_types.fields.late_exit') . ':') !!}
        {!! Form::number(
            'late_exit',
            old('late_exit', $shift->late_exit ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>
</div>

<!-- Entry Period Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">@lang('hr::models/hr_shift_types.sections.entry_period')</h4>
        <div class="separator mb-4"></div>
    </div>

    <div class="form-group col-md-6 mb-3">
        {!! Form::label('entry_end', __('hr::models/hr_shift_types.fields.entry_start') . ':') !!}
        {!! Form::number(
            'entry_end',
            old('entry_end', $shift->entry_end ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>

    <div class="form-group col-md-6 mb-3">
        {!! Form::label('exit_start', __('hr::models/hr_shift_types.fields.exit_end') . ':') !!}
        {!! Form::number(
            'exit_start',
            old('exit_start', $shift->exit_start ?? 0),
            ['class'=>'form-control','min'=>0,'max'=>1440,'placeholder'=>__('hr::models/hr_shift_types.placeholders.minutes')]
        ) !!}
    </div>
</div>

<!-- Days Configuration Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">@lang('hr::models/hr_shift_types.sections.days_configuration')</h4>
        <div class="separator mb-4"></div>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('work_days', __('hr::models/hr_shift_types.fields.work_days') . ':') !!}
        <div class="row mt-2">
            @foreach (config('week_days') as $key => $item)
            <div class="col-12 col-md-6 mb-2">
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" id="work_day_{{ $key }}" name="work_days[]"
                        value="{{ $item }}"
                        {{ in_array($item, old('work_days', $shift->work_days ?? config('week_days'))) ? 'checked' : '' }} />
                    <label class="form-check-label " for="work_day_{{ $key }}">
                        @lang('hr::lang.' . $item)
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('exempt_days', __('hr::models/hr_shift_types.fields.exempt_days') . ':') !!}
        <div class="row mt-2">
            @foreach (config('week_days') as $key => $item)
            <div class="col-12 col-md-6 mb-2">
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" id="exempt_day_{{ $key }}" name="exempt_days[]"
                        value="{{ $item }}"
                        {{ in_array($item, old('exempt_days', $shift->exempt_days ?? [])) ? 'checked' : '' }} />
                    <label class="form-check-label  " for="exempt_day_{{ $key }}">
                        @lang('hr::lang.' . $item)
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Shifts Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">@lang('hr::models/hr_shift_types.sections.shifts')</h4>
        <div class="separator mb-4"></div>
    </div>

    @php
        $shiftsData = old('shifts', $shift->shifts ?? []);
    @endphp

    <div class="col-12">
        {!! Form::label('shifts', __('hr::models/hr_shift_types.fields.shifts') . ':') !!}
        <div id="shifts">
            <div class="form-group">
                <div data-repeater-list="shifts">
                    @forelse ($shiftsData as $item)
                    <div data-repeater-item class="mb-3">
                        <div class="form-group row align-items-end border rounded p-3 bg-light">
                            <div class="col-md-3">
                                {!! Form::label('from', __('hr::models/hr_shift_types.fields.from') . ':') !!}
                                {!! Form::time('from', $item['from'] ?? $item->from ?? null, ['class'=>'form-control']) !!}
                                <input type="hidden" name="shift_id" value="{{ $item['id'] ?? $item->id ?? null }}">
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('to', __('hr::models/hr_shift_types.fields.to') . ':') !!}
                                {!! Form::time('to', $item['to'] ?? $item->to ?? null, ['class'=>'form-control']) !!}
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="is_active_{{ $loop->index }}" name="is_active"
                                        {{ ($item['is_active'] ?? $item->is_active ?? 0) ? 'checked' : '' }} />
                                    <label class="form-check-label text-dark" for="is_active_{{ $loop->index }}">
                                        @lang('hr::models/hr_shift_types.fields.is_active')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-light-danger">
                                    <i class="ki-duotone ki-trash fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div data-repeater-item class="mb-3">
                        <div class="form-group row align-items-end border rounded p-3 bg-light">
                            <div class="col-md-3">
                                {!! Form::label('from', __('hr::models/hr_shift_types.fields.from') . ':') !!}
                                {!! Form::time('from', null, ['class'=>'form-control']) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('to', __('hr::models/hr_shift_types.fields.to') . ':') !!}
                                {!! Form::time('to', null, ['class'=>'form-control']) !!}
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_active_new" name="is_active" />
                                    <label class="form-check-label text-dark" for="is_active_new">
                                        @lang('hr::models/hr_shift_types.fields.is_active')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-light-danger">
                                    <i class="ki-duotone ki-trash fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="form-group mt-5">
                    <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                        <i class="ki-duotone ki-plus fs-3"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Repeater
        $('#shifts').repeater({
            initEmpty: false,
            show: function () { $(this).slideDown(); },
            hide: function (deleteElement) { $(this).slideUp(deleteElement); }
        });

        // Toggle date period section based on type
        function toggleDatePeriod() {
            const typeValue = $('#shift_type').val();
            if (typeValue == 3) $('#date_period_section').slideDown();
            else $('#date_period_section').slideUp();
        }

        toggleDatePeriod();
        $('#shift_type').on('change', toggleDatePeriod);
    });
</script>
@endpush
