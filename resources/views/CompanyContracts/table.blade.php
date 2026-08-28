<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="company-contracts-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/CompanyContracts.fields.id')</th>
                    <th>@lang('models/CompanyContracts.fields.company')</th>
                    <th>@lang('models/CompanyContracts.fields.company_pricing_type')</th>
                    <th>@lang('models/CompanyContracts.fields.company_pricing_value')</th>
                    <th>@lang('models/CompanyContracts.fields.driver_payment_type')</th>
                    <th>@lang('models/CompanyContracts.fields.settlement_cycle')</th>
                    <th>@lang('models/CompanyContracts.fields.start_date')</th>
                    <th>@lang('models/CompanyContracts.fields.end_date')</th>
                    <th>@lang('models/CompanyContracts.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($CompanyContracts as $CompanyContract)
                    <tr>
                        <td>{{ $CompanyContract->id }}</td>
                        <td>{{ optional($CompanyContract->company)->name ?? '—' }}</td>
                        <td>{{ \App\Models\CompanyContract::companyPricingTypes()[$CompanyContract->company_pricing_type] ?? $CompanyContract->company_pricing_type }}
                        </td>
                        <td>{{ $CompanyContract->company_pricing_value }}</td>
                        <td>{{ \App\Models\CompanyContract::driverPaymentTypes()[$CompanyContract->driver_payment_type] ?? $CompanyContract->driver_payment_type }}
                        </td>
                        <td>{{ \App\Models\CompanyContract::settlementCycles()[$CompanyContract->settlement_cycle] ?? $CompanyContract->settlement_cycle }}
                        </td>
                        <td>{{ optional($CompanyContract->start_date)->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ optional($CompanyContract->end_date)->format('Y-m-d') ?? '—' }}</td>
                        <td><span class="{{ $CompanyContract->status_badge }}">{{ $CompanyContract->status_text }}</span></td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['CompanyContracts.destroy', $CompanyContract->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                @can('CompanyContracts.show')
                                    <a href="{{ route('CompanyContracts.show', [$CompanyContract->id]) }}"
                                        class='btn btn-icon btn-sm btn-primary btn-xs'>
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                @endcan
                                @can('CompanyContracts.edit')
                                    <a href="{{ route('CompanyContracts.edit', [$CompanyContract->id]) }}"
                                        class='btn btn-icon btn-sm btn-primary mx-1  btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endcan
                                @can('CompanyContracts.destroy')
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-primary   btn-xs',
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
    <div class="card-footer clearfix py-4 {{ $CompanyContracts->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $CompanyContracts])
        </div>
    </div>
</div>
