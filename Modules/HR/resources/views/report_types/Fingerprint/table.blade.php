<div class="card-body p-3">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered align-middle text-center gy-3 gs-3"
            id="hr-report-types-table">
            <thead class="table-light">
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th class="text-nowrap">{{ __('hr::models/hr_report_types.Fingerprint_table.employee_id') }}</th>
                    <th class="text-start">{{ __('hr::models/hr_report_types.Fingerprint_table.employee_name') }}</th>
                    <th class="text-nowrap">{{ __('hr::models/hr_report_types.Fingerprint_table.total_days') }}</th>
                    <th class="text-nowrap">{{ __('hr::models/hr_report_types.Fingerprint_table.days_with_punches') }}</th>

                    <th class="text-nowrap">{{ __('hr::models/hr_report_types.Fingerprint_table.total_punches') }}</th>
                    <th class="text-nowrap">{{ __('hr::models/hr_report_types.Fingerprint_table.attendance_percentage') }}</th>
                </tr>
            </thead>

            <tbody id="payroll-data">
                @forelse ($Fingerprint as $row)
                    @php
                        $employeeId = data_get($row, 'job_number', '—');
                        $employeeName = data_get($row, 'employee_name', '—');
                        $totalDays = (int) data_get($row, 'total_days', 0);
                        $daysWith = (int) data_get($row, 'days_with_punches', 0);
                        $daysWithout = (int) data_get($row, 'days_without_punches', 0);
                        $totalPunches = (int) data_get($row, 'total_punches', 0);
                        $attendanceRate = $totalDays ? round(($daysWith / $totalDays) * 100, 1) : 0;
                    @endphp

                    <tr>
                        <td class="text-nowrap">{{ $employeeId }}</td>

                        <td class="text-start">
                            {{ $employeeName }}
                        </td>

                        <td class="text-nowrap">
                            {{ $totalDays }}
                        </td>

                        <td class="text-nowrap">
                            <span class="text-success">{{ $daysWith }}</span>
                        </td>



                        <td>
                            <span class="text-success">{{ $totalPunches }}</span>
                        </td>

                        <td class="text-nowrap">
                            {{ $attendanceRate }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ __('hr::messages.no_records_found') ?? 'لا توجد سجلات' }}
                        </td>
                    </tr>
                @endforelse
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
            const worksheet = workbook.addWorksheet('Fingerprint Report');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.employee_id')", key: 'employee_id', width: 20 },
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.employee_name')", key: 'employee_name', width: 30 },
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.total_days')", key: 'total_days', width: 20 },
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.days_with_punches')", key: 'days_with_punches', width: 20 },
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.total_punches')", key: 'total_punches', width: 20 },
                { header: "@lang('hr::models/hr_report_types.Fingerprint_table.attendance_percentage')", key: 'attendance_percentage', width: 25 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length === 1) return; // Skip "no records found" row
                rows.push({
                    employee_id: cols[0].innerText,
                    employee_name: cols[1].innerText,
                    total_days: cols[2].innerText,
                    days_with_punches: cols[3].innerText,
                    total_punches: cols[4].innerText,
                    attendance_percentage: cols[5].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Fingerprint_Report.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
