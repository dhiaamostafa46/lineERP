@php
    $activeCols = 5;
    if ($templateConfig['show_item_number'] ?? false) $activeCols++;
    if ($templateConfig['show_item_image'] ?? false) $activeCols++;
    if ($templateConfig['show_item_unit'] ?? false) $activeCols++;
    if ($templateConfig['show_item_discount'] ?? false) $activeCols++;
    if ($templateConfig['show_item_subtotal'] ?? false) $activeCols++;
    if ($templateConfig['show_item_tax_percent'] ?? false) $activeCols++;
    if ($templateConfig['show_item_total_with_tax'] ?? false) $activeCols++;

    if ($activeCols >= 11) {
        $tableFontSize = '0.68rem';
    } elseif ($activeCols >= 9) {
        $tableFontSize = '0.75rem';
    } else {
        $tableFontSize = '0.85rem';
    }

    $activeDetails = 0;
    if ($templateConfig['show_item_number'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_image'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_unit'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_discount'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_subtotal'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_tax_percent'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_barcode'] ?? false) $activeDetails++;
    if ($templateConfig['show_item_options'] ?? false) $activeDetails++;

    if ($activeDetails >= 6) {
        $thermalTableFontSize = '0.68rem';
    } elseif ($activeDetails >= 4) {
        $thermalTableFontSize = '0.73rem';
    } else {
        $thermalTableFontSize = '0.8rem';
    }
