<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $name }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 10px;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            background-color: #ffffff;
            color: #333333;
        }

        .report-container {
            padding: 20px;
            max-width: 100%;
        }

        /* Organization Info Table at Top */
        .org-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid #000000;
        }

        .org-info-table td {
            padding: 8px 15px;
            border: 1px solid #000000;
            vertical-align: middle;
            font-size: 12px;
        }

        .org-info-table .label-cell {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            width: 20%;
        }

        .org-info-table .value-cell {
            text-align: center;
            width: 30%;
        }

        .org-logo-cell {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            width: 15%;
        }

        .org-logo-cell img {
            max-height: 100px;
            max-width: 120px;
        }

        /* Report Title */
        .report-title-section {
            text-align: center;
            margin: 20px 0;
            padding: 10px 0;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #000000;
        }

        /* Data Table - Enhanced Design */
        .data-table-wrapper {
            overflow-x: auto;
            margin-top: 15px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border: 2px solid #000000;
            margin-top: 10px;
        }

        .data-table thead {
            background-color: #ffffff;
        }

        .data-table thead th {
            padding: 6px 8px;
            text-align: center;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #000000;
            vertical-align: middle;
            font-size: 10px;
        }

        /* Status Colors - Original Design */
        .bg-red {
            background-color: #FA9D94 !important;
            color: #000000 !important;
        }

        .bg-yellow {
            background-color: #FBFB86 !important;
            color: #000000 !important;
        }

        .bg-green {
            background-color: #82F591 !important;
            color: #000000 !important;
        }

        .work-period-cell,
        .fingerprint-cell {
            font-size: 9px;
            white-space: nowrap;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9px;
            color: #666666;
            border-top: 1px solid #dddddd;
            padding-top: 15px;
        }

        /* Legend Section */
        .legend-section {
            display: none;
        }

        /* Print optimizations */
        @media print {
            .report-container {
                padding: 10px;
            }

            .org-info-table {
                page-break-inside: avoid;
            }
        }

        /* No Data Message */
        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #666666;
        }
    </style>
</head>

