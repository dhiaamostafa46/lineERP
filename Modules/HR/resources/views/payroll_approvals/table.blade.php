<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-payroll-approvals-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_payroll_approvals.fields.Payroll Id')</th>
                    <th>@lang('hr::models/hr_payroll_approvals.fields.Employee Id')</th>
                    <th>@lang('hr::models/hr_payroll_approvals.fields.Status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payroll_approvals as $payroll_approval)
                <tr>
                    <td>{{ $payroll_approval->payroll_id }}</td>
                    <td>{{ $payroll_approval->employee_id }}</td>
                    <td>{{ $payroll_approval->status }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.payroll-approvals.destroy', $payroll_approval->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.payroll-approvals.show', [$payroll_approval->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.payroll-approvals.edit', [$payroll_approval->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $payroll_approvals])
        </div>
    </div>
</div>