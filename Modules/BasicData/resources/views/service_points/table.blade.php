<div class="table-responsive">
    <table class="table front-table text-start align-middle" id="db-service-points-table">
        <thead>
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <div class="front-form-check">
                        <input class="form-check-input" type="checkbox" id="check-all" title="تحديد الكل" />
                    </div>
                </th>
                <th class="ps-2"><x-table-sort column="name" :title="__('basicdata::models/db_service_points.fields.name')" /></th>
                <th><x-table-sort column="code" :title="__('basicdata::models/db_service_points.fields.code')" /></th>
                <th><x-table-sort column="type" :title="__('basicdata::models/db_service_points.fields.type')" /></th>
                <th><x-table-sort column="status" :title="__('basicdata::models/db_service_points.fields.status')" /></th>
                <th class="pe-4 text-end">@lang('crud.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($servicePoints as $point)
                <tr class="point-row" data-id="{{ $point->id }}">
                    <td class="ps-4">
                        <div class="front-form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $point->id }}" />
                        </div>
                    </td>

                    <td class="ps-2">
                        <a href="javascript:void(0)" 
                           x-on:click="$dispatch('openEditModal', { id: {{ $point->id }} })"
                           onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $point->id }} })"
                           class="text-gray-900 fw-bold text-hover-primary text-decoration-none fs-7">
                            {{ $point->name }}
                        </a>
                    </td>

                    <td>
                        <span class="text-gray-600 font-monospace fs-7">{{ $point->code ?? '—' }}</span>
                    </td>

                    <td>
                        <span class="badge bg-light-primary text-primary fs-8">{{ $point->type_text }}</span>
                    </td>

                    <td>
                        @if($point->status == 1 || strtolower($point->status_text) == 'active' || $point->status_text == 'نشط')
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-success"></span>
                                {{ $point->status_text }}
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center fs-7 fw-medium text-gray-800">
                                <span class="front-legend-indicator bg-danger"></span>
                                {{ $point->status_text }}
                            </span>
                        @endif
                    </td>

                    <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-1">
                            @can('basicdata.service_points.edit')
                                <button type="button" 
                                   x-on:click="$dispatch('openEditModal', { id: {{ $point->id }} })"
                                   onclick="if(window.Livewire) Livewire.dispatch('openEditModal', { id: {{ $point->id }} })"
                                   class="btn btn-icon btn-sm btn-light-primary rounded-circle" 
                                   title="@lang('crud.edit')"
                                   data-bs-toggle="tooltip">
                                    <i class="fa-solid fa-pen-to-square fs-8"></i>
                                </button>
                            @endcan

                            @can('basicdata.service_points.destroy')
                                {!! Form::open(['route' => ['basicdata.service_points.destroy', $point->id], 'method' => 'delete', 'class' => 'd-inline']) !!}
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
                        <i class="fa-solid fa-location-dot fs-1 text-gray-300 mb-3 d-block"></i>
                        @lang('crud.no_data_found')
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3 border-top">
    <div class="text-muted fs-8">
        @if($servicePoints->total() > 0)
            عرض {{ $servicePoints->firstItem() }} إلى {{ $servicePoints->lastItem() }} من إجمالي {{ $servicePoints->total() }} سجل
        @endif
    </div>
    <div>
        {!! $servicePoints->links() !!}
    </div>
</div>
