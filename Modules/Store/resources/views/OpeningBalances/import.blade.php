@extends('layouts.app')

@section('title', __('store::models/st_opening_balances.import.title'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('store::models/st_opening_balances.import.title') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('store.openingbalance.index') }}" class="text-muted text-hover-primary">{{ __('store::models/st_opening_balances.plural') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ __('crud.import') }}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('store.openingbalance.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-right"></i> {{ __('crud.back') }}
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">{{ __('store::models/st_opening_balances.import.upload_title') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.openingbalance.importSave') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-10">
                            <div class="col-md-8">
                                <label class="fs-6 fw-semibold mb-2 required">{{ __('store::models/st_opening_balances.import.select_file') }}</label>
                                <input type="file" name="file" class="form-control form-control-lg form-control-solid" required accept=".xlsx, .xls, .csv">
                                <div class="text-muted fs-7 mt-2">
                                    {{ __('store::models/st_opening_balances.import.file_help') }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-5 border border-dashed border-primary rounded bg-light-primary text-center">
                                    <i class="fas fa-file-download fs-2x text-primary mb-3"></i>
                                    <h4 class="text-gray-800">{{ __('store::models/st_opening_balances.import.download_template') }}</h4>
                                    <p class="text-muted fs-7">{{ __('store::models/st_opening_balances.import.template_help') }}</p>
                                    <a href="{{ route('store.openingbalance.import') }}?template=1" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download"></i> {{ __('store::models/st_opening_balances.import.download_template') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-10"></div>

                        <div class="row">
                            <div class="col-12">
                                <h4 class="mb-5 text-gray-800">{{ __('store::models/st_opening_balances.import.important_notes') }}</h4>
                                <ul class="text-muted fs-6 lh-lg">
                                    <li><strong>{{ __('store::models/st_opening_balances.import.store_names') }}:</strong> {{ __('store::models/st_opening_balances.import.store_names_help') }}</li>
                                    <li><strong>{{ __('store::models/st_opening_balances.import.new_products') }}:</strong> {{ __('store::models/st_opening_balances.import.new_products_help') }}</li>
                                    <li><strong>{{ __('store::models/st_opening_balances.import.units_categories') }}:</strong> {{ __('store::models/st_opening_balances.import.units_categories_help') }}</li>
                                    <li><strong>{{ __('basicdata::models/db_products.fields.barcode') }}:</strong> {{ __('store::models/st_opening_balances.import.barcode_help') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-10">
                            <button type="reset" class="btn btn-light me-3">{{ __('lang.cancel') }}</button>
                            <button type="submit" class="btn btn-primary px-10">
                                <i class="fas fa-cloud-upload-alt"></i> {{ __('store::models/st_opening_balances.import.start_upload') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
