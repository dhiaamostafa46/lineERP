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
        $tableFontSize = '0.8rem';
    }
@endphp
<!-- ================== A4 DESIGN 5: OFFICIAL TAX PREVIEW ================== -->
<style>
    .design5-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        margin-bottom: 0;
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    .design5-table th, .design5-table td {
        border: 1px solid #94a3b8;
        padding: 6px;
        text-align: center;
        font-size: {{ $tableFontSize }} !important;
        white-space: normal !important;
        overflow: hidden;
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    .design5-table th {
        background-color: #e0f2fe !important;
        color: #0f172a;
        font-weight: bold;
    }
    .design5-title-bar {
        background-color: #e0f2fe;
        padding: 10px;
        text-align: center;
        margin-bottom: 10px;
        border-radius: 2px;
    }
    .design5-data-box {
        border: 1px solid #94a3b8;
        border-radius: 2px;
        padding: 10px;
    }

    /* ===== Overrides للطباعة ===== */
    @media print {
        .design5-table th,
        .design5-table td {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .design5-table th {
            background-color: #e0f2fe !important;
        }
        .design5-title-bar {
            background-color: #e0f2fe !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .design5-data-box {
            page-break-inside: avoid;
        }
    }
</style>

<div class="invoice-border shadow-sm" style="padding: 20px;">
    <!-- Header -->
    <div class="row align-items-center mb-3">
        <div class="col-4 text-start">
            @if($templateConfig['show_logo'])
                <div style="width: 150px; height: 60px; background-color: #e5e7eb; display: inline-flex; justify-content: center; align-items: center; border-radius: 4px; color: #9ca3af; font-weight: bold; font-size: 0.8rem;">
                    <img src="{{ $previewData['company_logo'] ?? '' }}" alt="Logo" style="max-height: 100%; max-width: 100%;">
                </div>
            @endif
        </div>
        <div class="col-8 text-end">
            <div style="background-color: #e0f2fe; padding: 5px 15px; display: inline-block; width: 100%; text-align: center; margin-bottom: 5px;">
                @if($templateConfig['show_company_name'])
                    <h3 class="fw-bolder text-dark m-0" style="color: #1e3a8a !important;">{{ $previewData['organization_name'] ?? '' }}</h3>
                @endif
                <h4 class="fw-bolder mb-0 mt-1 text-dark">{{ $previewData['invoice_title_ar'] ?? 'فاتورة ضريبية' }}@if($templateConfig['enable_english']) <span class="ms-2">/ {{ $previewData['invoice_title_en'] ?? 'Tax Invoice' }}</span>@endif</h4>
                <div class="mt-1 text-dark fw-bold" style="font-size: 0.8rem;">
                    @if($templateConfig['show_branch_name'])
                        {{ $previewData['branch_name'] ?? '' }}
                    @endif
                    @if($templateConfig['show_address'])
                        <span class="text-muted fw-normal"> | {{ $previewData['seller_address'] ?? '' }}</span>
                    @endif
                    @if(!empty($previewData['seller_phone']))
                        <span class="text-muted fw-normal"> | @if($templateConfig['enable_english']) Phone: @else الهاتف: @endif {{ $previewData['seller_phone'] ?? '' }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Title Bar -->
    <div class="design5-title-bar text-dark d-flex justify-content-center align-items-center gap-4 flex-wrap">
        <div class="fw-bold mb-1">رقم الفاتورة @if($templateConfig['enable_english']) <span class="mx-2">INVOICE No</span>@endif <span class="mx-2">{{ $previewData['invoice_number'] ?? '' }}</span></div>
        @if($templateConfig['show_tax_number'])
            <div class="fw-bold mb-1">الرقم الضريبي @if($templateConfig['enable_english']) <span class="mx-2">VAT No.</span>@endif <span class="mx-2">{{ $previewData['seller_vat'] ?? '' }}</span></div>
        @endif
        @if($templateConfig['show_company_cr'])
            <div class="fw-bold mb-1">السجل التجاري @if($templateConfig['enable_english']) <span class="mx-2">CR No.</span>@endif <span class="mx-2">{{ $previewData['seller_cr'] ?? '' }}</span></div>
        @endif
    </div>
    
    @if($templateConfig['show_small_barcode'] && !empty($previewData['invoice_number']))
        <div class="text-center mb-3">
            <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($previewData['invoice_number'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 25px; max-width: 100%;">
        </div>
    @endif

    <!-- Meta Data Box -->
    <div class="design5-data-box mb-3">
        <div class="row g-0">
            <!-- Left Side -->
            <div class="col-7 border-end border-secondary pe-2">
                @if($templateConfig['show_customer_data'])
                    <table class="w-100" style="font-size: 0.8rem;">
                        <tr>
                            <td class="fw-bold pb-1 text-start" width="35%">اسم المشتري @if($templateConfig['enable_english']) <br><small class="text-muted">Buyer Name</small> @endif</td>
                            <td class="pb-1 text-center fw-bold">{{ $previewData['customer_name'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold pb-1 text-start">العنوان @if($templateConfig['enable_english']) <br><small class="text-muted">Address</small> @endif</td>
                            <td class="pb-1 text-center">{{ $previewData['customer_address_full'] ?? '' }}</td>
                        </tr>
                        @if($templateConfig['show_customer_phone'])
                            <tr>
                                <td class="fw-bold pb-1 text-start">رقم الجوال @if($templateConfig['enable_english']) <br><small class="text-muted">Phone</small> @endif</td>
                                <td class="pb-1 text-center">{{ $previewData['customer_phone'] ?? '' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="fw-bold pb-1 text-start">الرقم الضريبي @if($templateConfig['enable_english']) <br><small class="text-muted">VAT No.</small> @endif</td>
                            <td class="pb-1 text-center">{{ $previewData['customer_tax'] ?? '' }}</td>
                        </tr>
                        @if($templateConfig['show_customer_cr'])
                            <tr>
                                <td class="fw-bold pb-1 text-start">السجل التجاري @if($templateConfig['enable_english']) <br><small class="text-muted">CR No.</small> @endif</td>
                                <td class="pb-1 text-center">{{ $previewData['customer_cr'] ?? '' }}</td>
                            </tr>
                        @endif
                    </table>
                @endif
            </div>
            <!-- Right Side -->
            <div class="col-5 ps-2">
                <div class="d-flex justify-content-between align-items-start h-100">
                    <table class="w-100" style="font-size: 0.8rem;">
                        <tr>
                            <td class="fw-bold pb-2 text-start" width="40%">تاريخ الإصدار @if($templateConfig['enable_english']) <br><small class="text-muted">Issue Date</small> @endif</td>
                            <td class="pb-2 text-start fw-bold">{{ $previewData['issue_date'] ?? '' }}</td>
                        </tr>
                        @if(isset($previewData['supply_date']))
                        <tr>
                            <td class="fw-bold pb-2 text-start">تاريخ التوريد @if($templateConfig['enable_english']) <br><small class="text-muted">Supply Date</small> @endif</td>
                            <td class="pb-2 text-start">{{ $previewData['supply_date'] ?? '' }}</td>
                        </tr>
                        @endif
                        @if(!empty($previewData['customer_invoice_number']))
                            <tr>
                                <td class="fw-bold pb-2 text-start">رقم المرجع @if($templateConfig['enable_english']) <br><small class="text-muted">Reference No.</small> @endif</td>
                                <td class="pb-2 text-start">#{{ $previewData['customer_invoice_number'] ?? '' }}</td>
                            </tr>
                        @endif
                        @if($templateConfig['show_order_number'] && !empty($previewData['order_number']))
                            <tr>
                                <td class="fw-bold pb-2 text-start">رقم الطلب @if($templateConfig['enable_english']) <br><small class="text-muted">Order No.</small> @endif</td>
                                <td class="pb-2 text-start">#{{ $previewData['order_number'] ?? '' }}</td>
                            </tr>
                        @endif
                    </table>
                    @if($templateConfig['qr_size'] != 'none' && !empty($previewData['qr_code']))
                        <div class="ms-2">
                            <div class="qr-container" style="width: {{ $templateConfig['qr_size'] == 'small' ? '100px' : ($templateConfig['qr_size'] == 'large' ? '160px' : '130px') }}; height: {{ $templateConfig['qr_size'] == 'small' ? '100px' : ($templateConfig['qr_size'] == 'large' ? '160px' : '130px') }};">
                                <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                                    <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="design5-table mb-0 text-center">
        <thead>
            <tr>
                @if($templateConfig['show_item_number'])
                    <th width="3%">#</th>
                @endif
                @if($templateConfig['show_item_image'])
                    <th width="5%">صورة @if($templateConfig['enable_english']) <br><small>Image</small> @endif</th>
                @endif
                <th width="16%" class="text-start">اسم الصنف @if($templateConfig['enable_english']) <br><small>Item Name</small> @endif</th>
                <th width="12%" class="text-start">الوصف @if($templateConfig['enable_english']) <br><small>Description</small> @endif</th>
                @if($templateConfig['show_item_unit'])
                    <th width="6%">الوحدة @if($templateConfig['enable_english']) <br><small>Unit</small> @endif</th>
                @endif
                <th width="9%">سعر الوحدة @if($templateConfig['enable_english']) <br><small>Unit Price</small> @endif</th>
                <th width="6%">الكمية @if($templateConfig['enable_english']) <br><small>Quantity</small> @endif</th>
                @if($templateConfig['show_item_discount'])
                    <th width="7%">الخصم @if($templateConfig['enable_english']) <br><small>Discount</small> @endif</th>
                @endif
                @if($templateConfig['show_item_subtotal'])
                    <th width="9%">الخاضع للضريبة @if($templateConfig['enable_english']) <br><small>Taxable Amount</small> @endif</th>
                @endif
                @if($templateConfig['show_item_tax_percent'])
                    <th width="8%">نسبة الضريبة @if($templateConfig['enable_english']) <br><small>VAT Rate</small> @endif</th>
                @endif
                <th width="9%">مبلغ الضريبة @if($templateConfig['enable_english']) <br><small>VAT Amount</small> @endif</th>
                @if($templateConfig['show_item_total_with_tax'])
                    <th width="10%">الإجمالي @if($templateConfig['enable_english']) <br><small>Total</small> @endif</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($previewData['items'] as $item)
                <tr>
                    @if($templateConfig['show_item_number'])
                        <td>{{ $loop->iteration }}</td>
                    @endif
                    @if($templateConfig['show_item_image'])
                        <td class="text-center">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" style="max-height: 35px; max-width: 35px;">
                            @endif
                        </td>
                    @endif
                    <td class="text-start">
                        <div class="fw-bold">{{ $item['product_name'] }}</div>
                        @if($templateConfig['show_item_barcode'] && !empty($item['barcode']))
                            <div class="mt-1">
                                <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($item['barcode'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 15px;">
                            </div>
                        @endif
                        @if($templateConfig['show_item_options'] && !empty($item['options']))
                            <div style="font-size: 0.75rem; color: #6b7280;" class="mt-1">{{ $item['options'] }}</div>
                        @endif
                    </td>
                    <td class="text-start text-muted small">{{ $item['description'] }}</td>
                    @if($templateConfig['show_item_unit'])
                        <td>{{ $item['unit_name'] }}</td>
                    @endif
                    <td>{{ $item['unit_price'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    @if($templateConfig['show_item_discount'])
                        <td class="text-danger small">{{ $item['discount'] }}</td>
                    @endif
                    @if($templateConfig['show_item_subtotal'])
                        <td>{{ $item['unit_price'] }}</td>
                    @endif
                    @if($templateConfig['show_item_tax_percent'])
                        <td>{{ $item['vat_rate'] }}%</td>
                    @endif
                    <td>{{ $item['vat_amount'] }}</td>
                    @if($templateConfig['show_item_total_with_tax'])
                        <td class="fw-bold">{{ $item['total'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="design5-data-box mt-0 border-top-0 pt-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
        <div class="row g-0">
            <div class="col-7 pe-2 border-end border-secondary pt-2">
                @if($templateConfig['show_payment_methods'] && !empty($previewData['payment_method']))
                    <div class="small fw-bold mb-2">تفاصيل السداد @if($templateConfig['enable_english']) <span class="fw-normal">/ Payment Details</span> @endif:</div>
                    <div class="text-muted small">{{ $previewData['payment_method'] ?? '' }}</div>
                @endif
                @if($templateConfig['show_invoice_description'] && !empty($previewData['notes']))
                    <div class="small fw-bold mt-2">ملاحظات @if($templateConfig['enable_english']) <span class="fw-normal">/ Notes</span> @endif:</div>
                    <div class="text-muted small">{{ $previewData['notes'] ?? '' }}</div>
                @endif
            </div>
            <div class="col-5">
                <table class="w-100" style="font-size: 0.85rem;">
                    <tr>
                        <td class="px-2 py-1 text-start">الإجمالي قبل الضريبة @if($templateConfig['enable_english']) <br><small class="text-muted">(Total Excl. VAT)</small> @endif</td>
                        <td class="px-2 py-1 text-end fw-bold">{{ $previewData['total_exclusive_vat'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-start">إجمالي الخصم @if($templateConfig['enable_english']) <br><small class="text-muted">(Total Discount)</small> @endif</td>
                        <td class="px-2 py-1 text-end fw-bold text-danger">- {{ $previewData['total_discount'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="px-2 py-1 text-start">ضريبة القيمة المضافة @if($templateConfig['enable_english']) <br><small class="text-muted">(VAT 15%)</small> @endif</td>
                        <td class="px-2 py-1 text-end fw-bold">+ {{ $previewData['total_vat'] ?? '' }}</td>
                    </tr>
                    @if(isset($previewData['shipping_cost']) && $previewData['shipping_cost'] > 0)
                    <tr>
                        <td class="px-2 py-1 text-start">مصاريف الشحن @if($templateConfig['enable_english']) <br><small class="text-muted">(Shipping Cost)</small> @endif</td>
                        <td class="px-2 py-1 text-end fw-bold">+ {{ $previewData['shipping_cost'] ?? '' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="px-2 py-1 border-top border-dark text-start fw-bolder">الإجمالي النهائي @if($templateConfig['enable_english']) <br><small class="text-muted">(Grand Total)</small> @endif</td>
                        <td class="px-2 py-1 border-top border-dark text-end fw-bolder">{{ $previewData['total_inclusive_vat'] ?? $previewData['total'] ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="border-top border-secondary mt-2 pt-2">
            @if($templateConfig['show_total_in_words'])
                <div class="fw-bold text-center" style="font-size: 0.85rem;">
                     {{ $previewData['total_in_words'] ?? '' }} 
                </div>
            @endif
        </div>
    </div>

    <!-- Footer details -->
    <div class="mt-4 pt-2 border-top border-secondary d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
        <div class="text-start">
            @if(($previewData['zatca_details'] ?? '0') == '1')
                <div class="text-success fw-bold">
                    <i class="fa fa-check-circle"></i> تم الربط مع هيئة الزكاة @if($templateConfig['enable_english']) (ZATCA Synced) @endif
                </div>
            @endif
        </div>
        <div class="text-center">
            @if($templateConfig['show_seller_name'])
                <div>أعد بواسطة @if($templateConfig['enable_english']) / Prepared by @endif</div>
                <div class="fw-bold mt-1">{{ $previewData['created_by_name'] ?? 'System' }}</div>
            @endif
        </div>
        <div class="text-end">
            
        </div>
    </div>
    @if(!empty($templateConfig['small_receipt_notes']))
        <div class="text-center mt-2 fw-semibold text-dark" style="font-size: 0.75rem;">
            {{ $templateConfig['small_receipt_notes'] }}
        </div>
    @endif
</div>