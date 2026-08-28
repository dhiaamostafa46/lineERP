<div>

    <!-- End At Field -->
    <div class="form-group col-sm-6 mb-3">
        {{-- {!! Form::label('request_type', __('hr::lang.request_type') . ':') !!}
         {!! Form::select(
            'request_type',
            ['holidays' => __('hr::models/hr_holidays.plural'), 'advances' => __('hr::models/hr_advances.plural')],
            null,
            [
                'class' => 'form-control',
                'wire:model.live' => 'request_type',
            ],
        ) !!} --}}
        <input type="hidden" name="request_type" value="holidays">
    </div>


    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('crud.create') @lang("hr::models/hr_$request_type.singular")
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('hr.empdashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('hr.empdashboard.index') }}" class="text-muted text-hover-primary">
                                @lang("hr::models/hr_$request_type.plural")
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
                            @lang('crud.create')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('hr.my-requests.index') }}" class="btn btn-sm btn-secondary">
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
                @include('adminlte-templates::common.errors')
                <div class="clearfix"></div>
                <div class="card">
                    @if ($tab == 'vacations')
                        @include('hr::my_requests.holidays_create_form')
                    @elseif ($tab == 'advances')
                        @include('hr::my_requests.advances_create_form')
                    @else
                      @include('hr::my_requests.holidays_create_form')
                    @endif

                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
</div>
