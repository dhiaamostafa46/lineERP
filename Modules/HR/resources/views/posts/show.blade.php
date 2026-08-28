@extends('layouts.app')

@section('title', __('hr::models/hr_posts.singular'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('hr::models/hr_posts.singular')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('hr.posts.index') }}" class="text-muted text-hover-primary">@lang('hr::models/hr_posts.plural')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ $post->title }}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @can('hr.posts.edit')
                <a href="{{ route('hr.posts.edit', $post->id) }}" class="btn btn-sm btn-primary">@lang('crud.edit')</a>
                @endcan
                <a href="{{ route('hr.posts.index') }}" class="btn btn-sm btn-secondary">@lang('crud.back')</a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card">
                <div class="card-body">
                    @include('hr::posts.show_fields')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
