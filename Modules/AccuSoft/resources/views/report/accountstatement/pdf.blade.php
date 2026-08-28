<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $name ?? __('accusoft::models/as_reports.reports.account_statement') }}</title>
    <style>
        body { font-family: 'XBRiyaz', 'Amiri', 'aealarabiya', sans-serif; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; }
        .text-start { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>{{ $name ?? __('accusoft::models/as_reports.reports.account_statement') }}</h2>
    </div>

    @include('accusoft::report.accountstatement.table')
    
</body>
</html>
