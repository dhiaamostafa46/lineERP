<div class="card-body p-0">
    <div class="table-responsive">
        @php
            $sortBy = request('sort_by');
            $sortOrder = request('sort_order', 'desc');
            $buildSortUrl = function($column) use ($sortBy, $sortOrder) {
                $order = ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_order' => $order]);
            };
            $getSortIcon = function($column) use ($sortBy, $sortOrder) {
                if ($sortBy !== $column) return '<i class="fas fa-sort text-muted ms-1" style="font-size: 10px;"></i>';
                return $sortOrder === 'asc' 
                    ? '<i class="fas fa-sort-up text-primary ms-1"></i>' 
                    : '<i class="fas fa-sort-down text-primary ms-1"></i>';
            };
        @endphp
        <table class="table table-striped text-center gy-7 gs-7" id="db-customers-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('name') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/inv_customers.fields.name') {!! $getSortIcon('name') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('phone') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/inv_customers.fields.phone') {!! $getSortIcon('phone') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('email') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/inv_customers.fields.email') {!! $getSortIcon('email') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('vat_number') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/inv_customers.fields.vat_number') {!! $getSortIcon('vat_number') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('status') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/inv_customers.fields.status') {!! $getSortIcon('status') !!}
                        </a>
                    </th>
                    <th class="text-center table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->vat_number }}</td>
                        <td>
                            <span class="badge {{ $customer->status_badge }}">{{ $customer->status_text }}</span>
                        </td>

                        <td style="width: 120px" class="table-action">
                            {!! Form::open(['route' => ['invoices.customers.destroy', $customer->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('invoices.customers.show', [$customer->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.customers.edit', [$customer->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('Are you sure?')",
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
            @include('adminlte-templates::common.paginate', ['records' => $customers])
        </div>
    </div>
</div>
