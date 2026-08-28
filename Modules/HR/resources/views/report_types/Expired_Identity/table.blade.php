<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.username')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.department_name')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.job_name')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.gender')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.nationality')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.start_date')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.identity_no')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.identity_expired_at')</th>
                    <th>@lang('hr::models/hr_report_types.Expired_identity_table.remaining_days')</th>
                </tr>
            </thead>
            <tbody>

            
                
                @foreach ($Expired_Identity as $report_type)
                    <tr>
                        <td>{{ $report_type->username }}</td>
                        <td>{{ $report_type->department_name }}</td>
                        <td>{{ $report_type->job_name }}</td>
                        <td>{{ $report_type->gender == 1 ? __('lang.male') : __('lang.female') }}</td>
                        <td>{{ $report_type->nationality }}</td>
                        <td>{{ $report_type->start_at }}</td>
                        <td>{{ $report_type->identity_no }}</td>
                        <td>{{ $report_type->identity_expired_at }}</td>
                        <td>
                            @if ($report_type->remaining_days !== null)
                                @if ($report_type->remaining_days > 0)
                                    <span class="badge badge-success">{{ $report_type->remaining_days }}</span>
                                @elseif($report_type->remaining_days == 0)
                                    <span class="badge badge-warning">0</span>
                                @else
                                    <span class="badge badge-danger">{{ abs($report_type->remaining_days) }}</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
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
                const worksheet = workbook.addWorksheet('Expired Identities');

                // إضافة عناوين الأعمدة
                worksheet.columns = [{
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.username')',
                        key: 'username',
                        width: 30
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.department_name')',
                        key: 'department_name',
                        width: 30
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.job_name')',
                        key: 'job_name',
                        width: 30
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.gender')',
                        key: 'gender',
                        width: 15
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.nationality')',
                        key: 'nationality',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.start_date')',
                        key: 'start_date',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.identity_no')',
                        key: 'identity_no',
                        width: 20
                    },
                    {
                        header: '@lang('hr::models/hr_report_types.Expired_identity_table.identity_expired_at')',
                        key: 'identity_expired_at',
                        width: 20
                    },
                ];

                // إضافة بيانات الجدول إلى ورقة العمل
                const rows = [];
                document.querySelectorAll('#hr-report-types-table tbody tr').forEach(row => {
                    const cols = row.querySelectorAll('td');
                    rows.push({
                        username: cols[0].innerText,
                        department_name: cols[1].innerText,
                        job_name: cols[2].innerText,
                        gender: cols[3].innerText,
                        nationality: cols[4].innerText,
                        start_date: cols[5].innerText,
                        identity_no: cols[6].innerText,
                        identity_expired_at: cols[7].innerText,
                    });
                });

                worksheet.addRows(rows);

                // إنشاء دالة لتحميل الملف
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Expired_Identities.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 300); // تأخير التنفيذ لمدة 300 مللي ثانية
        }
    </script>
@endsection
