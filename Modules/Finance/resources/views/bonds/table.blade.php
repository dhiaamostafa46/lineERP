<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="db-bonds-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('finance::models/fnc_bond.fields.voucher_number')</th>
                    <th>@lang('finance::models/fnc_bond.fields.bond_type')</th>
                    <th>@lang('finance::models/fnc_bond.fields.fund_account_id')</th>
                    <th>@lang('finance::models/fnc_bond.fields.contact_account_id')</th>
                    <th>@lang('finance::models/fnc_bond.fields.amount')</th>
                    <th>@lang('finance::models/fnc_bond.fields.date')</th>
                    <th>@lang('finance::models/fnc_bond.fields.status')</th>
                    <th class="text-center table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bonds as $bond)
                    <tr>
                        <td>{{ $bond->voucher_number }}</td>
                        <td>{{ $bond->type_text }}</td>
                        <td>{{ $bond->fundAccount->name ?? $bond->fundAccount->name_ar ?? '' }}</td>
                        <td>{{ $bond->contactAccount->name ?? $bond->contactAccount->name_ar ?? '' }}</td>
                        <td>{{ number_format($bond->amount, 2) }}</td>
                        <td>{{ $bond->date ? $bond->date->format('Y-m-d') : '' }}</td>
                        <td>
                            
                            <span> {{ $bond->status_text }}</span>
                        </td>
                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['fnc.bonds.destroy', $bond->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('fnc.bonds.show', [$bond->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('fnc.bonds.edit', [$bond->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('".__('crud.are_you_sure')."')",
                                ]) !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $bonds])
        </div>
    </div>
</div>
