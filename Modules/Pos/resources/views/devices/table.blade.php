<div class="row g-5 p-5">
    @foreach ($devices as $device)
        <div class="col-md-4">
            <div class="card shadow-sm border border-gray-300 h-100 rounded-3 hover-elevate-up">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark fs-3">{{ $device->name }}</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">@lang('pos::models/devices.fields.id') #{{ $device->id }}</span>
                    </h3>
                    <div class="card-toolbar">
                        @if($device->is_active)
                            <span class="badge badge-light-success fw-bold fs-7 px-3 py-2"><i
                                    class="fa-solid fa-circle-check text-success me-1"></i>
                                @lang('pos::models/devices.fields.is_active')</span>
                        @else
                            <span class="badge badge-light-secondary fw-bold fs-7 px-3 py-2"><i
                                    class="fa-solid fa-circle-xmark text-secondary me-1"></i> @lang('pos::models/devices.inactive')</span>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-3 pb-4">
                    <div class="d-flex flex-column gap-4 mb-2">
                        <div class="d-flex align-items-center bg-light-primary rounded p-3">
                            <i class="fa-solid fa-code-branch text-primary me-4 fs-2"></i>
                            <div class="d-flex flex-column">
                                <span
                                    class="fw-bold text-gray-800 fs-6">@lang('pos::models/devices.fields.branch_id')</span>
                                <span
                                    class="text-gray-600 fs-7">{{ $device->branch ? $device->branch->name : '---' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-warning rounded p-3">
                            <i class="fa-solid fa-warehouse text-warning me-4 fs-2"></i>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-800 fs-6">@lang('pos::models/devices.fields.store_id')</span>
                                <span class="text-gray-600 fs-7">{{ $device->store ? $device->store->name : '---' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center border-0 pt-0 pb-5">
                    <a href="{{ route('pos.terminal', ['device' => $device->uuid]) }}"
                        class="btn btn-sm btn-success flex-grow-1 me-3 fw-bold" title="@lang('pos::models/devices.open_pos')">
                        <i class="fa-solid fa-cash-register"></i> @lang('pos::models/devices.open_pos')
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pos.devices.edit', [$device->id]) }}"
                            class="btn btn-sm btn-icon btn-light-primary" title="@lang('crud.edit')">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                       
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($devices->isEmpty())
    <div class="d-flex flex-column align-items-center justify-content-center p-15">
        <i class="fa-solid fa-desktop text-muted fs-5x mb-4"></i>
        <span class="text-muted fw-bold fs-3">@lang('pos::models/devices.no_devices')</span>
        <a href="{{ route('pos.devices.create') }}" class="btn btn-primary mt-4">
            <i class="fa-solid fa-plus"></i> @lang('crud.add_new')
        </a>
    </div>
@endif

<div class="d-flex justify-content-center p-5 border-top border-gray-200">
    @include('adminlte-templates::common.paginate', ['records' => $devices])
</div>