<div>
    @if (count($Places) > 0)
        <div class="row">
            @foreach ($Places as $Place)
                <div class="col-12 col-sm-6 col-lg-4 mb-6">
                    <div class="card">
                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                            <div class="symbol symbol-90px symbol-circle mb-5">
                                <img src="/admin_assets/media/logos/locat2.png" alt="image" />
                            </div>
                            <a href="#"
                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{ $Place->name }}</a>
                            <div class="fw-semibold text-gray-500 mb-6">{{ $Place->address }}</div>
                            <div class="d-flex flex-center flex-wrap text-center">

                                @if ($shiftEmployees->where('places_id', $Place->id)->isEmpty())
                                    <button type="button"
                                        class="attendance-btn border btn btn-light-primary border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3"
                                        data-type="1" data-place="{{ $Place->id }}">
                                        <div class="fs-6 fw-bold text-gray-700">
                                            @lang('hr::models/hr_attendances.presence')
                                        </div>
                                    </button>
                                @else
                                    <button type="button"
                                        class="attendance-btn border btn btn-light-warning border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3"
                                        data-type="2" data-place="{{ $Place->id }}">
                                        <div class="fs-6 fw-bold text-gray-700">
                                            @lang('hr::models/hr_attendances.checkout')
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>



        {{-- Attendance Log --}}
        <div class="card card-flush mt-2">
            <div class="card-header">
                <h3 class="fw-bold my-2">@lang('hr::models/hr_attendances.attendance_movement')</h3>
            </div>
            <div class="card-body pt-4">
                @if ($Attendance->count() > 0)
                    <div class="timeline timeline-border-dashed">
                        @foreach ($Attendance as $item)
                            <div class="timeline-item">
                                <div class="timeline-line"></div>
                                <div class="timeline-icon">
                                    <i class="ki-duotone ki-geolocation fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-4">
                                        <div class="me-md-5">
                                            <div class="fs-5 fw-bold text-gray-800 mb-2">
                                                {{ \Carbon\Carbon::parse($item->date)->locale('en')->translatedFormat('l, j F Y') }}
                                            </div>

                                            <div class="fs-7 fw-semibold text-muted">
                                                @lang('hr::models/hr_shift_types.fields.from'):
                                                <span
                                                    class="fw-bold text-gray-700">{{ $item->shift_from ? \Carbon\Carbon::parse($item->shift_from)->format('h:i A') : '—' }}</span>
                                                @lang('hr::models/hr_shift_types.fields.to'):
                                                <span
                                                    class="fw-bold text-gray-700">{{ $item->shift_to ? \Carbon\Carbon::parse($item->shift_to)->format('h:i A') : '—' }}</span>
                                            </div>
                                            <div class="fs-7 fw-semibold text-muted">
                                                @lang('hr::models/hr_attendances.Attendance_table.first_record'):
                                                <span
                                                    class="fw-bold text-gray-700">{{ $item->first_check_in ? \Carbon\Carbon::parse($item->first_check_in)->format('h:i A') : '—' }}</span>
                                                @lang('hr::models/hr_attendances.Attendance_table.last_record'):
                                                <span
                                                    class="fw-bold text-gray-700">{{ $item->last_check_out ? \Carbon\Carbon::parse($item->last_check_out)->format('h:i A') : '—' }}</span>
                                            </div>
                                            <div class="d-flex flex-wrap fs-7 fw-semibold text-gray-500 mt-1">
                                                <div class="me-3">
                                                    @lang('hr::models/hr_attendances.Attendance_table.earlyArrival'):
                                                    <span
                                                        class="fw-bold text-gray-700">{{ secondsToTime($item->early_arrival) ?? 'N/A' }}</span>
                                                </div>
                                                <div class="me-3">
                                                    @lang('hr::models/hr_attendances.Attendance_table.late'):
                                                    <span
                                                        class="fw-bold text-gray-700">{{ secondsToTime($item->delay) ?? 'N/A' }}</span>
                                                </div>
                                                <div class="me-3">
                                                    @lang('hr::models/hr_attendances.Attendance_table.departure'):
                                                    <span
                                                        class="fw-bold text-gray-700">{{ secondsToTime($item->early_leave ) ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    @lang('hr::models/hr_attendances.Attendance_table.overtime'):
                                                    <span
                                                        class="fw-bold text-gray-700">{{ secondsToTime($item->overtime ) ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('hr.empdashboard.justificationsEmployee') }}"
                                            class="btn btn-primary btn-sm d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                            @lang('hr::models/hr_justifications.singular')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-20">
                      <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">
                        <p class="fw-bold fs-4"></p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('admin_assets/media/illustrations/sigma-1/4.png') }}" alt="" class="mw-100 h-200px h-sm-300px mb-10">
                <h4 style="color: brown"></h4>
            </div>
        </div>
    @endif
</div>
