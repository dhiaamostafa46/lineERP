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
        <table class="table table-striped text-center gy-7 gs-7" id="db-purchase-invoices-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('invoice_number') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_orders.fields.invoice_number') {!! $getSortIcon('invoice_number') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('supplier_id') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_orders.fields.supplier_id') {!! $getSortIcon('supplier_id') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('issue_date') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_orders.fields.issue_date') {!! $getSortIcon('issue_date') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('total_inclusive_vat') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_orders.fields.total_inclusive_vat') {!! $getSortIcon('total_inclusive_vat') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('status') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_orders.fields.status') {!! $getSortIcon('status') !!}
                        </a>
                    </th>
                    <th colspan="3">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrders as $purchaseOrder)
                    <tr>

                        <td>{{ $purchaseOrder->invoice_number }}</td>
                       
                        <td>{{ $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '---' }}</td>
                        <td>{{ $purchaseOrder->issue_date ? \Carbon\Carbon::parse($purchaseOrder->issue_date)->format('Y-m-d') : '---' }}
                        </td>
                        <td>{{ number_format($purchaseOrder->total_inclusive_vat, 2) }}</td>
                        <td>

                            <span class="{{ $purchaseOrder->status_badge }}">{{ $purchaseOrder->status_text }}</span>

                        </td>


                        <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['invoices.purchase_orders.destroy', $purchaseOrder->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('invoices.purchase_orders.show', [$purchaseOrder->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.purchase_orders.edit', [$purchaseOrder->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>

                               
                                @if($purchaseOrder->status  !== \Modules\Invoices\App\Models\PurchaseOrder::STATUS_COMPLETED)
                                <a href="{{ route('invoices.purchase_orders.convert', [$purchaseOrder->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1' title="تحويل لفاتورة شراء">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                                @endif
                                {{-- {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('Are you sure?')",
                                ]) !!} --}}
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
            @include('adminlte-templates::common.paginate', ['records' => $purchaseOrders])
        </div>
    </div>
</div>
