<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.rewards_table.employee_id')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.deduction_or_bonus_type')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.date')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.amount')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.bonus_type')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.description')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.due_date')</th>
                    <th>@lang('hr::models/hr_report_types.rewards_table.status')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rewards as $reward)
                <tr>
                    <td>{{ $reward->employee_name }}</td>
                    <td>
                        @if ($reward->typeReasc == "reward")
                            @lang('hr::models/hr_report_types.rewards_table.bonus')
                        @elseif ($reward->typeReasc == "penalty")
                            @lang('hr::models/hr_report_types.rewards_table.penalty')
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($reward->created_at)->format('d-m-Y') }}</td>
                    <td>{{ $reward->amount }}</td>
                    <td>{{ \Modules\HR\App\Models\HrReward::getTypeText($reward->type) }}</td>
                    <td>{{ $reward->description ?? '---' }}</td>
                    <td>{{ $reward->due_date ?? '---' }}</td>
                    <td>{{ \Modules\HR\App\Models\HrReward::getstatusesText($reward->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
      
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script>
    function downloadRewardsExcel() {
        setTimeout(async () => {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('بيانات المكافآت');

            // إضافة عناوين الأعمدة
            worksheet.columns = [
                { header: '@lang('hr::models/hr_report_types.rewards_table.employee_id')', key: 'employee_id', width: 20 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.deduction_or_bonus_type')', key: 'deduction_or_bonus_type', width: 25 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.date')', key: 'date', width: 15 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.amount')', key: 'amount', width: 15 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.bonus_type')', key: 'bonus_type', width: 20 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.description')', key: 'description', width: 30 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.due_date')', key: 'due_date', width: 15 },
                { header: '@lang('hr::models/hr_report_types.rewards_table.status')', key: 'status', width: 15 },
            ];

            // إضافة بيانات الجدول إلى ورقة العمل
            const rows = [];
            document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                rows.push({
                    employee_id: cols[0].innerText,
                    deduction_or_bonus_type: cols[1].innerText,
                    date: cols[2].innerText,
                    amount: cols[3].innerText,
                    bonus_type: cols[4].innerText,
                    description: cols[5].innerText,
                    due_date: cols[6].innerText,
                    status: cols[7].innerText,
                });
            });

            worksheet.addRows(rows);

            // تحميل الملف
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'بيانات_المكافآت.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
    }
</script>
@endsection
