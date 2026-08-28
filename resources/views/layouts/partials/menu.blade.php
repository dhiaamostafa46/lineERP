@push('scripts')
    <script>
        window.setTimeout(function() {
            $('.active').closest(".menu-accordion").addClass('hover show');
        }, 200);

        // Sidebar Menu Search Functionality
        $(document).ready(function() {
            $('#menuSearch').on('input', function() {
                var value = $(this).val().toLowerCase().trim();
                var $menuItems = $('#kt_app_sidebar_menu .menu-item');
                
                if (value === '') {
                    $menuItems.show();
                    // Close accordions that are not active
                    $('#kt_app_sidebar_menu .menu-accordion:not(:has(.active))').removeClass('show hover');
                    return;
                }
                
                var tokens = value.split(/\s+/).filter(Boolean);
                $menuItems.hide();
                
                $menuItems.each(function() {
                    var title = $(this).find('> .menu-link .menu-title').text().toLowerCase();
                    var content = $(this).text().toLowerCase();
                    var matches = tokens.some(function(token) {
                        return title.indexOf(token) > -1 || content.indexOf(token) > -1;
                    });
                    
                    if (matches) {
                        // Show this item
                        $(this).show();
                        // Show all parents
                        $(this).parents('.menu-item').show();
                        $(this).parents('.menu-accordion').addClass('show hover');
                        // Show all children
                        $(this).find('.menu-item').show();
                    }
                });
            });
        });
    </script>
@endpush


{{-- @can('Subscription.index')
    <div class="menu-item">
        <a class="menu-link {{ Route::is('Subscription*') ? 'active' : '' }}" href="{{ route('Subscription.index') }}">
            <span class="menu-bullet">

                <i class="fas fa-layer-group fs-2 {{ Route::is('Subscription*') ? 'text-primary' : '' }}"></i>
            </span>
            <span class="menu-title">@lang('models/Subscription.plural')</span>
        </a>

    </div>
@endcan --}}






    @can('dashboard')
        <div class="menu-item">
            <a class="menu-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="menu-bullet">
                    <i class="nav-icon fas fa-home {{ Route::is('dashboard') ? 'text-primary' : '' }}"></i>
                </span>
                <span class="menu-title">@lang('lang.dashboard')</span>
            </a>
        </div>
    @endcan









