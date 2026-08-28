<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="attendance-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">

                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.employee_name')</th>
                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.date')</th>
                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.work_period')</th>

                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.earlyArrival')</th>
                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.late')</th>
                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.early_departure')</th>
                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.overtime')</th>

                    <th>@lang('hr::models/hr_report_types.DeductionAttendance_table.movement')</th>
                </tr>
            </thead>
            <tbody id="attendance-data">
                @foreach ($DeductionAttendance as $attendance)
                <tr>
                    <td>{{ $attendance->employee->username ?? 'N/A' }}</td>
                    <td>{{ $attendance->date ?? 'N/A' }}</td>
                    <td>
                        <div style="display: flex;">
                            <div style="background-color: #e0f7fa; border-radius: 5px; padding: 5px; margin-right: 5px;">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <strong>{{ $attendance->shift_from }}</strong>
                            </div>
                            <span style="font-weight: bold;">-</span>
                            <div style="background-color: #e0f7fa; border-radius: 5px; padding: 5px; margin-left: 5px;">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <strong>{{ $attendance->shift_to }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ secondsToTime($attendance->early_arrival) ?? 'N/A' }}</td>
                    <td>{{ secondsToTime($attendance->min_delay) ?? 'N/A' }}</td>
                    <td>{{ secondsToTime($attendance->min_early_leave) ?? 'N/A' }}</td>
                    <td>{{ secondsToTime($attendance->max_overtime) ?? 'N/A' }}</td>

                    <th></th>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("#attendance-table tbody tr").forEach(function(row) {
            const earlyArrival = timeToSeconds(row.querySelector("td:nth-child(4)").innerText) || 0;

            const minDelay = timeToSeconds(row.querySelector("td:nth-child(5)").innerText) || 0;
            const minEarlyLeave = timeToSeconds(row.querySelector("td:nth-child(6)").innerText) || 0;
            const maxOvertime = timeToSeconds(row.querySelector("td:nth-child(7)").innerText) || 0;


            let totalSeconds = (minDelay + minEarlyLeave) - (earlyArrival + maxOvertime);

            const movementCell = row.querySelector("td:nth-child(8)");

            if (earlyArrival + maxOvertime > minDelay + minEarlyLeave) {
                if (earlyArrival > maxOvertime) {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.earlyArrival')';
                    movementCell.setAttribute("data-movement", "early_arrival");
                } else {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.overtime')';
                    movementCell.setAttribute("data-movement", "overtime");
                }
            } else {
                if (minDelay > minEarlyLeave) {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.late')';
                    movementCell.setAttribute("data-movement", "late");
                } else {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.departure')';
                    movementCell.setAttribute("data-movement", "early_leave");
                }
            }

            if (totalSeconds === 0) {
                movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.Balanced')';
                movementCell.setAttribute("data-movement", "balanced");
            }
        });
    });
</script>
@endsection