<body>
    <div class="report-container">


        @php
            $logoBase64 = null;
            if (!empty($organization['logo_path'])) {
                $path = parse_url($organization['logo_path'], PHP_URL_PATH);
                $path = urldecode($path);
                $fullPath = public_path(ltrim($path, '/'));
                if (file_exists($fullPath)) {
                    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $logoData = file_get_contents($fullPath);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
                }
            }
        @endphp

        <!-- Organization Information Table -->
        <table class="org-info-table" style="border: 2px solid #000;">
            <tr>
                <td rowspan="3" class="org-logo-cell" style="width: 20%; background-color: #ffffff; text-align: center;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo" style="max-height: 90px; max-width: 140px;">
                    @else
                        <div style="font-size: 14px; font-weight: bold;">{{ $organization['company_name'] ?? '' }}</div>
                    @endif
                </td>
                <td class="label-cell" style="width: 15%;">{{ __('hr::models/hr_report_types.AttendanceRecords_table.employee_name') }}</td>
                <td class="value-cell" style="width: 25%;">{{ $data[0]['employee_name'] ?? '' }}</td>

                <td class="label-cell" style="width: 15%;">{{ __('hr::models/hr_employees.fields.department_id') }}</td>
                <td class="value-cell" style="width: 25%;">{{ $data[0]['department'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ __('hr::models/hr_employees.fields.job_id') }}</td>
                <td class="value-cell">{{ $data[0]['job'] ?? '' }}</td>

                <td class="label-cell">{{ __('hr::models/hr_employees.fields.attendance_type') }}</td>
                <td class="value-cell">{{ $data[0]['attendance_type'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ __('hr::models/hr_report_types.fields.created_at') }}</td>
                <td class="value-cell">{{ \Carbon\Carbon::now()->translatedFormat('Y-m-d') }}</td>

                <td class="label-cell">{{ __('hr::models/hr_report_types.AttendanceRecords_table.date') }}</td>
                <td class="value-cell">
                    @php
                        use Carbon\Carbon;
                        $startDate = request()->filled('start_date')
                            ? Carbon::parse(request('start_date'))
                            : Carbon::now()->startOfMonth();
                        $endDate = request()->filled('end_date')
                            ? Carbon::parse(request('end_date'))
                            : Carbon::now()->endOfMonth();
                    @endphp
                    <span style="direction: ltr; display: inline-block;">{{ $startDate->translatedFormat('Y-m-d') }} - {{ $endDate->translatedFormat('Y-m-d') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center; font-size: 16px; font-weight: bold; background-color: #e9ecef; padding: 12px; border-top: 2px solid #000;">
                    {{ $organization['company_name'] ?? '' }} - {{ $name }}
                </td>
            </tr>
        </table>

        <!-- Report Title -->


        <!-- Data Table -->
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (count($data) > 0)
                        @foreach ($data as $row)
                            @php
                                $rowClass = '';
                                if (!empty($row['row_bg_color'])) {
                                    if ($row['row_bg_color'] == '#E42312') {
                                        $rowClass = 'background-color:#FA9D94';
                                    } elseif ($row['row_bg_color'] == '#FFFF00') {
                                        $rowClass = 'background-color:#FBFB86';
                                    } elseif ($row['row_bg_color'] == '#12E42E') {
                                        $rowClass = 'background-color:#82F591';
                                    }
                                }

                                $itemClass = '';
                                if (!empty($row['item_bg_color'])) {
                                    if ($row['item_bg_color'] == '#E42312') {
                                        $itemClass = 'background-color:#FA9D94';
                                    } elseif ($row['item_bg_color'] == '#12E42E') {
                                        $itemClass = 'background-color:#82F591';
                                    }
                                }
                            @endphp
                            <tr style="{{ $rowClass }}">
                                @if ($row['employee_name'] !== null)
                                    {{-- <td @if ($row['rowspan_count'] > 0) rowspan="{{ $row['rowspan_count'] }}" @endif>
                                        {{ $row['employee_name'] }}</td> --}}
                                    <td @if ($row['rowspan_count'] > 0) rowspan="{{ $row['rowspan_count'] }}" @endif>
                                        {{ $row['date'] }}</td>
                                    {{-- <td @if ($row['rowspan_count'] > 0) rowspan="{{ $row['rowspan_count'] }}" @endif>
                                        {{ $row['day'] }}</td> --}}
                                    <td @if ($row['rowspan_count'] > 0) rowspan="{{ $row['rowspan_count'] }}" @endif>
                                        {{ $row['work_hours'] }}</td>
                                    <td @if ($row['rowspan_count'] > 0) rowspan="{{ $row['rowspan_count'] }}" @endif>
                                        {{ $row['status'] }}</td>
                                @endif

                                @if (isset($row['no_details']) && $row['no_details'])
                                    <td colspan="8" style="{{ $rowClass }}"></td>
                                @else
                                    <td style="{{ $itemClass }}">{{ $row['location'] }}</td>
                                    <td style="{{ $itemClass }}">{{ $row['detail_status'] }}</td>
                                    <td class="work-period-cell" style="{{ $rowClass }}">{{ $row['work_period'] }}
                                    </td>
                                    <td class="fingerprint-cell" style="{{ $rowClass }}">{{ $row['fingerprint'] }}
                                    </td>
                                    <td style="{{ $itemClass }}">{{ $row['early_arrival'] }}</td>
                                    <td style="{{ $itemClass }}">{{ $row['late'] }}</td>
                                    <td style="{{ $itemClass }}">{{ $row['early_departure'] }}</td>
                                    <td style="{{ $itemClass }}">{{ $row['overtime'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ count($headers) }}" class="no-data-message">
                                {{ __('No data available') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Footer with Signatures -->

    </div>
</body>

</html>
