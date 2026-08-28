<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="employees-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Dob</th>
                    <th>Religion</th>
                    <th>Gender</th>
                    <th>Marital Status</th>
                    <th>Nationality</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->username }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->dob }}</td>
                        <td>{{ $employee->religion }}</td>
                        <td>{{ $employee->gender_text }}</td>
                        <td>{{ $employee->marital_status_text }}</td>
                        <td>{{ $employee->nationality }}</td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['employees.destroy', $employee->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                @can('employees.show')
                                    <a href="{{ route('employees.show', [$employee->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan

                                @can('employees.edit')
                                    <a href="{{ route('employees.edit', [$employee->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan

                                @can('employees.destroy')
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                        'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                @endcan
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
