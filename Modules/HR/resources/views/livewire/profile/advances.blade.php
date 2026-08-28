<div class="advances-page">

    <!-- Page Header -->
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4 mb-8">
        <div>
            <h1 class="page-title fw-bold text-gray-800 mb-2"> @lang('hr::models/hr_advances.plural') </h1>

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
                <div class="col-12 col-sm-6 col-xl-4 col-xxl-3" wire:key="advance-{{ $item->id }}">
                    <div class="card card-flush h-100 position-relative advance-card">
                        <span class="badge position-absolute top-0 end-0 m-4 fs-7 {{ $item->status_Badge }}">
                            {{ $item->status_text }}
                        </span>

                        <div class="card-header pt-6 pb-4">
                            <div class="card-title">
                                <h4 class="fw-bold text-gray-800 mb-1 text-truncate" title="@lang('hr::models/hr_advances.singular')">
                                    @lang('hr::models/hr_advances.singular')
                                </h4>
                            </div>
                        </div>

                        <div class="card-body pt-0 pb-6 px-6">
                            <div class="d-flex align-items-center gap-2 text-gray-600 fw-semibold mb-4">
                                <i class="ki-duotone ki-wallet fs-4 text-success">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                </i>
                                <span class="fs-5 fw-bolder">{{ number_format($item->amount, 2) }}</span>
                            </div>
                            <p class="text-gray-700 fw-semibold fs-6 mb-4 line-clamp-3" title="{{ $item->reason ?? '—' }}">
                                {{ $item->reason ?? '—' }}
                            </p>

                            @if($item->status == 2)
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between w-100 fs-7 fw-semibold text-gray-600 mb-2">
                                        <span>@lang('hr::models/hr_advances.paid')</span>
                                        <span>{{ number_format($item->paid_amount, 2) }} / {{ number_format($item->amount, 2) }}</span>
                                    </div>
                                    <div class="progress h-6px w-100">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item->paid_percentage }}%;" aria-valuenow="{{ $item->paid_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endif


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
                           @lang('hr::models/hr_advances.advance_details')
                        </h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" wire:click="toggleOpenModal(0)">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div class="row g-8">
                            <!-- Left Column -->
                            <div class="col-lg-6">
                                <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_advances.fields.advance_details')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.employee_id')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ $model->employee->username ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.amount')</span>
                                        <span class="fw-bold text-gray-800 text-end">{{ number_format($model->amount, 2) }} </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.status')</span>
                                        <span class="badge {{ $model->status_Badge }}">{{ $model->status_text ?? '-' }}</span>
                                    </div>
                                     <div class="d-flex flex-column gap-2">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.reason')</span>
                                        <p class="fw-normal text-gray-800">{{ $model->reason ?? '-' }}</p>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.description')</span>
                                        <p class="fw-normal text-gray-800">{{ $model->description ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6">
                                <h5 class="mb-4 text-gray-800">@lang('hr::models/profileemployees.fields.system_info')</h5>
                                <div class="d-flex flex-column gap-6">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.from_date')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->from_date?->format('Y-m-d') ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.to_date')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->to_date?->format('Y-m-d') ?? '-' }}</span>
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
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.approved_id')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->approver->username ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">@lang('hr::models/hr_advances.fields.approved_at')</span>
                                        <span class="fw-bold text-gray-800">{{ $model->approved_at?->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Payments Section -->
                        @if($model->monthlyPayments->isNotEmpty())
                        <div class="mt-8">
                            <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_advances.installment_details')</h5>
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 gy-5">
                                    <thead>
                                        <tr class="fw-bold fs-6 text-gray-800">
                                            <th>@lang('hr::models/hr_monthly_payments.fields.due_at')</th>
                                            <th>@lang('hr::models/hr_monthly_payments.fields.amount')</th>
                                            <th>@lang('hr::models/hr_monthly_payments.fields.status')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($model->monthlyPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->due_at->format('Y-m') }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>

                                                    <span class="{{ $payment->types_badge }}">{{ $payment->types_text }}</span>


                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="mt-8">
                             <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_advances.no_monthly_payments')</h5>
                        </div>
                        @endif
                        <!-- Attachment Section -->
                        <div class="mt-8">
                            <h5 class="mb-4 text-gray-800">@lang('hr::models/hr_advances.fields.attachment')</h5>
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
                                            <h3 class="card-title">@lang('hr::models/profileemployees.fields.attachment_preview')</h3>
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
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">@lang('hr::models/profileemployees.fields.no_attachment')</span>
                                    </div>
                                </div>
                            @endif
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
