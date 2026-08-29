<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-products-table">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input" type="checkbox" id="check-all" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_products.fields.name')" /></th>
                <th><x-table-sort column="category_id" :title="__('basicdata::models/db_products.fields.category_id')" /></th>
                <th><x-table-sort column="type" :title="__('basicdata::models/db_products.fields.type')" /></th>
                <th><x-table-sort column="cost_price" :title="__('basicdata::models/db_products.fields.cost_price')" /></th>
                <th><x-table-sort column="prod_price" :title="__('basicdata::models/db_products.fields.prod_price')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_products.fields.status')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="product-row" data-id="{{ $product->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $product->id }}" />
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
                                <a href="javascript:void(0)" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $product->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $product->id }} })"
                                   class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7 mb-0">
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

                    <!-- Status -->
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

                    <!-- Action Link (Icon Only) -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                            @can('basicdata.products.edit')
                                <button type="button" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $product->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $product->id }} })"
                                   class="btn btn-icon btn-sm btn-light-primary rounded-circle" 
                                   title="@lang('crud.edit')"
                                   data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-pen-to-square fs-8"></i>
                                </button>
                            @endcan

                            @can('basicdata.products.destroy')
                                {!! Form::open(['route' => ['basicdata.products.destroy', $product->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    <button type="button" 
                                            class="btn btn-icon btn-sm btn-light-danger rounded-circle" 
                                            title="@lang('crud.delete')"
                                            data-bs-toggle="tooltip"
                                            onclick="confirmDelete(this.closest('form'))">
                                        <i class="fa-solid fa-trash-can fs-8"></i>
                                    </button>
                                {!! Form::close() !!}
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-box-open fs-1 text-gray-300 mb-3 d-block"></i>
                        @lang('crud.no_data_found')
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3 border-top">
    <div class="text-muted fs-8">
        @if($products->total() > 0)
            عرض {{ $products->firstItem() }} إلى {{ $products->lastItem() }} من إجمالي {{ $products->total() }} سجل
        @endif
    </div>
    <div>
        {!! $products->links() !!}
    </div>
</div>
