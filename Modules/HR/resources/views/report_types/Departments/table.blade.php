<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.employee_name')</th> <!-- اسم الموظف -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.department')</th> <!-- القسم -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.job_title')</th> <!-- المسمى الوظيفي -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.start_date')</th> <!-- تاريخ المباشر -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.mobile')</th> <!-- الجوال -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.email')</th> <!-- الايميل -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.identity')</th> <!-- الهوية -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.identity_expiry')</th> <!-- انتهاء الهوية -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.insurance')</th> <!-- التامين -->
                    <th>@lang('hr::models/hr_report_types.Departmentss_table.salary')</th> <!-- الراتب -->


                </tr>
            </thead>
            <tbody id="payroll-data">
                @foreach ($Departments as $report_type)
                <tr>
                    <td>{{ $report_type->username ?? 'N/A' }}</td> <!-- اسم الموظف -->
                    <td>{{ $report_type->department_name ?? 'N/A' }}</td> <!-- القسم -->
                    <td>{{ $report_type->job_name ?? 'N/A' }}</td> <!-- المسمى الوظيفي -->
                    <td>{{ $report_type->start_at ?? 'N/A' }}</td> <!-- تاريخ المباشر -->
                    <td>{{ $report_type->phone ?? 'N/A' }}</td> <!-- الجوال -->
                    <td>{{ $report_type->email?? 'N/A' }}</td> <!-- الايميل -->
                    <td>{{ $report_type->identity_no?? 'N/A' }}</td> <!-- الهوية -->
                    <td>{{ $report_type->identity_expired_at  ?? 'N/A' }}</td> <!-- انتهاء الهوية -->
                    <td>{{ $report_type->insurance_no ?? 'N/A' }}</td> <!-- التامين -->
                    <td>{{ $report_type->basic ?? 'N/A' }}</td> <!-- الراتب -->


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
            const worksheet = workbook.addWorksheet('بيانات الموظفين');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.employee_name')', key: 'employee_name', width: 30 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.department')', key: 'department', width: 30 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.job_title')', key: 'job_title', width: 30 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.start_date')', key: 'start_date', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.mobile')', key: 'mobile', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.email')', key: 'email', width: 30 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.identity')', key: 'identity', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.identity_expiry')', key: 'identity_expiry', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.insurance')', key: 'insurance', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Departmentss_table.salary')', key: 'salary', width: 20 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#payroll-data tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_name: cols[0].innerText,
                    department: cols[1].innerText,
                    job_title: cols[2].innerText,
                    start_date: cols[3].innerText,
                    mobile: cols[4].innerText,
                    email: cols[5].innerText,
                    identity: cols[6].innerText,
                    identity_expiry: cols[7].innerText,
                    insurance: cols[8].innerText,
                    salary: cols[9].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'بيانات_الموظفين.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300);
    }
</script>
@endsection
