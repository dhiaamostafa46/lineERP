<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-termination-type-rewards-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/modelVariable.fields.Termination Type Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Percentage')</th>

                    <th>@lang('hr::models/modelVariable.fields.Worked Days')</th>

                    <th>@lang('hr::models/modelVariable.fields.Fixed Amount')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($termination_type_rewards as $termination_type_reward)
                <tr>
                    <td>{{ $termination_type_reward->termination_type_id }}</td>
                    <td>{{ $termination_type_reward->percentage }}</td>
                    <td>{{ $termination_type_reward->worked_days }}</td>
                    <td>{{ $termination_type_reward->fixed_amount }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.termination-type-rewards.destroy',
                        $termination_type_reward->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.termination-type-rewards.show', [$termination_type_reward->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.termination-type-rewards.edit', [$termination_type_reward->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', ['type' => 'submit', 'class' => 'btn
                            btn-icon btn-sm btn-light-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"])
                            !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $termination_type_rewards])
        </div>
    </div>
</div>