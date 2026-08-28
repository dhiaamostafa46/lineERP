<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-penalties-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_penalties.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_penalties.fields.description')</th>
                    <th>@lang('hr::models/hr_penalties.fields.amount')</th>
                    <th>@lang('hr::models/hr_penalties.fields.due_date')</th>
                    <th>@lang('hr::models/hr_penalties.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penalties as $penalty)
                <tr>
                    <td>{{ $penalty->employee->username ?? '' }}</td>
                    <td>{{ $penalty->description }}</td>
                    <td>{{ $penalty->amount }}</td>
                    <td>{{ $penalty->due_date_text }}</td>
                    <td>

                        {{ $penalty->status_text }}
                        {{-- @livewire('hr::trackers.get-status', ['model' => $penalty], key('trackers_get_status')) --}}
                    </td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.penalties.destroy', $penalty->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.penalties.show', [$penalty->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($penalty->status == 1)
                            <a href="{{ route('hr.penalties.edit', [$penalty->id]) }}"
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
                    <td>
                        @livewire('hr::payrolls.add-transactions', ['hr_setting' => $hr_setting, 'model' => $penalty , 'showAddButton' => ($penalty->status==2 ? true : false)],
                        key('add-transactions-' . $penalty->id))
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $penalties])
        </div>
    </div>
</div>
