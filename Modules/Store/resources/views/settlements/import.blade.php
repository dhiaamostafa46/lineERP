@extends('layouts.app')

@php
    $langPrefix = 'store::models/st_settlements.smart_import';
@endphp

@section('title', __($langPrefix . '.title'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __($langPrefix . '.title') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted"><a href="{{ route('store.settlement.index') }}" class="text-muted text-hover-primary">@lang('store::models/st_settlements.plural')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __($langPrefix . '.title') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('flash::message')
            <div class="clearfix"></div>
            
            <div class="card">
                <div class="card-body">
                    <div class="row g-5">
                        @include('store::settlements.import_fields')
                    </div>
                </div>

                <div class="card-footer py-4 text-end d-none" id="import_footer">
                    <a href="{{ route('store.settlement.index') }}" class="btn btn-sm btn-secondary me-3">@lang('crud.cancel')</a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="finalizeImport()">
                        {{ __($langPrefix . '.finalize_convert') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    const smartImportLang = {
        analyzing: "{{ __($langPrefix . '.analyzing') }}",
        analyzing_ai: "{{ __($langPrefix . '.analyzing_ai') }}",
        ai_subtext: "{{ __($langPrefix . '.ai_subtext') }}",
        success_import: "{{ __($langPrefix . '.success_import') }}",
        select_store_warning_title: "{{ __($langPrefix . '.select_store_warning_title') }}",
        select_store_warning_text: "{{ __($langPrefix . '.select_store_warning_text') }}",
        processing_and_loading: "{{ __($langPrefix . '.processing_and_loading') }}",
        process_success: "{{ __($langPrefix . '.process_success') }}",
        sample_downloaded: "{{ __($langPrefix . '.sample_downloaded') }}",
        empty_file: "{{ __($langPrefix . '.empty_file') }}",
        unsupported_file: "{{ __($langPrefix . '.unsupported_file') }}",
        source_excel: "{{ __($langPrefix . '.source_excel') }}",
        source_ai: "{{ __($langPrefix . '.source_ai') }}",
        ok: "{{ __($langPrefix . '.ok') }}"
    };

    async function runAnalysis() {
        const file = document.getElementById('invoice_file').files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        
        if (['xlsx', 'xls', 'csv'].includes(ext)) {
             Swal.fire({
                title: smartImportLang.analyzing,
                html: '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div></div>',
                showConfirmButton: false, allowOutsideClick: false
            });
            processExcel(file);
        } else if (['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
            processWithAI(file);
        } else {
            toastr.error(smartImportLang.unsupported_file);
        }
    }

    // 1. معالجة ملفات الإكسيل والـ CSV (محلياً وبشكل آلي بدون استخدام الذكاء الاصطناعي)
    function processExcel(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1 });
            
            if (json.length < 1) {
                Swal.close();
                toastr.error(smartImportLang.empty_file);
                return;
            }

            const headerRow = json[0].map(h => String(h).toLowerCase().trim());
            const keywords = {
                barcode: ['barcode', 'باركود', 'الباركود', 'code', 'كود', 'upc', 'ean', 'رقم المنتج', 'رقم الصنف'],
                name: ['product', 'item', 'المنتج', 'اسم المنتج', 'الصنف', 'اسم الصنف', 'الصنف / المنتج', 'description', 'البيان', 'name'],
                qty_actual: ['الكمية الفعلية', 'actual_quantity'],
                qty_book: ['الكمية الدفترية', 'book_quantity', 'quantity', 'qty', 'الكمية', 'العدد', 'الكميه', 'count', 'الوحدات'],
                price: ['متوسط التكلفة', 'التكلفة التقديرية', 'التكلفة', 'السعر', 'price', 'cost', 'سعر', 'سعر الشراء', 'rate', 'amount', 'قيمة']
            };

            const mapping = {
                barcode: headerRow.findIndex(h => keywords.barcode.some(k => h.includes(k))),
                name: headerRow.findIndex(h => keywords.name.some(k => h.includes(k))),
                qty_actual: headerRow.findIndex(h => keywords.qty_actual.some(k => h.includes(k))),
                qty_book: headerRow.findIndex(h => keywords.qty_book.some(k => h.includes(k))),
                price: headerRow.findIndex(h => keywords.price.some(k => h.includes(k)))
            };

            if (mapping.name === -1) mapping.name = 1;

            const items = json.slice(1).map(row => {
                let barcode = mapping.barcode !== -1 ? String(row[mapping.barcode] || '').trim() : '';
                let name = mapping.name !== -1 ? String(row[mapping.name] || '').trim() : '';
                
                let qtyVal = '';
                if (mapping.qty_actual !== -1 && row[mapping.qty_actual] !== undefined) {
                    qtyVal = String(row[mapping.qty_actual] || '').trim();
                }
                if ((!qtyVal || qtyVal.includes('_') || isNaN(parseFloat(qtyVal))) && mapping.qty_book !== -1) {
                    qtyVal = String(row[mapping.qty_book] || '').trim();
                }

                let qty = parseFloat(qtyVal);
                let price = mapping.price !== -1 ? parseFloat(row[mapping.price]) : 0;

                if (isNaN(qty) || qty <= 0) qty = 1;
                if (isNaN(price) || price < 0) price = 0;

                return { barcode: barcode || '---', name: name, qty: qty, price: price };
            }).filter(i => i.name && i.name !== '' && i.name !== 'الصنف / المنتج');

            Swal.close();
            renderResults(smartImportLang.source_excel, items);
        };
        reader.readAsArrayBuffer(file);
    }

    // 2. معالجة الصور والـ PDF باستخدام الذكاء الاصطناعي
    async function processWithAI(file) {
        Swal.fire({
            title: smartImportLang.analyzing_ai,
            html: `<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div><p class="fs-7 text-muted">${smartImportLang.ai_subtext}</p></div>`,
            showConfirmButton: false, allowOutsideClick: false
        });

        try {
            const apiKey = '{{ env('GEMINI_API_KEY') }}';
            
            if (!apiKey) {
                throw new Error("GEMINI_API_KEY is not defined in .env");
            }

            const base64String = await fileToBase64(file);
            const base64Data = base64String.split(',')[1];
            
            let mimeType = file.type;
            if (file.name.toLowerCase().endsWith('.pdf')) {
                mimeType = 'application/pdf';
            } else if (!mimeType) {
                mimeType = 'image/jpeg';
            }

            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=${apiKey}`;
            const payload = {
                contents: [
                    {
                        parts: [
                            { text: "Analyze this inventory settlement / invoice document. Return ONLY a valid JSON array of items. Format: [{\"barcode\":\"...\",\"name\":\"...\",\"qty\":1,\"price\":0}]. No markdown, no extra text." },
                            {
                                inline_data: {
                                    mime_type: mimeType,
                                    data: base64Data
                                }
                            }
                        ]
                    }
                ]
            };
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error.message);
            }

            let text = data.candidates[0].content.parts[0].text;
            let cleanJson = text.replace(/^```json\s*|```$/gm, '').trim();
            const items = JSON.parse(cleanJson);
            
            Swal.close();
            renderResults(smartImportLang.source_ai, items);

        } catch (error) {
            Swal.close();
            console.error("AI Error:", error);
            toastr.error('Error: ' + error.message);
        }
    }

    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    let currentExtractedItems = [];

    function renderResults(source, items) {
        if (items.length === 0) {
            document.getElementById('result_section').classList.add('d-none');
            document.getElementById('import_footer').classList.add('d-none');
            return;
        }

        currentExtractedItems = items;
        const tbody = document.getElementById('items_table_body');
        
        tbody.innerHTML = items.map(item => {
            return `<tr class="text-center align-middle">
                <td class="text-start ps-6"><span class="badge badge-light-dark fs-8">${item.barcode}</span></td>
                <td class="text-start fw-bold text-gray-800 fs-7">${item.name}</td>
                <td class="fs-7 fw-bold text-primary">${item.qty}</td>
            </tr>`;
        }).join('');
        
        document.getElementById('result_section').classList.remove('d-none');
        document.getElementById('import_footer').classList.remove('d-none');
        toastr.success(smartImportLang.success_import);
    }

    function finalizeImport() {
        if (currentExtractedItems.length === 0) return;

        const storeId = document.getElementById('store_id')?.value;
        if (!storeId) {
            Swal.fire({
                icon: 'warning',
                title: smartImportLang.select_store_warning_title,
                text: smartImportLang.select_store_warning_text,
                confirmButtonText: smartImportLang.ok
            });
            document.getElementById('store_id')?.focus();
            return;
        }

        Swal.fire({
            title: smartImportLang.processing_and_loading,
            html: '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div></div>',
            showConfirmButton: false, allowOutsideClick: false
        });

        $.ajax({
            url: "{{ route('store.settlement.process_smart_import') }}",
            method: 'POST',
            data: { 
                _token: "{{ csrf_token() }}", 
                store_id: storeId,
                items: currentExtractedItems 
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(smartImportLang.process_success);
                    window.location.href = response.redirect_url;
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed', 'error');
            }
        });
    }

    function downloadSampleExcel() {
        const rows = [
            ["باركود", "المنتج", "الكمية"],
            ["62810001", "عصير تفاح 200 مل", "10"],
            ["62810002", "مياه معدنية 500 مل", "24"]
        ];

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        rows.forEach(row => { csvContent += row.join(",") + "\r\n"; });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Sample_Settlement_Import.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        toastr.success(smartImportLang.sample_downloaded);
    }
</script>
@endpush
