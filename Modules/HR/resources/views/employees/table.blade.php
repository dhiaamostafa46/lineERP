<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-employees-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_employees.fields.id')</th>
                    <th>@lang('hr::models/hr_employees.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.job_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.department_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.phone')</th>
                    <th>@lang('hr::models/hr_employees.fields.job_level')</th>
                    <th>@lang('hr::models/hr_employees.fields.specialty')</th>
                    <th>@lang('hr::models/hr_employees.fields.license_expired_at')</th>
                    <th>@lang('models/employees.fields.identity_expired_at')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                <tr>
                  
                    <td>{{ $employee->job_number}}</td>
                    <td>{{ $employee->username }}</td>
                    <td>{{ $employee->job->name??'' }}</td>
                    <td>{{ $employee->department->name??'' }}</td>
                    <td>{{ $employee->main_employee->phone??'' }}</td>
                    <td>{{ $employee->job_level }}</td>
                    <td>{{ $employee->specialty }}</td>
                    <td class="upComingCheck">{{ $employee->license_expired_at }}</td>
                    <td class="upComingCheck">{{ @optional($employee->main_employee->identity)->identity_expired_at??''
                        }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.employees.destroy', $employee->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.employees.show', [$employee->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.employees.edit', [$employee->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $employees])
        </div>
    </div>
</div>
