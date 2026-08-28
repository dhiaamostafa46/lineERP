@php
    // حساب الإجمالي قبل الخصم والضريبة
    $totalBeforeDiscount = $quotation->items->sum(function ($item) {
        return $item->quantity * $item->unit_price;
    });
    $org = $quotation->branch->organization ?? \App\Models\Organization::first();

    // ZATCA Settings
    $zatcaSetting = \Modules\Invoices\App\Models\ZatcaSetting::resolveForBranch($quotation->branch_id);

    // Determine Invoice Category
    $isSimplified = empty($quotation->customer->vat_number);
    $invoiceTitleAr = $isSimplified ? 'عرض سعر ' : 'عرض سعر';
    $invoiceTitleEn = $isSimplified ? ' Quotation' : 'Quotation';
@endphp

<div class="web-content">
    <style>
        /* تصميم فاتورة رسمية معتمدة (أبيض وأسود) */
        .saudi-invoice {
            background: #fff;
            font-family: 'Arial', sans-serif;
            color: #000;
            padding: 10px;
        }

        .invoice-border {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 0;
        }

        .line-divider {
            border-top: 1px solid #000;
            margin: 15px 0;
        }

        .table-zatca {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-zatca th {
            border: 1px solid #000;
            background: #f0f0f0 !important;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            font-size: 0.85rem;
            color: #000;
        }

        .table-zatca td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .qr-container {
            border: 1px solid #000;
            padding: 5px;
            width: 160px;
            height: 160px;
            display: inline-block;
            background: #fff;
        }

        .qr-container svg {
            width: 100% !important;
            height: 100% !important;
        }

        .data-label {
            font-weight: bold;
            font-size: 0.80rem;
        }

        .data-value {
            font-size: 0.90rem;
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

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            #kt_app_sidebar,
            #kt_app_header,
            #kt_app_toolbar,
            #kt_app_footer,
            .btn,
            .icon-btn,
            .breadcrumb,
            .alert,
            .card-header,
            .no-print {
                display: none !important;
            }

            body,
            .app-wrapper,
            .app-main,
            .app-content,
            .container-xxl,
            .card,
            .card-body,
            .content {
                background-color: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
            }

            .saudi-invoice {
                padding: 10px !important;
            }

            /* Remove ALL background colors for clean professional printing */
            th,
            td,
            tr,
            .bg-row,
            .table-zatca th {
                background-color: transparent !important;
                background: transparent !important;
            }

            .invoice-border {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }
        }
    </style>

    <div class="saudi-invoice w-100">
        <div class="invoice-border shadow-sm">
            <!-- الرأس (Header) -->
            <div class="row align-items-center mb-5">
                <div class="col-4 text-start">
                    <div class="qr-container">
                        {{-- @if($quotation->qr_code)
                            <img src="{{ (new \chillerlan\QRCode\QRCode)->render($quotation->qr_code) }}" alt="QR Code" style="width: 100%; height: 100%;">
                        @else
                            <div class="small text-muted pt-10 text-center">QR Code</div>
                        @endif --}}
                    </div>
                </div>
                <div class="col-4 text-center">
                    <h2 class="fw-bolder mb-1">{{ $invoiceTitleAr }}</h2>
                    <h5 class="text-muted mb-3">{{ $invoiceTitleEn }}</h5>
                    <div class="fw-bold fs-4">رقم عرض السعر: #{{ $quotation->quotation_number }}</div>
                    <div class="mt-2">
                        @if($quotation->quotation_number)
                            <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($quotation->quotation_number, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="max-width: 250px; height: 40px;">
                        @endif
                    </div>
                </div>
                <div class="col-4 text-end">
                                    @php
                    $parseVal = function($val) {
                        if (is_string($val) && str_starts_with(trim($val), '{') && str_ends_with(trim($val), '}')) {
                            $arr = json_decode($val, true);
                            if (is_array($arr)) return $arr[app()->getLocale()] ?? $arr['ar'] ?? $arr['en'] ?? $val;
                        }
                        return $val;
                    };
                    $sellerName = $zatcaSetting?->organization_name ?? ($org->name ?? '---');
                    $sellerName = $parseVal($sellerName);
                    $sellerVat = $zatcaSetting?->vat_number ?? ($org->tax_number ?? '---');
                @endphp
                    <div class="fw-bolder fs-3 mb-1">{{ $sellerName }}</div>
                    <div class="small fw-bold">الرقم الضريبي (VAT ID):</div>
                    <div class="fw-bolder fs-4 border-bottom border-dark border-2 d-inline-block">{{ $sellerVat }}
                    </div>
                    @if ($zatcaSetting && $zatcaSetting->building_number)
                        <div class="small mt-1">{{ $parseVal($zatcaSetting->building_number) }} {{ $parseVal($zatcaSetting->street_name) }}, {{ $parseVal($zatcaSetting->city_name) }}</div>
                    @endif
                </div>
            </div>

            <div class="line-divider"></div>

            <!-- أطراف المعاملة -->
            <div class="row g-10 mb-5">
                <div class="col-6">
                    <h6 class="fw-bolder border-bottom border-dark pb-1 mb-3">بيانات المشتري / Buyer Info</h6>
                    <table class="w-100">
                        <tr>
                            <td class="data-label" width="45%">الاسم / Name:</td>
                            <td class="data-value fw-bolder">{{ $quotation->customer->name ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">الرقم الضريبي / VAT ID:</td>
                            <td class="data-value">{{ $quotation->customer->vat_number ?? '---' }}</td>
                        </tr>
                        @php
                            $customerAddress = '';
                            if ($quotation->customer) {
                                $customerAddressParts = array_filter([
                                    $parseVal($quotation->customer->building_number),
                                    $parseVal($quotation->customer->street),
                                    $parseVal($quotation->customer->district),
                                    $parseVal($quotation->customer->city),
                                    $parseVal($quotation->customer->country),
                                    $parseVal($quotation->customer->postal_code),

                                ]);
                                $customerAddress = implode('، ', $customerAddressParts);
                            }
                        @endphp
                        <tr>
                            <td class="data-label">العنوان / Address:</td>
                            <td class="data-value text-muted small">{{ $customerAddress ?: '---' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 text-end">
                    <h6 class="fw-bolder border-bottom border-dark pb-1 mb-3">بيانات عرض السعر / Quotation Info</h6>
                    <table class="w-100">
                        <tr>
                            <td class="data-label text-start" width="45%">تاريخ عرض السعر / Date:</td>
                            <td class="data-value text-start fw-bolder">
                                {{ $quotation->issue_date ? $quotation->issue_date->format('Y-m-d') : '' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label text-start">تاريخ الانتهاء / Expiry Date:</td>
                            <td class="data-value text-start fw-bolder text-danger">
                                {{ $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '---' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label text-start">الفرع أو المستودع / Branch:</td>
                            <td class="data-value text-start">{{ $quotation->branch->name ?? '---' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- جدول الأصناف -->
            <table class="table-zatca">
                <thead>
                    <tr>
                        
                    <th width="5%">#</th>
                    <th width="15%">اسم الصنف<br>Item Name</th>
                    <th width="15%">الوصف<br>Description</th>
                    <th width="10%">سعر الوحدة<br>Unit Price</th>
                    <th width="10%">الكمية<br>Qty</th>
                    <th width="10%">الخصم<br>Discount</th>
                    <th width="10%">الخاضع للضريبة<br>Taxable Amt</th>
                    <th width="5%">نسبة الضريبة<br>VAT %</th>
                    <th width="10%">مبلغ الضريبة<br>VAT Amt</th>
                    <th width="10%">الإجمالي<br>Total</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($quotation->items as $index => $item)
                        @php
                            $taxableAmount = $item->quantity * $item->unit_price - $item->discount_amount;
                            $vatAmount = $item->vat_amount ?? $item->subtotal_with_vat - $taxableAmount;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                    <td class="text-start">
                                <div class="fw-bold">{{ $item->product_name }}</div>
                                <div class="d-flex align-items-center mt-1">
                                    <span
                                        class="small text-muted me-2">{{ $item->product ? $item->product->sku : '' }}</span>
                                    @if ($item->product && $item->product->sku)
                                        <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($item->product->sku, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" style="height: 25px; max-width: 100px;">
                                    @endif
                                </div>
                            </td>
                    <td class="text-start">{{ $item->description ?? '-' }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-danger small">
                                {{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2) : '-' }}</td>
                            <td>{{ number_format($taxableAmount, 2) }}</td>
                            <td>{{ number_format($item->vat_rate, 0) }}%</td>
                            <td>{{ number_format($vatAmount, 2) }}</td>
                            <td class="fw-bolder">{{ number_format($item->subtotal_with_vat, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- الملخص المالي -->
            <div class="row mt-8 g-10 justify-content-end">
                <!-- ملخص الإجماليات -->
                <div class="col-5">
                    <table class="w-100 border border-dark border-2">
                        <tr>
                            <td class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">الإجمالي
                                (Excl. VAT)</td>
                            <td class="p-2 border-bottom border-dark text-end fw-bold">
                                {{ number_format($totalBeforeDiscount, 2) }}</td>
                        </tr>
                        @if ($quotation->total_discount > 0)
                            <tr>
                                <td class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">إجمالي
                                    الخصم (Discount)</td>
                                <td class="p-2 border-bottom border-dark text-end text-danger fw-bold">-
                                    {{ number_format($quotation->total_discount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">الضريبة
                                (VAT 15%)</td>
                            <td class="p-2 border-bottom border-dark text-end fw-bold">+
                                {{ $quotation->total_vat_formatted }}</td>
                        </tr>
                        @if ($quotation->shipping_cost > 0)
                            <tr>
                                <td class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">مصاريف
                                    الشحن (Shipping)</td>
                                <td class="p-2 border-bottom border-dark text-end fw-bold">+
                                    {{ number_format($quotation->shipping_cost + $quotation->shipping_vat_amount, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr style="background: #f0f0f0; color: #000; border-top: 2px solid #000;">
                            <td class="p-3 text-start fw-bolder fs-4">الإجمالي النهائي<br><small>Grand Total</small>
                            </td>
                            <td class="p-3 text-end fw-bolder fs-3">{{ $quotation->total_inclusive_vat_formatted }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($quotation->notes)
                <!-- الشروط والأحكام / الملاحظات -->
                <div class="mt-8 p-4 border border-dark text-start">
                    <h6 class="fw-bolder border-bottom border-dark pb-2 mb-3">ملاحظات وشروط عرض السعر / Notes & Terms
                    </h6>
                    <div class="small text-dark" style="white-space: pre-line; line-height: 1.6;">
                        {!! e($quotation->notes) !!}</div>
                </div>
            @endif

            <!-- التذيل -->
            <div class="mt-8 text-center small text-muted no-print">
                <div class="line-divider"></div>
                <div class="d-flex justify-content-between px-3 fw-bold text-dark">
                    <span>إنشاء: {{ $quotation->createdBy->name ?? '---' }}</span>
                    <span>الحالة: {{ $quotation->status_text }}</span>
                </div>
                <div class="mt-2 text-center" style="font-size: 0.7rem;">
                    تم استخراج عرض السعر هذا من نظام EVIX ERP وصالح حتى تاريخ الانتهاء الموضح أعلاه.
                </div>
            </div>
        </div>
    </div>
</div>
