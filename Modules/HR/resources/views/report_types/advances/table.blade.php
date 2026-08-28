<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.advances_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.department')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.job')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.salary')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.max_advance')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.amount')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.due_date')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.description')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.date')</th>
                    <th>@lang('hr::models/hr_report_types.advances_table.status')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($advances as $advance)
                <tr>
                    <td>{{ $advance->employee->username ?? 'N/A' }}</td>
                    <td>{{ $advance->employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $advance->employee->job->name ?? 'N/A' }}</td>
                    <td>{{ $advance->employee->salary->basic ?? 'N/A' }}</td>
                    <td>{{ $advance->employee->max_advance ?? 'N/A' }}</td>
                    <td>{{ $advance->amount ?? 'N/A' }}</td>
                    <td>{{ $advance->due_at ?? 'N/A' }}</td>
                    <td>{{ $advance->description ?? 'N/A' }}</td>
                    <td>{{ $advance->created_at ?? 'N/A' }}</td>
                    <td><span class="{{ $advance->status_badge }}">{{ $advance->status_text }}</span></td>
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
            const worksheet = workbook.addWorksheet('Advances');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.advances_table.employee_id')', key: 'employee_id', width: 30 },
                { header: '@lang('hr::models/hr_report_types.advances_table.department')', key: 'department', width: 30 },
                { header: '@lang('hr::models/hr_report_types.advances_table.job')', key: 'job', width: 30 },
                { header: '@lang('hr::models/hr_report_types.advances_table.salary')', key: 'salary', width: 20 },
                { header: '@lang('hr::models/hr_report_types.advances_table.max_advance')', key: 'max_advance', width: 20 },
                { header: '@lang('hr::models/hr_report_types.advances_table.amount')', key: 'amount', width: 20 },
                { header: '@lang('hr::models/hr_report_types.advances_table.due_date')', key: 'due_date', width: 20 },
                { header: '@lang('hr::models/hr_report_types.advances_table.description')', key: 'description', width: 30 },
                { header: '@lang('hr::models/hr_report_types.advances_table.status')', key: 'status', width: 20 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_id: cols[0].innerText,
                    department: cols[1].innerText,
                    job: cols[2].innerText,
                    salary: cols[3].innerText,
                    max_advance: cols[4].innerText,
                    amount: cols[5].innerText,
                    due_date: cols[6].innerText,
                    description: cols[7].innerText,
                    status: cols[8].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Advances_Report.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
