<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ $setting->fav_icon_original_path ?? '' }}" />
    @include('layouts.partials._styles')
    <style>
        body {
            background-color: #f8f9fa;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .error-code {
            font-size: 12rem;
            font-weight: 900;
            color: #e0e0e0;
            letter-spacing: 1rem;
            line-height: 1;
        }
        .error-title {
            font-size: 2.5rem;
            margin-top: -3rem;
            color: #495057;
        }
        .error-message {
            font-size: 1.2rem;
            color: #6c757d;
        }
        .error-actions a {
            text-decoration: none;
        }
    </style>
</head>
<body class="app-default">
    <div class="container">
        <div class="error-container">
            <div>
                <div class="error-code">
                    @yield('code')
                </div>
                <div class="error-title">
                    @yield('title')
                </div>
                <p class="error-message">
                    @yield('message')
                </p>
                <div class="error-actions mt-4">
                    <a href="{{ app('router')->has('home') ? route('home') : url('/') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-home"></i>
                        @lang('errors.back_to_home')
                    </a>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.partials._scripts')
</body>
</html>
