@extends('auth.layouts')

@section('title', __('lang.forget_password'))

@section('body')
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Password reset -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_password_reset_form"
                            action="{{ route('password.OPT') }}" method="post">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-10">
                                <h1 class="text-gray-900 fw-bolder mb-3"> @lang('lang.forget_password') </h1>
                                <!--end::Title-->
                                <!--begin::Link-->
                                {{-- <div class="text-gray-500 fw-semibold fs-6"> @lang('lang.forget_password')</div> --}}
                                <!--end::Link-->
                            </div>
                            <div class="separator separator-content my-14">
                                <span class="w-125px text-gray-500 fw-semibold fs-7"> phone</span>
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->

                                <input type="tel" placeholder="phone" name="phone" autocomplete="off"
                                    class="form-control bg-transparent" />
                                @error('phone')
                                    <b class="text-danger">{{ $message }}</b>
                                @enderror
                                <!--end::Email-->
                            </div>
                            <!--begin::Actions-->
                            <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                                <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">@lang('lang.submit') </span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-light">@lang('lang.cancel') </a>
                            </div>
                            <!--end::Actions-->
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
        <!--end::Authentication - Password reset-->
    </div>
    <!--end::Root-->
@endsection
