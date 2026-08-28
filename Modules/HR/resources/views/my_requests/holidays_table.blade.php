<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-holidays-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_holidays.fields.type_id')</th>
                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.status')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $holiday)
                    <tr>
                        <td>{{ $holiday->type->name ?? '' }}</td>
                        <td>{{ $holiday->from_at->format('Y-m-d h:i a') }}</td>
                        <td>{{ $holiday->end_at->format('Y-m-d h:i a') }}</td>
                        <td><span class="{{ $holiday->status_badge }}">{{ $holiday->status_text }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $holidays])
        </div>
    </div>
</div>
