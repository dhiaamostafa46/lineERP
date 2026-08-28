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
        <table class="table table-striped text-center gy-7 gs-7" id="db-quotations-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('quotation_number') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/quotations.fields.quotation_number') {!! $getSortIcon('quotation_number') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('customer_id') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/quotations.fields.customer_id') {!! $getSortIcon('customer_id') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('issue_date') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/quotations.fields.issue_date') {!! $getSortIcon('issue_date') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('total_inclusive_vat') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/quotations.fields.total_inclusive_vat') {!! $getSortIcon('total_inclusive_vat') !!}
                        </a>
                    </th>
                    <th class="text-center">
                        <a href="{{ $buildSortUrl('status') }}" class="text-gray-800 text-hover-primary">
                            @lang('invoices::models/quotations.fields.status') {!! $getSortIcon('status') !!}
                        </a>
                    </th>
                    <th colspan="3">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotations as $quotation)
                    <tr>
                        <td>{{ $quotation->quotation_number }}</td>
                        <td>{{ $quotation->customer ? $quotation->customer->name : '---' }}</td>
                        <td>{{ $quotation->issue_date ? $quotation->issue_date->format('Y-m-d') : '---' }}</td>
                        <td>{{ number_format($quotation->total_inclusive_vat, 2) }}</td>
                        <td>
                            <span class="{{ $quotation->status_badge }}">{{ $quotation->status_text }}</span>
                        </td>
                        <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['invoices.quotations.destroy', $quotation->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('invoices.quotations.show', [$quotation->id]) }}"
                                    class='btn btn-sm btn-primary float-right'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                
                                @if (in_array($quotation->status, ['new', 'sent']))
                                  @can('invoices.quotations.edit')
                                    <a href="{{ route('invoices.quotations.edit', [$quotation->id]) }}"
                                        class='btn btn-sm btn-primary float-right mx-1'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                      @endcan
                                @endif

                                @if ($quotation->status != 'converted')
                                @can('invoices.quotations.convert')
                                    <a href="{{ route('invoices.quotations.convert', $quotation->id) }}"
                                        class='btn btn-sm btn-primary float-right mx-1' title="تحويل لفاتورة">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                 @endcan
                                @endif

                                @if (in_array($quotation->status, ['new', 'sent']))
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-primary float-right',
                                        'onclick' => "return confirm('هل أنت متأكد من الحذف؟')",
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
            @include('adminlte-templates::common.paginate', ['records' => $quotations])
        </div>
    </div>
</div>
