@php
    $langPrefix = 'invoices::models/purchase_invoices';
@endphp

<!-- 1. منطقة الرفع والتعليمات (نقلت إلى هنا) -->
<div class="col-12 mb-5">
    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
        <i class="ki-duotone ki-artificial-intelligence fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
        <div class="fw-semibold">
            <h4 class="text-gray-900 fw-bold">{{ __($langPrefix . '.smart_import.title') }}</h4>
            <p class="fs-7 text-gray-600 mb-0">{{ __($langPrefix . '.smart_import.desc') }}</p>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-light-success border border-success border-dashed" onclick="downloadSampleExcel()">
                    <i class="ki-duotone ki-file-down fs-4 me-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                    {{ __($langPrefix . '.smart_import.download_sample') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 mb-8">
    <label class="form-label fw-bold small text-muted">@lang('invoices::models/purchase_invoices.ui.attachments'):</label>
    <div class="input-group input-group-solid">
        <input type="file" id="invoice_file" class="form-control" accept=".xlsx, .xls, .csv, .pdf, .jpg, .jpeg, .png" onchange="document.getElementById('btn_start').classList.remove('d-none')">
        <button class="btn btn-primary d-none" type="button" id="btn_start" onclick="runAnalysis()">
            <i class="fa-solid fa-microchip me-2"></i> {{ __($langPrefix . '.smart_import.start_processing') }}
        </button>
    </div>
</div>

<div id="result_section" class="d-none col-12">
    <!-- Card 2: Items Table -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="ki-duotone ki-abstract-26 fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                {{ __($langPrefix . '.smart_import.detected_items') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center table-light align-middle text-secondary fw-semibold">
                            <th width="15%" class="text-start ps-6">@lang($langPrefix . '.fields.id')</th>
                            <th width="30%" class="text-start">@lang($langPrefix . '.fields.product_name')</th>
                            <th width="10%">@lang($langPrefix . '.fields.quantity')</th>
                            <th width="15%">@lang($langPrefix . '.fields.unit_price')</th>
                            <th width="10%">@lang($langPrefix . '.fields.vat_amount')</th>
                            <th width="20%" class="pe-6">@lang($langPrefix . '.fields.subtotal_with_vat')</th>
                        </tr>
                    </thead>
                    <tbody id="items_table_body"></tbody>
                </table>
            </div>
            
            <div class="p-3 border-top bg-light-soft text-center row m-0">
                <div class="col-md-7"></div>
                <div class="col-md-5">
                    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
                        <div class="card-header py-3 px-4 bg-transparent border-bottom">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="ki-duotone ki-delivery fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> 
                                <span id="supplier_name_display">{{ __($langPrefix . '.smart_import.detected_summary') }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-4 text-start">
                            <div class="d-flex justify-content-between mb-2 fs-6">
                                <span class="text-muted">{{ __($langPrefix . '.smart_import.subtotal') }}:</span>
                                <span class="fw-bold" id="lbl_subtotal">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-6">
                                <span class="text-muted">{{ __($langPrefix . '.smart_import.vat_total') }}:</span>
                                <span class="fw-bold" id="lbl_vat">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-3 border-top border-primary border-2 text-primary fs-5 fw-bold">
                                <span>{{ __($langPrefix . '.smart_import.final_total') }}:</span>
                                <span id="lbl_final_total">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
