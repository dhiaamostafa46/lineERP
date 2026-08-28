@extends('layouts.app')

@section('title', __('models/applications.plural'))

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
                        <i class="ki-duotone ki-element-11 fs-1 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        @lang('models/applications.plural')
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
                    <li class="breadcrumb-item text-gray-900">
                        @lang('models/applications.title')
                    </li>
                </ul>
            </div>
            <!--end::Page title-->

            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" id="btnSyncHub" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-2 shadow-xs">
                    <i class="ki-duotone ki-arrows-circle fs-4" id="syncIcon">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span>@lang('models/applications.hub_sync_btn')</span>
                </button>
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

            <!--begin::Hub Status Banner-->
            <div class="card border-0 bg-light-primary mb-6 shadow-xs">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-45px symbol-circle bg-white shadow-xs p-2 d-flex align-items-center justify-content-center">
                                <i class="ki-duotone ki-cloud fs-2 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h4 class="fs-6 fw-bold text-gray-900 mb-0">@lang('models/applications.hub_hub_title')</h4>
                                    <span class="badge badge-light-success fw-bold fs-8">
                                        <span class="bullet bullet-dot bg-success me-1"></span> {{ $hubStatus['base_url'] }}
                                    </span>
                                </div>
                                <p class="text-muted fs-8 mb-0 mt-1">@lang('models/applications.hub_sync_desc')</p>
                            </div>
                        </div>

                        <div class="text-muted fs-8 text-md-end">
                            <span class="fw-semibold text-gray-700">@lang('models/applications.fields.environment'):</span>
                            <span class="badge badge-secondary fw-bold text-uppercase fs-8">{{ $hubStatus['environment'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Hub Status Banner-->

            <!--begin::Stats Row-->
            <div class="row g-4 mb-6">
                <!-- Total Platforms -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 shadow-xs border-0 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-gray-600 fw-bold fs-7">@lang('models/applications.stats_total')</span>
                                <div class="symbol symbol-30px symbol-circle bg-light-primary">
                                    <i class="ki-duotone ki-element-11 fs-4 text-primary p-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="fs-2x fw-bolder text-gray-900 me-2">{{ $stats['total'] }}</span>
                                <span class="text-muted fs-8">@lang('models/applications.singular')</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Integrations -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 shadow-xs border-0 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-gray-600 fw-bold fs-7">@lang('models/applications.stats_active')</span>
                                <div class="symbol symbol-30px symbol-circle bg-light-success">
                                    <i class="ki-duotone ki-check-circle fs-4 text-success p-2"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="fs-2x fw-bolder text-success me-2" id="statActiveCount">{{ $stats['active'] }}</span>
                                <span class="text-muted fs-8">@lang('lang.active')</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Connected Integrations -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 shadow-xs border-0 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-gray-600 fw-bold fs-7">@lang('models/applications.stats_connected')</span>
                                <div class="symbol symbol-30px symbol-circle bg-light-info">
                                    <i class="ki-duotone ki-shield-tick fs-4 text-info p-2"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="fs-2x fw-bolder text-info me-2">{{ $stats['connected'] }}</span>
                                <span class="text-muted fs-8">@lang('models/applications.statuses.connected')</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Needs Config -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 shadow-xs border-0 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-gray-600 fw-bold fs-7">@lang('models/applications.stats_need_config')</span>
                                <div class="symbol symbol-30px symbol-circle bg-light-warning">
                                    <i class="ki-duotone ki-setting-2 fs-4 text-warning p-2"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="fs-2x fw-bolder text-warning me-2">{{ $stats['need_config'] }}</span>
                                <span class="text-muted fs-8">@lang('models/applications.statuses.inactive')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Stats Row-->

            <!--begin::Search and Filters Card-->
            <div class="card shadow-sm my-3 border-0 bg-white">
                <div class="card-header collapsible cursor-pointer rotate {{ request()->has('search') || request()->has('category') || request()->has('status') ? 'active' : 'collapsed' }}"
                     data-bs-toggle="collapse" data-bs-target="#applicationsFilterCollapse"
                     aria-expanded="{{ request()->has('search') || request()->has('category') || request()->has('status') ? 'true' : 'false' }}">
                    <h3 class="card-title">
                        <i class="fa-solid fa-filter fs-2 me-2"></i>
                        @lang('crud.search')
                    </h3>
                    <div class="card-toolbar rotate-180">
                        <i class="ki-duotone ki-down fs-1"></i>
                    </div>
                </div>

                <div id="applicationsFilterCollapse" class="collapse show">
                    <form action="{{ route('applications.index') }}" method="GET" id="filterForm">
                        <div class="card-body py-4">
                            <div class="row g-3">
                                <!-- Search Input -->
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-bold text-gray-700 fs-7">@lang('models/applications.fields.name'):</label>
                                    <div class="position-relative">
                                        <i class="fa-solid fa-magnifying-glass fs-6 text-gray-500 position-absolute top-50 translate-middle-y ms-3"></i>
                                        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid ps-9 fs-7" placeholder="@lang('lang.searching')...">
                                    </div>
                                </div>

                                <!-- Status Dropdown -->
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-bold text-gray-700 fs-7">@lang('models/applications.fields.status'):</label>
                                    <select name="status" class="form-select form-select-solid fs-7" onchange="document.getElementById('filterForm').submit();">
                                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>@lang('models/applications.statuses.all')</option>
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>@lang('models/applications.statuses.active')</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>@lang('models/applications.statuses.inactive')</option>
                                        <option value="connected" {{ $status === 'connected' ? 'selected' : '' }}>@lang('models/applications.statuses.connected')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Category Pills -->
                            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top border-gray-100">
                                @php
                                    $categoriesList = [
                                        'all' => __('models/applications.categories.all'),
                                        'ecommerce' => __('models/applications.categories.ecommerce'),
                                        'payment_gateway' => __('models/applications.categories.payment_gateway'),
                                        'shipping' => __('models/applications.categories.shipping'),
                                        'government' => __('models/applications.categories.government'),
                                        'messaging' => __('models/applications.categories.messaging'),
                                        'accounting' => __('models/applications.categories.accounting'),
                                        'hr' => __('models/applications.categories.hr'),
                                        'internal_engine' => __('models/applications.categories.internal_engine'),
                                    ];
                                @endphp

                                @foreach($categoriesList as $catKey => $catName)
                                    <a href="{{ route('applications.index', array_merge(request()->query(), ['category' => $catKey])) }}"
                                       class="badge {{ $category === $catKey ? 'badge-primary fw-bolder shadow-xs' : 'badge-light-secondary text-gray-700' }} fs-7 py-2 px-3 text-decoration-none">
                                        {{ $catName }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer py-3 d-flex align-items-center justify-content-between">
                            <button type="submit" class="btn btn-sm btn-search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                @lang('crud.search')
                            </button>
                            <a class="btn btn-sm btn-primary" href="{{ route('applications.index') }}">
                                <i class="fa-solid fa-circle-xmark"></i>
                                @lang('crud.reset')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Search and Filters Card-->

            <!--begin::Applications Grid-->
            @if(empty($integrations))
                <div class="card shadow-sm my-3 border-0 bg-white">
                    <div class="card-body p-12 text-center">
                        <div class="symbol symbol-50px symbol-circle bg-light-warning mb-3">
                            <i class="ki-duotone ki-information fs-2 text-warning p-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </div>
                        <h4 class="fs-6 fw-bold text-gray-800 mb-1">@lang('lang.no_results')</h4>
                        <p class="text-muted fs-7 mb-4">لا توجد تطبيقات مطابقة لمعايير البحث حالياً.</p>
                        <a href="{{ route('applications.index') }}" class="btn btn-sm btn-light-primary">
                            @lang('crud.reset')
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-4 g-xl-6">
                    @foreach($integrations as $app)
                        @php
                            $categoryColors = [
                                'ecommerce' => 'badge-light-primary',
                                'payment_gateway' => 'badge-light-success',
                                'payments' => 'badge-light-success',
                                'shipping' => 'badge-light-warning',
                                'delivery' => 'badge-light-warning',
                                'government' => 'badge-light-danger',
                                'messaging' => 'badge-light-info',
                                'accounting' => 'badge-light-dark',
                                'hr' => 'badge-light-primary',
                                'internal_engine' => 'badge-light-secondary',
                                'other' => 'badge-light-secondary',
                            ];
                            $catBadgeClass = $categoryColors[$app->category] ?? 'badge-light-secondary';
                        @endphp

                        <div class="col-md-6 col-xl-4 app-card-wrapper" id="app-card-{{ $app->code }}">
                            <div class="card h-100 shadow-xs border border-gray-200 border-hover-primary d-flex flex-column justify-content-between position-relative overflow-hidden bg-white">
                                <!-- Status Accent Line -->
                                <div class="position-absolute top-0 start-0 end-0 h-3px {{ $app->is_active ? 'bg-success' : 'bg-transparent' }} status-bar"></div>

                                <div class="card-body p-5">
                                    <!-- Card Header -->
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="symbol symbol-50px symbol-rounded bg-light border p-2 d-flex align-items-center justify-content-center shadow-xs overflow-hidden">
                                                @if(!empty($app->logo_url))
                                                    <img src="{{ $app->logo_url }}" alt="{{ $app->name }}" class="mh-35px mw-35px object-fit-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div style="display: none;" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                        {!! \App\Helpers\PlatformLogoHelper::getBrandBadge($app->name, $app->category) !!}
                                                    </div>
                                                @else
                                                    {!! \App\Helpers\PlatformLogoHelper::getBrandBadge($app->name, $app->category) !!}
                                                @endif
                                            </div>

                                            <div>
                                                <h4 class="fs-6 fw-bold text-gray-900 mb-1">
                                                    <a href="javascript:void(0);" onclick="openConfigModal('{{ $app->code }}')" class="text-gray-900 text-hover-primary">
                                                        {{ $app->name }}
                                                    </a>
                                                </h4>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $catBadgeClass }} fs-8 fw-semibold py-1 px-2">
                                                        {{ __('models/applications.categories.' . $app->category) }}
                                                    </span>
                                                    @if($app->supports_oauth)
                                                        <span class="badge badge-light-info fs-9 py-0 px-1">OAuth 2.0</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Switch -->
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input h-18px w-30px app-toggle-switch cursor-pointer"
                                                   type="checkbox"
                                                   data-code="{{ $app->code }}"
                                                   {{ $app->is_active ? 'checked' : '' }}
                                                   title="{{ $app->is_active ? __('models/applications.deactivate') : __('models/applications.activate') }}" />
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-gray-600 fs-7 mb-3 min-h-35px line-clamp-2">
                                        {{ $app->description ?: 'تكامل وربط برمجي مباشر مع ' . $app->name }}
                                    </p>

                                    <!-- Features Tags -->
                                    @if(!empty($app->supported_features) && is_array($app->supported_features))
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            @foreach(array_slice($app->supported_features, 0, 3) as $feature)
                                                <span class="badge badge-light fs-9 text-muted px-2 py-1">{{ str_replace('_', ' ', $feature) }}</span>
                                            @endforeach
                                            @if(count($app->supported_features) > 3)
                                                <span class="badge badge-light fs-9 text-muted px-1">+{{ count($app->supported_features) - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Status Footer -->
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-100">
                                        <div class="status-badge-container">
                                            @if($app->is_active)
                                                <span class="badge badge-light-success d-flex align-items-center gap-1 fs-8 fw-bold py-1 px-2">
                                                    <span class="bullet bullet-dot bg-success"></span>
                                                    @lang('models/applications.statuses.active')
                                                </span>
                                            @else
                                                <span class="badge badge-light-secondary text-muted d-flex align-items-center gap-1 fs-8 fw-bold py-1 px-2">
                                                    <span class="bullet bullet-dot bg-gray-400"></span>
                                                    @lang('models/applications.statuses.inactive')
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-muted fs-8">
                                            @if($app->last_connected_at)
                                                <span>{{ \Carbon\Carbon::parse($app->last_connected_at)->diffForHumans() }}</span>
                                            @else
                                                <span>@lang('models/applications.never_connected')</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer Action -->
                                <div class="card-footer bg-light-subtle py-3 px-5 border-top">
                                    <button type="button" onclick="openConfigModal('{{ $app->code }}')" class="btn btn-sm {{ $app->is_active ? 'btn-light-success' : 'btn-light-primary' }} w-100 fw-bold d-flex align-items-center justify-content-center gap-2 card-action-btn">
                                        <i class="ki-duotone ki-setting-3 fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                        <span>{{ $app->is_active ? __('models/applications.configure') : __('models/applications.activate') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <!--end::Applications Grid-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>

<!--begin::Modal - Application Activation & Configuration-->
<div class="modal fade" id="kt_modal_app_config" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 border-0 shadow-lg bg-white">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-45px symbol-rounded bg-light border p-2 d-flex align-items-center justify-content-center shadow-xs overflow-hidden" id="modalAppLogoContainer">
                        <img src="" id="modalAppLogo" class="mh-30px mw-30px object-fit-contain" alt="">
                    </div>
                    <div>
                        <h3 class="fw-bold text-gray-900 mb-0" id="modalAppName">إعدادات التطبيق</h3>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge badge-light-primary fs-8 fw-semibold" id="modalAppCategory"></span>
                            <span class="badge badge-light-info fs-8 fw-semibold d-none" id="modalAppOauth">OAuth 2.0</span>
                            <span class="badge badge-light-secondary fs-8" id="modalAppVersion"></span>
                        </div>
                    </div>
                </div>

                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-6 px-lg-8 pt-4 pb-6">
                <!-- Loader inside modal -->
                <div id="modalLoadingState" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted fs-7 mt-3">جاري تحميل بيانات ومخطط المنصة...</p>
                </div>

                <!-- Form Content -->
                <form id="modalConfigForm" style="display: none;">
                    @csrf
                    <input type="hidden" id="modalAppCode" name="code" value="">

                    <!-- Description -->
                    <p class="text-gray-600 fs-7 mb-4" id="modalAppDescription"></p>

                    <!-- OAuth Button if supported -->
                    <div id="modalOauthContainer" class="mb-4 d-none">
                        <a href="#" id="modalOauthBtn" target="_blank" class="btn btn-sm btn-light-primary fw-bold d-flex align-items-center justify-content-center gap-2 w-100 py-3">
                            <i class="ki-duotone ki-shield-tick fs-3"><span class="path1"></span><span class="path2"></span></i>
                            <span>المصادقة السريعة عبر حساب المنصة الرسمي (OAuth 2.0)</span>
                        </a>
                    </div>

                    <!-- Environment Mode Selector -->
                    <div class="mb-5 p-3 bg-light rounded border border-gray-200">
                        <label class="form-label fw-bold text-gray-800 fs-7 mb-2">@lang('models/applications.fields.environment'):</label>
                        <div class="d-flex flex-wrap gap-2">
                            <label class="d-flex align-items-center gap-2 p-2 rounded border border-dashed border-primary cursor-pointer bg-white flex-grow-1">
                                <input class="form-check-input" type="radio" name="environment" value="production" id="modalEnvProduction" checked>
                                <div>
                                    <span class="fw-bold text-gray-900 d-block fs-8">@lang('models/applications.live_mode')</span>
                                </div>
                            </label>

                            <label class="d-flex align-items-center gap-2 p-2 rounded border border-dashed border-secondary cursor-pointer bg-white flex-grow-1">
                                <input class="form-check-input" type="radio" name="environment" value="sandbox" id="modalEnvSandbox">
                                <div>
                                    <span class="fw-bold text-gray-900 d-block fs-8">@lang('models/applications.sandbox_mode')</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Schema Fields Container -->
                    <div class="mb-5">
                        <h4 class="fw-bold fs-7 text-gray-900 mb-3 d-flex align-items-center gap-2">
                            <i class="ki-duotone ki-key fs-4 text-primary"><span class="path1"></span><span class="path2"></span></i>
                            @lang('models/applications.credentials_section')
                        </h4>
                        <div class="row g-3" id="modalDynamicFields">
                            <!-- Injected by JavaScript dynamically -->
                        </div>
                    </div>

                    <!-- Webhook Container -->
                    <div class="p-3 bg-light-subtle rounded border border-gray-200 mb-4" id="modalWebhookBox">
                        <label class="form-label fw-bold text-gray-800 fs-8 mb-1">@lang('models/applications.fields.webhook_url'):</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="modalWebhookInput" class="form-control form-control-solid font-monospace fs-8" readonly>
                            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="modalCopyWebhookBtn">
                                <i class="ki-duotone ki-copy fs-5"><span class="path1"></span><span class="path2"></span></i>
                                <span id="modalCopyWebhookText">@lang('models/applications.copy_webhook')</span>
                            </button>
                        </div>
                        <span class="text-muted fs-9 mt-1 d-block">@lang('models/applications.webhook_instructions')</span>
                    </div>

                    <!-- Documentation Link -->
                    <div id="modalDocContainer" class="text-end mb-4 d-none">
                        <a href="#" id="modalDocLink" target="_blank" class="text-primary fs-8 fw-semibold">
                            <i class="ki-duotone ki-book-open fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            @lang('models/applications.documentation')
                        </a>
                    </div>

                    <!-- Actions Footer inside Modal -->
                    <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                        <button type="button" class="btn btn-sm btn-secondary fw-bold" data-bs-dismiss="modal">
                            @lang('crud.cancel')
                        </button>

                        <button type="submit" id="btnModalSubmit" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span id="btnModalSubmitText">@lang('models/applications.activate')</span>
                        </button>
                    </div>
                </form>
            </div>
            <!--end::Modal body-->
        </div>
    </div>
</div>
<!--end::Modal-->

@endsection

@push('scripts')
<script>
    var currentModalCode = '';

    // Function to Open and Populate Configuration Modal on the Same Page
    function openConfigModal(code) {
        currentModalCode = code;
        var modalEl = document.getElementById('kt_modal_app_config');
        var modal = new bootstrap.Modal(modalEl);

        // Reset UI state
        $('#modalLoadingState').show();
        $('#modalConfigForm').hide();
        $('#modalAppCode').val(code);

        modal.show();

        // Fetch dynamic platform details via AJAX
        $.ajax({
            url: '{{ url('applications') }}/' + code + '/details',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;

                    // Set Header Info
                    $('#modalAppName').text(data.name);
                    $('#modalAppCategory').text(data.category_name);
                    $('#modalAppVersion').text('v' + data.version);
                    $('#modalAppDescription').text(data.description);

                    if (data.logo_url) {
                        $('#modalAppLogo').attr('src', data.logo_url).show();
                    } else {
                        $('#modalAppLogo').hide();
                    }

                    if (data.supports_oauth && data.oauth_url) {
                        $('#modalAppOauth').removeClass('d-none');
                        $('#modalOauthBtn').attr('href', data.oauth_url);
                        $('#modalOauthContainer').removeClass('d-none');
                    } else {
                        $('#modalAppOauth').addClass('d-none');
                        $('#modalOauthContainer').addClass('d-none');
                    }

                    if (data.doc_url) {
                        $('#modalDocLink').attr('href', data.doc_url);
                        $('#modalDocContainer').removeClass('d-none');
                    } else {
                        $('#modalDocContainer').addClass('d-none');
                    }

                    // Environment
                    if (data.environment === 'sandbox') {
                        $('#modalEnvSandbox').prop('checked', true);
                    } else {
                        $('#modalEnvProduction').prop('checked', true);
                    }

                    // Webhook
                    $('#modalWebhookInput').val(data.webhook_url);

                    // Button Text
                    if (data.is_active) {
                        $('#btnModalSubmitText').text('حفظ وتحديث بيانات الربط');
                    } else {
                        $('#btnModalSubmitText').text('{{ __('models/applications.activate') }}');
                    }

                    // Render Dynamic Schema Fields
                    var fieldsHtml = '';
                    var schema = data.fields || [];
                    var credentials = data.masked_credentials || {};
                    var settings = data.settings || {};

                    if (schema.length === 0) {
                        fieldsHtml = '<div class="col-12"><div class="alert alert-info py-2 px-3 fs-8 mb-0">هذا التطبيق يعمل بشكل مباشر ولا يتطلب إدخال مفاتيح. يمكنك التفعيل فوراً.</div></div>';
                    } else {
                        schema.forEach(function(field) {
                            var key = field.key || field.name || '';
                            var label = '{{ app()->getLocale() }}' === 'en' ? (field.label_en || field.label || key) : (field.label_ar || field.label || key);
                            var type = field.type || 'text';
                            var required = field.required === true;
                            var placeholder = field.placeholder || '';
                            var description = field.description || field.hint || '';
                            var val = credentials[key] || settings[key] || field.default || '';

                            if (type === 'switch' || type === 'checkbox') {
                                var isChecked = val ? 'checked' : '';
                                fieldsHtml += '<div class="col-md-6">' +
                                    '<div class="d-flex align-items-center justify-content-between p-3 rounded border border-gray-200 bg-light-subtle h-100">' +
                                    '<label class="form-label fw-bold text-gray-800 fs-8 mb-0">' + label + '</label>' +
                                    '<div class="form-check form-switch form-check-custom form-check-solid">' +
                                    '<input class="form-check-input h-18px w-30px" type="checkbox" name="settings[' + key + ']" value="1" ' + isChecked + '>' +
                                    '</div></div></div>';
                            } else if (type === 'password' || type === 'secret') {
                                fieldsHtml += '<div class="col-md-6">' +
                                    '<label class="form-label fw-bold text-gray-800 fs-8 ' + (required ? 'required' : '') + '">' + label + ':</label>' +
                                    '<div class="position-relative">' +
                                    '<input type="password" name="credentials[' + key + ']" id="modal_f_' + key + '" value="' + val + '" class="form-control form-control-solid form-control-sm pe-8 font-monospace fs-8" placeholder="' + placeholder + '" autocomplete="new-password">' +
                                    '<button type="button" class="btn btn-icon btn-sm position-absolute top-50 translate-middle-y end-0 me-1 text-gray-500 toggle-password-btn" data-target="modal_f_' + key + '">' +
                                    '<i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>' +
                                    '</button></div></div>';
                            } else if (type === 'textarea') {
                                fieldsHtml += '<div class="col-12">' +
                                    '<label class="form-label fw-bold text-gray-800 fs-8 ' + (required ? 'required' : '') + '">' + label + ':</label>' +
                                    '<textarea name="credentials[' + key + ']" rows="2" class="form-control form-control-solid form-control-sm font-monospace fs-8" placeholder="' + placeholder + '">' + val + '</textarea>' +
                                    '</div>';
                            } else {
                                fieldsHtml += '<div class="col-md-6">' +
                                    '<label class="form-label fw-bold text-gray-800 fs-8 ' + (required ? 'required' : '') + '">' + label + ':</label>' +
                                    '<input type="' + (type === 'url' ? 'url' : (type === 'number' ? 'number' : 'text')) + '" name="credentials[' + key + ']" value="' + val + '" class="form-control form-control-solid form-control-sm fs-8" placeholder="' + placeholder + '">' +
                                    '</div>';
                            }
                        });
                    }

                    $('#modalDynamicFields').html(fieldsHtml);

                    // Show Form
                    $('#modalLoadingState').hide();
                    $('#modalConfigForm').show();
                }
            },
            error: function() {
                toastr.error('حدث خطأ أثناء جلب تفاصيل المنصة.');
                modal.hide();
            }
        });
    }

    $(document).ready(function() {
        // Toggle Password Visibility in Modal
        $(document).on('click', '.toggle-password-btn', function() {
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

        // Copy Webhook inside Modal
        $('#modalCopyWebhookBtn').on('click', function() {
            var copyText = document.getElementById("modalWebhookInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);

            var $btnText = $('#modalCopyWebhookText');
            $btnText.text('{{ __('models/applications.copied') }}');
            toastr.success('تم نسخ رابط الويب هوك بنجاح');

            setTimeout(function() {
                $btnText.text('{{ __('models/applications.copy_webhook') }}');
            }, 2000);
        });

        // Submit Form inside Modal via AJAX
        $('#modalConfigForm').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#btnModalSubmit');
            var code = $('#modalAppCode').val();
            var formData = $(this).serialize();

            $btn.prop('disabled', true);

            $.ajax({
                url: '{{ url('applications') }}/' + code + '/activate',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $btn.prop('disabled', false);
                    $('#kt_modal_app_config').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: response.message || 'تم تفعيل وربط المنصة بنجاح',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Instantly update card on the same page
                    var $card = $('#app-card-' + code);
                    $card.find('.status-bar').removeClass('bg-transparent').addClass('bg-success');
                    $card.find('.app-toggle-switch').prop('checked', true);
                    $card.find('.status-badge-container').html('<span class="badge badge-light-success d-flex align-items-center gap-1 fs-8 fw-bold py-1 px-2"><span class="bullet bullet-dot bg-success"></span> {{ __('models/applications.statuses.active') }}</span>');
                    $card.find('.card-action-btn').removeClass('btn-light-primary').addClass('btn-light-success').find('span').text('{{ __('models/applications.configure') }}');
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'فشل تفعيل التطبيق.';
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: msg
                    });
                }
            });
        });

        // Handle Active/Inactive Switch Toggle on Cards
        $('.app-toggle-switch').on('change', function() {
            var $switch = $(this);
            var code = $switch.data('code');
            var isChecked = $switch.is(':checked');

            $.ajax({
                url: '{{ url('applications') }}/' + code + '/toggle-status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_active: isChecked ? 1 : 0
                },
                dataType: 'json',
                success: function(response) {
                    toastr.success(response.message || 'تم تحديث الحالة بنجاح');
                    var $card = $('#app-card-' + code);

                    if (isChecked) {
                        $card.find('.status-bar').removeClass('bg-transparent').addClass('bg-success');
                        $card.find('.status-badge-container').html('<span class="badge badge-light-success d-flex align-items-center gap-1 fs-8 fw-bold py-1 px-2"><span class="bullet bullet-dot bg-success"></span> {{ __('models/applications.statuses.active') }}</span>');
                        $card.find('.card-action-btn').removeClass('btn-light-primary').addClass('btn-light-success').find('span').text('{{ __('models/applications.configure') }}');
                    } else {
                        $card.find('.status-bar').removeClass('bg-success').addClass('bg-transparent');
                        $card.find('.status-badge-container').html('<span class="badge badge-light-secondary text-muted d-flex align-items-center gap-1 fs-8 fw-bold py-1 px-2"><span class="bullet bullet-dot bg-gray-400"></span> {{ __('models/applications.statuses.inactive') }}</span>');
                        $card.find('.card-action-btn').removeClass('btn-light-success').addClass('btn-light-primary').find('span').text('{{ __('models/applications.activate') }}');
                    }
                },
                error: function() {
                    $switch.prop('checked', !isChecked);
                    toastr.error('فشل تحديث الحالة.');
                }
            });
        });

        // Handle Hub Sync
        $('#btnSyncHub').on('click', function() {
            var $btn = $(this);
            var $icon = $('#syncIcon');

            $btn.prop('disabled', true);
            $icon.addClass('spinner-border spinner-border-sm text-white');

            Swal.fire({
                title: '{{ __('models/applications.hub_sync_btn') }}',
                text: 'جاري جلب أحدث المنصات من Evix Hub...',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('applications.sync_hub') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت العملية',
                        text: response.message || 'تم التحديث بنجاح',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'حدث خطأ أثناء الاتصال مع Hub.';
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: msg
                    });
                    $btn.prop('disabled', false);
                    $icon.removeClass('spinner-border spinner-border-sm text-white');
                }
            });
        });
    });
</script>
@endpush
