<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-terminations-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_terminations.fields.termination_type_id')</th>
                    <th>@lang('hr::models/hr_terminations.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_terminations.fields.worked_days')</th>
                    <th>@lang('hr::models/hr_terminations.fields.last_reward')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($terminations as $termination)
                <tr>
                    <td>{{ $termination->termination_type_id }}</td>
                    <td>{{ $termination->employee_id }}</td>
                    <td>{{ $termination->worked_days }}</td>
                    <td>{{ $termination->last_reward }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.terminations.destroy', $termination->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.terminations.show', [$termination->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.terminations.edit', [$termination->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $terminations])
        </div>
    </div>
</div>
