<div class="justifications-page">

    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-8">
        <div>
            <h1 class="page-title fw-bold text-gray-800 mb-2"> @lang('hr::models/hr_holidays.plural') </h1>
            {{-- TODO: Add this translation key --}}

        </div>

        <a href="{{ route('hr.my-requests.create') }}" class="btn btn-primary btn-lg d-flex align-items-center gap-2 shadow-sm">
            <i class="ki-duotone ki-plus fs-2"></i>
            @lang('crud.create')
        </a>
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
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-3" wire:key="justification-{{ $item->id }}">
                    <div class="card card-flush h-100 position-relative justification-card">
                        <span class="badge position-absolute top-0 end-0 m-4 fs-7 {{ $item->status_Badge }}">
                            {{ $item->status_text }}
                        </span>

                        <div class="card-header pt-6 pb-4">
                            <div class="card-title">
                                <h4 class="fw-bold text-gray-800 mb-1 text-truncate" title="{{ $item->type->name ?? '' }}">{{ $item->type->name ?? '' }}</h4>
                            </div>
                        </div>

                        <div class="card-body pt-0 pb-6 px-6">
                            <div class="d-flex align-items-center gap-2 text-gray-600 fw-semibold mb-4">
                                <i class="ki-duotone ki-calendar-8 fs-4">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                </i>
                                <span>{{ $item->from_at->format('d M Y') }} - {{ $item->end_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-gray-700 fw-semibold fs-6 mb-4 line-clamp-3" title="{{ $item->comments ?? '—' }}">
                                {{ $item->comments ?? '—' }}
                            </p>
                        </div>

                        <div class="card-footer border-0 pt-0 pb-6 px-6 d-flex justify-content-between align-items-center">
                            <span class="text-gray-600 fw-semibold fs-7">
                                <i class="ki-duotone ki-time fs-5 me-1">
                                    <span class="path1"></span><span class="path2"></span>
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
                           @lang('crud.detail')  @lang('hr::models/hr_holidays.singular')
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
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.employee_id')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ $model->employee->username ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.type_id')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ $model->type->name ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.status')</span>
                                        <span class="badge {{ $model->status_Badge }}">{{ $model->status_text ?? '-' }}</span>
                                    </div>
                                     <div class="d-flex flex-column gap-2">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.comments')</span>
                                        <p class="fw-normal text-gray-800">{{ $model->comments ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6">
                                <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_holidays.sections.dates')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.from_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->from_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_holidays.fields.end_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->end_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                </div>

                                <h5 class="mb-4 mt-8 text-gray-800">@lang('hr::models/hr_holidays.sections.system_information')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('crud.created_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('crud.updated_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->updated_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment Section -->
                        <div class="mt-8">
                            <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_holidays.sections.attachment')</h5>
                            @if ($model->attachment_original_path)
                                @php
                                    $filePath = $model->attachment_original_path;
                                    $fileName = basename($filePath);
                                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                                @endphp
                                <div class="card card-flush border">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ki-duotone ki-file-up fs-2x text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
                                        <div class="me-4 flex-grow-1">
                                            <a href="{{ asset($filePath) }}" target="_blank" class="text-gray-800 text-hover-primary fw-bold fs-6">{{ $fileName }}</a>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ strtoupper($ext) }} File</span>
                                        </div>
                                        <a href="{{ asset($filePath) }}" target="_blank" class="btn btn-sm btn-light-primary btn-icon"><i class="ki-duotone ki-arrow-down fs-2"><span class="path1"></span><span class="path2"></span></i></a>
                                    </div>
                                </div>

                                @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                                    <div class="card card-flush border mt-4">
                                        <div class="card-header">
                                            {{-- TODO: Add this translation key --}}
                                            <h3 class="card-title">معاينة المرفق</h3>
                                        </div>
                                        <div class="card-body text-center p-5">
                                            @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset($filePath) }}" class="img-fluid rounded" style="max-height:500px;">
                                            @elseif($ext === 'pdf')
                                                <iframe src="{{ asset($filePath) }}#toolbar=0" width="100%" height="600px" class="rounded border-0"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light-info d-flex align-items-center">
                                    <i class="ki-duotone ki-information-5 fs-2x text-info me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>

                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .justification-card {
            transition: all .3s ease;
            border: 1px solid transparent;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .justification-card:hover {
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
