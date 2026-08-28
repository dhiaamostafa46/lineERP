<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-service-points-table" data-table="bulk">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input bulk-check-all" type="checkbox" id="checkAllSP" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_service_points.fields.name')" /></th>
                <th><x-table-sort column="code" :title="__('basicdata::models/db_service_points.fields.code')" /></th>
                <th><x-table-sort column="type" :title="__('basicdata::models/db_service_points.fields.type')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_service_points.fields.status')" /></th>
                <th><x-table-sort column="created_at" :title="__('basicdata::models/db_service_points.fields.created_at')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($servicePoints as $servicePoint)
                <tr class="sp-row" data-id="{{ $servicePoint->id }}">
                    <!-- Row Checkbox -->
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input bulk-check sp-check" type="checkbox" value="{{ $servicePoint->id }}" />
                        </div>
                    </td>

                    <!-- Name -->
                    <td class="ps-2">
                        <a href="javascript:void(0)" 
                           onclick="Livewire.dispatch('openEditModal', { id: {{ $servicePoint->id }} })"
                           class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                            {{ $servicePoint->name }}
                        </a>
                    </td>

                    <!-- Code -->
                    <td>
                        <span class="badge bg-light-dark text-gray-700 font-monospace fs-8 px-2 py-1">{{ $servicePoint->code ?? '—' }}</span>
                    </td>

                    <!-- Type -->
                    <td>
                        <span class="text-gray-700 fs-7">{{ $servicePoint->type_text }}</span>
                    </td>

                    <!-- Status (Front Legend Indicator) -->
                    <td>
                        @if($servicePoint->status == 1 || strtolower($servicePoint->status_text) == 'active' || $servicePoint->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $servicePoint->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $servicePoint->status_text }}
                            </span>
                        @endif
                    </td>

                    <!-- Created At -->
                    <td>
                        <span class="text-muted fs-8 font-monospace">{{ $servicePoint->created_at ? $servicePoint->created_at->format('Y-m-d') : '—' }}</span>
                    </td>

                    <!-- Actions -->
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2">
                            @can('basicdata.service_points.edit')
                                <button type="button"
                                   x-on:click="$dispatch('openEditModal', { id: {{ $servicePoint->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $servicePoint->id }} })"
                                   class="btn btn-sm btn-white text-gray-700 py-1 px-2 border rounded-2 d-inline-flex align-items-center gap-1 text-hover-primary" 
                                   style="font-size: 12px; height: 28px;">
                                    <i class="fa-solid fa-pen fs-9 text-muted"></i>
                                    <span>@lang('crud.edit')</span>
                                </button>
                            @endcan

                            @can('basicdata.service_points.destroy')
                                {!! Form::open(['route' => ['basicdata.service_points.destroy', $servicePoint->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                            <i class="fa-solid fa-desktop fs-2tx mb-2 text-gray-300"></i>
                            <span class="fs-7 fw-semibold">@lang('basicdata::lang.no_data')</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Footer -->
@if(method_exists($servicePoints, 'hasPages') && $servicePoints->hasPages())
    <div class="front-card-footer">
        <div class="fs-8 text-muted">
            @lang('crud.showing') <span class="fw-bold text-gray-800">{{ $servicePoints->firstItem() ?? 0 }}</span> @lang('crud.to') <span class="fw-bold text-gray-800">{{ $servicePoints->lastItem() ?? 0 }}</span> @lang('crud.of') <span class="fw-bold text-gray-800">{{ $servicePoints->total() }}</span> @lang('crud.entries')
        </div>
        <div>
            @include('adminlte-templates::common.paginate', ['records' => $servicePoints])
        </div>
    </div>
@endif
