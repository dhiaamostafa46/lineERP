<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="tax_accounts-table" >
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/tax_accounts.fields.id')</th>
                    <th>@lang('models/tax_accounts.fields.name')</th>
                    <th>@lang('models/tax_accounts.fields.rate')</th>
                    <th>@lang('models/tax_accounts.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxAccounts as $taxAccount)
                <tr>
                    <td>{{ $taxAccount->id }}</td>
                    <td>{{ $taxAccount->name }}</td>
                    <td>{{ $taxAccount->rate }}</td>
                    <td><span class="{{ $taxAccount->status_badge }}">{{ $taxAccount->status_text }}</span></td>


                    <td style="width: 120px">
                        {!! Form::open(['route' => ['taxaccounts.destroy', $taxAccount->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('taxaccounts.show')
                            <a href="{{ route('taxaccounts.show', [$taxAccount->id]) }}"
                                class='btn btn-icon btn-sm btn-primary btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('taxaccounts.edit')
                            <a href="{{ route('taxaccounts.edit', [$taxAccount->id]) }}"
                                class='btn btn-icon btn-sm btn-primary mx-1  btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            {{-- @can('taxaccounts.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                'type' => 'submit',
                                'class' => 'btn btn-icon btn-sm btn-primary   btn-xs',
                                'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endcan --}}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4 {{ $taxAccounts->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $taxAccounts])
        </div>
    </div>
</div>
