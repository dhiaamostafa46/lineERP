<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="attendance-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.Attendance_table.employee_name')</th> <!-- اسم الموظف -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.attendance_date')</th> <!-- تاريخ الحضور -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.work_hours')</th>

                    <th>@lang('hr::models/hr_report_types.Attendance_table.work_period')</th>
                    <th>@lang('hr::models/hr_report_types.Attendance_table.first_record')</th> <!-- اول تسجيل -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.last_record')</th> <!-- اخر تسجيل -->

                    <th>@lang('hr::models/hr_report_types.Attendance_table.earlyArrival')</th> <!-- تاخير -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.late')</th> <!-- تاخير -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.departure')</th> <!-- الانصراف -->
                    <th>@lang('hr::models/hr_report_types.Attendance_table.overtime')</th> <!-- العمل الاضافي -->
                </tr>
            </thead>




            <tbody id="attendance-data">
                @foreach ($Attendance as $attendance)
                <tr>



                    <td>{{ $attendance->employee->username ??  'N/A'  }}</td> <!-- اسم الموظف -->
                    <td>{{ $attendance->date ?? 'N/A' }}</td> <!-- تاريخ الحضور -->
                    <td>{{ $attendance->employee->shift->work_hours ?? 'N/A' }}</td> <!-- ساعات العمل -->

                    {{-- <td>{{ $attendance->shift_from  }} ::{{ $attendance->shift_to  }}</td> --}}

                    <td>
                        <div style="display: flex; align-items: center;">
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


                    <td>{{ $attendance->first_check_in ?? '' }}</td> <!-- اول تسجيل -->
                    <td>{{ $attendance->last_check_out ?? '' }}</td> <!-- اخر تسجيل -->




                       <td>{{ secondsToTime($attendance->early_arrival) ?? 'N/A' }}</td>
                        <td>{{ secondsToTime($attendance->delay) ?? 'N/A' }}</td> <!-- تاخير -->
                        <td>{{ secondsToTime($attendance->early_leave) ?? 'N/A' }}</td> <!-- الانصراف -->
                        <td>{{ secondsToTime($attendance->overtime) ?? 'N/A' }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



@section('scripts')
<script>
    async function downloadAttendanceExcel() {
        // إنشاء مصنف وورقة عمل جديدة
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('بيانات الحضور');

        // إضافة عناوين الأعمدة
        worksheet.columns = [
            { header: '@lang('hr::models/hr_report_types.Attendance_table.employee_name')', key: 'employee_name', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.attendance_date')', key: 'attendance_date', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.first_record')', key: 'first_record', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.last_record')', key: 'last_record', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.work_hours')', key: 'work_hours', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.location')', key: 'location', width: 20 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.late')', key: 'late', width: 15 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.departure')', key: 'departure', width: 15 },
            { header: '@lang('hr::models/hr_report_types.Attendance_table.overtime')', key: 'overtime', width: 15 },
        ];

        // إضافة بيانات الجدول إلى ورقة العمل
        const rows = [];
        document.querySelectorAll('#attendance-data tr').forEach(row => {
            const cols = row.querySelectorAll('td');
            rows.push({
                employee_name: cols[0].innerText,
                attendance_date: cols[1].innerText,
                work_hours: cols[2].innerText,
                location: cols[3].innerText,
                first_record: cols[5].innerText,
                last_record: cols[6].innerText,
                late: cols[7].innerText,
                departure: cols[8].innerText,
                overtime: cols[9].innerText,
            });
        });

        worksheet.addRows(rows);

        // إنشاء دالة لتحميل الملف
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'بيانات_الحضور.xlsx';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>
@endsection
