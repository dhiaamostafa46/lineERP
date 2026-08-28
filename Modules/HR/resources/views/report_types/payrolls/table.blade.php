<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.Payroll_table.date')</th> <!-- التاريخ -->
                    <th>@lang('hr::models/hr_report_types.Payroll_table.total')</th> <!-- الاجمالي -->
                    <th>@lang('hr::models/hr_report_types.Payroll_table.currency')</th> <!-- العملة -->
                    <th>@lang('hr::models/hr_report_types.Payroll_table.status')</th> <!-- الحالة -->
                </tr>
            </thead>
            <tbody id="payroll-data">
                @foreach ($Payroll as $report_type)
                <tr>
                    <td>{{ $report_type->delivery_at ?? 'N/A' }}</td> <!-- التاريخ -->
                    <td>{{ $report_type->total ?? 'N/A' }}</td> <!-- الاجمالي -->
                    <td>{{ $report_type->currency ?? 'N/A' }}</td> <!-- العملة -->
                    <td>{{ $report_type->status_text ?? 'N/A' }}</td> <!-- الحالة -->
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
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
