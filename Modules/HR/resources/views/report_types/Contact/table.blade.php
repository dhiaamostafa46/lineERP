<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.Contact_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.Contact_table.department')</th> <!-- قسم -->
                    <th>@lang('hr::models/hr_report_types.Contact_table.job')</th> <!-- وظيفة -->
                    <th>@lang('hr::models/hr_report_types.Contact_table.salary')</th> <!-- راتب -->
                    <th>@lang('hr::models/hr_report_types.Contact_table.attendance')</th>
                    <th>@lang('hr::models/hr_report_types.Contact_table.working_hours')</th>
                    <th>@lang('hr::models/hr_report_types.Contact_table.type_id')</th>

                    <th>@lang('hr::models/hr_report_types.Contact_table.qiwa_no')</th>
                    <th>@lang('hr::models/hr_report_types.Contact_table.start_at')</th>
                    <th>@lang('hr::models/hr_report_types.Contact_table.end_at')</th>

                </tr>
            </thead>
            <tbody>

                @foreach ($Contact as $report_type)
                <tr>
                    <td>{{ $report_type->employee->username ?? 'N/A' }}</td>
                    <td>{{ $report_type->employee->department->name ?? 'N/A' }}</td> <!-- عرض القسم -->
                    <td>{{ $report_type->employee->job->name ?? 'N/A' }}</td> <!-- عرض الوظيفة -->
                    <td>{{ $report_type->employee->salary->basic ?? 'N/A' }}</td> <!-- عرض الراتب -->
                    <td>{{ $report_type->employee->shift->name ?? 'N/A' }}</td>
                    <td>{{  $report_type->employee->shift->work_hours ?? 'N/A' }}</td>
                    <td>{{ $report_type->type->name ?? 'N/A' }}</td>
                    <td>{{ $report_type->qiwa_no }}</td>
                    <td>{{ $report_type->start_at }}</td>
                    <td>{{ $report_type->end_at }}</td>



                    
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
            const worksheet = workbook.addWorksheet('@lang('hr::models/hr_report_types.Contact') ');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.Contact_table.employee_id')', key: 'employee_id', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.department')', key: 'department', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.job')', key: 'job', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.salary')', key: 'salary', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.attendance')', key: 'attendance', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.working_hours')', key: 'working_hours', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.type_id')', key: 'type_id', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.qiwa_no')', key: 'qiwa_no', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.start_at')', key: 'start_at', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Contact_table.end_at')', key: 'end_at', width: 20 },
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
                    attendance: cols[4].innerText,
                    working_hours: cols[5].innerText,
                    type_id: cols[6].innerText,
                    qiwa_no: cols[7].innerText,
                    start_at: cols[8].innerText,
                    end_at: cols[9].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = '@lang('hr::models/hr_report_types.Contact').xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
