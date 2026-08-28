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

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 12px;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            background-color: #ffffff;
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
        .report-container {
            padding: 0px;
        }

        /* Organization Info Table */
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
            width: 15%;
        }

        .org-info-table .value-cell {
            text-align: center;
            width: 35%;
        }

        .org-logo-cell {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            width: 15%;
        }

        .org-logo-cell img {
            max-height: 80px;
            max-width: 120px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            border: 1px solid #000000;
        }

        table.data-table th,
        table.data-table td {
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #000000;
        }

        table.data-table thead th {
            background-color: #f3f3f3;
            font-weight: bold;
            white-space: nowrap;
            word-break: keep-all;
            border: 1px solid #000000;
        }

        tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #aaaaaa;
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
                <td rowspan="2" class="org-logo-cell" style="width: 25%; background-color: #ffffff; text-align: center;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo" style="max-height: 90px; max-width: 140px;">
                    @else
                        <div style="font-size: 14px; font-weight: bold;">{{ $organization['company_name'] ?? '' }}</div>
                    @endif
                </td>
                <td class="label-cell" style="width: 15%;">{{ __('hr::models/hr_report_types.fields.created_at') }}</td>
                <td class="value-cell" style="width: 22%;">{{ \Carbon\Carbon::now()->translatedFormat('Y-m-d') }}</td>
                <td class="label-cell" style="width: 15%;">{{ __('hr::models/hr_report_types.AttendanceRecords_table.date') }}</td>
                <td class="value-cell" style="width: 23%;">
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
                <td colspan="4" style="text-align: center; font-size: 16px; font-weight: bold; background-color: #e9ecef; padding: 12px; border-top: 1px solid #000;">
                    {{ $organization['company_name'] ?? '' }} - {{ $name }}
                </td>
            </tr>
        </table>

        {{-- <table class="org-info-table">
            <tr>

                <td class="label-cell">{{ __('hr::models/hr_report_types.fields.name') }}</td>
                <td class="value-cell">{{ $name }}</td>
                <td class="label-cell">{{ __('hr::models/hr_report_types.fields.created_at') }}</td>
                <td class="value-cell">{{ \Carbon\Carbon::now()->translatedFormat('Y-m-d') }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ __('models/Organization.fields.organization_name') }}</td>
                <td class="value-cell">{{ $organization['company_name'] ?? '' }}</td>
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


                    {{ $startDate->translatedFormat('Y-m-d') }}
                    -
                    {{ $endDate->translatedFormat('Y-m-d') }}
                </td>
            </tr>
        </table> --}}

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
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ count($headers) }}" style="text-align: center; padding: 20px;">
                            {{ __('No data available') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>


    </div>

</body>

</html>
