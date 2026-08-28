@extends('layouts.app')

@section('title', __('models/Templates.plural'))

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
                    <h1>@lang('models/Templates.plural')</h1>
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
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
                        @lang('models/Templates.plural')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="clearfix"></div>

            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 mt-2">
                @foreach($Templates as $template)
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="card h-100 border border-dashed border-gray-300 {{ $template->print_format == 'A4' ? 'border-hover-primary' : 'border-hover-success' }} shadow-sm hover-elevate-up transition-all">
                            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                <a href="{{ route('Templates.edit', $template->id) }}" class="text-gray-800 {{ $template->print_format == 'A4' ? 'text-hover-primary' : 'text-hover-success' }} d-flex flex-column align-items-center">
                                    <div class="symbol symbol-75px mb-6">
                                        <div class="symbol-label {{ $template->print_format == 'A4' ? 'bg-light-primary' : 'bg-light-success' }}">
                                            <i class="fa-solid {{ $template->print_format == 'A4' ? 'fa-file-invoice text-primary' : 'fa-receipt text-success' }} fs-3x"></i>
                                        </div>
                                    </div>
                                    <div class="fs-4 fw-bold mb-2 text-gray-900">
                                        {{ $template->name }}
                                    </div>
                                </a>
                                <div class="fs-7 fw-semibold text-gray-600 mt-1">
                                    {{ $template->print_format == 'A4' ? __('models/Templates.formats.A4') : __('models/Templates.formats.thermal') }}
                                </div>
                                <div class="mt-6">
                                    <a href="{{ route('Templates.show', $template->id) }}" class="btn btn-sm btn-light-{{ $template->print_format == 'A4' ? 'primary' : 'success' }} fw-bold">
                                        <i class="fa-solid fa-eye me-1"></i> @lang('crud.show') @lang('models/Templates.singular')
                                    </a>

                                    <a href="{{ route('Templates.edit', $template->id) }}" class="btn btn-sm btn-light-{{ $template->print_format == 'A4' ? 'primary' : 'success' }} fw-bold">
                                        <i class="fa-solid fa-palette me-1"></i> @lang('crud.edit') @lang('models/Templates.singular')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($Templates->isEmpty())
                    <div class="col-12 text-center py-10">
                        <div class="text-muted fs-4 fw-bold">@lang('messages.not_found')</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
