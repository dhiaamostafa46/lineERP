@php
    $langPrefix = 'store::models/st_settlements.smart_import';
@endphp

<!-- 1. منطقة الرفع والتعليمات -->
<div class="col-12 mb-5">
    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
        <i class="ki-duotone ki-artificial-intelligence fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
        <div class="fw-semibold">
            <h4 class="text-gray-900 fw-bold">{{ __($langPrefix . '.title') }}</h4>
            <p class="fs-7 text-gray-600 mb-0">{{ __($langPrefix . '.desc') }}</p>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-light-success border border-success border-dashed" onclick="downloadSampleExcel()">
                    <i class="ki-duotone ki-file-down fs-4 me-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                    {{ __($langPrefix . '.download_sample') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6 mb-5">
    <label class="form-label fw-bold small text-dark">
        {{ __($langPrefix . '.select_store_label') }}
        <span class="text-danger">*</span>
    </label>
    <select name="store_id" id="store_id" class="form-select form-select-solid" required>
        <option value="">{{ __($langPrefix . '.select_store_placeholder') }}</option>
        @foreach($stores as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-5">
    <label class="form-label fw-bold small text-dark">{{ __($langPrefix . '.file_label') }} <span class="text-danger">*</span></label>
    <div class="input-group input-group-solid">
        <input type="file" id="invoice_file" class="form-control" accept=".xlsx, .xls, .csv, .pdf, .jpg, .jpeg, .png" onchange="document.getElementById('btn_start').classList.remove('d-none')">
        <button class="btn btn-primary d-none" type="button" id="btn_start" onclick="runAnalysis()">
            <i class="fa-solid fa-microchip me-2"></i> {{ __($langPrefix . '.start_processing') }}
        </button>
    </div>
</div>

<div id="result_section" class="d-none col-12">
    <!-- Card 2: Items Table -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="ki-duotone ki-abstract-26 fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                {{ __($langPrefix . '.detected_items') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center table-light align-middle text-secondary fw-semibold">
                            <th width="25%" class="text-start ps-6">{{ __($langPrefix . '.barcode') }}</th>
                            <th width="50%" class="text-start">{{ __($langPrefix . '.product_name') }}</th>
                            <th width="25%">{{ __($langPrefix . '.actual_quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody id="items_table_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
