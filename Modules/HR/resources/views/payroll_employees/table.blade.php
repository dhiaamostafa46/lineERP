<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-payroll-employees-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_payroll_employees.fields.Employee Id')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Payroll Id')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Salary Id')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Total Allowances')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Total Deducts')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Basic Salary')</th>
                    <th>@lang('hr::models/hr_payroll_employees.fields.Status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payroll_employees as $payroll_employee)
                <tr>
                    <td>{{ $payroll_employee->employee_id }}</td>
                    <td>{{ $payroll_employee->payroll_id }}</td>
                    <td>{{ $payroll_employee->salary_id }}</td>
                    <td>{{ $payroll_employee->total_allowances }}</td>
                    <td>{{ $payroll_employee->total_deducts }}</td>
                    <td>{{ $payroll_employee->basic_salary }}</td>
                    <td>{{ $payroll_employee->status }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.payroll-employees.destroy', $payroll_employee->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.payroll-employees.show', [$payroll_employee->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.payroll-employees.edit', [$payroll_employee->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $payroll_employees])
        </div>
    </div>
</div>