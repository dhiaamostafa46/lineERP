@extends('layouts.app')

@section('title', ($platform['name'] ?? ucfirst($code)) . ' - ' . __('models/applications.configure'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap gap-4">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <span class="d-flex align-items-center">
                        <i class="ki-duotone ki-setting-3 fs-1 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        {{ $platform['name'] ?? ucfirst($code) }}
                    </span>
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('settings.edit', 1) }}" class="text-muted text-hover-primary">
                            @lang('models/settings.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('applications.index') }}" class="text-muted text-hover-primary">
                            @lang('models/applications.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">
                        {{ $platform['name'] ?? ucfirst($code) }}
                    </li>
                </ul>
            </div>
            <!--end::Page title-->

            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(!empty($platform['supports_oauth']) && !empty($platform['oauth_url']))
                    <a href="{{ $platform['oauth_url'] }}" target="_blank" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-2">
                        <i class="ki-duotone ki-shield-tick fs-4"><span class="path1"></span><span class="path2"></span></i>
                        <span>OAuth 2.0</span>
                    </a>
                @endif

                @if(!empty($platform['doc_url']))
                    <a href="{{ $platform['doc_url'] }}" target="_blank" class="btn btn-sm btn-light-info fw-bold d-flex align-items-center gap-2">
                        <i class="ki-duotone ki-book-open fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        @lang('models/applications.documentation')
                    </a>
                @endif

                <a href="{{ route('applications.index') }}" class="btn btn-sm btn-secondary fw-bold">
                    @lang('crud.cancel')
                </a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Platform Header Card-->
            <div class="card mb-6 shadow-sm border-0 bg-white">
                <div class="card-body p-5 p-lg-6">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                        <div class="d-flex align-items-center gap-4">
                            <div class="symbol symbol-55px symbol-rounded bg-light border p-2 d-flex align-items-center justify-content-center shadow-xs">
                                @if(!empty($platform['logo_url']))
                                    <img src="{{ $platform['logo_url'] }}" alt="{{ $platform['name'] ?? $code }}" class="mh-35px mw-35px object-fit-contain" onerror="this.src='{{ asset('admin_assets/media/svg/brand-logos/abstract-1.svg') }}'">
                                @else
                                    <i class="ki-duotone ki-cube-2 fs-2x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                @endif
                            </div>

                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h2 class="fs-5 fw-bold text-gray-900 mb-0">{{ $platform['name'] ?? ucfirst($code) }}</h2>
                                    <span class="badge badge-light-primary fw-bold fs-8">
                                        {{ __('models/applications.categories.' . ($platform['category'] ?? 'other')) }}
                                    </span>
                                    @if(!empty($platform['version']))
                                        <span class="badge badge-light-secondary fs-8">v{{ $platform['version'] }}</span>
                                    @endif
                                    @if(!empty($platform['activation_type']))
                                        <span class="badge badge-light-info fs-8 text-uppercase">{{ $platform['activation_type'] }}</span>
                                    @endif
                                </div>
                                <p class="text-gray-600 fs-7 mb-0">{{ $platform['description_ar'] ?? ($platform['description'] ?? 'ربط وتكامل مع ' . ($platform['name'] ?? $code)) }}</p>
                            </div>
                        </div>

                        <!-- Status Display -->
                        <div class="d-flex flex-column align-items-md-end gap-1">
                            @php
                                $isActive = $hubApp ? (bool)$hubApp->is_active : false;
                            @endphp

                            @if($isActive)
                                <span class="badge badge-light-success d-flex align-items-center gap-2 fs-7 fw-bold py-2 px-3">
                                    <span class="bullet bullet-dot bg-success h-8px w-8px"></span>
                                    @lang('models/applications.statuses.active')
                                </span>
                            @else
                                <span class="badge badge-light-secondary text-muted d-flex align-items-center gap-2 fs-7 fw-bold py-2 px-3">
                                    <span class="bullet bullet-dot bg-gray-400 h-8px w-8px"></span>
                                    @lang('models/applications.statuses.inactive')
                                </span>
                            @endif

                            <div class="text-muted fs-8">
                                @if($hubApp && $hubApp->last_connected_at)
                                    @lang('models/applications.last_connected', ['time' => $hubApp->last_connected_at->diffForHumans()])
                                @else
                                    @lang('models/applications.never_connected')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Platform Header Card-->

            <!--begin::Settings Form-->
            <form id="integrationForm" action="{{ route('applications.activate', $code) }}" method="POST">
                @csrf

                <div class="card mb-6 shadow-sm border-0 bg-white">
                    <div class="card-header border-0 pt-5">
                        <div class="card-title">
                            <h3 class="fw-bold fs-5 text-gray-900 d-flex align-items-center gap-2">
                                <i class="ki-duotone ki-key fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                @lang('models/applications.credentials_section')
                            </h3>
                        </div>
                    </div>

                    <div class="card-body p-5 p-lg-6 pt-0">

                        <!-- Environment Mode Selector -->
                        @php
                            $currentEnv = $hubApp ? ($hubApp->settings['environment'] ?? 'production') : 'production';
                        @endphp
                        <div class="mb-5 p-4 bg-light-subtle rounded border border-gray-200">
                            <label class="form-label fw-bold text-gray-800 fs-7 mb-2">@lang('models/applications.fields.environment'):</label>
                            <div class="d-flex flex-wrap gap-3">
                                <label class="d-flex align-items-center gap-3 p-3 rounded border border-dashed border-primary cursor-pointer bg-white flex-grow-1">
                                    <input class="form-check-input" type="radio" name="environment" value="production" {{ $currentEnv !== 'sandbox' ? 'checked' : '' }}>
                                    <div>
                                        <span class="fw-bold text-gray-900 d-block fs-7">@lang('models/applications.live_mode')</span>
                                        <span class="text-muted fs-8">الربط المباشر مع الحساب الفعلي.</span>
                                    </div>
                                </label>

                                <label class="d-flex align-items-center gap-3 p-3 rounded border border-dashed border-secondary cursor-pointer bg-white flex-grow-1">
                                    <input class="form-check-input" type="radio" name="environment" value="sandbox" {{ $currentEnv === 'sandbox' ? 'checked' : '' }}>
                                    <div>
                                        <span class="fw-bold text-gray-900 d-block fs-7">@lang('models/applications.sandbox_mode')</span>
                                        <span class="text-muted fs-8">بيئة تجريبية للاختبار والتحقق.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Schema Fields -->
                        @php
                            $credentials = $hubApp ? $hubApp->credentials : [];
                        @endphp

                        @if(!empty($schema))
                            <div class="row g-4">
                                @foreach($schema as $field)
                                    @php
                                        $key = $field['key'] ?? ($field['name'] ?? '');
                                        $label = app()->getLocale() === 'en'
                                            ? ($field['label_en'] ?? ($field['label'] ?? ucfirst($key)))
                                            : ($field['label_ar'] ?? ($field['label'] ?? ucfirst($key)));
                                        $type = $field['type'] ?? 'text';
                                        $required = !empty($field['required']) && $field['required'] === true;
                                        $placeholder = $field['placeholder'] ?? '';
                                        $description = $field['description'] ?? ($field['hint'] ?? '');
                                        $options = $field['options'] ?? [];
                                        $default = $field['default'] ?? null;

                                        $currentVal = $credentials[$key] ?? ($hubApp?->settings[$key] ?? $default);
                                        $maskedVal = $maskedCredentials[$key] ?? $currentVal;
                                    @endphp

                                    @if($type === 'switch' || $type === 'checkbox')
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-center justify-content-between p-3 rounded border border-gray-200 bg-light-subtle h-100">
                                                <div class="me-3">
                                                    <label class="form-label fw-bold text-gray-800 fs-7 mb-1 d-block">{{ $label }}</label>
                                                    @if($description)
                                                        <span class="text-muted fs-8">{{ $description }}</span>
                                                    @endif
                                                </div>
                                                <div class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input h-20px w-35px"
                                                           type="checkbox"
                                                           name="settings[{{ $key }}]"
                                                           value="1"
                                                           {{ !empty($currentVal) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($type === 'select')
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold text-gray-800 fs-7 {{ $required ? 'required' : '' }}">{{ $label }}:</label>
                                            <select name="credentials[{{ $key }}]" class="form-select form-select-solid fs-7" {{ $required ? 'required' : '' }}>
                                                @foreach($options as $optKey => $optLabel)
                                                    <option value="{{ $optKey }}" {{ (string)$currentVal === (string)$optKey ? 'selected' : '' }}>
                                                        {{ $optLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($description)
                                                <div class="form-text text-muted fs-8 mt-1">{{ $description }}</div>
                                            @endif
                                        </div>
                                    @elseif($type === 'textarea')
                                        <div class="col-12">
                                            <label class="form-label fw-bold text-gray-800 fs-7 {{ $required ? 'required' : '' }}">{{ $label }}:</label>
                                            <textarea name="credentials[{{ $key }}]"
                                                      rows="3"
                                                      class="form-control form-control-solid font-monospace fs-7"
                                                      placeholder="{{ $placeholder }}"
                                                      {{ $required ? 'required' : '' }}>{{ $currentVal }}</textarea>
                                            @if($description)
                                                <div class="form-text text-muted fs-8 mt-1">{{ $description }}</div>
                                            @endif
                                        </div>
                                    @elseif($type === 'password' || $type === 'secret')
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold text-gray-800 fs-7 {{ $required ? 'required' : '' }}">{{ $label }}:</label>
                                            <div class="position-relative">
                                                <input type="password"
                                                       name="credentials[{{ $key }}]"
                                                       id="field_{{ $key }}"
                                                       value="{{ $maskedVal }}"
                                                       class="form-control form-control-solid pe-12 fs-7 font-monospace"
                                                       placeholder="{{ $placeholder }}"
                                                       autocomplete="new-password"
                                                       {{ $required && empty($currentVal) ? 'required' : '' }}>
                                                <button type="button" class="btn btn-icon btn-sm position-absolute top-50 translate-middle-y end-0 me-2 text-gray-500 text-hover-primary toggle-password-btn" data-target="field_{{ $key }}">
                                                    <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                </button>
                                            </div>
                                            @if($description)
                                                <div class="form-text text-muted fs-8 mt-1">{{ $description }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold text-gray-800 fs-7 {{ $required ? 'required' : '' }}">{{ $label }}:</label>
                                            <input type="{{ $type === 'url' ? 'url' : ($type === 'number' ? 'number' : 'text') }}"
                                                   name="credentials[{{ $key }}]"
                                                   value="{{ $currentVal }}"
                                                   class="form-control form-control-solid fs-7"
                                                   placeholder="{{ $placeholder }}"
                                                   {{ $required ? 'required' : '' }}>
                                            @if($description)
                                                <div class="form-text text-muted fs-8 mt-1">{{ $description }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center p-4 border-0 bg-light-info">
                                <i class="ki-duotone ki-information fs-2 text-info me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <span class="fs-7 text-gray-800">هذا التطبيق لا يتطلب مفاتيح إضافية، يمكنك تفعيله مباشرة.</span>
                            </div>
                        @endif

                    </div>
                </div>

                <!--begin::Webhook Settings Card-->
                <div class="card mb-6 shadow-sm border-0 bg-white">
                    <div class="card-header border-0 pt-5">
                        <div class="card-title">
                            <h3 class="fw-bold fs-5 text-gray-900 d-flex align-items-center gap-2">
                                <i class="ki-duotone ki-fasten fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                Webhooks
                            </h3>
                        </div>
                    </div>
                    <div class="card-body p-5 p-lg-6 pt-0">
                        <p class="text-gray-600 fs-7 mb-3">@lang('models/applications.webhook_instructions')</p>

                        <label class="form-label fw-bold text-gray-800 fs-7">@lang('models/applications.fields.webhook_url'):</label>
                        <div class="input-group">
                            <input type="text" id="webhookUrlInput" class="form-control form-control-solid font-monospace fs-7" value="{{ $webhookUrl }}" readonly>
                            <button type="button" class="btn btn-light-primary fw-bold d-flex align-items-center gap-2" id="copyWebhookBtn">
                                <i class="ki-duotone ki-copy fs-4"><span class="path1"></span><span class="path2"></span></i>
                                <span id="copyWebhookText">@lang('models/applications.copy_webhook')</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Webhook Settings Card-->

                <!--begin::Action Buttons Footer-->
                <div class="card shadow-sm border-0 bg-white">
                    <div class="card-body p-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                        <a href="{{ route('applications.index') }}" class="btn btn-sm btn-secondary fw-bold">
                            @lang('crud.cancel')
                        </a>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" id="btnSaveSettings" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-2">
                                <i class="fa-solid fa-check"></i>
                                <span>@lang('models/applications.activate')</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Action Buttons Footer-->

            </form>
            <!--end::Settings Form-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle Password Visibility
        $('.toggle-password-btn').on('click', function() {
            var targetId = $(this).data('target');
            var $input = $('#' + targetId);
            var $icon = $(this).find('i');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('ki-eye').addClass('ki-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('ki-eye-slash').addClass('ki-eye');
            }
        });

        // Copy Webhook URL
        $('#copyWebhookBtn').on('click', function() {
            var copyText = document.getElementById("webhookUrlInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            var $btnText = $('#copyWebhookText');
            $btnText.text('{{ __('models/applications.copied') }}');
            toastr.success('تم نسخ رابط الويب هوك بنجاح');

            setTimeout(function() {
                $btnText.text('{{ __('models/applications.copy_webhook') }}');
            }, 2000);
        });
    });
</script>
@endpush
