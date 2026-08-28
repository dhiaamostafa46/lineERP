<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $name ?? 'Trial Balance' }}</title>
    <style>
        body { font-family: 'XBRiyaz', 'Amiri', 'aealarabiya', sans-serif; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; }
        .text-start { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        
        /* Hierarchy styling for PDF */
        .parent-account { font-weight: bold; background-color: #f9f9f9; }
        .leaf-row { font-weight: normal; }
        .account-code { color: #555; }
        /* Ignore JS icons in PDF */
        .toggle-icon, .fa-folder, .fa-file { display: none; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>{{ $name ?? 'Trial Balance' }}</h2>
        @if(!empty($fromDate) && !empty($toDate))
            <p>من: {{ $fromDate }} - إلى: {{ $toDate }}</p>
        @endif
    </div>

    @include('accusoft::report.trialBalance.table')
    
</body>
</html>
