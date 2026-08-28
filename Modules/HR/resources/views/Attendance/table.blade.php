<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="attendance-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_attendances.Attendance_table.employee_name')</th> <!-- اسم الموظف -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.attendance_date')</th> <!-- تاريخ الحضور -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.work_period')</th>
                    <th>@lang('hr::models/hr_attendances.Attendance_table.first_record')</th> <!-- اول تسجيل -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.last_record')</th> <!-- اخر تسجيل -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.earlyArrival')</th>
                    <th>@lang('hr::models/hr_attendances.Attendance_table.late')</th> <!-- تاخير -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.departure')</th> <!-- الانصراف -->
                    <th>@lang('hr::models/hr_attendances.Attendance_table.overtime')</th>


                    {{-- <th>@lang('hr::models/hr_attendances.Attendance_table.movement')</th>
                    <th></th> --}}
            </thead>
            <tbody id="attendance-data">
                @foreach ($attendances as $index => $attendance)
                    <tr>

                        <td>{{ $attendance->employee->username ?? 'N/A' }}
                            <input type="hidden" id="attendanceemployee" value="{{ $attendance->employee_id }}">
                            <input type="hidden" id="attendancedate" value="{{ $attendance->date }}">
                        </td> <!-- اسم الموظف -->
                        <td>{{ $attendance->date ?? 'N/A' }}</td> <!-- تاريخ الحضور -->
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div
                                    style="background-color: #e0f7fa; border-radius: 5px; padding: 5px; margin-right: 5px;">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    <strong class="time" data-time="{{ $attendance->shift_from }}">
                                        {{ \Carbon\Carbon::parse($attendance->shift_from)->format('h:i A') }}
                                    </strong>
                                </div>
                                <span style="font-weight: bold;">-</span>
                                <div
                                    style="background-color: #e0f7fa; border-radius: 5px; padding: 5px; margin-left: 5px;">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    <strong class="time" data-time="{{ $attendance->shift_to }}">
                                        {{ \Carbon\Carbon::parse($attendance->shift_to)->format('h:i A') }}
                                    </strong>
                                </div>
                            </div>
                        </td>

                        <td>
                            <strong class="time" data-time="{{ $attendance->first_check_in }}">{{ $attendance->first_check_in ? \Carbon\Carbon::parse($attendance->first_check_in)->format('h:i A') : '' }}</strong>
                        </td> <!-- اول تسجيل -->
                        <td>
                            <strong class="time" data-time="{{ $attendance->last_check_out }}">{{ $attendance->last_check_out ? \Carbon\Carbon::parse($attendance->last_check_out)->format('h:i A') : '' }}</strong>
                        </td> <!-- اخر تسجيل -->
                        <td>{{ secondsToTime($attendance->early_arrival) ?? 'N/A' }}</td>
                        <td>{{ secondsToTime($attendance->delay) ?? 'N/A' }}</td> <!-- تاخير -->
                        <td>{{ secondsToTime($attendance->early_leave) ?? 'N/A' }}</td> <!-- الانصراف -->
                        <td>{{ secondsToTime($attendance->overtime) ?? 'N/A' }}</td>
                        <td style="display: none">


                        {{-- </td>

                        <td>


                        </td>

                        <th>


                        </th> --}}

                        <!-- العمل الاضافي -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


































