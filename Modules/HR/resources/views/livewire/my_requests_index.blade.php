<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <h1>@lang("hr::models/hr_$request_type.plural")</h1>
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted
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
                        @lang("hr::models/hr_$request_type.plural")
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center btn-group">
                <a class="btn btn-sm btn-primary float-right {{ $request_type == 'holidays' ? ' active' : '' }}"
                    wire:click="setRequestType('holidays')">
                    @lang('hr::models/hr_holidays.plural')
                </a>
                <a class="btn btn-sm btn-primary float-right {{ $request_type == 'advances' ? ' active' : '' }}"
                    wire:click="setRequestType('advances')">
                    @lang('hr::models/hr_advances.plural')
                </a>
            </div>

            <a href="{{ route('hr.my-requests.create') }}" class="btn btn-sm btn-primary">@lang('crud.create')</a>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="card">
                @switch($request_type)
                    @case('holidays')
                        @include('hr::my_requests.holidays_table')
                    @break

                    @case('advances')
                        @include('hr::my_requests.advances_table')
                    @break

                    @default
                @endswitch
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
