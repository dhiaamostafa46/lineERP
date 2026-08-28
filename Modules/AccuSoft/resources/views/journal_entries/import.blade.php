@extends('layouts.app')

@section('title', __('accusoft::models/as_journal_entries.import.title'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    @lang('accusoft::models/as_journal_entries.import.page_heading')
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('accusoft.JournalEntry.index') }}" class="text-muted text-hover-primary">@lang('accusoft::models/as_journal_entries.plural')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang('accusoft::models/as_journal_entries.import.breadcrumb')</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.JournalEntry.downloadTemplate') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-download"></i> @lang('accusoft::models/as_journal_entries.import.download_template')
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">@lang('accusoft::models/as_journal_entries.import.upload_file')</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> @lang('accusoft::models/as_journal_entries.import.important_notes')</h5>
                        <ul>
                            <li>@lang('accusoft::models/as_journal_entries.import.note_1')</li>
                            <li>@lang('accusoft::models/as_journal_entries.import.note_2')</li>
                            <li>@lang('accusoft::models/as_journal_entries.import.note_3')</li>
                            <li>@lang('accusoft::models/as_journal_entries.import.note_4')</li>
                        </ul>
                    </div>

                    <form action="{{ route('accusoft.JournalEntry.importSave') }}" method="POST" enctype="multipart/form-data" class="mt-5">
                        @csrf
                        <div class="form-group mb-5">
                            <label for="file" class="form-label fw-bold">@lang('accusoft::models/as_journal_entries.import.choose_file')</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('accusoft.JournalEntry.index') }}" class="btn btn-light me-3">@lang('accusoft::models/as_journal_entries.import.cancel')</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-upload"></i> @lang('accusoft::models/as_journal_entries.import.start_import')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
