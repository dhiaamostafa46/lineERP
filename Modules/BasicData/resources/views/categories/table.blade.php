<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-categories-table" data-table="bulk">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input bulk-check-all" type="checkbox" id="checkAllCats" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_categories.fields.name')" /></th>
                <th>@lang('basicdata::models/db_categories.fields.parent_id')</th>
                <th>@lang('basicdata::models/db_categories.fields.img')</th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_categories.fields.status')" /></th>
                <th><x-table-sort column="created_at" :title="__('basicdata::models/db_categories.fields.created_at')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr class="category-row" data-id="{{ $category->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input bulk-check cat-check" type="checkbox" value="{{ $category->id }}" />
                        </div>
                    </td>

                    <!-- Name -->
                    <td class="ps-2">
                        <div class="d-flex align-items-center gap-2">
                            @if($category->parent_id)
                                <span class="text-muted ms-3">↳</span>
                            @endif
                            <a href="javascript:void(0)" 
                               x-on:click="$dispatch('openEditModal', { id: {{ $category->id }} })"
                               onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $category->id }} })"
                               class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                                {{ $category->name }}
                            </a>
                        </div>
                    </td>

                    <!-- Parent Category -->
                    <td>
                        @if($category->parent)
                            <span class="badge bg-light-info text-info fw-semibold fs-8 px-2 py-1">
                                {{ $category->parent->name }}
                            </span>
                        @else
                            <span class="text-muted fs-8">—</span>
                        @endif
                    </td>

                    <!-- Image -->
                    <td>
                        @if($category->imgThumbPath)
                            <img src="{{ $category->imgThumbPath }}" 
                                 class="rounded-circle object-fit-cover border" 
                                 style="width: 28px; height: 28px;" 
                                 alt="{{ $category->name }}" />
                        @else
                            <div class="symbol-label bg-soft-primary text-primary fw-bold fs-8 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 28px; height: 28px;">
                                {{ mb_substr($category->name, 0, 1, 'utf-8') }}
                            </div>
                        @endif
                    </td>

                    <!-- Status (Front Legend Indicator) -->
                    <td>
                        @if($category->status == 1 || strtolower($category->status_text) == 'active' || $category->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $category->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $category->status_text }}
                            </span>
                        @endif
                    </td>

                    <!-- Created At -->
                    <td>
                        <span class="text-muted fs-8 font-monospace">{{ $category->created_at ? $category->created_at->format('Y-m-d') : '—' }}</span>
                    </td>

                    <!-- Actions -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                            @can('basicdata.categories.edit')
                                <button type="button"
                                   x-on:click="$dispatch('openEditModal', { id: {{ $category->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $category->id }} })"
                                   class="btn btn-sm btn-white text-gray-700 py-1 px-2 border rounded-2 d-inline-flex align-items-center gap-1 text-hover-primary" 
                                   style="font-size: 12px; height: 28px;">
                                    <i class="fa-solid fa-pen fs-9 text-muted"></i>
                                    <span>@lang('crud.edit')</span>
                                </button>
                            @endcan

                            @can('basicdata.categories.destroy')
                                {!! Form::open(['route' => ['basicdata.categories.destroy', $category->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa-solid fa-trash text-danger fs-9"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-icon btn-white border rounded-2 w-28px h-28px',
                                        'title' => __('crud.delete'),
                                        'onclick' => "return confirm('" . __('basicdata::lang.are_you_sure') . "')",
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
                            <i class="fa-solid fa-folder-open fs-2tx mb-2 text-gray-300"></i>
                            <span class="fs-7 fw-semibold">@lang('basicdata::lang.no_data')</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Footer -->
@if(method_exists($categories, 'hasPages') && $categories->hasPages())
    <div class="front-card-footer">
        <div class="fs-8 text-muted">
            @lang('crud.showing') <span class="fw-bold text-gray-800">{{ $categories->firstItem() ?? 0 }}</span> @lang('crud.to') <span class="fw-bold text-gray-800">{{ $categories->lastItem() ?? 0 }}</span> @lang('crud.of') <span class="fw-bold text-gray-800">{{ $categories->total() }}</span> @lang('crud.entries')
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $categories])
        </div>
    </div>
@endif
