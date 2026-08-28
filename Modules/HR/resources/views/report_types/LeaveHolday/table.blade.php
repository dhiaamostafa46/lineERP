<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.department')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.job')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.shift')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.direct_manager')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.leave_type')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.days')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.start_at')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.end_at')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoliday_table.status')</th> <!-- إضافة حالة -->
                </tr>
            </thead>
            <tbody>
                @foreach ($LeaveHoliday as $holiday)
                <tr>
                    <td>{{ $holiday->employee->username ?? 'N/A' }}</td>
                    <td>{{ $holiday->employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $holiday->employee->job->name ?? 'N/A' }}</td>
                    <td>{{ $holiday->employee->shift->name ?? 'N/A' }}</td>
                    <td>{{ $holiday->employee->DirectManager->username ?? 'N/A' }}</td>
                    <td>{{ $holiday->type->name ?? 'N/A' }}</td>
                    <td>
                        @if($holiday->from_at && $holiday->end_at)
                            {{ $holiday->from_at->diffInDays($holiday->end_at) }} <!-- حساب عدد الأيام -->
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $holiday->from_at->format('Y-m-d h:i A') ?? 'N/A' }}</td>
                    <td>{{ $holiday->end_at->format('Y-m-d h:i A') ?? 'N/A' }}</td>
                    <td>{{ $holiday->status_text }}</td>
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
            const worksheet = workbook.addWorksheet('Leave Holidays');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.employee_id')', key: 'employee_id', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.department')', key: 'department', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.job')', key: 'job', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.shift')', key: 'shift', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.direct_manager')', key: 'direct_manager', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.leave_type')', key: 'leave_type', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.days')', key: 'days', width: 10 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.start_at')', key: 'start_at', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.end_at')', key: 'end_at', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoliday_table.status')', key: 'status', width: 20 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_id: cols[0].innerText,
                    department: cols[1].innerText,
                    job: cols[2].innerText,
                    shift: cols[3].innerText,
                    direct_manager: cols[4].innerText,
                    leave_type: cols[5].innerText,
                    days: cols[6].innerText,
                    start_at: cols[7].innerText,
                    end_at: cols[8].innerText,
                    status: cols[9].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Leave_Holidays.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
