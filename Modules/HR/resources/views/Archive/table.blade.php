<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-employees-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_employees.fields.id')</th>
                    <th>@lang('hr::models/hr_employees.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.job_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.department_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.shift_id')</th>
                    <th>@lang('hr::models/hr_employees.fields.job_level')</th>
                    <th>@lang('hr::models/hr_employees.fields.specialty')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Hremployee as $employee)
                    <tr>
                        <td>{{ $employee->id ?? '' }}</td>
                        {{-- @dd($employee->main_employee) --}}
                        <td>{{ $employee->username ?? '' }}</td>
                        <td>{{ $employee->job->name ?? '' }}</td>
                        <td>{{ $employee->department->name ?? '' }}</td>
                        <td>{{ $employee->shift->name ?? '' }}</td>
                        <td>{{ $employee->job_level ?? '' }}</td>
                        <td>{{ $employee->specialty  ?? ''}}</td>


                        </td>
                        <td style="width: 120px">
                            <div class='btn-group'>
                                <a href="{{ route('hr.Archive.show', [$employee->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('hr.Archive.restore', $employee->id) }}" class="btn btn-sm btn-success ms-2">
                                    <i class="fa-solid fa-rotate-left me-1"></i>
                                </a>
                            </div>
                            {{-- {!! Form::open(['route' => ['hr.employees.destroy', $employee->id], 'method' => 'delete']) !!}

                            {!! Form::close() !!} --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">

        </div>
    </div>
</div>
