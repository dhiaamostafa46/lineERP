@extends('layouts.app')

@section('title', __('crud.detail') . ' ' . __('models/Templates.singular'))

@push('styles')
<style>
    /* ===== طباعة الفاتورة فقط - إخفاء كل الواجهة ===== */
    @media print {
        /* إخفاء كل شيء */
        #kt_app_header,
        #kt_app_sidebar,
        #kt_app_sidebar_toggle,
        #kt_app_toolbar,
        #kt_aside,
        .app-toolbar,
        .app-header,
        .page-title,
        .card-header,
        .card-toolbar,
        .btn,
        nav,
        footer {
            display: none !important;
        }

        /* إزالة الـ overflow */
        html, body {
            overflow: visible !important;
            height: auto !important;
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* إخفاء الـ card wrapper وإظهار المحتوى فقط */
        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        /* إعدادات الصفحة */
        @page {
            size: A4 portrait;
            margin: 1.5cm 1cm;
        }

        /* ضمان ألوان الخلفية */
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* إصلاح الجداول */
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }

        /* إخفاء ظلال الصناديق */
        * { box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        @lang('crud.detail') @lang('models/Templates.singular')
                    </h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a class="btn btn-sm btn-primary float-right" href="{{ route('Templates.index') }}">
                        <i class="fa-solid fa-arrow-left"></i>
                        @lang('crud.back')
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                

                <div class="card card-primary card-outline shadow-sm mt-5">
                    <div class="card-header">
                        <h3 class="card-title">@lang('crud.detail')</h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-info fw-bold" onclick="printInvoice()">
                                <i class="fa fa-print"></i> طباعة
                            </button>
                        </div>
                    </div>
                    <div class="card-body bg-light p-0">
                        <div id="invoice-content" class="w-100 bg-white">
                            {!! $Template->renderPreview() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printInvoice() {
            const invoiceEl = document.getElementById('invoice-content');
            if (!invoiceEl) { window.print(); return; }

            // Detect thermal from the wrapper class
            const wrapper = invoiceEl.querySelector('.invoice-preview-wrapper, .invoice-a4, .invoice-thermal');
            const isThermal = wrapper && wrapper.classList.contains('invoice-thermal');

            const pageSize   = isThermal ? '80mm auto' : 'A4 portrait';
            const pageMargin = isThermal ? '3mm 2mm'  : '1.5cm 1cm';

            // Collect all page styles
            const styles = Array.from(document.querySelectorAll('style')).map(s => s.innerHTML).join('\n');

            const iframe = document.createElement('iframe');
            iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;

            // Clone and remove no-print elements
            const cloned = invoiceEl.cloneNode(true);
            cloned.querySelectorAll('.no-print').forEach(el => el.remove());

            doc.open();
            doc.write(`<!DOCTYPE html>
<html dir="${document.documentElement.dir || 'rtl'}">
<head>
    <meta charset="UTF-8">
    <title>طباعة الفاتورة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        ${styles}

        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        html, body {
            background: #fff !important;
            margin: 0; padding: 0;
            font-family: 'Cairo', 'Arial', sans-serif;
            width: 100%; height: auto;
        }
        .invoice-preview-wrapper {
            box-shadow: none !important;
            margin: 0 !important; padding: 0 !important;
            width: 100% !important; min-height: auto !important;
        }
        .saudi-invoice { padding: 0 !important; width: 100% !important; }
        .invoice-border { box-shadow: none !important; page-break-inside: avoid; }
        .invoice-a4 { width: 100% !important; min-height: auto !important; box-shadow: none !important; }
        .invoice-thermal { width: 100% !important; min-height: auto !important; box-shadow: none !important; }
        .table-zatca { width: 100% !important; table-layout: fixed !important; page-break-inside: auto; }
        .table-zatca tr { page-break-inside: avoid; page-break-after: auto; }
        .table-zatca thead { display: table-header-group; }
        .design5-table, .design6-table { width: 100% !important; page-break-inside: auto; }
        .design5-table tr, .design6-table tr { page-break-inside: avoid; }
        .design5-table thead, .design6-table thead { display: table-header-group; }
        * { box-shadow: none !important; }

        @media print {
            @page { size: ${pageSize}; margin: ${pageMargin}; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    ${cloned.outerHTML}
    <script>
        window.onload = function() {
            window.focus();
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    try { window.parent.document.body.removeChild(window.frameElement); } catch(e) {}
                }, 1500);
            }, 800);
        };
    <\/script>
</body>
</html>`);
            doc.close();
        }
    </script>
@endsection