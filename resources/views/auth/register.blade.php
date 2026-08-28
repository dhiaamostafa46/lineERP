@extends('auth.layouts')

@section('title', __('lang.reset_password_subtitle'))

@section('body')

{{-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <div class="w-lg-500px p-10">
                    <form class="form w-100" novalidate="novalidate" method="post" action="{{ route('auth.AuthRegister') }}">
                        @csrf
                        <div class="text-center mb-11">
                            <h1 class="text-gray-900 fw-bolder mb-3">@lang('lang.welcome_to', ['app_name' => env('APP_NAME')])</h1>
                            <div class="text-gray-500 fw-semibold fs-6">@lang('lang.sign_up')</div>
                        </div>

                        <div class="separator separator-content my-14">
                            <span class="w-125px text-gray-500 fw-semibold fs-7">@lang('lang.create_new_account')</span>
                        </div>

                        <div class="fv-row mb-8">
                            <label for="name" class="form-label">@lang('lang.name')</label>
                            <input type="text" placeholder="name" name="name" id="name" autocomplete="off" class="form-control bg-transparent" />
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-8">
                            <label for="email" class="form-label">@lang('lang.email')</label>
                            <input type="email" placeholder="Email" name="email" id="email" autocomplete="off" class="form-control bg-transparent" />
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-8">
                            <label for="phone" class="form-label">@lang('lang.phone')</label>
                            <input type="tel" placeholder="Phone" name="phone" id="phone" autocomplete="off" class="form-control bg-transparent" required />
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-8" data-kt-password-meter="true">
                            <label for="password" class="form-label">@lang('lang.password')</label>
                            <div class="mb-1">
                                <div class="position-relative mb-3">
                                    <input class="form-control bg-transparent" type="password" placeholder="Password" name="password" id="password" autocomplete="off" />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                        <i class="ki-duotone ki-eye-slash fs-2"></i>
                                        <i class="ki-duotone ki-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                </div>
                            </div>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-8">
                            <label for="confirm-password" class="form-label">@lang('lang.confirm_new_password')</label>
                            <input placeholder="Repeat Password" name="password_confirmation" type="password" id="confirm-password" autocomplete="off" class="form-control bg-transparent" />
                            @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="fv-row mb-8">
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="toc" value="1" />
                                <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">@lang('lang.remember_me')</span>
                            </label>
                        </div>

                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                                <span class="indicator-label">@lang('lang.sign_up')</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                        <div class="text-gray-500 text-center fw-semibold fs-6">@lang('lang.already_have_an_account')
                            <a href="{{ route('login') }}" class="link-primary fw-semibold">@lang('lang.sign_in')</a>
                        </div>
                    </form>
                </div>
            </div>
            @include('auth.components.contact')
        </div>
        @include('auth.components.bgi-image')
    </div>
</div>
@endsection
