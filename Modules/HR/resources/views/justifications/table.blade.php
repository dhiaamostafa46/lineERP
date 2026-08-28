<div class="card-body p-5">
    <div class="table-responsive">
        <table class="table align-middle gs-0 gy-4" id="hr-justifications-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.employee_id')</th>
                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.request_date')</th>

                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.shift_id')</th>

                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.type')</th>
                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.status')</th>
                    <th class="min-w-125px text-end">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($justifications as $justification)
                    <tr>
                        <td>{{ $justification->employee->username ?? '' }}</td>
                        <td>{{ $justification->request_date->format('Y-m-d') }}</td>
                        <td>
                            @php
                                $shift = optional($justification->HrShift);
                            @endphp

                            @if ($shift->from && $shift->to)
                                <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <strong>{{ \Carbon\Carbon::parse($shift->from)->format('h:i A') }}</strong>
                                    </div>

                                    <span style="font-weight: bold;">-</span>

                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <strong>{{ \Carbon\Carbon::parse($shift->to)->format('h:i A') }}</strong>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span>{{ $justification->type_text }}</span>
                        </td>
                        <td>
                            @livewire('hr::trackers.get-status', ['model' => $justification], key('trackers_get_status_' . $justification->id))
                        </td>
                        <td class="text-end">
                            {!! Form::open(['route' => ['hr.justifications.destroy', $justification->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('hr.justifications.show', [$justification->id]) }}"
                                    class='btn btn-sm btn-icon btn-light-success'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                {{-- <a href="{{ route('hr.justifications.edit', [$justification->id]) }}"
                                class='btn btn-sm btn-icon btn-light-primary'>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a> --}}
                                {{-- {!! Form::button('<i class="fa-solid fa-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-icon btn-light-danger', 'onclick' => "return confirm('Are you sure?')"]) !!} --}}
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $justifications])
        </div>
    </div>
</div>
