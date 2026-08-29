<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
            <div class="page-title d-flex flex-column justify-content-center">
                <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0">
                    {{ $isEdit ?? false ? __('crud.edit') : __('crud.create') }} {{ $title }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary" wire:navigate>
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route($indexRoute) }}" class="text-muted text-hover-primary" wire:navigate>
                            {{ $pluralTitle }}
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                    <li class="breadcrumb-item text-muted">{{ $isEdit ?? false ? __('crud.edit') : __('crud.create') }}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route($indexRoute) }}" class="btn btn-sm btn-icon btn-light rounded-circle shadow-xs" title="@lang('crud.back')" data-bs-toggle="tooltip" wire:navigate>
                    <i class="fa-solid fa-arrow-left fs-7 text-gray-700"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @include('adminlte-templates::common.errors')

            <div class="card border shadow-xs rounded-3">
                {!! Form::open(['route' => $actionRoute, 'method' => $method ?? 'POST', 'files' => true]) !!}
                    <div class="card-body p-6">
                        @include($fieldsView)
                    </div>
                    <div class="card-footer py-4 px-6 border-top d-flex justify-content-between align-items-center bg-light-subtle">
                        <a href="{{ route($indexRoute) }}" class="btn btn-sm btn-light fs-7 px-4 rounded-2" wire:navigate>
                            <i class="fa-solid fa-xmark fs-8 me-1"></i>
                            @lang('crud.cancel')
                        </a>
                        <button type="submit" class="btn btn-sm btn-save-gradient fs-7 px-5 rounded-2">
                            <i class="fa-solid fa-check fs-8 me-1"></i>
                            @lang('crud.save')
                        </button>
                    </div>
                {!! Form::close() !!}
            </div>

        </div>
    </div>
</div>
