<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-salaries-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_salaries.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_salaries.fields.basic')</th>
                    <th>@lang('hr::models/hr_salaries.fields.total_allowance')</th>
                    <th>@lang('hr::models/hr_salaries.fields.total_deduct')</th>
                    <th>@lang('hr::models/hr_salaries.fields.total')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salaries as $salary)
                <tr>
                    <td>{{ $salary->employee->username ?? ''}}</td>
                    <td>{{ $salary->basic }}</td>
                    <td>{{ $salary->totalAllowance() }}</td>
                    <td>{{ $salary->totalDeduct() }}</td>
                    <td>{{  $salary->basic  + $salary->totalAllowance()  - $salary->totalDeduct() }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.salaries.destroy', $salary->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.salaries.show', [$salary->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.salaries.edit', [$salary->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $salaries])
        </div>
    </div>
</div>