@endphp
<div class="template-builder-container" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <style>
        .template-builder-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            font-family: 'Cairo', sans-serif;
            overflow: hidden;
            gap: 15px;
        }

        .tb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 15px 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .tb-header-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
        }

        .tb-header-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .tb-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .tb-btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .tb-btn-primary:hover {
            background-color: #2563eb;
        }

        .tb-btn-outline {
            background-color: transparent;
            border-color: #d1d5db;
            color: #374151;
        }

        .tb-btn-outline:hover {
            background-color: #f9fafb;
        }

        .tb-input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            outline: none;
        }

        .tb-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .tb-main {
            display: flex;
            flex-direction: row-reverse;
            flex: 1;
            height: calc(100vh - 180px);
            min-height: 500px;
            overflow: hidden;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e4e6ef;
        }

        /* Preview Area */
        .tb-preview-area {
            flex: 1;
            min-width: 0;
            padding: 0;
            overflow: auto;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            background: none;
        }

        flex: 1;
        min-width: 0;
        padding: 40px;
        overflow-y: auto;
        overflow-x: auto;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        background-color: #f1f3f7;
        background-image: radial-gradient(#e4e6ef 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        }

        /* Sidebar Area - System Design (White skin, dark text, gray borders) */
        .tb-sidebar {
            width: 340px;
            min-width: 340px;
            max-width: 340px;
            flex-shrink: 0;
            background-color: #ffffff;
            color: #181c32;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding-bottom: 50px;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.02);
        }

        [dir="rtl"] .tb-sidebar {
            border-left: 1px solid #e4e6ef;
            border-right: none !important;
        }

        [dir="ltr"] .tb-sidebar {
            border-right: 1px solid #e4e6ef;
            border-left: none !important;
        }

        .tb-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .tb-sidebar::-webkit-scrollbar-track {
            background: #f1f1f4;
        }

        .tb-sidebar::-webkit-scrollbar-thumb {
            background: #cdcdde;
            border-radius: 3px;
        }

        .tb-sidebar::-webkit-scrollbar-thumb:hover {
            background: #a1a1b5;
        }

        #kt_app_toolbar {
            background: #ffffff !important;
            border-bottom: 1px solid #e4e6ef !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
            border-radius: 8px;
            padding: 10px 20px !important;
        }

        #kt_app_toolbar .form-control,
        #kt_app_toolbar .form-select {
            border: 1px solid #dbdfe9 !important;
            background-color: #f9f9f9 !important;
            color: #181c32 !important;
            font-size: 0.9rem !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }

        #kt_app_toolbar .form-control:focus,
        #kt_app_toolbar .form-select:focus {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1) !important;
        }

        #kt_app_toolbar .form-check-input {
            border: 1px solid #d1d5db !important;
            cursor: pointer;
        }

        #kt_app_toolbar .form-check-input:checked {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }

        #kt_app_toolbar .btn-outline-dark {
            border: 1px solid #dbdfe9 !important;
            color: #4b5563 !important;
            background-color: #ffffff !important;
            transition: all 0.2s ease !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }

        #kt_app_toolbar .btn-outline-dark:hover {
            background-color: #f9fafb !important;
            border-color: #d1d5db !important;
            color: #1f2937 !important;
        }

        #kt_app_toolbar .btn-dark {
            border: 1px solid #2563eb !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            transition: all 0.2s ease !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2) !important;
        }

        #kt_app_toolbar .btn-dark:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border-color: #1d4ed8 !important;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-1px);
        }

        .tb-sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e4e6ef;
            font-size: 1.15rem;
            font-weight: 700;
            color: #181c32;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tb-section {
            border-bottom: 1px solid #e4e6ef;
            transition: all 0.2s ease;
        }

        .tb-section-title {
            padding: 16px 20px;
            font-weight: 700;
            color: #4b5563;
            font-size: 0.95rem;
            background-color: #fcfdfe;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .tb-section-title:hover {
            background-color: #f8fafc;
            color: #3b82f6;
        }

        .tb-toggle-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f8fafc;
        }

        .tb-toggle-row:hover {
            background-color: #f8fafc;
        }

        .tb-toggle-label {
            font-size: 0.9rem;
            color: #374151;
            font-weight: 500;
        }

        /* Toggle switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e4e6ef;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #3b82f6;
            /* system primary blue */
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        /* Select inputs in sidebar */
        .tb-sidebar-select {
            background-color: #ffffff;
            color: #181c32;
            border: 1px solid #dbdfe9;
            border-radius: 6px;
            padding: 8px 12px;
            width: 100%;
            margin-top: 5px;
            outline: none;
            font-size: 0.85rem;
            transition: border-color 0.2s;
        }

        .tb-sidebar-select:focus {
            border-color: #3b82f6;
        }

        .tb-sidebar-input {
            background-color: #ffffff;
            color: #181c32;
            border: 1px solid #dbdfe9;
            border-radius: 6px;
            padding: 8px 12px;
            width: 100%;
            margin-top: 5px;
            outline: none;
            box-sizing: border-box;
            font-size: 0.85rem;
            transition: border-color 0.2s;
        }

        .tb-sidebar-input:focus {
            border-color: #3b82f6;
        }


        /* Invoice Preview Styles */
        .invoice-preview-wrapper {
            width: 100%;
            height: auto;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .invoice-a4 {
            width: 21cm;
            min-height: 29.7cm;
            padding: 1cm;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            color: #000;
            box-sizing: border-box;
        }

        .invoice-thermal {
            width: 80mm;
            min-height: 100mm;
            padding: 5mm;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: #000;
            box-sizing: border-box;
        }

        /* Saudi Invoice Styling from invoicetemplate4k & invoicetemplatesimple */
        .saudi-invoice {
            background: #fff;
            font-family: 'Cairo', 'Arial', sans-serif;
            color: #000;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .invoice-border {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 0;
            box-sizing: border-box;
        }

        .invoice-thermal .invoice-border {
            border: none;
            padding: 0;
        }

        .line-divider {
            border-top: 1px solid #000;
            margin: 15px 0;
        }

        .invoice-thermal .line-divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .table-zatca {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
            margin-top: 20px;
            word-wrap: break-word !important;
            word-break: break-word !important;
        }

        .invoice-thermal .table-zatca {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .table-zatca th {
            border: 1px solid #000;
            background: #f0f0f0 !important;
            padding: 8px 4px;
            font-weight: bold;
            text-align: center;
            font-size: {{ $tableFontSize }};
            color: #000;
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
            overflow: hidden;
        }

        .invoice-thermal .table-zatca th {
            border: none;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            background: transparent !important;
            padding: 6px 2px;
            font-size: {{ $thermalTableFontSize }};
        }

        .table-zatca td {
            border: 1px solid #000;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: {{ $tableFontSize }};
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
            overflow: hidden;
        }

        .invoice-thermal .table-zatca td {
            border: none;
            border-bottom: 1px dotted #ddd;
            padding: 6px 2px;
            font-size: {{ $thermalTableFontSize }};
        }

        .qr-container {
            border: 1px solid #000;
            padding: 0;
            width: 160px;
            height: 160px;
            display: inline-block;
            background: #fff;
            box-sizing: border-box;
        }

        .invoice-thermal .qr-container {
            border: none;
            width: 120px;
            height: 120px;
            margin: 10px auto;
            display: block;
        }

        .qr-container svg,
        .qr-container img {
            width: 100% !important;
            height: 100% !important;
        }

        .data-label {
            font-weight: bold;
            font-size: 0.80rem;
        }

        .invoice-thermal .data-label {
            font-size: 0.75rem;
        }

        .data-value {
            font-size: 0.90rem;
        }

        .invoice-thermal .data-value {
            font-size: 0.75rem;
        }

        .bg-row {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* ========================================================
           @media print - قواعد الطباعة الشاملة
           ======================================================== */
        @media print {
            /* إخفاء عناصر الواجهة التي لا تطبع */
            .no-print,
            .template-selector-container,
            .tb-sidebar,
            .tb-header,
            #kt_app_header,
            #kt_app_sidebar,
            #kt_app_toolbar,
            .template-builder-container > *:not(.tb-main),
            .tb-main > .tb-sidebar {
                display: none !important;
            }

            /* إزالة الـ overflow المقيّد */
            html, body {
                overflow: visible !important;
                height: auto !important;
            }

            .template-builder-container {
                height: auto !important;
                overflow: visible !important;
                display: block !important;
            }

            .tb-main {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                box-shadow: none !important;
                border: none !important;
            }

            .tb-preview-area {
                overflow: visible !important;
                height: auto !important;
                padding: 0 !important;
            }

            /* ضبط صفحة A4 */
            .invoice-a4 {
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* ضبط صفحة حرارية */
            .invoice-thermal {
                width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
            }

            /* ضمان ظهور ألوان الخلفية عند الطباعة */
            .invoice-preview-wrapper,
            .saudi-invoice,
            .invoice-border,
            .table-zatca th,
            .bg-row,
            [style*="background"],
            [class*="bg-"] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* إصلاح الجداول - منع الانكسار بين الصفحات */
            .table-zatca {
                page-break-inside: auto;
            }

            .table-zatca tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .table-zatca thead {
                display: table-header-group;
            }

            /* منع انكسار الكتل الرئيسية */
            .invoice-border {
                page-break-inside: avoid;
            }

            /* إخفاء ظلال الصناديق */
            * {
                box-shadow: none !important;
            }

            /* إصلاح مشكلة @page */
            @page {
                size: A4 portrait;
                margin: 1.5cm 1cm;
            }

            /* override لصفحة حرارية - يحتاج JS للتبديل */
            .invoice-thermal ~ * {
                display: none;
            }
        }

        /* طباعة الصفحة الحرارية - يتم تطبيقها عبر JS قبل الطباعة */
        @media print {
            html.printing-thermal,
            html.printing-thermal body {
                width: 80mm !important;
            }

            html.printing-thermal .invoice-thermal {
                max-width: 80mm !important;
                margin: 0 auto !important;
            }
        }
    </style>

    <!-- Header/Toolbar - Modern Clean Design -->
    <div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-4 rounded mb-5"
        style="border: 1px solid #eff2f5;">
        <!-- Controls (Right side in RTL) -->
        <div class="d-flex align-items-center gap-4 flex-wrap ">
            <!-- Template Name -->
            <div class="position-relative d-none">
                <input type="text" wire:model="name" class="form-control form-control-solid w-250px fw-bold"
                    placeholder="{{ __('models/Templates.builder.name') }}"
                    style="background-color: #ffffff; border: 1px solid #dbdfe9;">
            </div>

            <!-- Format Select -->
            <div class="position-relative d-none">
                <select wire:model.live="print_format" class="form-select form-select-solid w-150px fw-bold"
                    style="background-color: #ffffff; border: 1px solid #dbdfe9;">
                    <option value="A4">{{ __('models/Templates.formats.A4') }}</option>
                    <option value="thermal">{{ __('models/Templates.formats.thermal') }}</option>
                </select>
            </div>

            <!-- Default checkbox -->
            <div class="form-check form-check-custom form-check-solid d-none">
                <input class="form-check-input" type="checkbox" wire:model="is_default" id="is_default_check"
                    style="cursor: pointer;">
                <label class="form-check-label fw-bold text-gray-700" for="is_default_check" style="cursor: pointer;">
                    {{ __('models/Templates.builder.default') }}
                </label>
            </div>
        </div>


        <!-- Actions (Left side in RTL) -->
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            @if (session()->has('message'))
                <span class="text-success fw-bold me-3">{{ session('message') }}</span>
            @endif
            {{-- <button class="btn btn-sm btn-secondary fw-bold"
                onclick="window.location.href='{{ route('Templates.index') }}'">{{ __('models/Templates.builder.cancel')
                }}</button>
            --}}
            <button class="btn btn-sm btn-outline-secondary fw-bold no-print" onclick="testPrint()">
                <i class="fa fa-print fs-5"></i> {{ __('models/Templates.builder.test_print') ?? 'اختبار الطباعة' }}
            </button>
            <button class="btn btn-sm btn-primary fw-bold" wire:click="save">
                <i class="fa fa-save fs-4"></i> {{ __('models/Templates.builder.save') }}
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="tb-main">
        <!-- Live Preview -->
        <div class="tb-preview-area"
            style="flex-direction: column; align-items: stretch; justify-content: flex-start; overflow-y: auto; overflow-x: auto; min-width: 0;">

            <style>
                /* Template Selector Styles */
                .template-selector-container {
                    margin-bottom: 25px;
                    width: 100%;
                    background: #ffffff;
                    padding: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                    border: 1px solid #e4e6ef;
                }

                .ts-title {
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #181c32;
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .ts-grid {
                    display: flex;
                    gap: 16px;
                    overflow-x: auto;
                    padding-bottom: 12px;
                }

                .ts-grid::-webkit-scrollbar {
                    height: 6px;
                }

                .ts-grid::-webkit-scrollbar-track {
                    background: #f1f1f4;
                }

                .ts-grid::-webkit-scrollbar-thumb {
                    background: #cdcdde;
                    border-radius: 3px;
                }

                .ts-card {
                    flex: 0 0 150px;
                    border: 1px solid #dbdfe9;
                    border-radius: 10px;
                    padding: 12px;
                    cursor: pointer;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    text-align: center;
                    background: #ffffff;
                    position: relative;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
                }

                .ts-card:hover {
                    border-color: #3b82f6;
                    transform: translateY(-4px) scale(1.02);
                    box-shadow: 0 6px 15px rgba(59, 130, 246, 0.1);
                }

                .ts-card.active {
                    border-color: #3b82f6;
                    background: #f0f7ff;
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
                }

                .ts-card.active::after {
                    content: "\f00c";
                    font-family: "Font Awesome 6 Free";
                    font-weight: 900;
                    position: absolute;
                    top: -6px;
                    left: -6px;
                    background: #3b82f6;
                    color: #ffffff;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    font-size: 0.75rem;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .ts-thumbnail {
                    height: 95px;
                    background: #f8fafc;
                    border-radius: 8px;
                    margin-bottom: 10px;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    padding: 8px;
                    gap: 6px;
                    border: 1px solid #e2e8f0;
                    transition: all 0.2s ease;
                }

                .ts-card.active .ts-thumbnail {
                    border-color: #93c5fd;
                    background: #ffffff;
                }

                .ts-label {
                    font-size: 0.85rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                /* CSS Mini Mockups for A4 templates */
                .mock-a4-d1 {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    height: 100%;
                }

                .mock-d1-hdr {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid #d1d5db;
                    padding-bottom: 4px;
                }

                .mock-logo {
                    width: 16px;
                    height: 10px;
                    background: #9ca3af;
                    border-radius: 1px;
                }

                .mock-title {
                    width: 30px;
                    height: 6px;
                    background: #4b5563;
                    margin: 0 auto;
                    border-radius: 1px;
                }

                .mock-lines {
                    display: flex;
                    flex-direction: column;
                    gap: 3px;
                    margin-top: 8px;
                }

                .mock-line {
                    height: 4px;
                    background: #e5e7eb;
                    border-radius: 1px;
                }

                .mock-line.short {
                    width: 60%;
                }

                .mock-table {
                    border: 1px solid #d1d5db;
                    height: 30px;
                    margin-top: 6px;
                    border-radius: 1px;
                    display: flex;
                    flex-direction: column;
                }

                .mock-th {
                    height: 8px;
                    background: #e5e7eb;
                    border-bottom: 1px solid #d1d5db;
                }

                .mock-a4-d2 {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    justify-content: space-between;
                }

                .mock-d2-hdr {
                    display: flex;
                    justify-content: space-between;
                    gap: 6px;
                }

                .mock-box-left {
                    width: 45%;
                    height: 20px;
                    border: 1px solid #e5e7eb;
                    background: #f9fafb;
                    border-radius: 2px;
                }

                .mock-box-right {
                    width: 45%;
                    height: 20px;
                    border: 1px solid #e5e7eb;
                    background: #f9fafb;
                    border-radius: 2px;
                }

                .mock-grid-lines {
                    display: flex;
                    flex-direction: column;
                    gap: 2px;
                    padding: 2px;
                }

                .mock-grid-line {
                    height: 2px;
                    background: #d1d5db;
                }

                .mock-a4-d3 {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                }

                .mock-d3-banner {
                    height: 12px;
                    background: #1e293b;
                    color: white;
                    border-radius: 2px 2px 0 0;
                }

                .mock-d3-th {
                    height: 8px;
                    background: #1e293b;
                }

                .mock-a4-d4 {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    border-top: 2px solid #0f172a;
                    padding-top: 4px;
                }

                .mock-d4-columns {
                    display: flex;
                    justify-content: space-between;
                    gap: 6px;
                    height: 35px;
                }

                .mock-d4-col-left {
                    width: 55%;
                    display: flex;
                    flex-direction: column;
                    gap: 3px;
                }

                .mock-d4-col-right {
                    width: 40%;
                    border-right: 1px solid #e5e7eb;
                    padding-right: 3px;
                    display: flex;
                    flex-direction: column;
                    gap: 3px;
                }

                /* Thermal mini mockups */
                .mock-thermal {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: space-between;
                    height: 100%;
                }

                .mock-thermal-d1-lines {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 3px;
                }

                .mock-thermal-line {
                    height: 4px;
                    background: #d1d5db;
                    width: 80%;
                    border-radius: 1px;
                }

                .mock-thermal-line.short {
                    width: 50%;
                }

                .mock-thermal-line.dashed {
                    border-top: 1px dashed #9ca3af;
                    height: 0;
                    background: none;
                }

                .mock-thermal-d2-box {
                    width: 60%;
                    height: 10px;
                    border: 1px solid #000;
                    border-radius: 2px;
                    background: #fff;
                    margin: 4px auto;
                }

                .mock-thermal-d3-hdr {
                    width: 100%;
                    text-align: center;
                    border-bottom: 1px dashed #000;
                    padding-bottom: 2px;
                    margin-bottom: 4px;
                }

                /* Design 2 preview box styling */
                .info-box {
                    border: 1px solid #e4e6ef;
                    border-radius: 6px;
                    padding: 15px;
                    height: 100%;
                    background: #f9fafb;
                }

                .meta-table {
                    width: 100%;
                }

                .meta-table td {
                    padding: 4px;
                    border-bottom: 1px dashed #e4e6ef;
                }

                .meta-table tr:last-child td {
                    border-bottom: none;
                }
            </style>

            <!-- Template Selector Row -->
            <div class="template-selector-container mb-5 no-print" dir="rtl">
                <div class="ts-title"><i class="fa fa-layer-group text-primary me-2"></i>
                    {{ $print_format == 'A4' ? __('models/Templates.builder.a4_designs') : __('models/Templates.builder.thermal_designs') }}</div>
                <div class="ts-grid">
                    @if($print_format == 'A4')
                        <!-- design1 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design1' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design1')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d1">
                                    <div class="mock-d1-hdr">
                                        <div class="mock-logo"></div>
                                        <div class="mock-logo" style="background:#e5e7eb;"></div>
                                    </div>
                                    <div class="mock-title"></div>
                                    <div class="mock-lines">
                                        <div class="mock-line"></div>
                                        <div class="mock-line short"></div>
                                    </div>
                                    <div class="mock-table">
                                        <div class="mock-th"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design1') }}</div>
                        </div>

                        <!-- design2 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design2' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design2')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d2">
                                    <div class="mock-d2-hdr">
                                        <div class="mock-box-left"></div>
                                        <div class="mock-box-right">
                                            <div class="mock-grid-lines">
                                                <div class="mock-grid-line"></div>
                                                <div class="mock-grid-line"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mock-lines" style="margin-top: 4px;">
                                        <div class="mock-line"></div>
                                    </div>
                                    <div class="mock-table" style="margin-top: 4px;">
                                        <div class="mock-th"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design2') }}</div>
                        </div>

                        <!-- design3 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design3' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design3')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d3">
                                    <div class="mock-d3-banner"></div>
                                    <div class="mock-lines" style="margin-top: 4px;">
                                        <div class="mock-line"></div>
                                        <div class="mock-line short"></div>
                                    </div>
                                    <div class="mock-table" style="margin-top: 4px;">
                                        <div class="mock-d3-th"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design3') }}</div>
                        </div>

                        <!-- design4 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design4' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design4')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d4">
                                    <div class="mock-d4-columns">
                                        <div class="mock-d4-col-left">
                                            <div class="mock-line"></div>
                                            <div class="mock-line short"></div>
                                            <div class="mock-logo" style="width: 25px; height: 15px; margin-top: 4px;">
                                            </div>
                                        </div>
                                        <div class="mock-d4-col-right">
                                            <div class="mock-line"></div>
                                            <div class="mock-line"></div>
                                        </div>
                                    </div>
                                    <div class="mock-table"
                                        style="margin-top: 4px; border: none; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                                        <div class="mock-th" style="background:#fff;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design4') }}</div>
                        </div>

                        <!-- design5 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design5' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design5')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d1">
                                    <div class="mock-d1-hdr">
                                        <div class="mock-logo"></div>
                                        <div class="mock-logo" style="background:#e0f2fe;"></div>
                                    </div>
                                    <div class="mock-title" style="background:#e0f2fe; width:100%;"></div>
                                    <div class="mock-table">
                                        <div class="mock-th" style="background:#e0f2fe;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design5') }}</div>
                        </div>

                        <!-- design6 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design6' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design6')">
                            <div class="ts-thumbnail">
                                <div class="mock-a4-d1">
                                    <div class="mock-title" style="margin-top: 10px;"></div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <div style="width: 48%; border: 1px solid #d1d5db; height: 20px;"></div>
                                        <div style="width: 48%; border: 1px solid #d1d5db; height: 20px;"></div>
                                    </div>
                                    <div class="mock-table">
                                        <div class="mock-th" style="background:#e0f2fe;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.a4.design6') ?? 'قالب 6' }}</div>
                        </div>
                    @else
                        <!-- design1 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design1' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design1')">
                            <div class="ts-thumbnail">
                                <div class="mock-thermal">
                                    <div class="mock-thermal-d1-lines">
                                        <div class="mock-thermal-line short"></div>
                                        <div class="mock-thermal-line"></div>
                                        <div class="mock-thermal-line short" style="width: 30%;"></div>
                                        <div class="mock-thermal-line dashed"></div>
                                    </div>
                                    <div class="mock-thermal-d1-lines">
                                        <div class="mock-thermal-line short"></div>
                                        <div class="mock-thermal-line short"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.thermal.design1') }}</div>
                        </div>

                        <!-- design2 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design2' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design2')">
                            <div class="ts-thumbnail">
                                <div class="mock-thermal">
                                    <div class="mock-thermal-d2-box"></div>
                                    <div class="mock-thermal-d1-lines">
                                        <div class="mock-thermal-line dashed"></div>
                                        <div class="mock-thermal-line"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.thermal.design2') }}</div>
                        </div>

                        <!-- design3 -->
                        <div class="ts-card {{ ($templateConfig['layout_design'] ?? 'design1') == 'design3' ? 'active' : '' }}"
                            wire:click="$set('templateConfig.layout_design', 'design3')">
                            <div class="ts-thumbnail">
                                <div class="mock-thermal">
                                    <div class="mock-thermal-d3-hdr">
                                        <div class="mock-thermal-line"
                                            style="width: 70%; height: 8px; background: #000; margin: 0 auto;"></div>
                                    </div>
                                    <div class="mock-thermal-d1-lines" style="margin-top: 5px;">
                                        <div class="mock-thermal-line"></div>
                                        <div class="mock-thermal-line short"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="ts-label">{{ __('models/Templates.designs.thermal.design3') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Wrapper container -->
            <div class="invoice-preview-wrapper {{ $print_format == 'A4' ? 'invoice-a4' : 'invoice-thermal' }}" style="font-size: {{ $templateConfig['font_size'] ?? 12 }}px;">

                @if($print_format == 'A4')
                    <!-- A4 Layout Preview -->
                    <div class="saudi-invoice w-100" style="font-size: {{ $templateConfig['font_size'] ?? 12 }}px;">

                        @if(($templateConfig['layout_design'] ?? 'design1') == 'design2')
                            @include('livewire.template-designs.a4_design2')
                        @elseif(($templateConfig['layout_design'] ?? 'design1') == 'design3')
                            @include('livewire.template-designs.a4_design3')
                        @elseif(($templateConfig['layout_design'] ?? 'design1') == 'design4')
                            @include('livewire.template-designs.a4_design4')
                        @elseif(($templateConfig['layout_design'] ?? 'design1') == 'design5')
                            @include('livewire.template-designs.a4_design5')
                        @elseif(($templateConfig['layout_design'] ?? 'design1') == 'design6')
                            @include('livewire.template-designs.a4_design6')
                        @else
                            @include('livewire.template-designs.a4_design1')
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Thermal Layout Preview -->
                    <div class="saudi-invoice w-100" style="font-size: {{ $templateConfig['font_size'] ?? 12 }}px;">

                        @if(($templateConfig['layout_design'] ?? 'design1') == 'design2')
                            @include('livewire.template-designs.thermal_design2')
                        @elseif(($templateConfig['layout_design'] ?? 'design1') == 'design3')
                            @include('livewire.template-designs.thermal_design3')
                        @else
                            @include('livewire.template-designs.thermal_design1')
                        @endif
                    </div>
                @endif
            </div> <!-- Close invoice-preview-wrapper -->
        </div> <!-- Close tb-preview-area -->

        <!-- Sidebar (Settings) -->
        <div class="tb-sidebar">
            <div class="tb-sidebar-header">
                {{ __('models/Templates.builder.template_settings') }}
            </div>

            <!-- Header Section -->
            <div class="tb-section" x-data="{ open: true }">
                <div class="tb-section-title" @click="open = !open">
                    <span><i class="fa fa-window-maximize text-muted me-2" style="width: 16px;"></i>
                        {{ __('models/Templates.builder.section_header') }}</span>
                    <i class="fa" :class="open ? 'fa-chevron-up text-primary' : 'fa-chevron-down text-muted'"
                        style="font-size: 0.8rem;"></i>
                </div>

                <div x-show="open" x-collapse>
                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_logo') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_logo">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_company_name') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_company_name">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.enable_english') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.enable_english">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_tax_number') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_tax_number">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_company_cr') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_company_cr">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_address') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_address">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_order_number') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_order_number">
                            <span class="slider"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Customer Section -->
            <div class="tb-section" x-data="{ open: false }">
                <div class="tb-section-title" @click="open = !open">
                    <span><i class="fa fa-user-tag text-muted me-2" style="width: 16px;"></i>
                        {{ __('models/Templates.builder.section_customer') }}</span>
                    <i class="fa" :class="open ? 'fa-chevron-up text-primary' : 'fa-chevron-down text-muted'"
                        style="font-size: 0.8rem;"></i>
                </div>

                <div x-show="open" x-collapse>
                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_customer_data') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_customer_data">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_customer_phone') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_customer_phone">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_customer_cr') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_customer_cr">
                            <span class="slider"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Products Section -->
            <div class="tb-section" x-data="{ open: false }">
                <div class="tb-section-title" @click="open = !open">
                    <span><i class="fa fa-box-open text-muted me-2" style="width: 16px;"></i>
                        {{ __('models/Templates.builder.section_products') }}</span>
                    <i class="fa" :class="open ? 'fa-chevron-up text-primary' : 'fa-chevron-down text-muted'"
                        style="font-size: 0.8rem;"></i>
                </div>

                <div x-show="open" x-collapse>
                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_number') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_number">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_barcode') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_barcode">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_image') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_image">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_unit') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_unit">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_discount') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_discount">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_tax_percent') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_tax_percent">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_item_subtotal') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_subtotal">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span
                            class="tb-toggle-label">{{ __('models/Templates.builder.show_item_total_with_tax') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_item_total_with_tax">
                            <span class="slider"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="tb-section" x-data="{ open: false }">
                <div class="tb-section-title" @click="open = !open">
                    <span><i class="fa fa-window-minimize text-muted me-2" style="width: 16px;"></i>
                        {{ __('models/Templates.builder.section_footer') }}</span>
                    <i class="fa" :class="open ? 'fa-chevron-up text-primary' : 'fa-chevron-down text-muted'"
                        style="font-size: 0.8rem;"></i>
                </div>

                <div x-show="open" x-collapse>
                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_total_in_words') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_total_in_words">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_payment_methods') ?? 'عرض تفاصيل السداد' }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_payment_methods">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_payment_status') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_payment_status">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_seller_name') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_seller_name">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span class="tb-toggle-label">{{ __('models/Templates.builder.show_small_barcode') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_small_barcode">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <label class="tb-toggle-row">
                        <span
                            class="tb-toggle-label">{{ __('models/Templates.builder.show_invoice_description') }}</span>
                        <div class="toggle-switch">
                            <input type="checkbox" wire:model.live="templateConfig.show_invoice_description">
                            <span class="slider"></span>
                        </div>
                    </label>

                    <div style="padding: 12px 20px;">
                        <div style="margin-bottom: 5px; font-size: 0.9rem; font-weight: 500;">
                            {{ __('models/Templates.builder.qr_size') }}</div>
                        <select wire:model.live="templateConfig.qr_size" class="tb-sidebar-select">
                            <option value="small">{{ __('models/Templates.builder.qr_small') }}</option>
                            <option value="medium">{{ __('models/Templates.builder.qr_medium') }}</option>
                            <option value="large">{{ __('models/Templates.builder.qr_large') }}</option>
                            <option value="none">{{ __('models/Templates.builder.qr_none') }}</option>
                        </select>
                    </div>

                    <div style="padding: 12px 20px 20px 20px;">
                        <div style="margin-bottom: 5px; font-size: 0.9rem; font-weight: 500;">
                            {{ __('models/Templates.builder.notes_thermal') }}</div>
                        <textarea wire:model.live="templateConfig.small_receipt_notes" class="tb-sidebar-input" rows="3"
                            placeholder="{{ __('models/Templates.builder.notes_placeholder') }}"></textarea>
                    </div>
                </div>
            </div>

            <!-- Global Styling Section -->
            <div class="tb-section" x-data="{ open: false }" style="padding-bottom: 30px;">
                <div class="tb-section-title" @click="open = !open">
                    <span><i class="fa fa-palette text-muted me-2" style="width: 16px;"></i>
                        {{ __('models/Templates.builder.section_styling') }}</span>
                    <i class="fa" :class="open ? 'fa-chevron-up text-primary' : 'fa-chevron-down text-muted'"
                        style="font-size: 0.8rem;"></i>
                </div>

                <div x-show="open" x-collapse>
                    <div style="padding: 12px 20px;">
                        <div style="margin-bottom: 5px; font-size: 0.9rem; font-weight: 500;">
                            {{ __('models/Templates.builder.font_size') }}</div>
                        <select wire:model.live="templateConfig.font_size" class="tb-sidebar-select">
                            <option value="10">10px</option>
                            <option value="11">11px</option>
                            <option value="12">12px</option>
                            <option value="14">14px</option>
                            <option value="16">16px</option>
                            <option value="18">18px</option>
                            <option value="20">20px</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== معالجة الطباعة المباشرة عبر Ctrl+P =====
    // إضافة class للتمييز بين A4 والحراري عند الطباعة
    window.addEventListener('beforeprint', function() {
        const thermalEl = document.querySelector('.invoice-thermal');
        if (thermalEl) {
            document.documentElement.classList.add('printing-thermal');
        }
    });

    window.addEventListener('afterprint', function() {
        document.documentElement.classList.remove('printing-thermal');
    });

    function testPrint() {
        const previewEl = document.querySelector('.invoice-preview-wrapper');
        if (!previewEl) return;

        // Check if thermal layout
        const isThermal = previewEl.classList.contains('invoice-thermal');

        // Create an iframe to contain the printable invoice
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.style.visibility = 'hidden';
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;

        // Collect all styles from the parent page (including dynamic Blade-generated ones)
        const styles = Array.from(document.querySelectorAll('style')).map(s => s.innerHTML).join('\n');

        // External stylesheets
        const bootstrapLink = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">';
        const fontAwesomeLink = '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">';
        const fontsLink = '<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">';

        // Page size & margin config
        const pageSize   = isThermal ? '80mm auto' : 'A4 portrait';
        const pageMargin = isThermal ? '3mm 2mm'  : '1.5cm 1cm';

        // Clone the preview so we can strip no-print elements safely
        const cloned = previewEl.cloneNode(true);
        cloned.querySelectorAll('.no-print, .template-selector-container').forEach(el => el.remove());

        doc.open();
        doc.write(`
        <!DOCTYPE html>
        <html dir="${document.documentElement.dir || 'rtl'}">
        <head>
            <meta charset="UTF-8">
            <title>طباعة الفاتورة</title>
            ${bootstrapLink}
            ${fontAwesomeLink}
            ${fontsLink}
            <style>
                /* ===== Styles copied from parent page ===== */
                ${styles}

                /* ===== Print-specific overrides ===== */
                *, *::before, *::after {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }

                html, body {
                    background: #fff !important;
                    margin: 0;
                    padding: 0;
                    font-family: 'Cairo', 'Arial', sans-serif;
                    width: 100%;
                    height: auto;
                }

                /* Remove wrapper constraints */
                .invoice-preview-wrapper {
                    box-shadow: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100% !important;
                    min-height: auto !important;
                }

                .saudi-invoice {
                    padding: 0 !important;
                    width: 100% !important;
                }

                .invoice-border {
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                .invoice-a4 {
                    width: 100% !important;
                    min-height: auto !important;
                    box-shadow: none !important;
                }

                .invoice-thermal {
                    width: 100% !important;
                    min-height: auto !important;
                    box-shadow: none !important;
                }

                /* Tables - prevent row breaks between pages */
                .table-zatca {
                    width: 100% !important;
                    table-layout: fixed !important;
                    page-break-inside: auto;
                }

                .table-zatca tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }

                .table-zatca thead {
                    display: table-header-group;
                }

                .design5-table,
                .design6-table {
                    width: 100% !important;
                    page-break-inside: auto;
                }

                .design5-table tr,
                .design6-table tr {
                    page-break-inside: avoid;
                }

                .design5-table thead,
                .design6-table thead {
                    display: table-header-group;
                }

                /* QR images */
                .qr-container img,
                .qr-container svg {
                    width: 100% !important;
                    height: 100% !important;
                }

                /* Bootstrap overrides for print */
                .row {
                    display: flex !important;
                    flex-wrap: wrap !important;
                }

                .col-4, .col-5, .col-6, .col-7, .col-8, .col-3 {
                    float: left;
                }

                /* @page settings */
                @media print {
                    @page {
                        size: ${pageSize};
                        margin: ${pageMargin};
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
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
                            try {
                                window.parent.document.body.removeChild(window.frameElement);
                            } catch(e) {}
                        }, 1500);
                    }, 800);
                };
            <\/script>
        </body>
        </html>
    `);
        doc.close();
    }
</script>






