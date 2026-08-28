<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-payrolls-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_payrolls.fields.payroll_date')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.preparing_at')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.delivery_at')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.total')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payrolls as $payroll)
                    <tr>
                        <td>{{ $payroll->payroll_date_text }}</td>
                        <td>{{ $payroll->preparing_at_text }}</td>
                        <td>{{ $payroll->delivery_at_text }}</td>
                        <td>{{ $payroll->total_text }}</td>
                        <td>
                            <span class="{{ $payroll->status_badge }}">
                                {{ $payroll->status_text }}
                            </span>
                        </td>
                        <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.payrolls.destroy', $payroll->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('hr.payrolls.show', [$payroll->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                {{-- <a href="{{ route('hr.payrolls.edit', [$payroll->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a> --}}
                            @if (in_array($payroll->status, [1, 2]))
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                        'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                @endif
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
            @include('adminlte-templates::common.paginate', ['records' => $payrolls])
        </div>
    </div>
</div>
