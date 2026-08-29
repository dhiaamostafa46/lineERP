<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-center justify-content-between">
        
        <!-- Breadcrumb Links (with wire:navigate for instantaneous SPA transition) -->
        <div class="page-title d-flex flex-column justify-content-center">
            <h1 class="page-heading text-gray-900 fw-bold fs-4 my-0 d-flex align-items-center gap-2">
                @if(isset($icon))
                    <i class="{{ $icon }} fs-4 text-primary"></i>
                @endif
                <span>{{ $title }}</span>
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-8 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary" wire:navigate>
                        @lang('lang.dashboard')
                    </a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('basicdata.products.index') }}" class="text-muted text-hover-primary" wire:navigate>
                        @lang('basicdata::lang.basicdata')
                    </a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-4px h-1px mx-2"></span></li>
                <li class="breadcrumb-item text-muted">
                    @if(isset($indexRoute))
                        <a href="{{ route($indexRoute) }}" class="text-muted text-hover-primary" wire:navigate>
                            {{ $title }}
                        </a>
                    @else
                        {{ $title }}
                    @endif
                </li>
            </ul>
        </div>

        <!-- Header Actions: Icon-only buttons with tooltips -->
        <div class="d-flex align-items-center gap-2">
            @if(isset($permission))
                @can($permission)
                    <button type="button" 
                            class="btn btn-sm btn-icon btn-primary front-btn-primary rounded-circle shadow-xs" 
                            title="@lang('crud.add_new')" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            x-on:click="$dispatch('openCreateModal')" 
                            onclick="if(window.Livewire) Livewire.dispatch('openCreateModal'{{ isset($createType) ? ', ' . $createType : '' }})">
                        <i class="fa-solid fa-plus fs-7"></i>
                    </button>
                @endcan
            @endif
        </div>

    </div>
</div>