{{-- @include('hr::Attendance.models')
@section('scripts')
    <script>
        document.querySelectorAll("#attendance-table tbody tr").forEach(function(row) {
            // جلب القيم الزمنية من الأعمدة وتحويلها إلى ثوانٍ
            const earlyArrival = timeToSeconds(row.querySelector("td:nth-child(6)").innerText) || 0; // حضور مبكر
            const maxOvertime = timeToSeconds(row.querySelector("td:nth-child(9)").innerText) || 0; // عمل إضافي
            const minDelay = timeToSeconds(row.querySelector("td:nth-child(7)").innerText) || 0; // تأخير
            const minEarlyLeave = timeToSeconds(row.querySelector("td:nth-child(8)").innerText) || 0; // انصراف مبكر

            // حساب total (بالثواني)
            let totalSeconds = (minDelay + minEarlyLeave) - (earlyArrival + maxOvertime);

            // إذا كانت النتيجة سالبة، نحذف الإشارة السالبة
            const isNegative = totalSeconds < 0;
            if (isNegative) {
                totalSeconds = Math.abs(totalSeconds); // تحويل إلى القيمة المطلقة
            }

            // تحويل الناتج إلى صيغة الوقت (HH:MM:SS)
            const totalTime = secondsToTime(totalSeconds);

            // تحديث خانة total
            const totalCell = row.querySelector("td:nth-child(10)");
            totalCell.innerText = totalTime !== "00:00:00" ? totalTime : "00:00:00";

            // تحديث خانة movement بناءً على القيم
            const movementCell = row.querySelector("td:nth-child(11)");

            let isBalanced = false; // افتراض أن الحالة غير متزنة
            if (earlyArrival + maxOvertime > minDelay + minEarlyLeave) {
                if (earlyArrival > maxOvertime) {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.earlyArrival')'; // حضور مبكر
                    movementCell.setAttribute("data-movement", "early_arrival");
                } else {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.overtime')'; // عمل إضافي
                    movementCell.setAttribute("data-movement", "overtime");
                }
            } else {
                if (minDelay > minEarlyLeave) {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.late')'; // تأخير
                    movementCell.setAttribute("data-movement", "late");
                } else {
                    movementCell.innerText = '@lang('hr::models/hr_attendances.Attendance_table.departure')'; // انصراف مبكر
                    movementCell.setAttribute("data-movement", "early_leave");
                }
            }

            // حالة التوازن (إذا كانت النتيجة تساوي صفر)
            if (totalSeconds === 0) {
                movementCell.innerText = ' @lang('hr::models/hr_attendances.Attendance_table.Balanced')'; // متوازن
                movementCell.setAttribute("data-movement", "balanced");
                isBalanced = true; // تحديث حالة التوازن
            }

            // إضافة الأزرار بناءً على شرط التوازن أو نوع الحضور
            const type = parseInt(row.querySelector('#attendanceemployee').getAttribute('data-type')) ||
            0; // جلب نوع الحضور
            if (!isBalanced && type !== 2) {
                const actionCell = row.querySelector("th:last-child"); // تحديد الخلية التي تحتوي على الأزرار
                actionCell.innerHTML = `
            <a href="#" class="btn btn-light-success">@lang('hr::models/hr_attendances.Attendance_table.apply')</a>

        `;
            }

            document.querySelectorAll('.btn-light-success').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); // منع إعادة التحميل

                    // الحصول على الصف الحالي
                    const row = button.closest('tr');
                    if (!row) {
                        console.error('الصف غير موجود!');
                        return;
                    }

                    // الحصول على نوع الحركة
                    const movementTypeElement = row.querySelector('td:nth-child(11)');
                    if (!movementTypeElement) {
                        console.error('نوع الحركة غير موجود في العمود المحدد!');
                        return;
                    }
                    const movementType = movementTypeElement.getAttribute('data-movement');

                    // الحصول على employee_id
                    const employeeInput = row.querySelector('#attendanceemployee');
                    if (!employeeInput) {
                        console.error('الحقل #attendanceemployee غير موجود!');
                        return;
                    }
                    const employeeId = employeeInput.value;

                    // الحصول على التاريخ
                    const attendanceDateInput = row.querySelector('#attendancedate');
                    if (!attendanceDateInput) {
                        console.error('الحقل #attendancedate غير موجود!');
                        return;
                    }
                    const attendancedate = attendanceDateInput.value;

                    // التحقق من نوع الحركة وفتح النموذج المناسب
                    if (movementType === 'overtime' || movementType === 'early_arrival') {
                        // فتح نموذج CreateRewards
                        const rewardsModal = document.querySelector('#CreateRewards');
                        if (rewardsModal) {
                            $('#CreateRewards').modal('show');
                            rewardsModal.querySelector('#employee_id').value = employeeId;
                            rewardsModal.querySelector('#date').value = attendancedate;
                        } else {
                            console.error('نموذج CreateRewards غير موجود!');
                        }
                    } else {
                        // فتح نموذج CreatePenalties
                        const penaltiesModal = document.querySelector('#CreatePenalties');
                        if (penaltiesModal) {
                            $('#CreatePenalties').modal('show');
                            penaltiesModal.querySelector('#employee_id').value = employeeId;
                            penaltiesModal.querySelector('#date').value = attendancedate;
                        } else {
                            console.error('نموذج CreatePenalties غير موجود!');
                        }
                    }
                });
            });

        });














        // دالة لتحويل الوقت من HH:MM إلى ثوانٍ
        function timeToSeconds(time) {
            const parts = time.split(":");
            const hours = parseInt(parts[0], 10) || 0;
            const minutes = parseInt(parts[1], 10) || 0;
            const seconds = parseInt(parts[2], 10) || 0;
            return hours * 3600 + minutes * 60 + seconds;
        }

        // دالة لتحويل الوقت من ثوانٍ إلى HH:MM:SS
        function secondsToTime(seconds) {
            const h = Math.floor(seconds / 3600)
                .toString()
                .padStart(2, "0");
            const m = Math.floor((seconds % 3600) / 60)
                .toString()
                .padStart(2, "0");
            const s = (seconds % 60).toString().padStart(2, "0");
            return `${h}:${m}:${s}`;
        }
    </script>
@endsection --}}
