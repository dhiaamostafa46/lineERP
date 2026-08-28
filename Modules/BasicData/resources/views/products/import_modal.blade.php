<style>
    .evix-notice-box {
        background-color: #fff8ed !important;
        border-color: #f49e0b !important;
    }
    .evix-notice-box .evix-icon {
        color: #f49e0b !important;
    }
    .evix-error-box {
        background-color: #fff5f8 !important;
        border-color: #f1416c !important;
    }
    .evix-error-box .evix-icon {
        color: #f1416c !important;
    }
    .evix-btn-primary {
        background-color: #f49e0b !important;
        border-color: #f49e0b !important;
        color: #ffffff !important;
    }
    .evix-text-required {
        color: #f49e0b;
        font-weight: bold;
    }
    .evix-text-optional {
        color: #9ABF80;
        font-weight: bold;
    }
    .form-label.required::after {
        content: " *";
        color: #f49e0b;
    }
</style>

<div class="row">
    <!-- File Upload Section -->
    <div class="col-md-12 mb-5">
        <div class="form-group">
            {!! Form::label('file', __('crud.import') . ' (Excel):', ['class' => 'form-label required']) !!}
            {!! Form::file('file', ['class' => 'form-control', 'accept' => '.xlsx, .xls', 'required' => true]) !!}
        </div>
    </div>

    <!-- Instructions Section -->
    <div class="col-md-12">
        <div class="notice d-flex evix-notice-box rounded border border-dashed p-6">
            <!--begin::Icon-->
            <i class="ki-duotone ki-information-5 fs-2tx evix-icon me-4">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            <!--end::Icon-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack flex-grow-1">
                <!--begin::Content-->
                <div class="fw-semibold">
                    <h4 class="text-gray-900 fw-bold">@lang('basicdata::lang.instructions')</h4>
                    <div class="fs-6 text-gray-700">
                        <p>@lang('basicdata::models/db_products.import_instructions.line1')</p>
                        <p>@lang('basicdata::models/db_products.import_instructions.line2') <a href="{{ route('basicdata.products.importTemplate') }}"
                                class="btn btn-sm btn-primary" download>
                                <i class="fa-solid fa-file-excel me-1"></i>
                                @lang('crud.download')
                            </a>.</p>
                        <p>@lang('basicdata::models/db_products.import_instructions.line3'):</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item bg-transparent">1. <strong>@lang('basicdata::models/db_products.fields.barcode')</strong> (<span class="evix-text-optional">@lang('basicdata::crud.optional')</span>).</li>
                                    <li class="list-group-item bg-transparent">2. <strong>@lang('basicdata::models/db_products.fields.name') (AR-EN)</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                    <li class="list-group-item bg-transparent">3. <strong>@lang('basicdata::models/db_products.import.category_name_description', ['field' => __('basicdata::models/db_products.fields.category_id')])</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                    <li class="list-group-item bg-transparent">4. <strong>@lang('basicdata::models/db_products.fields.prod_price')</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                    <li class="list-group-item bg-transparent">5. <strong>@lang('basicdata::models/db_products.fields.cost_price')</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                    <li class="list-group-item bg-transparent">6. <strong>@lang('basicdata::models/db_products.import.unit_name_description', ['field' => __('basicdata::models/db_products.fields.unit_id')])</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item bg-transparent">7. <strong>@lang('basicdata::models/db_products.fields.min_quantity')</strong> (<span class="evix-text-optional">@lang('basicdata::crud.optional')</span>).</li>
                                    <li class="list-group-item bg-transparent">8. <strong>@lang('basicdata::models/db_products.fields.vat')</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                    <li class="list-group-item bg-transparent">9. <strong>@lang('basicdata::models/db_products.fields.type') (Product / Service / Size)</strong> (<span class="evix-text-required">@lang('basicdata::crud.required')</span>).</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-3 text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> @lang('basicdata::models/db_products.import_instructions.line4')</p>
                    </div>
                </div>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>
    </div>
</div>

<!-- Errors Section -->
@if (session()->has('failures'))
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="notice d-flex evix-error-box rounded border border-dashed p-6">
                <!--begin::Icon-->
                <i class="ki-duotone ki-shield-cross fs-2tx evix-icon me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <!--end::Icon-->
                <!--begin::Wrapper-->
                <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold">@lang('crud.import_errors_title')</h4>
                        <div class="fs-6 text-gray-700">
                            <p>@lang('crud.import_errors_message')</p>
                            <ul class="list-unstyled">
                                @foreach (session()->get('failures') as $failure)
                                    <li class="mb-2">
                                        <i class="fa-solid fa-circle-xmark text-danger me-2"></i>
                                        <strong>@lang('crud.row') {{ $failure->row() }}:</strong>
                                        <ul class="ms-4 mt-1">
                                            @foreach ($failure->errors() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
        </div>
    </div>
@endif

<!-- Row Level Errors Section -->
@if (session()->has('import_errors'))
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="notice d-flex evix-notice-box rounded border border-dashed p-6">
                <!--begin::Icon-->
                <i class="ki-duotone ki-information-5 fs-2tx evix-icon me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <!--end::Icon-->
                <!--begin::Wrapper-->
                <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold">@lang('crud.import_errors_title')</h4>
                        <div class="fs-6 text-gray-700">
                            <p>@lang('crud.import_errors_message')</p>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th>#</th>
                                            <th>@lang('basicdata::models/db_products.fields.name')</th>
                                            <th>@lang('crud.error')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session()->get('import_errors') as $error)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $error['row'][1] ?? 'N/A' }}</td>
                                                <td class="text-danger">{{ $error['error'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
        </div>
    </div>
@endif
