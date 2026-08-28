<div class="card-body p-0">
    <div class="table-responsive text-center">
        <table class="table table-bordered text-center table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.department')</th>
                    {{-- <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.contract')</th> --}}
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.leave_days')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.join_date')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.number_of_years')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.carried_balance')</th>
                    <th>@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.current_balance')</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($LeaveHoldaybalance as $holiday)
                    <tr>
                        <td>{{ $holiday['name'] ?? 'N/A' }}</td>
                        <td>{{ $holiday['department'] ?? 'N/A' }}</td>
                        {{-- <td>{{ $holiday['contract'] ?? 'N/A' }}</td> --}}
                        <td>{{ $holiday['max_off_days'] ?? 'N/A' }}</td>
                        <td>{{ $holiday['start_at'] ?? 'N/A' }}</td>
                        <td>{{ $holiday['number_of_years'] ?? 0 }}</td>
                        <td>{{ $holiday['leave_balance'] ?? 'N/A' }}</td>
                        <td>{{ $holiday['current_balance'] ?? 'N/A' }}</td>
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
            const worksheet = workbook.addWorksheet('Leave Balance');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.employee_id')', key: 'employee_id', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.department')', key: 'department', width: 30 },
                // { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.contract')', key: 'contract', width: 30 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.leave_days')', key: 'leave_days', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.join_date')', key: 'join_date', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.number_of_years')', key: 'number_of_years', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.carried_balance')', key: 'carried_balance', width: 20 },
                { header: '@lang('hr::models/hr_report_types.LeaveHoldaybalance_table.current_balance')', key: 'current_balance', width: 20 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_id: cols[0].innerText,
                    department: cols[1].innerText,
                    // contract: cols[2].innerText, // Uncomment if you decide to use contract column
                    leave_days: cols[2].innerText,
                    join_date: cols[3].innerText,
                    number_of_years: cols[4].innerText,
                    carried_balance: cols[5].innerText,
                    current_balance: cols[6].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Leave_Balance.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection