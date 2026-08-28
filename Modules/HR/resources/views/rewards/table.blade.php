<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-rewards-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_rewards.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_rewards.fields.type')</th>
                    <th>@lang('hr::models/hr_rewards.fields.value')</th>
                    <th>@lang('hr::models/hr_penalties.fields.due_date')</th>
                    <th>@lang('hr::models/hr_rewards.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rewards as $reward)
                <tr>
                    <td>{{ $reward->employee->username ?? '' }}</td>
                    <td>{{ $reward->type_text }}</td>
                    <td>{{ $reward->value_text }}</td>
                    <td>{{ $reward->due_date ?? '-' }}</td>
                    <td>
                        <span class="{{ $reward->status_badge}}">
                            {{ $reward->status_text }}

                        </span>

                       
                    </td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.rewards.destroy', $reward->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.rewards.show', [$reward->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($reward->status == 1)
                            <a href="{{ route('hr.rewards.edit', [$reward->id]) }}"
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
                        @livewire('hr::payrolls.add-transactions', ['hr_setting' => $hr_setting, 'model' => $reward , 'showAddButton' => ($reward->status==2 ? true : false)], key('add-transactions-' . $reward->id))
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $rewards])
        </div>
    </div>
</div>
