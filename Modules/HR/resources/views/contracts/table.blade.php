<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-contracts-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_contracts.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_contracts.fields.type_id')</th>
                    <th>@lang('hr::models/hr_contracts.fields.contract_number')</th>
                    <th>@lang('hr::models/hr_contracts.fields.qiwa')</th>
                    <th>@lang('hr::models/hr_contracts.fields.start_at')</th>
                    <th>@lang('hr::models/hr_contracts.fields.end_at')</th>
                    <th>@lang('hr::models/hr_contracts.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contracts as $contract)
                    <tr>
                        <td>{{ $contract->employee->username }}</td>
                        <td>{{ $contract->type->name }}</td>
                        <td>{{ $contract->contract_number }}</td>
                        <td>{{ $contract->qiwa_text }}</td>
                        <td>{{ $contract->start_date->format('Y-m-d') }}</td>
                        <td>{{ optional($contract->end_date)->format('Y-m-d') }}</td>
                        <td><span class="{{ $contract->status_badge }}">{{ $contract->status_text }}</span></td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['hr.contracts.destroy', $contract->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                {{-- @can('hr.contracts.show')
                            <a href="{{ route('hr.contracts.show', [$contract->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan --}}
                                @can('hr.contracts.edit')
                                    <a href="{{ route('hr.contracts.edit', [$contract->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan
                                {{-- @can('hr.ContractItem.show')
                            <a href="{{ route('hr.ContractItem.show', [$contract->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa fa-list-ol"></i>
                            </a>
                            @endcan --}}
                                @can('hr.contracts.destroy')
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
            @include('adminlte-templates::common.paginate', ['records' => $contracts])
        </div>
    </div>
</div>
