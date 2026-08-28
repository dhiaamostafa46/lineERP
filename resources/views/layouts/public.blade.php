<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', __('Privacy.main_title')) | {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', __('Privacy.intro_paragraph'))" />
    <link rel="icon" type="image/x-icon" href="{{ asset('admin_assets/media/logos/newevixicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('admin_assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @endif

    @stack('styles')
</head>
<body id="kt_body" class="app-blank bg-light">
    <div class="d-flex flex-column min-vh-100">
        <header class="border-bottom bg-white">
            <div class="container py-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a href="{{ route('login') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('admin_assets/media/logos/Logoevix.png') }}" alt="{{ config('app.name') }}" class="h-40px" />
                </a>

                <div class="d-flex align-items-center gap-4">
                    <div class="me-2">
                        <button class="btn btn-flex btn-link btn-color-gray-700 btn-active-color-primary rotate fs-base"
                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                            data-kt-menu-offset="0px, 0px" type="button">
                            <img class="w-20px h-20px rounded me-2"
                                src="{{ asset('admin_assets/media/flags/'.(app()->getLocale() === 'ar' ? 'saudi-arabia' : 'united-states').'.svg') }}"
                                alt="" />
                            <span class="me-1">{{ app()->getLocale() === 'ar' ? __('lang.arabic') : __('lang.english') }}</span>
                            <i class="ki-duotone ki-down fs-5 text-muted"><span class="path1"></span><span class="path2"></span></i>
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-4 fs-7"
                            data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('switchLang', 'en') }}" class="menu-link d-flex px-5 {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                                    <span class="symbol symbol-20px me-4">
                                        <img class="rounded-1" src="{{ asset('admin_assets/media/flags/united-states.svg') }}" alt="" />
                                    </span>
                                    @lang('lang.english')
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="{{ route('switchLang', 'ar') }}" class="menu-link d-flex px-5 {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                                    <span class="symbol symbol-20px me-4">
                                        <img class="rounded-1" src="{{ asset('admin_assets/media/flags/saudi-arabia.svg') }}" alt="" />
                                    </span>
                                    @lang('lang.arabic')
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="btn btn-sm btn-light-primary">@lang('lang.sign_in')</a>
                </div>
            </div>
        </header>

        <main class="flex-grow-1">
            @yield('content')
        </main>

        <footer class="border-top bg-white py-4 mt-auto">
            <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3 text-muted fs-7">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <div class="d-flex gap-4">
                    <a href="{{ route('privacy') }}" class="text-primary text-hover-dark">@lang('lang.privacy_policy')</a>
                    <a href="{{ route('login') }}" class="text-primary text-hover-dark">@lang('lang.sign_in')</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('admin_assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('admin_assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
