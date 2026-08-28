<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.employee_id')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->employee->username ?? '' }}</span>
    </div>
</div>

<!-- Request Date Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.request_date')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->request_date->format('Y-m-d') }}</span>
    </div>
</div>

<!-- Attendance ID Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.shift_id')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">



            @php
                $shift = optional($justification->HrShift);
            @endphp

            @if ($shift->from && $shift->to)
                <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                        <strong>{{ \Carbon\Carbon::parse($shift->from)->format('h:i A') }}</strong>
                    </div>

                    <span style="font-weight: bold;">-</span>

                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                        <strong>{{ \Carbon\Carbon::parse($shift->to)->format('h:i A') }}</strong>
                    </div>
                </div>
            @else
                <span class="text-muted">—</span>
            @endif
        </span>
    </div>
</div>

<!-- Type Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.type')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->type_text }}</span>
    </div>
</div>

@if ($justification->type == Modules\HR\App\Models\HrJustification::TYPE_PERMISSION)


<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.from')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->from_time }}</span>
    </div>
</div>

<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.to')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->to_time }}</span>
    </div>
</div>

@endif

<!-- Reason Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.reason')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->reason }}</span>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_justifications.fields.status')
        </p>
    </div>

    <div class="col-8">
        <div class="form-control">
            @livewire('hr::trackers.get-status', ['model' => $justification], key('trackers_get_status_' . $justification->id))
        </div>
    </div>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('crud.created_at')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->created_at }}</span>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('crud.updated_at')
        </p>
    </div>

    <div class="col-8">
        <span class="form-control">{{ $justification->updated_at }}</span>
    </div>
</div>
<!-- Attachment Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.attachment')</h5>
    <hr>
</div>

<div class="col-sm-12 row">
    @if ($justification->attachment_original_path)
        <div class="col-12">
            @php
                $filePath = $justification->attachment_original_path;
                $fileName = basename($filePath);
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            @endphp


            <!-- File Information -->
            <div class="card mb-3">
                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <!-- File Icon -->
                            <i class="fas fa-file-{{ $fileExtension }} fa-2x text-primary me-3"></i>

                            <!-- File Details -->
                            <div>
                                <p><a href="{{ $justification->attachment_original_path }}" target="_blank"
                                        class="mb-1"><strong>{{ $fileName }}</strong></a></p>
                                <small class="text-muted">
                                    {{ $fileExtension ? strtoupper($fileExtension) . ' File' : 'Unknown Format' }}
                                </small>
                            </div>
                        </div>

                        <!-- Download Button -->
                        {{-- <a href="{{ route('download-attachment', ['path' => base64_encode($filePath)]) }}"
                           class="btn btn-sm btn-primary"
                           download>
                            <i class="fas fa-download me-2"></i>تحميل
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- File Preview (للملفات المدعومة) -->
            @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                <div class="card">

                    <div class="card-body text-center">
                        @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset($filePath) }}" alt="{{ $fileName }}" class="img-fluid"
                                style="max-height: 400px;">
                        @elseif(strtolower($fileExtension) === 'pdf')
                            <iframe src="{{ asset($filePath) }}#toolbar=0" width="100%" height="600px"
                                frameborder="0">
                            </iframe>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="col-8">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد مرفقات لهذا الطلب
            </div>
        </div>
    @endif
</div>
