@extends('auth.layouts')

@section('title', __('lang.enter_your_details_to_login'))

@section('body')

<style>
    .badge {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translate(-50%, -60%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }
</style>

    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->

                        {{-- عرض رسائل Flash في وسط الصفحة --}}
                        @if (session('success') || session('error') || session('warning') || session('info'))
                            <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999; min-width: 400px;">
                                @if (session('success'))
                                    <div class="badge badge-success badge-dismissible fade show shadow-lg text-center" role="badge">
                                        <i class="ki-duotone ki-check-circle fs-2 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <strong>{{ session('success') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="badge"></button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="badge badge-danger badge-dismissible fade show shadow-lg text-center" role="badge">
                                        <i class="ki-duotone ki-cross-circle fs-2 text-danger me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <strong>{{ session('error') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="badge"></button>
                                    </div>
                                @endif

                                @if (session('warning'))
                                    <div class="badge badge-warning badge-dismissible fade show shadow-lg text-center" role="badge">
                                        <i class="ki-duotone ki-information fs-2 text-warning me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <strong>{{ session('warning') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="badge"></button>
                                    </div>
                                @endif

                                @if (session('info'))
                                    <div class="badge badge-info badge-dismissible fade show shadow-lg text-center" role="badge">
                                        <i class="ki-duotone ki-information-5 fs-2 text-info me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <strong>{{ session('info') }}</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="badge"></button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form"
                            action="{{ route('authenticate') }}" method="POST">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3"> @lang('lang.welcome_to', ['app_name' => env('APP_NAME')]) </h1>
                                <!--end::Title-->
                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6"> @lang('lang.enter_your_details_to_login')</div>
                                <!--end::Subtitle=-->
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Login options-->
                            <div class="row g-3 mb-9">
                                <!--end::Col-->
                            </div>
                            <!--end::Login options-->
                            <!--begin::Separator-->
                            <div class="separator separator-content my-14">
                                <span class="w-125px text-gray-500 fw-semibold fs-7"></span>
                            </div>

                            <!--end::Separator-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Phone-->
                                <input class="form-control bg-transparent" type="number"
                                    placeholder="{{ __('admins.phone') }}" name="phone" />
                                @error('phone')
                                    <b class="text-danger">{{ $message }}</b>
                                @enderror
                                <!--end::Phone-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                <input class="form-control bg-transparent" type="password"
                                    placeholder="{{ __('admins.password') }}" name="password" />
                                @error('password')
                                    <b class="text-danger">{{ $message }}</b>
                                @enderror
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                <!--begin::Link-->
                                <a href="{{ route('reset.password') }}" class="link-primary"> @lang('lang.forget_password') </a>
                                <!--end::Link-->
                            </div>
                            <!--end::Wrapper-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary" style="background-color:#4bd5b5">
                                    <!--begin::Indicator label-->
                                    @lang('lang.sign_in')
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
                <!--begin::Footer-->
                @include('auth.components.contact')
                <!--end::Footer-->
            </div>
            <!--end::Body-->
            <!--begin::Aside-->
            @include('auth.components.bgi-image')
            <!--end::Aside-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->

    {{-- JavaScript لإخفاء الـ badge تلقائياً بعد 5 ثواني --}}
    @if (session('success') || session('error') || session('warning') || session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var badges = document.querySelectorAll('.badge');
                    badges.forEach(function(badge) {
                        var bsbadge = new bootstrap.badge(badge);
                        bsbadge.close();
                    });
                }, 5000); // 5 ثواني
            });
        </script>
    @endif
@endsection

