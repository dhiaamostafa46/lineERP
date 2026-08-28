<html lang="en" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>

    <base href="" />
    <title>@lang('lang.sign_in') | {{ env('APP_NAME') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <link rel="canonical" href="{{ url('/') }}" />
    <link rel="icon" type="image/x-icon" href="url({{ asset('admin_assets') }}/media/logos/newevixicon.ico)" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->


    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('admin_assets') }}/css/style.bundle.rtl.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.rtl.css" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
            type="text/css" />
    @endif

    {{-- <link href="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" /> --}}
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
     @include('flash::message')
    @yield('body')



    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.js"></script>
    <script src="{{ asset('admin_assets') }}/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="{{ asset('admin_assets') }}/js/custom/authentication/sign-in/general.js"></script>

    <script>
        // تحسين التحقق عند إدخال كلمة المرور
        // ربط حدث الإدخال بحقل كلمة المرور
        document.querySelector('input[name="password"]').addEventListener('input', function(event) {
            const passwordValue = event.target.value;
            const meterSegments = document.querySelectorAll(
                '[data-kt-password-meter-control="highlight"] .flex-grow-1');

            // إعادة تعيين تلوين كل segment في كل إدخال
            meterSegments.forEach((segment) => {
                segment.classList.remove('bg-success'); // إزالة اللون الأخضر من جميع العناصر
            });

            // إضافة التلوين التدريجي عند كل إدخال رقم
            meterSegments.forEach((segment, index) => {
                if (index < passwordValue.length) { // تلوين العدد المطابق لعدد الأرقام المدخلة
                    segment.classList.add('bg-success');
                }
            });
        });
    </script>
      <script>
        // الاستماع لتغيرات الإدخال في كل حقل OTP
        document.querySelectorAll('input[name^="code_"]').forEach((input, index, inputs) => {
            input.addEventListener('input', function() {
                // إذا كان الحقل ليس فارغاً، انتقل للحقل التالي
                if (this.value !== '' && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // إضافة خاصية التنقل للخلف عند استخدام زر Backspace
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace' && this.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->

    <script src="//code.jquery.com/jquery.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>



<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>
</body>
<!--end::Body-->

</html>




























