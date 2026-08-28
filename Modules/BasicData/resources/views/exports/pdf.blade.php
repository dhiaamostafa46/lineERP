<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $name ?? 'Report' }}</title>
    <style>
        body {
            font-family: 'Cairo', 'xbriyaz', 'freeserif', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }

        .header-container {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }

        .meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 8px 10px;
            border: 1px solid #1d4ed8;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        td {
            padding: 7px 10px;
            font-size: 10px;
            color: #334155;
            border: 1px solid #e2e8f0;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .footer {
            margin-top: 20px;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table style="width: 100%; margin: 0; border: none;">
            <tr style="background: transparent;">
                <td style="border: none; padding: 0; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};">
                    <div class="title">{{ $name }}</div>
                    <div class="meta">تاريخ التصدير: {{ $date ?? now()->format('Y-m-d H:i') }} | نظام Evix ERP</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        تم استخراج هذا التقرير تلقائياً بواسطة Evix ERP &copy; {{ date('Y') }}
    </div>

</body>
</html>
