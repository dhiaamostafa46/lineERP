<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-termination-contracts-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/modelVariable.fields.Termination Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Contract Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Worked Days')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($termination_contracts as $termination_contract)
                <tr>
                    <td>{{ $termination_contract->termination_id }}</td>
                    <td>{{ $termination_contract->contract_id }}</td>
                    <td>{{ $termination_contract->worked_days }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.termination-contracts.destroy', $termination_contract->id],
                        'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.termination-contracts.show', [$termination_contract->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.termination-contracts.edit', [$termination_contract->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $termination_contracts])
        </div>
    </div>
</div>