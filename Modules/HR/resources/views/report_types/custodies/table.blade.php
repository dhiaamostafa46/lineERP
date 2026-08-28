<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="custodies-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.custodies_table.employee_name')</th> <!-- اسم الموظف -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.department')</th> <!-- القسم -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.custody')</th> <!-- العهدة -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.asset_type')</th> <!-- نوع الأصل -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.original')</th> <!-- الأصل -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.delivery_date')</th> <!-- تاريخ التسليم -->
                    <th>@lang('hr::models/hr_report_types.custodies_table.description')</th> <!-- الوصف -->
                    {{-- <th>@lang('hr::models/hr_report_types.custodies_table.return_date')</th> <!-- تاريخ الرجوع --> --}}


                </tr>
            </thead>
            <tbody id="custodies-data">
                @foreach ($custodies as $custody)
                <tr>
                    <td>{{ $custody->employee_name ?? 'N/A' }}</td> <!-- اسم الموظف -->
                    <td>{{ $custody->department ?? 'N/A' }}</td> <!-- القسم -->
                    <td>{{ $custody->custody ?? 'N/A' }}</td> <!-- العهدة -->
                    <td>{{ $custody->asset_type ?? 'N/A' }}</td> <!-- نوع الأصل -->
                    <td>{{ $custody->original ?? 'N/A' }}</td> <!-- الأصل -->
                    <td>{{ $custody->delivery_date ?? 'N/A' }}</td> <!-- تاريخ التسليم -->
                    <td>{{ $custody->description ?? 'N/A' }}</td> <!-- الوصف -->
                    {{-- <td>{{ $custody->return_date ?? 'N/A' }}</td> <!-- تاريخ الرجوع --> --}}


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
            const worksheet = workbook.addWorksheet('بيانات الرواتب');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.Payroll_table.date')', key: 'date', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Payroll_table.total')', key: 'total', width: 20 },
                { header: '@lang('hr::models/hr_report_types.Payroll_table.currency')', key: 'currency', width: 15 },
                { header: '@lang('hr::models/hr_report_types.Payroll_table.status')', key: 'status', width: 15 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#payroll-data tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    date: cols[0].innerText,
                    total: cols[1].innerText,
                    currency: cols[2].innerText,
                    status: cols[3].innerText,
                });
            });

            worksheet.addRows(rows);

            // إنشاء دالة لتحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'بيانات_الرواتب.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300);
    }
</script>
@endsection
