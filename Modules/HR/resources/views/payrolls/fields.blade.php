<!-- Payroll Date Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('payroll_date', __('hr::models/hr_payrolls.fields.payroll_date') . ':') !!}
    {!! Form::month('payroll_date', $payroll_date, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
</div>

<!-- Preparingl At Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('preparing_at', __('hr::models/hr_payrolls.fields.preparing_at') . ':') !!}
    {!! Form::date('preparing_at', now()->format('Y-m-d'), ['class' => 'form-control ', 'disabled' => 'disabled']) !!}
</div>

<!-- Delivery At Field -->
<div class="form-group col-md-4 col-sm-12 mb-3">
    {!! Form::label('delivery_at', __('hr::models/hr_payrolls.fields.delivery_at') . ':') !!}
    {!! Form::date('delivery_at', now()->format('Y-m-d'), ['class' => 'form-control']) !!}
</div>
<input type="hidden" name="total" value="{{ $payroll_employees->sum('net_wage') }}">

<div class="col-12">
    <hr>
    <h2>@lang('hr::models/hr_payrolls.fields.employees')</h2>
    <div class="table-responsive " >
        <table class="table table-striped gy-7 gs-7 " id="kt_datatable_zero_configuration">
            <thead>
                <tr>
                    {{-- <th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                            <label class="form-check-label" for="flexCheckDefault">
                                All
                            </label>
                        </div>
                    </th> --}}
                    <th>@lang('hr::models/hr_payrolls.fields.employee')</th>
                    <th>
                        @lang('hr::models/hr_payrolls.fields.job')
                        @
                        @lang('hr::models/hr_payrolls.fields.department')</th>
                    </th>
                    <th>@lang('hr::models/hr_payrolls.fields.basic_salary')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.allowances')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.deductions')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.penalties')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.advances')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.rewards')</th>
                    <th>@lang('hr::models/hr_payrolls.fields.total')</th>
                </tr>
            </thead>
            <tbody class="lozad">
                @foreach ($payroll_employees as $employee)
                <tr>
                    {{-- <td>
                        <div class="form-check">
                            <input class="form-check-input flexCheckDefault" type="checkbox" value="{{ $employee->employee_id }}" name="employees[]"
                                id="flexCheckDefault{{ $employee->employee_id }}" />
                        </div>
                    </td> --}}
                    <td>
                        {{ $employee->username }}
                        <input type="hidden" value="{{ $employee->employee_id }}" name="employees[]"/>
                    </td>
                    <td>
                        {{ $employee->job_name }}
                        {{ '@'. $employee->department_name }}
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ $employee->basic_wage }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ $employee->total_allowances }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->total_deducts }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->total_penalties }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->total_advances }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->total_rewards }}
                            {{ $currency }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ $employee->net_wage }}
                            {{ $currency }}
                        </span>
                    </td>
                </tr>
                {{-- <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input flexCheckDefault" type="checkbox" value=""
                                id="flexCheckDefault{{ $employee->id }}" />
                        </div>
                    </td>
                    <td>{{ $employee->username }}</td>
                    <td>
                        {{ $employee->job->name }}
                        {{ '@'. $employee->department->name }}
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ $employee->salary->basic }}
                            SAR
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ $employee->salary->totalAllowance() }}
                            SAR
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->salary->totalDeduct() }}
                            SAR
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->totalPenalties() }}
                            SAR
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger text-white">
                            {{ $employee->totalAdvances() }}
                            SAR
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success ">
                            {{ ($employee->salary->basic + $employee->salary->totalAllowance()) -
                            ($employee->totalPenalties() + $employee->totalAdvances() +
                            $employee->salary->totalDeduct()) }}
                            SAR
                        </span>
                    </td>
                </tr> --}}
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
<script src="{{ asset('admin_assets') }}/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    $("#kt_datatable_zero_configuration").DataTable({
        "scrollY": "500px",
        "scrollCollapse": true,
        "scrollX": true,
        "paging": false,
        "dom": "<'table-responsive'tr>",
        'paginate': true,
        "fixedColumns": {
            left: 2
        }
    });

    $(document).ready(function() {
        $('#flexCheckDefault').on('change', function() {
            var els = $('.flexCheckDefault');
            $.each(els, function(index, el) {
                //check if not checked
                if(!$(el).prop('checked')) {
                    $(el).attr('checked', true);
                }else {
                    $(el).attr('checked', false);
                }
            });
        })
    })
</script>
@endpush
