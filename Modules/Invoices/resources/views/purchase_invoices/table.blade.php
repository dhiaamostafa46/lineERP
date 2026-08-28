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
                            @lang('invoices::models/purchase_invoices.fields.invoice_number') {!! $getSortIcon('invoice_number') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('supplier_id') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_invoices.fields.supplier_id') {!! $getSortIcon('supplier_id') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('issue_date') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_invoices.fields.issue_date') {!! $getSortIcon('issue_date') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('total_inclusive_vat') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_invoices.fields.total_inclusive_vat') {!! $getSortIcon('total_inclusive_vat') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('status') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/purchase_invoices.fields.status') {!! $getSortIcon('status') !!}
                        </a>
                    </th>
                    <th colspan="3">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseInvoices as $purchaseInvoice)
                    <tr>

                        <td>{{ $purchaseInvoice->invoice_number }}</td>
                        <td>{{ $purchaseInvoice->supplier ? $purchaseInvoice->supplier->name : '---' }}</td>
                        <td>{{ $purchaseInvoice->issue_date ? \Carbon\Carbon::parse($purchaseInvoice->issue_date)->format('Y-m-d') : '---' }}
                        </td>
                        <td>{{ number_format($purchaseInvoice->total_inclusive_vat, 2) }}</td>
                        <td>

                            <span class="{{ $purchaseInvoice->status_badge }}">{{ $purchaseInvoice->status_text }}</span>

                        </td>


                        <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['invoices.purchase.destroy', $purchaseInvoice->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('invoices.purchase.show', [$purchaseInvoice->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if ($purchaseInvoice->status == \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_DRAFT)
                                    @can('invoices.purchase.edit')
                                        <a href="{{ route('invoices.purchase.edit', [$purchaseInvoice->id]) }}"
                                            class='btn btn-sm btn-primary float-right mx-1' title="تعديل المسودة">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                    @endcan
                                @else
                                    @if ($purchaseInvoice->status != \Modules\Invoices\App\Models\PurchaseInvoice::STATUS_RETURNED)
                                        @can('invoices.purchase_return.create')
                                            <a href="{{ route('invoices.purchase_return.create', ['parent_id' => $purchaseInvoice->id]) }}"
                                                class='btn btn-sm btn-primary float-right mx-1' title="إنشاء مرتجع">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @endif
                                {{-- @can('invoices.purchase.delete') --}}
                                    {{-- {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-danger float-right mx-1',
                                        'title' => __('crud.delete'),
                                        'onclick' => "return confirm('" . __('lang.are_you_sure') . "')",
                                    ]) !!} --}}
                                {{-- @endcan --}}
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
            @include('adminlte-templates::common.paginate', ['records' => $purchaseInvoices])
        </div>
    </div>
</div>
