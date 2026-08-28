@extends('layouts.app')

@php
    $langPrefix = 'invoices::models/purchase_invoices';
@endphp

@section('title', __($langPrefix . '.smart_import.title'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __($langPrefix . '.smart_import.title') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a></li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">@lang($langPrefix . '.plural')</li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __($langPrefix . '.smart_import.title') }}</li>
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
                        @include('invoices::purchase_invoices.import_fields')
                    </div>
                </div>

                <div class="card-footer py-4 text-end d-none" id="import_footer">
                    <a href="{{ route('invoices.purchase.index') }}" class="btn btn-sm btn-secondary me-3">@lang('crud.cancel')</a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="finalizeImport()">
                        {{ __($langPrefix . '.smart_import.finalize_convert') }}
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
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    async function runAnalysis() {
        const file = document.getElementById('invoice_file').files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        
        if (['xlsx', 'xls', 'csv'].includes(ext)) {
             Swal.fire({
                title: "{{ __($langPrefix . '.smart_import.analyzing') }}",
                html: '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div></div>',
                showConfirmButton: false, allowOutsideClick: false
            });
            processExcel(file);
        } else if (['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
            processWithAI(file);
        } else {
            toastr.error("{{ __($langPrefix . '.smart_import.unsupported_file') }}");
        }
    }

    // 1. معالجة ملفات الإكسيل (محلياً)
    function processExcel(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1 });
            
            if (json.length < 1) {
                Swal.close();
                toastr.error("{{ __($langPrefix . '.smart_import.empty_file') }}");
                return;
            }

            const headerRow = json[0].map(h => String(h).toLowerCase().trim());
            const keywords = {
                barcode: ['barcode', 'باركود', 'الباركود', 'code', 'كود', 'upc', 'ean', 'رقم المنتج'],
                name: ['product', 'item', 'المنتج', 'اسم المنتج', 'الصنف', 'اسم الصنف', 'description', 'البيان', 'name'],
                qty: ['quantity', 'qty', 'الكمية', 'العدد', 'الكميه', 'count', 'الوحدات'],
                price: ['price', 'cost', 'السعر', 'سعر', 'سعر الشراء', 'rate', 'amount', 'قيمة'],
                vat: ['vat', 'tax', 'الضريبة', 'ضريبة', 'الضريبه', 'نسبة الضريبة', 'tax rate']
            };

            const mapping = {
                barcode: headerRow.findIndex(h => keywords.barcode.some(k => h.includes(k))),
                name: headerRow.findIndex(h => keywords.name.some(k => h.includes(k))),
                qty: headerRow.findIndex(h => keywords.qty.some(k => h.includes(k))),
                price: headerRow.findIndex(h => keywords.price.some(k => h.includes(k))),
                vat: headerRow.findIndex(h => keywords.vat.some(k => h.includes(k)))
            };

            if (mapping.name === -1) mapping.name = 1;

            const items = json.slice(1).map(row => {
                let barcode = mapping.barcode !== -1 ? String(row[mapping.barcode] || '').trim() : '';
                let name = mapping.name !== -1 ? String(row[mapping.name] || '').trim() : '';
                let qty = mapping.qty !== -1 ? parseFloat(row[mapping.qty]) : 1;
                let price = mapping.price !== -1 ? parseFloat(row[mapping.price]) : 0;
                let vat_rate = mapping.vat !== -1 ? parseFloat(row[mapping.vat]) : 15;

                if (isNaN(qty) || qty <= 0) qty = 1;
                if (isNaN(price) || price < 0) price = 0;
                if (isNaN(vat_rate) || vat_rate < 0) vat_rate = 15;

                return { barcode: barcode || '---', name: name, qty: qty, price: price, vat_rate: vat_rate };
            }).filter(i => i.name && i.name !== '');

            Swal.close();
            renderResults('EXCEL', items);
        };
        reader.readAsArrayBuffer(file);
    }

    // 2. معالجة الصور والـ PDF باستخدام الذكاء الاصطناعي عبر (JavaScript / Browser)
    async function processWithAI(file) {
        Swal.fire({
            title: "{{ __($langPrefix . '.smart_import.analyzing') }}",
            html: '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div><p class="fs-7 text-muted">جاري تحليل الفاتورة بواسطة الذكاء الاصطناعي...</p></div>',
            showConfirmButton: false, allowOutsideClick: false
        });

        try {
            // جلب المفتاح مباشرة من الـ env
            const apiKey = '{{ env('GEMINI_API_KEY') }}';
            
            if (!apiKey) {
                throw new Error("مفتاح GEMINI_API_KEY غير موجود في ملف .env");
            }

            // تحويل الملف لـ Base64
            const base64String = await fileToBase64(file);
            const base64Data = base64String.split(',')[1];
            
            // تحديد نوع الملف الفعلي
            let mimeType = file.type;
            if (file.name.toLowerCase().endsWith('.pdf')) {
                mimeType = 'application/pdf';
            } else if (!mimeType) {
                mimeType = 'image/jpeg';
            }

            // استخدام الموديل المجاني السريع ذو الحصة المجانية الكبيرة
            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=${apiKey}`;
            const payload = {
                contents: [
                    {
                        parts: [
                            { text: "Analyze this invoice. Return ONLY a valid JSON array of products. Format: [{\"barcode\":\"...\",\"name\":\"...\",\"qty\":1,\"price\":0,\"vat_rate\":15}]. No markdown, no extra text." },
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
            renderResults('AI (JavaScript)', items);

        } catch (error) {
            Swal.close();
            console.error("AI Error:", error);
            toastr.error('خطأ من الذكاء الاصطناعي: ' + error.message);
        }
    }

    // دالة مساعدة لتحويل الملف إلى Base64
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    let currentExtractedItems = [];

    function renderResults(supplier, items) {
        if (items.length === 0) {
            document.getElementById('result_section').classList.add('d-none');
            document.getElementById('import_footer').classList.add('d-none');
            return;
        }

        currentExtractedItems = items;
        let subtotal = 0;
        let totalVat = 0;
        const tbody = document.getElementById('items_table_body');
        
        tbody.innerHTML = items.map(item => {
            const lineTotal = item.qty * item.price;
            const lineVat = lineTotal * (item.vat_rate / 100);
            subtotal += lineTotal;
            totalVat += lineVat;

            return `<tr class="text-center align-middle">
                <td class="text-start ps-6"><span class="badge badge-light-dark fs-8">${item.barcode}</span></td>
                <td class="text-start fw-bold text-gray-800 fs-7">${item.name}</td>
                <td class="fs-7">${item.qty}</td>
                <td class="fs-7">${item.price.toFixed(2)}</td>
                <td class="fs-7">${lineVat.toFixed(2)} <small class="text-muted">(${item.vat_rate}%)</small></td>
                <td class="pe-6 fw-bold text-gray-900 fs-7">${(lineTotal + lineVat).toFixed(2)}</td>
            </tr>`;
        }).join('');

        document.getElementById('supplier_name_display').innerText = supplier;
        document.getElementById('lbl_subtotal').innerText = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ر.س';
        document.getElementById('lbl_vat').innerText = totalVat.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ر.س';
        document.getElementById('lbl_final_total').innerText = (subtotal + totalVat).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ر.س';
        
        document.getElementById('result_section').classList.remove('d-none');
        document.getElementById('import_footer').classList.remove('d-none');
        toastr.success("{{ __($langPrefix . '.smart_import.success_import') }}");
    }

    function finalizeImport() {
        if (currentExtractedItems.length === 0) return;

        Swal.fire({
            title: "{{ __($langPrefix . '.smart_import.analyzing') }}",
            html: '<div class="text-center py-5"><div class="spinner-border text-primary mb-3" role="status"></div></div>',
            showConfirmButton: false, allowOutsideClick: false
        });

        $.ajax({
            url: "{{ route('invoices.purchase.process_smart_import') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}", items: currentExtractedItems },
            success: function(response) {
                if (response.success) {
                    toastr.success('تمت المعالجة بنجاح.');
                    window.location.href = response.redirect_url;
                } else {
                    Swal.fire('خطأ', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire('خطأ تقني', xhr.responseJSON?.message || 'فشلت العملية', 'error');
            }
        });
    }

    function downloadSampleExcel() {
        const rows = [
            ["باركود", "المنتج", "الكمية", "السعر"],
            ["62810001", "عصير تفاح 200 مل", "10", "1.50"],
            ["62810002", "مياه معدنية 500 مل", "24", "0.50"]
        ];

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        rows.forEach(row => { csvContent += row.join(",") + "\r\n"; });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Sample_Invoice_AI.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        toastr.success('تم تحميل الملف التجريبي بنجاح.');
    }
</script>
@endpush
