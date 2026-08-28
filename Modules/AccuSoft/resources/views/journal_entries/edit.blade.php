@extends('layouts.app')

@section('title', __('accusoft::models/as_journal_entries.singular'))

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
                    @lang('crud.edit') @lang('accusoft::models/as_journal_entries.singular')
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
                        <a href="{{ route('accusoft.JournalEntry.index') }}" class="text-muted text-hover-primary">
                            @lang('accusoft::models/as_journal_entries.plural')
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
                        @lang('crud.edit')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.JournalEntry.index') }}" class="btn btn-sm btn-secondary">
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
        <div id="kt_app_content_container" class="app-container ">
            @include('adminlte-templates::common.errors')
            <div class="clearfix"></div>

            <div class="card border-0 shadow-sm">

                {!! Form::model($journalEntry, [
                'route' => ['accusoft.JournalEntry.update', $journalEntry->id],
                'method' => 'patch',
                'files' => true,
                ]) !!}

                <div class="card-body p-5">
                    @include('accusoft::journal_entries.fields')
                </div>

                <div class="card-footer py-4 px-5 d-flex align-items-center justify-content-between"
                     style="background: linear-gradient(135deg, #f8f9fb 0%, #eef0f8 100%); border-top: 1px solid #dde1ef;">
                    <div class="text-muted fs-7">
                        <i class="fa-regular fa-keyboard me-1"></i>
                        <span style="background:#2b2f4a;color:#c8cfe0;font-size:.65rem;padding:.15rem .45rem;border-radius:5px;font-family:monospace;">Ctrl+S</span>
                        @lang('crud.save')
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('accusoft.JournalEntry.index') }}"
                           class="btn btn-sm btn-light-secondary fw-semibold px-4">
                            <i class="fa-solid fa-xmark me-1"></i> @lang('crud.cancel')
                        </a>
                        {!! Form::submit(__('crud.save'), [
                            'class' => 'btn btn-sm btn-primary fw-semibold px-5',
                            'id'    => 'submit-btn',
                            'style' => 'border-radius:8px; transition:all .2s;',
                        ]) !!}
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
@endsection
