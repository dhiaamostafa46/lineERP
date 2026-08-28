<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-holidays-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_holidays.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_holidays.fields.status')</th>
                    <th>@lang('hr::models/hr_holidays.fields.type_id')</th>
                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $holiday)
                <tr>
                    <td>{{ $holiday->employee->username ?? '' }}</td>
                    <td>

                          <span class="{{ $holiday->status_badge }}">
            <!--If there is no trake for employee requests and request finished-->
                           {{ $holiday->status_text ?? '' }}
                    </span>

                        {{-- @livewire('hr::trackers.get-status', ['model' => $holiday], key('trackers_get_status_'.$holiday->id)) --}}
                    </td>
                    <td>{{ $holiday->type->name ?? '' }}</td>
                    <td>{{ $holiday->from_at->format('Y-m-d h:i a') }}</td>
                    <td>{{ $holiday->end_at->format('Y-m-d h:i a') }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.holidays.destroy', $holiday->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.holidays.show', [$holiday->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($holiday->status == 1)
                            <a href="{{ route('hr.holidays.edit', [$holiday->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </td>
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
