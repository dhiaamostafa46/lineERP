@php
    function sortLink($column, $title) {
        $currentSort = request('sort_by');
        $currentDir = request('sort_dir', 'desc');
        $isActive = ($currentSort === $column);
        $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
        
        $params = array_merge(request()->query(), [
            'sort_by' => $column,
            'sort_dir' => $nextDir
        ]);
        
        $url = request()->url() . '?' . http_build_query($params);
        
        $iconClass = 'fa-solid fa-sort text-muted opacity-40 ms-1';
        if ($isActive) {
            $iconClass = $currentDir === 'asc' ? 'fa-solid fa-sort-up text-primary ms-1' : 'fa-solid fa-sort-down text-primary ms-1';
        }
        
        return '<a href="' . e($url) . '" class="text-muted text-hover-primary d-inline-flex align-items-center text-decoration-none" wire:navigate>'
             . e($title) 
             . '<i class="' . $iconClass . '" style="font-size: 10px;"></i></a>';
    }
@endphp

<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-products-table">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input" type="checkbox" id="checkAllProducts" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2">{!! sortLink('name', 'NAME') !!}</th>
                <th>{!! sortLink('category_id', 'CATEGORY') !!}</th>
                <th>{!! sortLink('type', 'TYPE') !!}</th>
                <th>{!! sortLink('cost_price', 'COST PRICE') !!}</th>
                <th>{!! sortLink('prod_price', 'SALE PRICE') !!}</th>
                <th>{!! sortLink('status', 'STATUS') !!}</th>
                <th class="pe-4 text-end">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="product-row" data-id="{{ $product->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input product-check" type="checkbox" value="{{ $product->id }}" />
                        </div>
                    </td>

                    <!-- Product Avatar & Name & Barcode -->
                    <td class="ps-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-30px symbol-circle flex-shrink-0">
                                @if($product->imgThumbPath)
                                    <img src="{{ $product->imgThumbPath }}" class="rounded-circle object-fit-cover w-30px h-30px border" alt="{{ $product->name }}" />
                                @else
                                    <div class="symbol-label bg-soft-primary text-primary fw-bold fs-8 rounded-circle w-30px h-30px d-flex align-items-center justify-content-center">
                                        {{ mb_substr($product->name, 0, 1, 'utf-8') }}
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('basicdata.products.show', [$product->id]) }}" 
                                   class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7 mb-0" 
                                   wire:navigate>
                                    {{ $product->name }}
                                </a>
                                <span class="text-muted fs-8 font-monospace">{{ $product->barcode ?? '—' }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Category -->
                    <td>
                        <span class="text-gray-800 fw-medium fs-7">{{ $product->category->name ?? '—' }}</span>
                    </td>

                    <!-- Type -->
                    <td>
                        <span class="text-gray-600 fs-7">{{ $product->type_text }}</span>
                    </td>

                    <!-- Cost Price -->
                    <td>
                        <span class="text-gray-800 fw-semibold font-monospace fs-7">{{ number_format($product->cost_price, 2) }}</span>
                    </td>

                    <!-- Sale Price -->
                    <td>
                        <span class="text-primary fw-bold font-monospace fs-7">{{ number_format($product->prod_price, 2) }}</span>
                    </td>

                    <!-- Status (Front Dashboard Legend Indicator) -->
                    <td>
                        @if($product->status == 1 || strtolower($product->status_text) == 'active' || $product->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $product->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $product->status_text }}
                            </span>
                        @endif
                    </td>

                    <!-- Action Link / Dropdown -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                            @can('basicdata.products.edit')
                                <a href="{{ route('basicdata.products.edit', [$product->id]) }}" 
                                   class="btn btn-sm btn-white text-gray-700 py-1 px-2 border rounded-2 d-inline-flex align-items-center gap-1 text-hover-primary" 
                                   style="font-size: 12px; height: 28px;"
                                   wire:navigate>
                                    <i class="fa-solid fa-pen fs-9 text-muted"></i>
                                    <span>Edit</span>
                                </a>
                            @endcan

                            @can('basicdata.products.destroy')
                                {!! Form::open(['route' => ['basicdata.products.destroy', $product->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-trash text-danger fs-9"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-icon btn-white border rounded-2 w-28px h-28px',
                                        'title' => __('crud.delete'),
                                        'onclick' => "return confirm('هل أنت متأكد من الحذف؟')",
                                    ]) !!}
                                {!! Form::close() !!}
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="fa-solid fa-inbox fs-2tx mb-2 text-gray-300"></i>
                            <span class="fs-7 fw-semibold">No records found</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Front Card Footer: Count & Pagination -->
@if(method_exists($products, 'hasPages') && $products->hasPages())
    <div class="front-card-footer">
        <div class="fs-8 text-muted">
            Showing <span class="fw-bold text-gray-800">{{ $products->firstItem() ?? 0 }}</span> to <span class="fw-bold text-gray-800">{{ $products->lastItem() ?? 0 }}</span> of <span class="fw-bold text-gray-800">{{ $products->total() }}</span> entries
            <span id="selectedCountBadge" class="badge bg-primary text-white ms-2 d-none">0 محدد</span>
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $products])
        </div>
    </div>
@endif

<!-- Front Checkbox Logic Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initProductCheckboxes();
    });

    // Support for Livewire SPA navigation
    document.addEventListener('livewire:navigated', function() {
        initProductCheckboxes();
    });

    function initProductCheckboxes() {
        const checkAll = document.getElementById('checkAllProducts');
        const checkboxes = document.querySelectorAll('.product-check');
        const badge = document.getElementById('selectedCountBadge');

        if (!checkAll || checkboxes.length === 0) return;

        function updateSelection() {
            let selectedCount = 0;
            checkboxes.forEach(cb => {
                const tr = cb.closest('tr');
                if (cb.checked) {
                    selectedCount++;
                    if (tr) tr.classList.add('table-light');
                } else {
                    if (tr) tr.classList.remove('table-light');
                }
            });

            checkAll.checked = (selectedCount > 0 && selectedCount === checkboxes.length);
            checkAll.indeterminate = (selectedCount > 0 && selectedCount < checkboxes.length);

            if (badge) {
                if (selectedCount > 0) {
                    badge.textContent = selectedCount + ' محدد';
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
        }

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateSelection();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelection);
        });
    }
</script>
