@extends('layouts.app')

@section('title', __('models/notifications.plural'))

@section('content')
    <style>
        .notif-title-style {
            font-weight: 800 !important;
            color: var(--bs-text-primary, #1E2B50) !important;
        }

        .notif-card-pill {
            transition: all 0.2s ease-in-out;
            border: 1px solid #E4E6EF;
            border-radius: 0.85rem;
            background: #ffffff;
        }

        .notif-card-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27, 50, 91, 0.08) !important;
        }

        .notif-card-pill.active {
            background: linear-gradient(135deg, var(--bs-primary-active, #1B325B) 0%, var(--bs-primary, #6A669D) 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(27, 50, 91, 0.2) !important;
        }

        .notif-card-pill.active i,
        .notif-card-pill.active span {
            color: #ffffff !important;
        }

        .notif-btn-theme {
            background-color: var(--bs-primary, #6A669D) !important;
            border-color: var(--bs-primary, #6A669D) !important;
            color: #ffffff !important;
        }

        .notif-btn-theme:hover {
            background-color: var(--bs-primary-active, #1B325B) !important;
            border-color: var(--bs-primary-active, #1B325B) !important;
            color: #ffffff !important;
        }

        /* Dark Mode */
        [data-bs-theme="dark"] .notif-card-pill {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        [data-bs-theme="dark"] .notif-title-style {
            color: #f1f5f9 !important;
        }

        [data-bs-theme="dark"] .notif-card-pill:not(.active) span.text-gray-800 {
            color: #cbd5e1 !important;
        }
    </style>

    <div class="d-flex flex-column flex-column-fluid py-2">

        <!-- Toolbar Header -->
        <div id="kt_app_toolbar" class="app-toolbar mb-4">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap gap-3">
                <div class="page-title d-flex flex-column justify-content-center">
                    <h1 class="page-heading d-flex notif-title-style fs-2 my-0 align-items-center">
                        <span class="symbol symbol-40px me-3 circle" style="background: rgba(106, 102, 157, 0.15);">
                            <span class="symbol-label">
                                <i class="ki-duotone ki-notification-on fs-2x" style="color: var(--bs-primary, #6A669D);">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                        </span>
                        @lang('models/notifications.plural')
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1 text-muted">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">
                                @lang('lang.dashboard')
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item fw-bold" style="color: var(--bs-text-primary, #1E2B50);">
                            @lang('models/notifications.plural')
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('notifications.markAllAsRead') }}" class="btn btn-sm notif-btn-theme rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check-double me-1"></i>
                        {{ __('models/notifications.mark_all_read') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <!-- Module Filter Grid Tabs -->
                <div class="row g-3 mb-5">
                    <!-- All Tab -->
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ route('notifications.index', request()->except(['module', 'page'])) }}"
                           class="card h-100 notif-card-pill text-decoration-none p-3 text-center d-flex flex-column align-items-center justify-content-center {{ !request('module') || request('module') == 'all' ? 'active' : '' }}">
                            <i class="fa-solid fa-layer-group fs-2 mb-2 {{ !request('module') || request('module') == 'all' ? 'text-white' : '' }}" style="{{ !request('module') || request('module') == 'all' ? '' : 'color: var(--bs-primary, #6A669D);' }}"></i>
                            <span class="fw-bold fs-7 mb-1 {{ !request('module') || request('module') == 'all' ? 'text-white' : 'text-gray-800' }}">
                                {{ __('models/notifications.all') }}
                            </span>
                            <span class="badge {{ !request('module') || request('module') == 'all' ? 'bg-white text-primary' : 'badge-light-primary' }} rounded-pill px-3 py-1 fw-bolder">
                                {{ $countsByModule['all'] ?? 0 }}
                            </span>
                        </a>
                    </div>

                    <!-- Individual Modules -->
                    @foreach ($modules as $key => $label)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['module' => $key])) }}"
                               class="card h-100 notif-card-pill text-decoration-none p-3 text-center d-flex flex-column align-items-center justify-content-center {{ request('module') == $key ? 'active' : '' }}">
                                <i class="fa-solid {{ $moduleIcons[$key] ?? 'fa-bell' }} fs-2 mb-2 {{ request('module') == $key ? 'text-white' : '' }}" style="{{ request('module') == $key ? '' : 'color: var(--bs-primary, #6A669D);' }}"></i>
                                <span class="fw-bold fs-7 mb-1 text-truncate w-100 {{ request('module') == $key ? 'text-white' : 'text-gray-800' }}">
                                    {{ $label }}
                                </span>
                                @if (($countsByModule[$key] ?? 0) > 0)
                                    <span class="badge {{ request('module') == $key ? 'bg-white text-primary' : 'badge-light-danger' }} rounded-pill px-3 py-1 fw-bolder">
                                        {{ $countsByModule[$key] }}
                                    </span>
                                @else
                                    <span class="badge {{ request('module') == $key ? 'bg-white text-primary' : 'badge-light-secondary' }} rounded-pill px-3 py-1 fw-bold">0</span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Filter Card -->
                <div class="card shadow-sm border-0 mb-5" style="border-radius: 0.85rem;">
                    <div class="card-header cursor-pointer border-0 py-3 {{ request()->anyFilled(['search', 'notification_type', 'status', 'user_id', 'start_date']) ? '' : 'collapsed' }}"
                         data-bs-toggle="collapse" data-bs-target="#kt_notifications_filter">
                        <div class="card-title m-0">
                            <span class="symbol symbol-35px me-2 circle" style="background: rgba(106, 102, 157, 0.15);">
                                <span class="symbol-label">
                                    <i class="fa-solid fa-sliders fs-6" style="color: var(--bs-primary, #6A669D);"></i>
                                </span>
                            </span>
                            <h3 class="fw-bold fs-6 notif-title-style m-0">@lang('crud.search')</h3>
                        </div>
                        <div class="card-toolbar">
                            <span class="btn btn-icon btn-sm btn-light-primary rounded-circle">
                                <i class="ki-duotone ki-down fs-2"></i>
                            </span>
                        </div>
                    </div>

                    <div id="kt_notifications_filter" class="collapse {{ request()->anyFilled(['search', 'notification_type', 'status', 'user_id', 'start_date']) ? 'show' : '' }}">
                        {!! Form::model(request()->all(), ['route' => 'notifications.index', 'method' => 'GET']) !!}
                        @if(request('module'))
                            {!! Form::hidden('module', request('module')) !!}
                        @endif
                        <div class="card-body py-4 border-top border-gray-200">
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    {!! Form::label('search', __('lang.search') . ':', ['class' => 'form-label fw-bold fs-7 text-gray-700']) !!}
                                    {!! Form::text('search', request('search'), ['class' => 'form-control form-control-solid rounded-3 fs-7', 'placeholder' => __('lang.search') . '...']) !!}
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    {!! Form::label('notification_type', __('models/notifications.fields.notification_type') . ':', ['class' => 'form-label fw-bold fs-7 text-gray-700']) !!}
                                    {!! Form::select('notification_type', ['' => __('lang.all')] + $types, request('notification_type'), ['class' => 'form-select form-select-solid rounded-3 fs-7']) !!}
                                </div>

                                <div class="col-md-2 col-sm-6">
                                    {!! Form::label('status', __('models/notifications.fields.status') . ':', ['class' => 'form-label fw-bold fs-7 text-gray-700']) !!}
                                    {!! Form::select('status', ['' => __('lang.all')] + $statuses, request('status'), ['class' => 'form-select form-select-solid rounded-3 fs-7']) !!}
                                </div>

                                <div class="col-md-2 col-sm-6">
                                    {!! Form::label('user_id', __('models/notifications.fields.notifiable_id') . ':', ['class' => 'form-label fw-bold fs-7 text-gray-700']) !!}
                                    {!! Form::select('user_id', ['' => __('lang.all')] + $users, request('user_id'), ['class' => 'form-select form-select-solid rounded-3 fs-7']) !!}
                                </div>

                                <div class="col-md-2 col-sm-6">
                                    {!! Form::label('pagination', __('crud.pagination') . ':', ['class' => 'form-label fw-bold fs-7 text-gray-700']) !!}
                                    {!! Form::select('pagination', config('statusSystem.pagination', [20 => 20, 50 => 50, 100 => 100]), request('pagination', 20), ['class' => 'form-select form-select-solid rounded-3 fs-7']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="card-footer py-3 d-flex justify-content-between bg-light rounded-bottom">
                            <button type="submit" class="btn btn-sm notif-btn-theme rounded-pill px-4 fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>
                                @lang('crud.search')
                            </button>

                            <a class="btn btn-sm btn-light-danger rounded-pill px-4 fw-bold" href="{{ route('notifications.index') }}">
                                <i class="fa-solid fa-rotate-left me-1"></i>
                                @lang('crud.reset')
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card shadow-sm border-0" style="border-radius: 0.85rem;">
                    @include('notifications.table')
                </div>

            </div>
        </div>
    </div>
@endsection
