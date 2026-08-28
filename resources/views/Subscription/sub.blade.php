@extends('layouts.app')

@section('title', __('models/Subscription.singular'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('models/Subscription.singular')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}"
                                class="text-muted
                            text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('Subscription.edit', 1) }}" class="text-muted text-hover-primary">
                                @lang('models/Subscription.plural')
                            </a>
                        </li>
                        <!--end::Item-->


                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->

                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                @include('adminlte-templates::common.errors')
                <div class="clearfix"></div>

                <div class="card">
                    <div class="card-body" dir="ltr">
                        <div class="row">

                            @if ($Subscription->payment_type == 2)
                                <form action="{{route('Subscription.paymentSubscriptionSave',$Subscription->id )}}" class="paymentWidgets" data-brands="VISA MASTER"></form>
                            @elseif ($Subscription->payment_type == 1)
                                <form action="{{route('Subscription.paymentSubscriptionSave',$Subscription->id )}}" class="paymentWidgets" data-brands="MADA"></form>
                            @elseif ($Subscription->payment_type == 3)
                               <form action="{{route('Subscription.paymentSubscriptionSave',$Subscription->id )}}" class="paymentWidgets" data-brands="APPLEPAY"></form>
                            @endif


                        </div>
                    </div>



                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>


    <script>
        var wpwlOptions = {
            locale: "en",
            paymentTarget: "_top",
            applePay: {
                displayName: "Eyein for Lenses & Glasses",
                total: { label: "EyeinCo, INC.", amount: "0.00" }, // Add a placeholder for amount
                supportedNetworks: ["masterCard", "visa", "mada"],
                supportedCountries: ["SA"],
                buttonStyle: "black",
                buttonType: "check-out",
                buttonSource: "js",
                version: 10
            }
        };
    </script>

    <!-- Include Apple Pay SDK -->
    <script src="https://applepay.cdn-apple.com/jsapi/v1.1.0/apple-pay-sdk.js"></script>
    <script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId={{ $Subscription->chachtoken }}"></script>

    <style>
        apple-pay-button {
            --apple-pay-button-width: 100%;
            --apple-pay-button-height: 50px;
            --apple-pay-button-border-radius: 5px;
        }
    </style>

@endsection
