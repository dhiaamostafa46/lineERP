<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-payroll-transactions-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_payroll_transactions.fields.Payroll Employee Id')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.name')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Forable Type')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Amount')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Currency')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Is Deduct')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Type')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Status')</th>

                    <th>@lang('hr::models/hr_payroll_transactions.fields.Note')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payroll_transactions as $payroll_transaction)
                <tr>
                    <td>{{ $payroll_transaction->payroll_employee_id }}</td>
                    <td>{{ $payroll_transaction->forable_id }}</td>
                    <td>{{ $payroll_transaction->forable_type }}</td>
                    <td>{{ $payroll_transaction->amount }}</td>
                    <td>{{ $payroll_transaction->currency }}</td>
                    <td>{{ $payroll_transaction->is_deduct }}</td>
                    <td>{{ $payroll_transaction->type }}</td>
                    <td>{{ $payroll_transaction->status }}</td>
                    <td>{{ $payroll_transaction->note }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.payroll-transactions.destroy', $payroll_transaction->id],
                        'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.payroll-transactions.show', [$payroll_transaction->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.payroll-transactions.edit', [$payroll_transaction->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $payroll_transactions])
        </div>
    </div>
</div>
