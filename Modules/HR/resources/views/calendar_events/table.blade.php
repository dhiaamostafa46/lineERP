<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-CalendarEvents-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_calendar_events.fields.id')</th>
                    <th>@lang('hr::models/hr_calendar_events.fields.name')</th>
                    <th>@lang('hr::models/hr_calendar_events.fields.type')</th>
                    <th>@lang('hr::models/hr_calendar_events.fields.start_date')</th>
                    <th>@lang('hr::models/hr_calendar_events.fields.end_date')</th>
                    <th>@lang('hr::models/hr_calendar_events.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($calendar_events as $calendarEvent)
                <tr>
                    <td>{{ $calendarEvent->id }}</td>
                    <td>{{ $calendarEvent->name }}</td>
                    <td>{{ $calendarEvent->type_text }}</td>
                    <td>{{ $calendarEvent->start_date ? $calendarEvent->start_date->format('Y-m-d') : '' }}</td>
                    <td>{{ $calendarEvent->end_date ? $calendarEvent->end_date->format('Y-m-d') : '' }}</td>
                    <td><span class="{{ $calendarEvent->status_badge }}">{{ $calendarEvent->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.CalendarEvents.destroy', $calendarEvent->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('hr.CalendarEvents.show')
                            <a href="{{ route('hr.CalendarEvents.show', [$calendarEvent->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan

                            @can('hr.CalendarEvents.edit')
                            <a href="{{ route('hr.CalendarEvents.edit', [$calendarEvent->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.CalendarEvents.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endcan
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $calendar_events->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $calendar_events])
        </div>
    </div>
</div>
