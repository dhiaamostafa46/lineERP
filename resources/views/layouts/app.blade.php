<!DOCTYPE html>
<html lang="en" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <title>@yield('title') || {{ config('app.name', 'LineERP') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="LineERP — Enterprise Resource Planning" />
    <meta name="keywords" content="ERP, CRM, HR, Accounting, Invoices, POS, Store" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="LineERP" />
    <meta property="og:site_name" content="LineERP" />
    <link rel="shortcut icon" href="{{ $setting->fav_icon_original_path ?? '' }}" />
    @include('layouts.partials._script_dark_mode')
    @include('layouts.partials._styles')
    @livewireStyles
    @stack('styles')

     {{-- @laravelPWA --}}
</head>

<body data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"
    class="app-default">


    @include('layouts.partials._loader')

    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            @include('layouts.partials.header')
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                @include('layouts.partials.sidebar')
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container">
                                @include('flash::message')
                                @yield('content')
                            </div>
                        </div>
                    </div>
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>
    </div>
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-outline ki-arrow-up"></i>
    </div>

    {{-- Scripts --}}
    @include('layouts.partials._scripts')
    @livewireScripts

<script data-navigate-once>
    window.confirmDelete = function ({
        id,
        event = 'delete-item',
        title = @json( __('messages.confirm_Del_title')),
        text = @json( __('messages.confirm_Del_text')),
        confirmText = @json( __('messages.confirm_Del_btn')),
        cancelText = @json( __('messages.cancel_btn')),
    }) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch(event, { id });
            }
        });
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('swal-success', (data) => {
            Swal.fire({
                icon: 'success',
                title: 'تم',
                text: data.message || data
            });
        });

        Livewire.on('swal-error', (data) => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: data.message || data
            });
        });
    });
</script>
    @stack('scripts')
</body>

</html>
