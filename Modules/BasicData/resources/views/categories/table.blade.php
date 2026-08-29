<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-categories-table">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input" type="checkbox" id="check-all" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_categories.fields.name')" /></th>
                <th><x-table-sort column="parent_id" :title="__('basicdata::models/db_categories.fields.parent_id')" /></th>
                <th><x-table-sort column="sort" :title="__('basicdata::models/db_categories.fields.sort')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_categories.fields.status')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr class="category-row" data-id="{{ $category->id }}">
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $category->id }}" />
                        </div>
                    </td>

                    <td class="ps-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-30px symbol-circle flex-shrink-0">
                                @if($category->imgThumbPath)
                                    <img src="{{ $category->imgThumbPath }}" class="rounded-circle object-fit-cover w-30px h-30px border" alt="{{ $category->name }}" />
                                @else
                                    <div class="symbol-label bg-soft-primary text-primary fw-bold fs-8 rounded-circle w-30px h-30px d-flex align-items-center justify-content-center">
                                        {{ mb_substr($category->name, 0, 1, 'utf-8') }}
                                    </div>
                                @endif
                            </div>
                            <a href="javascript:void(0)" 
                               x-on:click="$dispatch('openEditModal', { id: {{ $category->id }} })"
                               onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $category->id }} })"
                               class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                                {{ $category->name }}
                            </a>
                        </div>
                    </td>

                    <td>
                        <span class="text-gray-700 fs-7">{{ $category->parent->name ?? '—' }}</span>
                    </td>

                    <td>
                        <span class="text-gray-600 font-monospace fs-7">{{ $category->sort ?? 0 }}</span>
                    </td>

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

                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                            @can('basicdata.categories.edit')
                                <button type="button" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $category->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $category->id }} })"
                                   class="btn btn-icon btn-sm btn-light-primary rounded-circle" 
                                   title="@lang('crud.edit')"
                                   data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-pen-to-square fs-8"></i>
                                </button>
                            @endcan

                            @can('basicdata.categories.destroy')
                                {!! Form::open(['route' => ['basicdata.categories.destroy', $category->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-folder-open fs-1 text-gray-300 mb-3 d-block"></i>
                        @lang('crud.no_data_found')
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3 border-top">
    <div class="text-muted fs-8">
        @if($categories->total() > 0)
            عرض {{ $categories->firstItem() }} إلى {{ $categories->lastItem() }} من إجمالي {{ $categories->total() }} سجل
        @endif
    </div>
    <div>
        {!! $categories->links() !!}
    </div>
</div>
