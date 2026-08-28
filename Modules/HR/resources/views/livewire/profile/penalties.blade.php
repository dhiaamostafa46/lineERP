<div class="penalties-page">
    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-8">
        <div>
            <h1 class="page-title fw-bold text-gray-800 mb-2"> @lang('hr::models/hr_penalties.plural') </h1>
        </div>

        {{-- The create button might be removed if users can't create penalties for themselves --}}
    </div>

    @if ($dataInf->isEmpty())
        <!-- Empty State -->
        <div class="card card-flush">
            <div class="card-body text-center p-lg-20">
               <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">
            </div>
        </div>
    @else
        <!-- Cards Grid -->
        <div class="row g-6">
            @foreach ($dataInf as $item)
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-3" wire:key="penalty-{{ $item->id }}">
                    <div class="card card-flush h-100 position-relative advance-card">
                        <span class="badge position-absolute top-0 end-0 m-4 fs-7 {{ $item->status_Badge }}">
                            {{ $item->status_text }}
                        </span>

                        <div class="card-header pt-6 pb-4">
                            <div class="card-title">
                                <h4 class="fw-bold text-gray-800 mb-1 text-truncate" title="@lang('hr::models/hr_penalties.singular')">
                                    @lang('hr::models/hr_penalties.singular')
                                </h4>
                            </div>
                        </div>

                        <div class="card-body pt-0 pb-6 px-6">
                            <div class="d-flex align-items-center gap-2 text-gray-600 fw-semibold mb-4">
                                <i class="ki-duotone ki-wallet fs-4 text-danger">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                </i>
                                <span class="fs-5 fw-bolder">{{ number_format($item->amount, 2) }}</span>
                            </div>
                            <p class="text-gray-700 fw-semibold fs-6 mb-4 line-clamp-3" title="{{ $item->description ?? '—' }}">
                                {{ $item->description ?? '—' }}
                            </p>
                            <span class="text-gray-600 fw-semibold fs-7">@lang('hr::models/hr_penalties.fields.due_date'): {{ $item->due_date?->format('Y-m-d') ?? '—' }}</span>
                        </div>

                        <div class="card-footer border-0 pt-0 pb-6 px-6 d-flex justify-content-between align-items-center">
                            <span class="text-gray-600 fw-semibold fs-7">
                                <i class="ki-duotone ki-calendar-8 fs-5 me-1">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                </i>
                                {{ $item->created_at->format('Y-m-d') }}
                            </span>
                            <button class="btn btn-sm btn-icon btn-light-primary shadow-sm" wire:click="toggleOpenModal({{ $item->id }})">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-8">
            {{ $dataInf->links() }}
        </div>
    @endif

    <!-- Details Modal -->
    @if ($openModal && $model)
        <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">
                           @lang('hr::models/hr_penalties.singular')
                        </h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" wire:click="toggleOpenModal(0)">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div class="row g-8">
                            <!-- Left Column -->
                            <div class="col-lg-6">
                                <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_holidays.sections.employee_information')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.employee_id')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ $model->employee->username ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.amount')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ number_format($model->amount, 2) }} </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.status')</span>
                                        <span class="badge {{ $model->status_Badge }}">{{ $model->status_text ?? '-' }}</span>
                                    </div>

                                    <div class="d-flex flex-column gap-2">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.description')</span>
                                        <p class="fw-normal text-gray-800">{{ $model->description ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6">
                                <h5 class="mb-4 text-gray-800">@lang('hr::models/profileemployees.fields.system_info')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.due_date')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->due_date?->format('Y-m-d') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('crud.created_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('crud.updated_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->updated_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    @if($model->approved_id)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.approved_id')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->approver->username ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_penalties.fields.approved_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->approved_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .advance-card {
            transition: all .3s ease;
            border: 1px solid transparent;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .advance-card:hover {
            transform: translateY(-5px);
            border-color: var(--bs-primary);
            box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb), 0.15);
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
