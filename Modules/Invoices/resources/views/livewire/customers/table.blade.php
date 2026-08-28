<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="db-customers-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('invoices::models/inv_customers.fields.name')</th>
                    <th>@lang('invoices::models/inv_customers.fields.phone')</th>
                    <th>@lang('invoices::models/inv_customers.fields.email')</th>
                    <th>@lang('invoices::models/inv_customers.fields.vat_number')</th>
                    <th>@lang('invoices::models/inv_customers.fields.status')</th>
                    <th class="text-center table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->vat_number }}</td>
                        <td>
                            <span class="badge {{ $customer->status_badge }}">{{ $customer->status_text }}</span>
                        </td>
                        <td style="width: 120px" class="table-action">
                            <div class='btn-group'>
                                <a href="{{ route('customers.show', [$customer->id]) }}"
                                    class='btn btn-sm btn-icon btn-light-primary me-2'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <button type="button" wire:click="$dispatch('openEditModal', { id: {{ $customer->id }} })"
                                    class='btn btn-sm btn-icon btn-light-warning me-2'>
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button type="button" wire:confirm="@lang('messages.are_you_sure')" wire:click="delete({{ $customer->id }})"
                                    class="btn btn-sm btn-icon btn-light-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10">
                            <span class="text-muted">@lang('crud.no_records_found')</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            {{ $customers->links() }}
        </div>
    </div>
</div>
