<div class="card-body p-0">

    <div class="table-responsive">
        <div class="d-flex flex-wrap justify-content-end mb-3 px-4">
            <div class="legend-item mx-2 d-flex align-items-center">
                <span style="display: inline-block; width: 15px; height: 15px; background-color: #FA9D94; border: 1px solid #ccc; vertical-align: middle;"></span>
                <span style="vertical-align: middle; margin-left: 5px;">غائب (Absent)</span>
            </div>
            <div class="legend-item mx-2 d-flex align-items-center">
                <span style="display: inline-block; width: 15px; height: 15px; background-color: #FBFB86; border: 1px solid #ccc; vertical-align: middle;"></span>
                <span style="vertical-align: middle; margin-left: 5px;">إجازة / عطلة (Leave/Holiday)</span>
            </div>
            <div class="legend-item mx-2 d-flex align-items-center">
                <span style="display: inline-block; width: 15px; height: 15px; background-color: #82F591; border: 1px solid #ccc; vertical-align: middle;"></span>
                <span style="vertical-align: middle; margin-left: 5px;">معفى (Exempt) - justification تسوية </span>
            </div>
            <div class="legend-item mx-2 d-flex align-items-center">
                <span style="display: inline-block; width: 15px; height: 15px; background-color: #FAE294; border: 1px solid #ccc; vertical-align: middle;"></span>
                <span style="vertical-align: middle; margin-left: 5px;">بصمة ناقصة (Missed Punch)</span>
            </div>
            <div class="legend-item mx-2 d-flex align-items-center">
                <span style="display: inline-block; width: 15px; height: 15px; background-color: #82F5DA; border: 1px solid #ccc; vertical-align: middle;"></span>
                <span style="vertical-align: middle; margin-left: 5px;"> عطلة رسمية (official holiday )</span>
            </div>
        </div>

        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.employee_name') </th>
                    <th colspan="2">@lang('hr::models/hr_report_types.AttendanceRecords_table.date') </th>

                    <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.work_hours')</th>
                     <th> @lang('hr::models/hr_report_types.fields.status') </th>
                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.location') </th>
                       <th> @lang('hr::models/hr_report_types.fields.status') </th>

                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.work_period') </th>
                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.fingerprint') </th>
                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.earlyArrival') </th>
                    <th>@lang('hr::models/hr_report_types.AttendanceRecords_table.late')</th>
                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.early_departure')</th>
                    <th> @lang('hr::models/hr_report_types.AttendanceRecords_table.overtime') </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($SummaryAttendance as $attendanceRecord)
                    @if (count($attendanceRecord->timeTrackDetails) > 0)
                        @foreach ($attendanceRecord->timeTrackDetails as $index => $item)
                             @php
                                 $color = '';
                                 if($item->type ==1 &&  $attendanceRecord->type == 2){
                                    $color="#FA9D94";
                                 }elseif($item->type ==8){
                                     $color="#FAE294";
                                 }elseif($item->type ==6){
                                    $color="#12E42EFF";
                                 }
                             @endphp

                            <tr
                                @if ($attendanceRecord->type == 1) style="background-color:#FA9D94;" @elseif ($attendanceRecord->type == 4)  style="background-color: #FBFB86;"  @elseif ($attendanceRecord->type == 5)  style="background-color: #82F591;"   @elseif ($attendanceRecord->type == 6)  style="background-color: #82F5DA;" @endif>
                                @if ($loop->first)
                                    <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                        {{ $employeesdata->username }}
                                    </td>
                                    <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                        {{ $attendanceRecord->date }}
                                    </td>
                                    <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                        {{ \Carbon\Carbon::parse($attendanceRecord->date)->locale('en')->translatedFormat('l') }}
                                    </td>
                                    <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">
                                        {{ $attendanceRecord->hour }}
                                    </td>
                                    <td rowspan="{{ $attendanceRecord->timeTrackDetails->count() }}">{{   $attendanceRecord->type_text }}</td>
                                @endif

                                <td style="background: {{  $color }}">{{ $item->address }}</td>
                                <td style="background: {{  $color }}">{{ $item->type_text }}</td>
                                <td style="background: {{  $color }}">
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <strong>{{ \Carbon\Carbon::parse($item->shift_from)->format('h:i A') }}</strong>
                                        </div>
                                        <span style="font-weight: bold;">-</span>
                                        <div>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <strong>{{ \Carbon\Carbon::parse($item->shift_to)->format('h:i A') }}</strong>
                                        </div>
                                    </div>
                                </td>
                                 <td style="background: {{  $color }}">
                                    <div style="display: flex; align-items: center;">
                                        <div>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <strong>{{ $item->check_time }}</strong>
                                        </div>
                                        <span style="font-weight: bold;">-</span>
                                        <div>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <strong>{{ $item->check_out }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td style="background: {{  $color }}">{{ secondsToTime($item->early_arrival) }}</td>
                                <td style="background: {{  $color }}">{{ secondsToTime($item->delay) }}</td>
                                <td style="background: {{  $color }}">{{ secondsToTime($item->early_leave) }}</td>
                                <td style="background: {{  $color }}">{{ secondsToTime($item->overtime) }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr @if ($attendanceRecord->type == 3 || $attendanceRecord->type == 4) style="background-color: yellow;"  @elseif ($attendanceRecord->type == 5)  style="background-color: #82F591;"   @endif>
                            <th> {{ $employeesdata->username }} </th>
                            <th> {{ $attendanceRecord->date }}</th>
                            <th> {{ \Carbon\Carbon::parse($attendanceRecord->date)->locale('en')->translatedFormat('l') }}
                            </th>
                            <th> {{ $attendanceRecord->hour }} </th>
                              <th> {{ $attendanceRecord->type_text }} </th>
                            <th colspan="8">  </th>

                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@section('scripts')
    <script>
        function downloadExcel() {
            const getFillColor = (styleColor) => {
                if (!styleColor) {
                    return 'FFFFFFFF'; // Default white
                }
                const color = styleColor.replace(/\s/g, '').toLowerCase();

                // Red
                if (color === 'red' || color.includes('rgb(228,35,18)') || color.includes('#e42312') || color.includes('e42312ff')) {
                    return 'FFE42312';
                }
                // Green
                if (color.includes('rgb(18,228,46)') || color.includes('#12e42e') || color.includes('12e42eff')) {
                    return 'FF12E42E';
                }
                // Yellow
                if (color === 'yellow' || color.includes('rgb(255,255,0)') || color.includes('ffff00')) {
                    return 'FFFFFF00';
                }
                return 'FFFFFFFF'; // Default white
            };
            setTimeout(async () => {
                // إنشاء مصنف وورقة عمل جديدة
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('بيانات الحضور');

                // إضافة عناوين الأعمدة
                worksheet.columns = [{
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.employee_name')",
                        key: 'employee_name',
                        width: 20
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.date')",
                        key: 'date',
                        width: 15
                    },
                    {
                        header: 'اليوم',
                        key: 'day',
                        width: 15
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.work_hours')",
                        key: 'work_hours',
                        width: 15
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.location')",
                        key: 'location',
                        width: 25
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.work_period')",
                        key: 'work_period',
                        width: 25
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.earlyArrival')",
                        key: 'earlyArrival',
                        width: 15
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.late')",
                        key: 'late',
                        width: 15
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.early_departure')",
                        key: 'early_departure',
                        width: 15
                    },
                    {
                        header: "@lang('hr::models/hr_report_types.AttendanceRecords_table.overtime')",
                        key: 'overtime',
                        width: 15
                    }
                ];

                // تنسيق صف العناوين
                const headerRow = worksheet.getRow(1);
                headerRow.font = {
                    bold: true,
                    size: 12
                };
                headerRow.alignment = {
                    horizontal: 'center',
                    vertical: 'middle'
                };
                headerRow.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'FFD3D3D3'
                    }
                };

                // إضافة بيانات الجدول إلى ورقة العمل
                const rows = [];
                document.querySelectorAll('tbody tr').forEach(row => {
                    const cols = row.querySelectorAll('td, th');

                    if (cols.length > 0) {
                        // التحقق من وجود rowspan (السطر الأول من مجموعة)
                        const hasRowspan = cols[0].hasAttribute('rowspan');
                        const hasColspan = cols.length >= 5 && cols[4].hasAttribute('colspan');

                        if (hasRowspan && !hasColspan) {
                            // السطر الأول من مجموعة (يحتوي على اسم الموظف والتاريخ)
                            rows.push({
                                employee_name: cols[0].innerText.trim(),
                                date: cols[1].innerText.trim(),
                                day: cols[2].innerText.trim(),
                                work_hours: cols[3].innerText.trim(),
                                location: cols[4].innerText.trim(),
                                work_period: cols[5].innerText.trim(),
                                earlyArrival: cols[6].innerText.trim(),
                                late: cols[7].innerText.trim(),
                                early_departure: cols[8].innerText.trim(),
                                overtime: cols[9].innerText.trim(),
                                bgColor: row.style.backgroundColor
                            });
                            // Handle the specific cell color
                            rows[rows.length - 1].earlyArrivalBgColor = cols[6].style.backgroundColor;
                        } else if (!hasRowspan && cols.length === 6) {
                            // صفوف إضافية في نفس المجموعة (مواقع متعددة)
                            rows.push({
                                employee_name: '', // فارغ لأنه جزء من rowspan
                                date: '',
                                day: '',
                                work_hours: '',
                                location: cols[0].innerText.trim(),
                                work_period: cols[1].innerText.trim(),
                                earlyArrival: cols[2].innerText.trim(),
                                late: cols[3].innerText.trim(),
                                early_departure: cols[4].innerText.trim(),
                                overtime: cols[5].innerText.trim(),
                                bgColor: row.style.backgroundColor
                            });
                            // Handle the specific cell color
                            rows[rows.length - 1].earlyArrivalBgColor = cols[2].style.backgroundColor;
                        } else if (hasColspan) {
                            // سطر الإجازة (colspan="6")
                            rows.push({
                                employee_name: cols[0].innerText.trim(),
                                date: cols[1].innerText.trim(),
                                day: cols[2].innerText.trim(),
                                work_hours: cols[3].innerText.trim(),
                                location: cols[4].innerText.trim(),
                                work_period: '',
                                earlyArrival: '',
                                late: '',
                                early_departure: '',
                                overtime: '',
                                bgColor: row.style.backgroundColor
                            });
                        }
                    }
                });

                // إضافة الصفوف وتنسيقها
                rows.forEach((rowData, index) => {
                    const excelRow = worksheet.addRow(rowData);

                    const rowFillColor = getFillColor(rowData.bgColor);
                    if (rowFillColor !== 'FFFFFFFF') {
                        excelRow.eachCell((cell) => {
                            cell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: {
                                    argb: rowFillColor
                                }
                            };
                        });
                    }

                    // تنسيق الخلايا
                    excelRow.eachCell((cell) => {
                        // Apply specific color for earlyArrival cell if it exists
                        if (cell.address.startsWith('G') && rowData.earlyArrivalBgColor) {
                            const earlyArrivalFillColor = getFillColor(rowData.earlyArrivalBgColor);
                            if (earlyArrivalFillColor !== 'FFFFFFFF') {
                                cell.fill = {
                                    type: 'pattern',
                                    pattern: 'solid',
                                    fgColor: { argb: earlyArrivalFillColor }
                                };
                            }
                        }
                        cell.alignment = {
                            horizontal: 'center',
                            vertical: 'middle'
                        };
                        cell.border = {
                            top: {
                                style: 'thin'
                            },
                            left: {
                                style: 'thin'
                            },
                            bottom: {
                                style: 'thin'
                            },
                            right: {
                                style: 'thin'
                            }
                        };
                    });
                });

                // إنشاء دالة لتحميل الملف
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'بيانات_الحضور.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 300);
        }
    </script>
@endsection
