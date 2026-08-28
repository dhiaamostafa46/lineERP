<!-- Employee Information Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.employee_information')</h5>
    <hr>
</div>

<!-- Employee Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.employee_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->employee->username ?? '-' }}</b>
    </div>
</div>

<!-- Status Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            @livewire('hr::trackers.get-status', ['model' => $holiday], key('trackers_get_status'))
        </b>
    </div>
</div>

<!-- Approver Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::lang.approver')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->approver->name ?? '-' }}</b>
    </div>
</div>

<!-- Comments Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.comments')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->comments ?? '-' }}</b>
    </div>
</div>



<!-- Holiday Details Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.holiday_details')</h5>
    <hr>
</div>

<!-- Type Id Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.type_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->type->name ?? '-' }}</b>
    </div>
</div>

<!-- Required Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.required_days')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control" id="required_days">
            @php
                $fromDate = $holiday->from_at;
                $toDate = $holiday->end_at;
                $requiredDays = 0;

                $employee = optional($holiday)->employee;
                $workingDays = $employee ? $employee->shift->work_days : null;

                if (!empty($workingDays) && is_array($workingDays)) {
                    $period = \Carbon\CarbonPeriod::create($fromDate, $toDate);
                    foreach ($period as $date) {
                        if (in_array(strtolower($date->format('l')), $workingDays)) {
                            $requiredDays++;
                        }
                    }
                } else {
                    // Fallback if no shift is defined for the employee
                    $requiredDays = $toDate->diffInDays($fromDate) + 1;
                }
            @endphp
            {{ $requiredDays }}
        </b>
    </div>
</div>

<!-- Allowed Days Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.allowed')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $balance['allowed'] ?? 0 }}</b>
    </div>
</div>

<!-- Annual Balance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.annual_balance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $balance['annual_balance'] ?? 0 }}</b>
    </div>
</div>

<!-- Used Balance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.used_balance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $balance['balance'] ?? 0 }}</b>
    </div>
</div>

<!-- Remaining Balance Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.remaining_balance')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            @php
                $remainingBalance = ($balance['annual_balance'] ?? 0) - ($balance['balance'] ?? 0);
            @endphp
            {{ $remainingBalance }}
        </b>
    </div>
</div>



<!-- Dates Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.dates')</h5>
    <hr>
</div>

<!-- From At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.from_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->from_at->format('Y-m-d H:i') }}</b>
    </div>
</div>

<!-- End At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.end_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->end_at->format('Y-m-d H:i') }}</b>
    </div>
</div>



<!-- System Information Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.system_information')</h5>
    <hr>
</div>

<!-- Created At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.created_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->created_at->format('Y-m-d H:i') }}</b>
    </div>
</div>

<!-- Updated At Field -->
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">
            @lang('hr::models/hr_holidays.fields.updated_at')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $holiday->updated_at->format('Y-m-d H:i') }}</b>
    </div>
</div>




<!-- Attachment Section -->
<div class="col-sm-12">
    <h5 class="mb-3 mt-3">@lang('hr::models/hr_holidays.sections.attachment')</h5>
    <hr>
</div>

<div class="col-sm-12 row">
    @if($holiday->attachment_original_path)
        <div class="col-12">
            @php
                $filePath = $holiday->attachment_original_path;
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
                               <p><a href="{{  $holiday->attachment_original_path   }}" target="_blank" class="mb-1"><strong>{{ $fileName }}</strong></a></p>
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
            @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                <div class="card">

                    <div class="card-body text-center">
                        @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))

                            <img src="{{ asset( $filePath) }}"
                                 alt="{{ $fileName }}"
                                 class="img-fluid"
                                 style="max-height: 400px;">
                        @elseif(strtolower($fileExtension) === 'pdf')
                            <iframe src="{{ asset($filePath) }}#toolbar=0"
                                    width="100%"
                                    height="600px"
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




@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeDatePickers();
        });

        function initializeDatePickers() {
            const requestDateEl = document.getElementById('request_date');
            const fromAtEl = document.getElementById('from_at');
            const endAtEl = document.getElementById('end_at');

            if (requestDateEl) {
                requestDateEl.flatpickr({
                    dateFormat: "Y-m-d",
                });
            }

            if (fromAtEl) {
                fromAtEl.flatpickr({
                    dateFormat: "Y-m-d",
                    onChange: calculateDays,
                });
            }

            if (endAtEl) {
                endAtEl.flatpickr({
                    dateFormat: "Y-m-d",
                    onChange: calculateDays,
                });
            }
        }

        function calculateDays() {
            const fromAtEl = document.getElementById('from_at');
            const endAtEl = document.getElementById('end_at');
            const requiredDaysEl = document.getElementById('required_days');

            if (!fromAtEl || !endAtEl || !requiredDaysEl) return;

            const fromDate = fromAtEl.value;
            const toDate = endAtEl.value;

            if (fromDate && toDate) {
                const start = new Date(fromDate);
                const end = new Date(toDate);

                if (end >= start) {
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    requiredDaysEl.textContent = diffDays;
                } else {
                    requiredDaysEl.textContent = 0;
                }
            }
        }
    </script>
@endpush
