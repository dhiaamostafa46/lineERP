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
        $tableFontSize = '0.72rem';
    } else {
        $tableFontSize = '0.75rem';
    }
@endphp
<!-- ================== A4 DESIGN 6: ZATCA STANDARD PREVIEW ================== -->
<style>
    .design6-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        margin-bottom: 15px;
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    .design6-table th, .design6-table td {
        border: 1px solid #d1d5db;
        padding: 6px;
        font-size: {{ $tableFontSize }} !important;
        white-space: normal !important;
        overflow: hidden;
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
    .design6-table th {
        text-align: center;
        background-color: #e0f2fe !important;
        color: #1e3a8a;
    }
    .design6-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .design6-info-table td {
        border: 1px solid #d1d5db;
        padding: 4px 8px;
        font-size: 0.8rem;
        vertical-align: top;
    }
    .design6-info-label {
        width: 20%;
        color: #4b5563;
        background-color: #f3f4f6;
    }
    .design6-info-value {
        width: 30%;
        font-weight: bold;
        color: #111827;
    }

    /* ===== Overrides للطباعة ===== */
    @media print {
        .design6-table th,
        .design6-table td,
        .design6-info-table td {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .design6-table th {
            background-color: #e0f2fe !important;
        }
        .design6-info-label {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .design6-info-table {
            page-break-inside: avoid;
        }
    }
</style>

<div class="invoice-border" style="padding: 20px;">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <!-- Logo -->
        <div class="col-3 text-start">
            @if($templateConfig['show_logo'])
                <div style="width: 120px; height: 60px; background-color: #e5e7eb; display: inline-flex; justify-content: center; align-items: center; border-radius: 4px; color: #9ca3af; font-weight: bold; font-size: 0.8rem;">
                    <img src="{{ $previewData['company_logo'] ?? '' }}" alt="Logo" style="max-height: 100%; max-width: 100%;">
                </div>
            @endif
        </div>
        <!-- Title -->
        <div class="col-6 text-center">
            <h3 class="fw-bolder text-dark mb-1">{{ $previewData['invoice_title_ar'] ?? 'الفاتورة الضريبية' }}</h3>
            @if($templateConfig['enable_english'])
                <h4 class="fw-bolder text-dark mb-1">{{ $previewData['invoice_title_en'] ?? 'Tax Invoice' }}</h4>
            @endif
        </div>
        <!-- QR Code -->
        <div class="col-3 text-end">
            @if($templateConfig['qr_size'] != 'none' && !empty($previewData['qr_code']))
                <div class="qr-container d-inline-block border border-light p-1" style="width: 110px; height: 110px; border-color: #d1d5db !important;">
                    <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                        <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;">
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Seller Info -->
    <table class="design6-info-table">
        @if($templateConfig['show_company_name'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Seller Name:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['organization_name'] ?? '' }}</td>
            <td class="design6-info-label text-end">اسم البائع:</td>
        </tr>
        @endif
        @if($templateConfig['show_branch_name'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Branch:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['branch_name'] ?? '' }}</td>
            <td class="design6-info-label text-end">الفرع:</td>
        </tr>
        @endif
        @if($templateConfig['show_address'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Address:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['seller_address'] ?? '' }}</td>
            <td class="design6-info-label text-end">العنوان:</td>
        </tr>
        @endif
        @if(!empty($previewData['seller_phone']))
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Phone:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['seller_phone'] ?? '' }}</td>
            <td class="design6-info-label text-end">الهاتف:</td>
        </tr>
        @endif
        @if($templateConfig['show_tax_number'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">VAT No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['seller_vat'] ?? '' }}</td>
            <td class="design6-info-label text-end">الرقم الضريبي:</td>
        </tr>
        @endif
        @if($templateConfig['show_company_cr'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">CR No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['seller_cr'] ?? '' }}</td>
            <td class="design6-info-label text-end">السجل التجاري:</td>
        </tr>
        @endif
    </table>

    <!-- Invoice Info -->
    <table class="design6-info-table">
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Invoice No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">
                {{ $previewData['invoice_number'] ?? '' }}
                @if($templateConfig['show_small_barcode'] && !empty($previewData['invoice_number']))
                    <div class="mt-1">
                        <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($previewData['invoice_number'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 18px;">
                    </div>
                @endif
            </td>
            <td class="design6-info-label text-end">رقم الفاتورة:</td>
        </tr>
        @if(!empty($previewData['customer_invoice_number']))
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Reference No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_invoice_number'] ?? '' }}</td>
            <td class="design6-info-label text-end">رقم المرجع:</td>
        </tr>
        @endif
        @if($templateConfig['show_order_number'] && !empty($previewData['order_number']))
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Order No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['order_number'] ?? '' }}</td>
            <td class="design6-info-label text-end">رقم الطلب:</td>
        </tr>
        @endif
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Issue Date:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['issue_date'] ?? '' }}</td>
            <td class="design6-info-label text-end">تاريخ الإصدار:</td>
        </tr>
        @if(isset($previewData['supply_date']))
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Supply Date:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['supply_date'] ?? '' }}</td>
            <td class="design6-info-label text-end">تاريخ التوريد:</td>
        </tr>
        @endif
    </table>

    <!-- Buyer Info -->
    @if($templateConfig['show_customer_data'])
    <table class="design6-info-table">
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Buyer Name:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_name'] ?? '' }}</td>
            <td class="design6-info-label text-end">اسم المشتري:</td>
        </tr>
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Address:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_address_full'] ?? '' }}</td>
            <td class="design6-info-label text-end">العنوان:</td>
        </tr>
        @if($templateConfig['show_customer_phone'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">Phone:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_phone'] ?? '' }}</td>
            <td class="design6-info-label text-end">رقم الجوال:</td>
        </tr>
        @endif
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">VAT No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_tax'] ?? '' }}</td>
            <td class="design6-info-label text-end">الرقم الضريبي:</td>
        </tr>
        @if($templateConfig['show_customer_cr'])
        <tr>
            @if($templateConfig['enable_english']) <td class="design6-info-label text-start">CR No:</td> @endif
            <td colspan="{{ $templateConfig['enable_english'] ? 2 : 3 }}" class="design6-info-value text-center">{{ $previewData['customer_cr'] ?? '' }}</td>
            <td class="design6-info-label text-end">السجل التجاري:</td>
        </tr>
        @endif
    </table>
    @endif

    <!-- Items Table -->
    <table class="design6-table text-center mb-4">
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
                    <td class="text-start pe-2">
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
                    <td class="text-start pe-2 text-muted small">{{ $item['description'] }}</td>
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

    <!-- الملخص المالي -->
    <div class="row mt-1 justify-content-end">
        <div class="col-7 pe-4">
            @if($templateConfig['show_payment_methods'] && !empty($previewData['payment_method']))
                <div class="mt-2">
                    <span class="fw-bold me-2" style="font-size: 0.85rem;">تفاصيل السداد @if($templateConfig['enable_english']) <small>(Payment Details)</small> @endif:</span>
                    <span class="text-muted small">{{ $previewData['payment_method'] ?? '' }}</span>
                </div>
            @endif
            @if($templateConfig['show_invoice_description'] && !empty($previewData['notes']))
                <div class="mt-2">
                    <span class="fw-bold me-2" style="font-size: 0.85rem;">ملاحظات @if($templateConfig['enable_english']) <small>(Notes)</small> @endif:</span>
                    <span class="text-muted small">{{ $previewData['notes'] ?? '' }}</span>
                </div>
            @endif
        </div>

        <div class="col-5">
            <table class="w-100 border border-dark border-2">
                <tr>
                    <td class="p-2 border-bottom border-dark bg-light text-start text-dark fw-bold" style="font-size: 0.8rem;">
                        الإجمالي قبل الضريبة @if($templateConfig['enable_english']) <small>(Total Excl. VAT)</small> @endif</td>
                    <td class="p-2 border-bottom border-dark text-end fw-bold">
                        {{ $previewData['total_exclusive_vat'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="p-2 border-bottom border-dark bg-light text-start text-dark fw-bold" style="font-size: 0.8rem;">
                        إجمالي الخصم @if($templateConfig['enable_english']) <small>(Total Discount)</small> @endif</td>
                    <td class="p-2 border-bottom border-dark text-end text-danger fw-bold">-
                        {{ $previewData['total_discount'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="p-2 border-bottom border-dark bg-light text-start text-dark fw-bold" style="font-size: 0.8rem;">
                        ضريبة القيمة المضافة @if($templateConfig['enable_english']) <small>(VAT 15%)</small> @endif</td>
                    <td class="p-2 border-bottom border-dark text-end fw-bold">+
                        {{ $previewData['total_vat'] ?? '' }}</td>
                </tr>
                @if(isset($previewData['shipping_cost']) && $previewData['shipping_cost'] > 0)
                <tr>
                    <td class="p-2 border-bottom border-dark bg-light text-start text-dark fw-bold" style="font-size: 0.8rem;">
                        مصاريف الشحن @if($templateConfig['enable_english']) <small>(Shipping Cost)</small> @endif</td>
                    <td class="p-2 border-bottom border-dark text-end fw-bold">+
                        {{ $previewData['shipping_cost'] ?? '' }}</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #000;">
                    <td class="p-2 bg-light text-start fw-bolder text-dark" style="font-size: 0.8rem;">الإجمالي النهائي @if($templateConfig['enable_english']) <small>(Grand Total)</small> @endif</td>
                    <td class="p-2 text-end fw-bolder text-dark">
                        {{ $previewData['total_inclusive_vat'] ?? '' }}</td>
                </tr>
            </table>
            @if($templateConfig['show_total_in_words'])
                <div class="mt-2 text-start fw-bold" style="font-size: 0.85rem;"> {{ $previewData['total_in_words'] ?? '' }}  </div>
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