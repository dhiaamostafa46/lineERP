@extends('auth.layouts')

@section('title', __('lang.enter_verification_code'))

@section('body')
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Two-factor -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Form-->
                        <form class="form w-100 mb-13" novalidate="novalidate"
                            action="{{ route('password.OPTCheck') }}" id="kt_sing_in_two_factor_form" method="post">
                            @csrf
                            <!--begin::Icon-->
                            {{-- <div class="text-center mb-10">
                                <img alt="Logo" class="mh-125px" src="assets/media/svg/misc/smartphone-2.svg" />
                            </div> --}}
                            <!--end::Icon-->
                            <!--begin::Heading-->

                            <input type="hidden" value="{{ $token }}" name="token">
                            <div class="text-center mb-10">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 mb-3"> @lang('lang.OTP')</h1>
                                <!--end::Title-->
                                <!--begin::Sub-title-->
                                <div class="text-muted fw-semibold fs-5 mb-5"> @lang('lang.enter_verification_code') </div>
                                <!--end::Sub-title-->
                                <!--begin::Mobile no-->
                                <div class="fw-bold text-gray-900 fs-3">{{ $phone }}</div>
                                <!--end::Mobile no-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Section-->
                            <div class="mb-10">
                                <!--begin::Label-->
                                <div class="fw-bold text-start text-gray-900 fs-6 mb-1 ms-1">@lang('lang.type_your_6_digit_security_code') </div>
                                <!--end::Label-->
                                <!--begin::Input group-->
                                <div class="d-flex flex-wrap flex-stack" dir="ltr">
                                    <input type="text" name="code_1" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                    <input type="text" name="code_2" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                    <input type="text" name="code_3" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                    <input type="text" name="code_4" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                    <input type="text" name="code_5" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                    <input type="text" name="code_6" data-inputmask="'mask': '9', 'placeholder': ''"
                                        maxlength="1"
                                        class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2"
                                        value="" />
                                </div>
                                <!--begin::Input group-->
                            </div>
                            <!--end::Section-->
                            <!--begin::Submit-->
                            <div class="d-flex flex-center">
                                <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">@lang('lang.submit') </span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit-->
                        </form>
                        <!--end::Form-->
                        <!--begin::Notice-->
                        <div class="text-center fw-semibold fs-5">
                            <span class="text-muted me-1">@lang('lang.did_not_get_code') </span> <a href="{{ route('login') }}"
                                class="btn btn-light">@lang('lang.cancel') </a>

                        </div>
                        <!--end::Notice-->
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
        <!--end::Authentication - Two-factor-->
    </div>


    @endsection
    <!--end::Root-->

