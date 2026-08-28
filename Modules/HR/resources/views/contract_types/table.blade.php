<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-contract-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_contract_types.fields.name')</th>
                    <th>@lang('hr::models/hr_contract_types.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contract_types as $contract_type)
                <tr>
                    <td>{{ $contract_type->name }}</td>
                    <td><span class="{{ $contract_type->status_badge }}">{{ $contract_type->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.contract_types.destroy', $contract_type->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            @can('hr.contract_types.show')
                            {{-- <a href="{{ route('hr.contract_types.show', [$contract_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @endcan
                            @can('hr.contract_types.edit')
                            <a href="{{ route('hr.contract_types.edit', [$contract_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.contract_types.destroy')
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
            @include('adminlte-templates::common.paginate', ['records' => $contract_types])
        </div>
    </div>
</div>