@if (auth()->user()?->user_type !== 'service_center')
<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-bullet">
            <i class="ki-duotone ki-address-book fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </span>
        <span class="menu-title">@lang('lang.Settings')</span>
        <span class="menu-arrow"></span>
    </span>

    <div class="menu-sub menu-sub-accordion">
        <!---------------------------------------------- Settings ------------------------------------------------------------------------------->

        {{-- <div class="menu-item">
            <a class="menu-link {{ Route::is('applications*') ? 'active' : '' }}"
                href="{{ route('applications.index') }}">
                <span class="menu-bullet">
                    <i class="ki-duotone ki-element-11 fs-2 {{ Route::is('applications*') ? 'text-primary' : '' }}">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </span>
                <span class="menu-title">@lang('applications.title')</span>
            </a>
        </div> --}}

        @can('Organization.edit')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('Organization*') ? 'active' : '' }}"
                    href="{{ route('Organization.edit', 1) }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-info"></i>
                    </span>
                    <span class="menu-title">@lang('models/Organization.plural')</span>
                </a>
            </div>
        @endcan

        
        @can('Templates.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('Templates*') ? 'active' : '' }}" href="{{ route('Templates.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-palette"></i>
                        </span>
                        <span class="menu-title">@lang('models/Templates.plural')</span>
                    </a>
                </div>
        @endcan
        

        @can('Branches.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('Branches*') ? 'active' : '' }}" href="{{ route('Branches.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-code-branch"></i>
                    </span>
                    <span class="menu-title">@lang('models/Branches.plural')</span>
                </a>
            </div>
        @endcan

        @if (\Illuminate\Support\Facades\Route::has('Areas.index'))
            @can('Areas.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('Areas*') ? 'active' : '' }}" href="{{ route('Areas.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-map"></i>
                        </span>
                        <span class="menu-title">@lang('models/Areas.plural')</span>
                    </a>
                </div>
            @endcan
        @endif

        @if (\Illuminate\Support\Facades\Route::has('Cities.index'))
            @can('Cities.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('Cities*') ? 'active' : '' }}" href="{{ route('Cities.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-city"></i>
                        </span>
                        <span class="menu-title">@lang('models/Cities.plural')</span>
                    </a>
                </div>
            @endcan
        @endif

        @php
            $showCompaniesAndContractsMenu =
                (\Illuminate\Support\Facades\Route::has('Companies.index') &&
                    \Illuminate\Support\Facades\Gate::allows('Companies.index')) ||
                (\Illuminate\Support\Facades\Route::has('CompanyContracts.index') &&
                    \Illuminate\Support\Facades\Gate::allows('CompanyContracts.index'));
        @endphp
        @if ($showCompaniesAndContractsMenu)
            <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion {{ \Illuminate\Support\Facades\Route::is('Companies*', 'CompanyContracts*') ? 'hover show' : '' }}">
                <span class="menu-link">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-building"></i>
                    </span>
                    <span class="menu-title">@lang('lang.companies_and_contracts')</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    @if (\Illuminate\Support\Facades\Route::has('Companies.index'))
                        @can('Companies.index')
                            <div class="menu-item">
                                <a class="menu-link {{ \Illuminate\Support\Facades\Route::is('Companies*') ? 'active' : '' }}"
                                    href="{{ route('Companies.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-industry"></i>
                                    </span>
                                    <span class="menu-title">@lang('models/Companies.plural')</span>
                                </a>
                            </div>
                        @endcan
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('CompanyContracts.index'))
                        @can('CompanyContracts.index')
                            <div class="menu-item">
                                <a class="menu-link {{ \Illuminate\Support\Facades\Route::is('CompanyContracts*') ? 'active' : '' }}"
                                    href="{{ route('CompanyContracts.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-file-contract"></i>
                                    </span>
                                    <span class="menu-title">@lang('models/CompanyContracts.plural')</span>
                                </a>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>
        @endif

        @can('users.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <span class="menu-bullet">
                        <i class="ki-duotone ki-profile-user fs-2 mx-2 {{ Route::is('users*') ? 'text-primary' : '' }}">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">@lang('models/users.plural')</span>
                </a>
            </div>
        @endcan

        @can('roles.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                    <span class="menu-bullet">
                        <i class="bi bi-person-lock fs-2 {{ Route::is('roles*') ? 'text-primary' : '' }}"></i>
                    </span>
                    <span class="menu-title">@lang('models/roles.plural')</span>
                </a>

            </div>
        @endcan



        @can('DeviceSessions.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('DeviceSessions*') ? 'active' : '' }}"
                    href="{{ route('DeviceSessions.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-code-branch"></i>
                    </span>
                    <span class="menu-title">@lang('models/DeviceSessions.plural')</span>
                </a>
            </div>
        @endcan

        @can('taxaccounts.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('taxaccounts*') ? 'active' : '' }}"
                    href="{{ route('taxaccounts.index') }}">
                    <span class="menu-bullet">
                        <i class="bi bi-person-lock fs-2 {{ Route::is('taxaccounts*') ? 'text-primary' : '' }}"></i>
                    </span>
                    <span class="menu-title">@lang('models/tax_accounts.plural')</span>
                </a>

            </div>
        @endcan



        @if (!config('statusSystem.modules.hr'))
            @can('employees.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('employees*') ? 'active' : '' }}"
                        href="{{ route('employees.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-home"></i>
                        </span>
                        <span class="menu-title">@lang('models/employees.plural')</span>
                    </a>
                </div>
            @endcan
        @endif




    </div>
</div>
@endif


{{-- @can('themes.index')
<div class="menu-item">
    <a class="menu-link {{ Route::is('themes*') ? 'active' : '' }}" href="{{ route('themes.index') }}">
        <span class="menu-bullet">
            <i class="ki-duotone ki-profile-user fs-2 mx-2 {{ Route::is('themes*') ? 'text-primary' : '' }}">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        </span>
        <span class="menu-title">@lang('models/themes.plural')</span>
    </a>
</div>
@endcan

@can('languages.index')
<div class="menu-item">
    <a class="menu-link {{ Route::is('languages.index') ? 'active' : '' }}" href="{{ route('languages.index') }}">
        <span class="menu-bullet">
            <i class="nav-icon fas fa-home"></i>
        </span>
        <span class="menu-title">@lang('models/languages.plural')</span>
    </a>
</div>
@endcan --}}





@if (auth()->user()?->user_type !== 'service_center')
@if (View::exists('basicdata::layouts._menu'))
    @include('basicdata::layouts._menu')
@endif

@if (View::exists('invoices::layouts._menu'))
    @include('invoices::layouts._menu')
@endif

@if (View::exists('pos::layouts._menu'))
    @include('pos::layouts._menu')
@endif
@if (View::exists('store::layouts._menu'))
    @include('store::layouts._menu')
@endif
@if (View::exists('finance::layouts._menu'))
    @include('finance::layouts._menu')
@endif
@if (View::exists('accusoft::layouts._menu'))
    @include('accusoft::layouts._menu')
@endif
{{-- Start HR --}}
@if (View::exists('hr::layouts._menu'))
    @include('hr::layouts._menu')
@endif
@endif








{{-- End HR --}}
