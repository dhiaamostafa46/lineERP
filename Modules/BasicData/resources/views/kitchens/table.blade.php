<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-kitchens-table" data-table="bulk">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input bulk-check-all" type="checkbox" id="checkAllKitchens" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_kitchens.fields.name')" /></th>
                <th><x-table-sort column="barcode" :title="__('basicdata::models/db_kitchens.fields.barcode')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_kitchens.fields.status')" /></th>
                <th><x-table-sort column="created_at" :title="__('basicdata::models/db_kitchens.fields.created_at')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kitchens as $kitchen)
                <tr class="kitchen-row" data-id="{{ $kitchen->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input bulk-check kitchen-check" type="checkbox" value="{{ $kitchen->id }}" />
                        </div>
                    </td>

                    <!-- Name -->
                    <td class="ps-2">
                        <a href="javascript:void(0)" 
                           onclick="Livewire.dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                           class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                            {{ $kitchen->name }}
                        </a>
                    </td>

                    <!-- Barcode -->
                    <td>
                        <span class="badge bg-light-dark text-gray-700 font-monospace fs-8 px-2 py-1">{{ $kitchen->barcode ?? '—' }}</span>
                    </td>

                    <!-- Status (Front Legend Indicator) -->
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

                    <!-- Created At -->
                    <td>
                        <span class="text-muted fs-8 font-monospace">{{ $kitchen->created_at ? $kitchen->created_at->format('Y-m-d') : '—' }}</span>
                    </td>

                    <!-- Actions -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                            @can('basicdata.kitchens.edit')
                                <button type="button"
                                   onclick="Livewire.dispatch('openEditModal', { id: {{ $kitchen->id }} })"
                                   class="btn btn-sm btn-white text-gray-700 py-1 px-2 border rounded-2 d-inline-flex align-items-center gap-1 text-hover-primary" 
                                   style="font-size: 12px; height: 28px;">
                                    <i class="fa-solid fa-pen fs-9 text-muted"></i>
                                    <span>@lang('crud.edit')</span>
                                </button>
                            @endcan

                            @can('basicdata.kitchens.destroy')
                                {!! Form::open(['route' => ['basicdata.kitchens.destroy', $kitchen->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                    <td colspan="6" class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="fa-solid fa-utensils fs-2tx mb-2 text-gray-300"></i>
                            <span class="fs-7 fw-semibold">@lang('basicdata::lang.no_data')</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Footer -->
@if(method_exists($kitchens, 'hasPages') && $kitchens->hasPages())
    <div class="front-card-footer">
        <div class="fs-8 text-muted">
            @lang('crud.showing') <span class="fw-bold text-gray-800">{{ $kitchens->firstItem() ?? 0 }}</span> @lang('crud.to') <span class="fw-bold text-gray-800">{{ $kitchens->lastItem() ?? 0 }}</span> @lang('crud.of') <span class="fw-bold text-gray-800">{{ $kitchens->total() }}</span> @lang('crud.entries')
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $kitchens])
        </div>
    </div>
@endif
