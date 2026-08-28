<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.EndService_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.department')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.position')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.join_date')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.contract_end_date')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.years_of_service')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.termination_reason')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.leave_days')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.leave_amount')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.end_service_bonus')</th>
                    <th>@lang('hr::models/hr_report_types.EndService_table.total')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($EndService as $report_type)
                <tr>
                    <td>{{ $report_type->employee->username ?? 'N/A' }}</td>
                    <td>{{ $report_type->employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $report_type->employee->job->name ?? 'N/A' }}</td>
                    <td>{{ $report_type->employee->start_at ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($report_type->end)->format('Y-m-d') }}</td>

                    @php
                        $start_at = \Carbon\Carbon::parse($report_type->employee->start_at);
                        $end_date = \Carbon\Carbon::parse($report_type->end);
                        $years_of_service = $start_at->gt($end_date) ? 0 : $start_at->diffInYears($end_date);
                    @endphp

                    <td>{{ $years_of_service }} سنوات</td>
                    <td>{{ $report_type->reason_text }}</td>

                    <td>{{ $report_type->employee->max_off_days ?? 0 }}</td>

                    @php
                        // حساب مبلغ الإجازة
                        $leave_days = $report_type->employee->max_off_days ?? 0;
                        $basic_salary = $report_type->employee->salary->basic ?? 0;
                        $leave_amount = $leave_days > 0 ? $basic_salary / $leave_days : 0;

                        $leave_amount_final = $leave_amount * $leave_days;
                    @endphp

                    <td>{{ $leave_amount_final }}</td>
                    <td>{{ $report_type->reward_amount ?? 'N/A' }}</td>
                    <td>{{ ($leave_amount_final + ($report_type->reward_amount ?? 0)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script>
    function downloadEndServiceExcel() {
        setTimeout(async () => {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('بيانات إنهاء الخدمة');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.EndService_table.employee_id')', key: 'employee_id', width: 20 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.department')', key: 'department', width: 25 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.position')', key: 'position', width: 20 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.join_date')', key: 'join_date', width: 15 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.contract_end_date')', key: 'contract_end_date', width: 15 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.years_of_service')', key: 'years_of_service', width: 20 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.termination_reason')', key: 'termination_reason', width: 25 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.leave_days')', key: 'leave_days', width: 15 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.leave_amount')', key: 'leave_amount', width: 15 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.end_service_bonus')', key: 'end_service_bonus', width: 15 },
                { header: '@lang('hr::models/hr_report_types.EndService_table.total')', key: 'total', width: 15 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_id: cols[0].innerText,
                    department: cols[1].innerText,
                    position: cols[2].innerText,
                    join_date: cols[3].innerText,
                    contract_end_date: cols[4].innerText,
                    years_of_service: cols[5].innerText,
                    termination_reason: cols[6].innerText,
                    leave_days: cols[7].innerText,
                    leave_amount: cols[8].innerText,
                    end_service_bonus: cols[9].innerText,
                    total: cols[10].innerText,
                });
            });

            worksheet.addRows(rows);

            // تحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'بيانات_إنهاء_الخدمة.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
