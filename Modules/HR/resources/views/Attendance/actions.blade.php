@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        <h1>@lang('hr::models/hr_attendances.attendance_actions')</h1>
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('hr::models/hr_attendances.attendance_actions')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>

        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container">

                <div class="modal fade" tabindex="-1" id="kt_modal_1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row mb-5">
                                    <div class="form-group col-md-8 col-sm-12 my-auto">
                                        <h2>@lang('hr::models/hr_employees.fields.template')</h2>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <a href="{{ asset('uploads/files/AttendanceDemo.xlsx') }}"
                                            class="btn btn-sm btn-primary" download>
                                            <i class="fa-solid fa-file"></i>
                                            @lang('crud.downloadSample')
                                        </a>
                                    </div>
                                </div>
                                <hr class="mb-5">
                                {!! Form::open(['route' => 'hr.attendance.import', 'class' => 'row', 'files' => true]) !!}
                                <div class="form-group col-sm-8">
                                    {!! Form::label('file', __('hr::models/hr_employees.fields.file') . ':') !!}
                                    {!! Form::file('file', null, ['class' => 'form-control d-none']) !!}
                                </div>
                                <div class="form-group col-sm-4">
                                    {!! Form::button(__('crud.import'), [
                                        'type' => 'submit',
                                        'class' => 'btn btn-sm btn-success',
                                    ]) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                @if (true)
                    <div class="card shadow-sm my-3 no-print">
                        <div class="card-header collapsible cursor-pointer rotate {{ request()->has('pagination') ? 'active' : 'collapsed' }}"
                            data-bs-toggle="collapse" data-bs-target="#kt_docs_card_collapsible"
                            aria-expanded="{{ request()->has('pagination') ? 'true' : 'false' }}">
                            <h3 class="card-title">
                                <i class="fa-solid fa-filter fs-2 me-2"></i>
                                @lang('crud.search')
                            </h3>
                            <div class="card-toolbar rotate-180">
                                <i class="ki-duotone ki-down fs-1"></i>
                            </div>
                        </div>
                        <div id="kt_docs_card_collapsible"
                            class="collapse {{ request()->has('pagination') ? 'show' : '' }}">
                            {!! Form::open(['route' => 'hr.attendance.actions', 'method' => 'GET']) !!}
                            <div class="card-body">
                                <div class="row">

                                    <!-- Employee Id Field -->
                                    <div class="form-group col-sm-4 mb-3">
                                        {!! Form::label('employee_id', __('hr::models/hr_custodies.fields.employee_id') . ':') !!}
                                        <x-select2-input name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                                            :selected_id="request('employee_id')">
                                        </x-select2-input>
                                    </div>

                                    <!-- Start Date Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('start_date', __('Start Date') . ':') !!}
                                        {!! Form::date('start_date', request('start_date', now()->startOfMonth()->format('Y-m-d')), [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>

                                    <!-- End Date Field -->
                                    <div class="form-group col-sm-4 mb-4">
                                        {!! Form::label('end_date', __('End Date') . ':') !!}
                                        {!! Form::date('end_date', request('end_date', now()->endOfMonth()->format('Y-m-d')), [
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-4">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    @lang('crud.search')
                                </button>
                                <a class="btn btn-sm btn-danger float-right" href="{{ route('hr.report_types.index') }}">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    @lang('crud.reset')
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <div class="d-flex flex-wrap justify-content-end mb-3 px-4">
                                <div class="legend-item mx-2 d-flex align-items-center">
                                    <span style="display: inline-block; width: 15px; height: 15px; background-color: #E42312FF; border: 1px solid #ccc; vertical-align: middle;"></span>
                                    <span style="vertical-align: middle; margin-left: 5px;">غائب (Absent)</span>
                                </div>
                                <div class="legend-item mx-2 d-flex align-items-center">
                                    <span style="display: inline-block; width: 15px; height: 15px; background-color: yellow; border: 1px solid #ccc; vertical-align: middle;"></span>
                                    <span style="vertical-align: middle; margin-left: 5px;">إجازة / عطلة (Leave/Holiday)</span>
                                </div>
                                <div class="legend-item mx-2 d-flex align-items-center">
                                    <span style="display: inline-block; width: 15px; height: 15px; background-color: #12E42EFF; border: 1px solid #ccc; vertical-align: middle;"></span>
                                    <span style="vertical-align: middle; margin-left: 5px;">معفى (Exempt) - justification تسوية </span>
                                </div>
                                <div class="legend-item mx-2 d-flex align-items-center">
                                    <span style="display: inline-block; width: 15px; height: 15px; background-color: red; border: 1px solid #ccc; vertical-align: middle;"></span>
                                    <span style="vertical-align: middle; margin-left: 5px;">بصمة ناقصة (Missed Punch)</span>
                                </div>
                            </div>

                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.employee_name') </th>
                                        <th colspan="2">@lang('hr::models/hr_report_types.AttendanceRecords_table.date') </th>
                                        <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.work_hours')</th>
                                        <th> @lang('hr::models/hr_report_types.fields.status') </th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.location') </th>
                                        <th> @lang('hr::models/hr_report_types.fields.status') </th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.work_period') </th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.fingerprint') </th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.earlyArrival') </th>
                                        <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.late')</th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.early_departure')</th>
                                        <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.overtime') </th>
                                        <th>@lang('crud.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($SummaryAttendance as $attendanceRecord)
                                        @php
                                            // Check if penalty action should be available
                                            $canApplyPenalty = in_array($attendanceRecord->type, [1, 2]); // 1=Absent, 2=Late
                                            $hasDelay = false;
                                            if (count($attendanceRecord->timeTrackDetails) > 0) {
                                                foreach ($attendanceRecord->timeTrackDetails as $detail) {
                                                    if ($detail->delay > 0) {
                                                        $hasDelay = true;
                                                        break;
                                                    }
                                                }
                                            }
                                            $canApplyPenalty = $canApplyPenalty || $hasDelay;
                                        @endphp

                                        @if (count($attendanceRecord->timeTrackDetails) > 0)
                                            @foreach ($attendanceRecord->timeTrackDetails as $index => $item)
                                                @php
                                                    $color = '';
                                                    if ($item->type == 1 && $attendanceRecord->type == 2) {
                                                        $color = 'red';
                                                    } elseif ($item->type == 6) {
                                                        $color = '#12E42EFF';
                                                    }
                                                @endphp

                                                <tr @if ($attendanceRecord->type == 1) style="background-color: #E42312FF;"
                                                    @elseif ($attendanceRecord->type == 4) style="background-color: yellow;"
                                                    @elseif ($attendanceRecord->type == 5) style="background-color: #12E42EFF;" @endif>

                                                    @if ($loop->first)
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            {{ $attendanceRecord->employee->username ?? 'n/l' }}
                                                        </td>
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            {{ $attendanceRecord->date }}
                                                        </td>
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            {{ \Carbon\Carbon::parse($attendanceRecord->date)->locale('en')->translatedFormat('l') }}
                                                        </td>
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            {{ $attendanceRecord->hour }}
                                                        </td>
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            {{ $attendanceRecord->type_text }}
                                                        </td>
                                                    @endif

                                                    <td style="background: {{ $color }}">{{ $item->address }}</td>
                                                    <td style="background: {{ $color }}">{{ $item->type_text }}</td>
                                                    <td style="background: {{ $color }}">
                                                        <div style="display: flex; align-items: center;">
                                                            <div>
                                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                                <strong>{{ \Carbon\Carbon::parse($item->shift_from)->format('h:i A') }}</strong>
                                                            </div>
                                                            <span style="font-weight: bold;">-</span>
                                                            <div>
                                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                                <strong>{{ \Carbon\Carbon::parse($item->shift_to)->format('h:i A') }}</strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="background: {{ $color }}">
                                                        <div style="display: flex; align-items: center;">
                                                            <div>
                                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                                <strong>{{ $item->check_time }}</strong>
                                                            </div>
                                                            <span style="font-weight: bold;">-</span>
                                                            <div>
                                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                                <strong>{{ $item->check_out }}</strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="background: {{ $color }}">{{ secondsToTime($item->early_arrival) }}</td>
                                                    <td style="background: {{ $color }}">{{ secondsToTime($item->delay) }}</td>
                                                    <td style="background: {{ $color }}">{{ secondsToTime($item->early_leave) }}</td>
                                                    <td style="background: {{ $color }}">{{ secondsToTime($item->overtime) }}</td>

                                                    @if ($loop->first)
                                                        <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                                            @if ($canApplyPenalty && $attendanceRecord->process != 2  && !empty($attendanceRecord->employee->username))
                                                                <button type="button"
                                                                        class="btn btn-sm btn-danger apply-penalty-btn"
                                                                        data-employee-id="{{ $attendanceRecord->employee_id }}"
                                                                        data-employee-name="{{ $attendanceRecord->employee->username  ?? 'n/l'}}"
                                                                        data-date="{{ $attendanceRecord->date }}"
                                                                        data-type="{{ $attendanceRecord->type }}"
                                                                        data-timetrack="{{ $attendanceRecord->id }}"
                                                                        data-type-text="{{ $attendanceRecord->type_text }}">
                                                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                                                    تطبيق جزاء
                                                                </button>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr @if ($attendanceRecord->type == 3 || $attendanceRecord->type == 4) style="background-color: yellow;"
                                                @elseif ($attendanceRecord->type == 5) style="background-color: #12E42EFF;" @endif>
                                                <th> {{ $attendanceRecord->employee->username }} </th>
                                                <th> {{ $attendanceRecord->date }}</th>
                                                <th> {{ \Carbon\Carbon::parse($attendanceRecord->date)->locale('en')->translatedFormat('l') }}</th>
                                                <th> {{ $attendanceRecord->hour }} </th>
                                                <th> {{ $attendanceRecord->type_text }} </th>
                                                <th colspan="8"> </th>
                                                <th>
                                                    @if ($canApplyPenalty && $attendanceRecord->process != 2  && !empty($attendanceRecord->employee->username) )
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger apply-penalty-btn"
                                                                data-employee-id="{{ $attendanceRecord->employee_id }}"
                                                                data-employee-name="{{ $attendanceRecord->employee->username ?? 'n/l' }}"
                                                                data-date="{{ $attendanceRecord->date }}"
                                                                data-type="{{ $attendanceRecord->type }}"
                                                                data-timetrack="{{ $attendanceRecord->id }}"
                                                                data-type-text="{{ $attendanceRecord->type_text }}">
                                                            <i class="fa-solid fa-exclamation-triangle"></i>
                                                            تطبيق جزاء
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </th>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>

    @include('hr::Attendance.models')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle penalty button clicks
            const penaltyButtons = document.querySelectorAll('.apply-penalty-btn');

            penaltyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const employeeId = this.getAttribute('data-employee-id');
                    const employeeName = this.getAttribute('data-employee-name');
                    const timetrack = this.getAttribute('data-timetrack');
                    const date = this.getAttribute('data-date');
                    const type = this.getAttribute('data-type');
                    const typeText = this.getAttribute('data-type-text');

                    // Set values in the penalty modal
                    document.getElementById('employee_id').value = employeeId;
                    document.getElementById('timetrack').value = timetrack;
                    document.getElementById('date').value = date;

                    // Update modal title to show employee name and date
                    const modalTitle = document.querySelector('#CreatePenalties .modal-title');
                    if (modalTitle) {
                        modalTitle.textContent = `تطبيق جزاء - ${employeeName} - ${date} (${typeText})`;
                    }

                    // Show the modal
                    const penaltyModal = new bootstrap.Modal(document.getElementById('CreatePenalties'));
                    penaltyModal.show();
                });
            });
        });
    </script>
@endsection
