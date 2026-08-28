<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-categories-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">

                    <th>@lang('store::models/st_opening_balances.fields.document_number')</th>
                    <th>@lang('store::models/st_opening_balances.fields.document_date')</th>
                    <th>@lang('store::models/st_opening_balances.fields.store_id')</th>
                    <th>@lang('store::models/st_opening_balances.fields.total_value')</th>
                    <th>@lang('store::models/st_opening_balances.fields.status')</th>
                    <th class="text-center min-w-100px table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($openingBalances as $openingBalance)
                    <tr>
                        <td>{{ $openingBalance->document_number }}</td>
                        <td>{{ $openingBalance->document_date->format('Y-m-d') }}</td>
                        <td>{{ $openingBalance->store->name ?? '' }}</td>
                        <td>{{ number_format($openingBalance->total_value, 2) }}</td>
                        <td>{{ $openingBalance->status_text }}</td>

                         <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['store.openingbalance.destroy', $openingBalance->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('store.openingbalance.show', [$openingBalance->id]) }}"
                                    class='btn btn-sm btn-primary float-right' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.openingbalance.print')
                                <a href="{{ route('store.openingbalance.show', [$openingBalance->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                @if($openingBalance->is_editable)
                                <a href="{{ route('store.openingbalance.edit', [$openingBalance->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                @endif
                                @if($openingBalance->is_deletable)
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-danger float-right',
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
            @include('adminlte-templates::common.paginate', ['records' => $openingBalances])
        </div>
    </div>
</div>
