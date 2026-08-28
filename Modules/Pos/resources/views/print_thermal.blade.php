<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Print Receipt' }}</title>
    @include('layouts.partials._styles')
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1500);">
    {!! $html !!}
</body>
</html>
