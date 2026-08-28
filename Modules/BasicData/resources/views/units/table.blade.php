<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-units-table" data-table="bulk">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input bulk-check-all" type="checkbox" id="checkAllUnits" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_units.fields.name')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_units.fields.status')" /></th>
                <th><x-table-sort column="created_at" :title="__('basicdata::models/db_units.fields.created_at')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($units as $unit)
                <tr class="unit-row" data-id="{{ $unit->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input bulk-check unit-check" type="checkbox" value="{{ $unit->id }}" />
                        </div>
                    </td>

                    <!-- Name -->
                    <td class="ps-2">
                        <a href="javascript:void(0)" 
                           x-on:click="$dispatch('openEditModal', { id: {{ $unit->id }} })"
                           onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $unit->id }} })"
                           class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                            {{ $unit->name }}
                        </a>
                    </td>

                    <!-- Status (Front Legend Indicator) -->
                    <td>
                        @if($unit->status == 1 || strtolower($unit->status_text) == 'active' || $unit->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $unit->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $unit->status_text }}
                            </span>
                        @endif
                    </td>

                    <!-- Created At -->
                    <td>
                        <span class="text-muted fs-8 font-monospace">{{ $unit->created_at ? $unit->created_at->format('Y-m-d') : '—' }}</span>
                    </td>

                    <!-- Actions -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                            @can('basicdata.units.edit')
                                <button type="button" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $unit->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $unit->id }} })"
                                   class="btn btn-sm btn-white text-gray-700 py-1 px-2 border rounded-2 d-inline-flex align-items-center gap-1 text-hover-primary" 
                                   style="font-size: 12px; height: 28px;">
                                    <i class="fa-solid fa-pen fs-9 text-muted"></i>
                                    <span>@lang('crud.edit')</span>
                                </button>
                            @endcan

                            @can('basicdata.units.destroy')
                                {!! Form::open(['route' => ['basicdata.units.destroy', $unit->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                    <td colspan="5" class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="fa-solid fa-ruler fs-2tx mb-2 text-gray-300"></i>
                            <span class="fs-7 fw-semibold">@lang('basicdata::lang.no_data')</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Footer -->
@if(method_exists($units, 'hasPages') && $units->hasPages())
    <div class="front-card-footer">
        <div class="fs-8 text-muted">
            @lang('crud.showing') <span class="fw-bold text-gray-800">{{ $units->firstItem() ?? 0 }}</span> @lang('crud.to') <span class="fw-bold text-gray-800">{{ $units->lastItem() ?? 0 }}</span> @lang('crud.of') <span class="fw-bold text-gray-800">{{ $units->total() }}</span> @lang('crud.entries')
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $units])
        </div>
    </div>
@endif
