<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.name')</th> <!-- الاسم -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.from_date')</th> <!-- من تاريخ -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.to_date')</th> <!-- إلى تاريخ -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.attendance_count')</th> <!-- عدد أيام الحضور -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.absence_count')</th> <!-- عدد أيام الغياب -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.holiday_days_count')</th>
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.vacation_days_count')</th>
                      <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.exempt_days_count')</th>  <!-- عدد أيام الإجازة -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.earlyArrival')</th>
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.late')</th> <!-- تأخير -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.departure')</th> <!-- انصراف مبكر -->
                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.overtime_hours')</th> <!-- ساعات العمل الإضافية -->

                    <th>@lang('hr::models/hr_report_types.SummaryAttendance_table.procedure')</th> <!-- إجراء -->
                </tr>
            </thead>


            <tbody id="payroll-data">
                @foreach ($SummaryAttendance as $report_type)
                    <tr>
                        <td>{{ $report_type->employee->username ?? 'N/A' }}</td> <!-- الاسم -->
                        <td>{{ \Carbon\Carbon::parse($report_type->start_date)->format('Y-m-d') ?? 'N/A' }}</td>
                        <!-- تاريخ البداية -->
                        <td>{{ \Carbon\Carbon::parse($report_type->end_date)->format('Y-m-d') ?? 'N/A' }}</td>
                        <!-- تاريخ النهاية -->
                        <td>{{ $report_type->present_days ?? 0 }}</td>
                        <td>{{ $report_type->absent_days ?? 0 }}</td>
                        <td>{{ $report_type->holiday_days ?? 0 }}</td>
                        <td>{{ $report_type->vacation_days ?? 0 }}</td>
                         <td>{{ $report_type->exempt_days ?? 0 }}</td>

                        <td>{{ substr($report_type->total_early_arrival, 0, 8) }}</td>
                        <td>{{ substr($report_type->total_delay, 0, 8) }}</td> <!-- تأخير -->
                        <td>{{ substr($report_type->total_early_leave, 0, 8) }}</td> <!-- انصراف مبكر -->
                        <td>{{ substr($report_type->total_overtime, 0, 8) }}</td> <!-- ساعات العمل الإضافية -->


                        <td>


                            {!! Form::open(['route' => 'hr.Report.AttendanceRecords', 'method' => 'GET']) !!}
                            <div class='btn-group'>

                                <input type="hidden" name="employee_id" value="{{ $report_type->employee_id }}">
                                <input type="hidden" name="start_date" value="{{ $report_type->start_date }}">
                                <input type="hidden" name="end_date" value="{{ $report_type->end_date }}">

                                {!! Form::button('<i class="fa fa-calendar"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-icon btn-sm btn-light-primary btn-xs',
                                ]) !!}

                            </div>
                            {!! Form::close() !!}
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>




@section('scripts')
    <script>
        function downloadExcel() {
            setTimeout(async () => {
                // إنشاء مصنف وورقة عمل جديدة
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('بيانات الحضور');

                // إضافة عناوين الأعمدة
                worksheet.columns = [{
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.name')',
                        key: 'name',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.from_date')',
                        key: 'from_date',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.to_date')',
                        key: 'to_date',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.attendance_count')',
                        key: 'attendance_count',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.absence_count')',
                        key: 'absence_count',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.holiday_days_count')',
                        key: 'holiday_days_count',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.vacation_days_count')',
                        key: 'vacation_days_count',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.earlyArrival')',
                        key: 'early_arrival',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.late')',
                        key: 'late',
                        width: 15
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.departure')',
                        key: 'departure',
                        width: 15
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.SummaryAttendance_table.overtime_hours')',
                        key: 'overtime_hours',
                        width: 20
                    },

                ];

                // إضافة بيانات الجدول إلى ورقة العمل
                const rows = [];
                document.querySelectorAll('#payroll-data tr').forEach(row => {
                    const cols = row.querySelectorAll('td');
                    rows.push({
                        name: cols[0].innerText,
                        from_date: cols[1].innerText,
                        to_date: cols[2].innerText,
                        attendance_count: cols[3].innerText,
                        absence_count: cols[4].innerText,
                        holiday_days_count: cols[5].innerText,
                        vacation_days_count: cols[6].innerText,
                        early_arrival: cols[7].innerText, // القيمة الجديدة
                        late: cols[8].innerText,
                        departure: cols[9].innerText,
                        overtime_hours: cols[10].innerText,

                    });
                });

                worksheet.addRows(rows);

                // إنشاء دالة لتحميل الملف
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'بيانات_الحضور.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
        }
    </script>
@endsection
