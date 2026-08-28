<div class="col-12">

    <div class="card">
 <!---------------------Approve All _Regect ALL Button-------------------------------->
        <div class="card-header border-0 pt-6">

            <div class="card-title">
                @lang('hr::models/hr_payroll_employees.plural')
            </div>
            <div class="card-toolbar pull-right">
                @if ($hr_setting->payroll_id)
                <div class="btn-group">
                    <button class="btn btn-sm btn-light-primary" wire:click="approveAll()">
                        <i class="fa-solid fa-check"></i> @lang('crud.all')
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                            wire:target="approveAll">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                    <button class="btn btn-sm btn-light-danger" wire:click="rejectAll()">
                        <i class="fa-solid fa-x"></i> @lang('crud.all')
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                            wire:target="rejectAll">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
                @endif
            </div>
        </div>

      
 <!---------------------End Approve All _Regect ALL Button-------------------------------->
        <div class="card-body">
 <!---------------------Search Section-------------------------------->
            <div class="row">
                <div class="col-4">
                    <!--begin::Input group-->
                    <div class="input-group input-group-solid mb-5">
                        <input type="text" class="form-control" wire:model='search'
                            placeholder="@lang('hr::models/hr_payrolls.fields.username'), @lang('hr::models/hr_payrolls.fields.job'), @lang('hr::models/hr_payrolls.fields.department')"
                            aria-label="Username" />
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-4">
                    <div class="btn-group my-auto">
                        @if ($search)
                        <button type="button" class="btn btn-icon btn-danger my-auto" wire:click="clearSearch">
                            <i class="fa-solid fa-x"></i>
                        </button>
                        @endif
                        <button type="button" class="btn btn-icon btn-primary my-auto" wire:click="searching">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
 <!---------------------End Serach Section-------------------------------->
  <!--------------------Employees-------------------------------->
            <div class="table-responsive">
                <table class="table table-striped gy-7 gs-7" id="kt_datatable_zero_configuration">
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
                            <th>@lang('hr::models/hr_payrolls.fields.username')</th>
                            <th>
                                @lang('hr::models/hr_payrolls.fields.job')
                                @
                                @lang('hr::models/hr_payrolls.fields.department')</th>
                            </th>
                            <th>@lang('hr::models/hr_payrolls.fields.basic_salary')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.allowances')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.rewards')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.deductions')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.penalties')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.advances')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.total')</th>
                            <th>@lang('hr::models/hr_payrolls.fields.status')</th>
                            <th class="text-center">@lang('crud.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payroll_employees as $employee)
                        <tr>
                            <td>
                                {{ $employee->username }}
                            </td>
                            <td>
                                {{ $employee->job_name }}
                                {{ '@'. $employee->department_name }}
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $employee->basic_wage }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $employee->total_allowances }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $employee->total_rewards }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger text-white">
                                    {{ $employee->total_deducts }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger text-white">
                                    {{ $employee->total_penalties }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger text-white">
                                    {{ $employee->total_advances }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success ">
                                    {{ $employee->net_wage }}
                                    {{ $employee->currency }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $employee->status_badge }}">
                                    {{ $employee->status_text }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                    wire:click="show({{ $employee->id }})">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
  <!--------------------EndEmployees-------------------------------->
        </div>
        <div class="card-footer">
            {{ $payroll_employees->onEachSide(2)->links('vendor/livewire/bootstrap') }}
        </div>
    </div>
    @livewire('hr::payrolls.employees.show',[], key('payrolls_employees_show'))
</div>
