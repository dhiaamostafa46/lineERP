<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-kitchens-table">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input" type="checkbox" id="check-all" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_kitchens.fields.name')" /></th>
                <th><x-table-sort column="barcode" :title="__('basicdata::models/db_kitchens.fields.barcode')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_kitchens.fields.status')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kitchens as $kitchen)
                <tr class="kitchen-row" data-id="{{ $kitchen->id }}">
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $kitchen->id }}" />
                        </div>
                    </td>

                    <td class="ps-2">
                        <a href="javascript:void(0)" 
                           x-on:click="$dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                           onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                           class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                            {{ $kitchen->name }}
                        </a>
                    </td>

                    <td>
                        <span class="text-gray-600 font-monospace fs-7">{{ $kitchen->barcode ?? '—' }}</span>
                    </td>

                    <td>
                        @if($kitchen->status == 1 || strtolower($kitchen->status_text) == 'active' || $kitchen->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $kitchen->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $kitchen->status_text }}
                            </span>
                        @endif
                    </td>

                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                            @can('basicdata.kitchens.edit')
                                <button type="button" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                                   class="btn btn-icon btn-sm btn-light-primary rounded-circle" 
                                   title="@lang('crud.edit')"
                                   data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-pen-to-square fs-8"></i>
                                </button>
                            @endcan

                            @can('basicdata.kitchens.destroy')
                                {!! Form::open(['route' => ['basicdata.kitchens.destroy', $kitchen->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-utensils fs-1 text-gray-300 mb-3 d-block"></i>
                        @lang('crud.no_data_found')
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3 border-top">
    <div class="text-muted fs-8">
        @if($kitchens->total() > 0)
            عرض {{ $kitchens->firstItem() }} إلى {{ $kitchens->lastItem() }} من إجمالي {{ $kitchens->total() }} سجل
        @endif
    </div>
    <div>
        {!! $kitchens->links() !!}
    </div>
</div>
