<div class="table-responsive border-0 m-0">
    <table class="table align-middle table-row-dashed fs-7 gy-3 gs-4 mb-0" id="db-products-table">
        <thead>
            <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0 border-bottom border-gray-200" style="background: #f8fafc;">
                <th class="ps-5 min-w-200px">@lang('basicdata::models/db_products.fields.name')</th>
                <th class="min-w-120px">@lang('basicdata::models/db_products.fields.category_id')</th>
                <th class="min-w-100px">@lang('basicdata::models/db_products.fields.type')</th>
                <th class="min-w-100px">@lang('basicdata::models/db_products.fields.cost_price')</th>
                <th class="min-w-100px">@lang('basicdata::models/db_products.fields.prod_price')</th>
                <th class="min-w-100px text-center">@lang('basicdata::models/db_products.fields.status')</th>
                <th class="pe-5 text-end min-w-100px">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 fw-semibold">
            @forelse ($products as $product)
                <tr class="transition-all hover-bg-light">
                    <!-- Product Info with Thumb -->
                    <td class="ps-5">
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-shrink-0 border rounded-1 bg-white d-flex align-items-center justify-content-center p-0" 
                                 style="width: 28px; height: 28px; min-width: 28px; overflow: hidden;">
                                <img src="{{ $product->imgThumbPath }}" 
                                     alt="{{ $product->name }}" 
                                     style="width: 28px !important; height: 28px !important; max-width: 28px !important; max-height: 28px !important; object-fit: cover !important;" />
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('basicdata.products.show', [$product->id]) }}" 
                                   class="text-gray-900 fw-bold text-hover-primary fs-7 mb-0 line-clamp-1" 
                                   wire:navigate>
                                    {{ $product->name }}
                                </a>
                                @if($product->barcode)
                                    <span class="text-muted fs-8 font-monospace">
                                        {{ $product->barcode }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Category -->
                    <td>
                        @if($product->category)
                            <span class="badge badge-light-info fw-semibold fs-8 px-2 py-1">
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-muted fs-8">—</span>
                        @endif
                    </td>

                    <!-- Type -->
                    <td>
                        <span class="badge {{ $product->type == 1 ? 'badge-light-primary' : 'badge-light-warning' }} fw-semibold fs-8 px-2 py-1">
                            {{ $product->type_text }}
                        </span>
                    </td>

                    <!-- Cost Price -->
                    <td>
                        <span class="text-gray-800 fw-bold font-monospace fs-7">
                            {{ number_format($product->cost_price, 2) }}
                        </span>
                    </td>

                    <!-- Sale Price -->
                    <td>
                        <span class="text-primary fw-bolder font-monospace fs-7">
                            {{ number_format($product->prod_price, 2) }}
                        </span>
                    </td>

                    <!-- Status -->
                    <td class="text-center">
                        <span class="badge {{ $product->status_badge }} fw-bold px-3 py-1 fs-8">
                            {{ $product->status_text }}
                        </span>
                    </td>

                    <!-- Action Buttons -->
                    <td class="pe-5 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                            @can('basicdata.products.show')
                                <a href="{{ route('basicdata.products.show', [$product->id]) }}"
                                   class="btn btn-icon btn-sm btn-light-primary w-30px h-30px rounded-2"
                                   title="@lang('crud.view')"
                                   wire:navigate>
                                    <i class="fa-solid fa-eye fs-8"></i>
                                </a>
                            @endcan

                            @can('basicdata.products.edit')
                                <a href="{{ route('basicdata.products.edit', [$product->id]) }}"
                                   class="btn btn-icon btn-sm btn-light-success w-30px h-30px rounded-2"
                                   title="@lang('crud.edit')"
                                   wire:navigate>
                                    <i class="fa-solid fa-pen-to-square fs-8"></i>
                                </a>
                            @endcan

                            @can('basicdata.products.destroy')
                                {!! Form::open(['route' => ['basicdata.products.destroy', $product->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-trash-can fs-8 text-danger"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-light-danger w-30px h-30px rounded-2',
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
                    <td colspan="7" class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="fa-solid fa-box-open fs-2tx mb-3 text-gray-300"></i>
                            <div class="fs-6 fw-semibold">لا توجد بيانات مسجلة</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Integrated Pagination Footer -->
@if(method_exists($products, 'hasPages') && $products->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between py-3 px-5 border-top bg-white flex-wrap gap-2">
        <div class="fs-8 text-muted fw-semibold">
            عرض <span class="fw-bold text-gray-800">{{ $products->firstItem() ?? 0 }}</span> إلى <span class="fw-bold text-gray-800">{{ $products->lastItem() ?? 0 }}</span> من أصل <span class="fw-bold text-gray-800">{{ $products->total() }}</span> سجل
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $products])
        </div>
    </div>
@endif
