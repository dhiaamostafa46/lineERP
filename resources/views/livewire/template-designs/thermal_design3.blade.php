                            <!-- ================== THERMAL DESIGN 3: COMPACT RESTAURANT PREVIEW ================== -->
                            <div class="invoice-border" style="border: 1px dashed #000; padding: 20px 8px 20px 8px; border-radius: 4px; width: 100%; box-sizing: border-box;">
                                <!-- Prominent Order Header -->
                                <div class="text-center"
                                    style="border-bottom: 2px dashed #000; padding-bottom: 8px; margin-bottom: 10px;">
                                    <h1 class="mb-1 fw-bolder" style="font-size: 1.8rem;">رقم الفاتورة @if($templateConfig['enable_english']) <small style="font-size: 0.9rem;">(Invoice Number)</small> @endif
                                        #{{ $previewData['invoice_number'] }}</h1>
                                    @if($templateConfig['show_small_barcode'] && !empty($previewData['invoice_number']))
                                        <div class="mt-2 mb-2">
                                            <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($previewData['invoice_number'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 30px; max-width: 100%;">
                                        </div>
                                    @endif
                                    <div class="small fw-bold">تاريخ الإصدار @if($templateConfig['enable_english']) (Issue Date) @endif: {{ $previewData['issue_date'] }}</div>
                                    <div class="small mt-1">الكاشير @if($templateConfig['enable_english']) (Cashier) @endif: {{ $previewData['created_by_name'] }}</div>
                                </div>

                                <div class="text-center mb-3">
                                    @if($templateConfig['show_logo'])
                                        <div class="mb-2">
                                            <div
                                                style="width: 80px; height: 35px; background-color: #e5e7eb; display: inline-flex; justify-content: center; align-items: center; border-radius: 4px; color: #9ca3af; font-weight: bold; font-size: 0.7rem; margin: 0 auto;">
                                                <img src="{{ $previewData['company_logo'] ?? '' }}" alt="Logo" style="max-height: 100%; max-width: 100%;"></div>
                                        </div>
                                    @endif
                                    @if($templateConfig['show_company_name'])
                                        <div class="fw-bolder fs-5 mb-1">{{ $previewData['organization_name'] }}</div>
                                    @endif
                                    @if($templateConfig['show_branch_name'])
                                        <div class="fw-bold fs-7 mb-2 text-muted">{{ $previewData['branch_name'] }}</div>
                                    @endif
                                    @if($templateConfig['show_tax_number'])
                                        <div class="small fw-bold">الرقم الضريبي @if($templateConfig['enable_english']) (VAT ID) @endif: {{ $previewData['seller_vat'] }}</div>
                                    @endif
                                    @if($templateConfig['show_company_cr'])
                                        <div class="small fw-bold mt-1">السجل التجاري @if($templateConfig['enable_english']) (CR) @endif: {{ $previewData['seller_cr'] }}</div>
                                    @endif
                                    @if($templateConfig['show_address'])
                                        <div class="small mt-1 text-center text-muted">{{ $previewData['seller_address'] }}</div>
                                    @endif
                                    @if($templateConfig['qr_size'] != 'none' && !empty($previewData['qr_code']))
                                        <div class="qr-container mt-3" style="width: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; height: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; margin: 0 auto;">
                                            <div
                                                style="width: 100%; height: 100%; background: #e5e7eb; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.7rem; font-weight: bold; border: 1px solid #ddd;">
                                                <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;"></div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center mb-3">
                                    <h2 class="fw-bolder mb-1" style="font-size: 1.1rem;">
                                        {{ $previewData['invoice_title_ar'] ?? 'فاتورة ضريبية مبسطة' }}
                                    </h2>
                                    @if($templateConfig['enable_english'])
                                        <h5 class="text-muted mb-2" style="font-size: 0.9rem;">
                                            {{ $previewData['invoice_title_en'] ?? 'Simplified Tax Invoice' }}
                                        </h5>
                                    @endif
                                </div>

                                @if($templateConfig['show_customer_data'])
                                    <div style="margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px; font-size: 0.8rem;">
                                        <div class="fw-bold mb-2">بيانات المشتري @if($templateConfig['enable_english']) / Buyer Info @endif:</div>
                                        <table class="w-100">
                                            <tr>
                                                <td class="data-label text-right" width="40%">الاسم @if($templateConfig['enable_english']) / Name @endif:</td>
                                                <td class="data-value text-left fw-bolder">{{ $previewData['customer_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="data-label text-right">الرقم الضريبي @if($templateConfig['enable_english']) / VAT ID @endif:</td>
                                                <td class="data-value text-left">{{ $previewData['customer_tax'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="data-label text-right">العنوان @if($templateConfig['enable_english']) / Address @endif:</td>
                                                <td class="data-value text-left text-muted" style="font-size: 0.75rem;">{{ $previewData['customer_address_full'] }}</td>
                                            </tr>
                                            @if($templateConfig['show_customer_phone'])
                                                <tr><td class="data-label text-right">الجوال @if($templateConfig['enable_english']) / Phone @endif:</td><td class="data-value text-left">{{ $previewData['customer_phone'] }}</td></tr>
                                            @endif
                                            @if($templateConfig['show_customer_cr'])
                                                <tr><td class="data-label text-right">السجل التجاري @if($templateConfig['enable_english']) / CR @endif:</td><td class="data-value text-left">{{ $previewData['customer_cr'] }}</td></tr>
                                            @endif
                                        </table>
                                    </div>
                                @endif

                                <table class="table-zatca mb-0" style="margin-top: 8px;">
                                    <thead>
                                        <tr>
                                            <th class="text-right"
                                                style="border-bottom: 2px dashed #000; border-top: 2px dashed #000; padding: 5px 2px;"
                                                width="60%">الصنف @if($templateConfig['enable_english']) <br><small>Item Name</small> @endif</th>
                                            <th class="text-center"
                                                style="border-bottom: 2px dashed #000; border-top: 2px dashed #000; padding: 5px 2px;"
                                                width="20%">الكمية @if($templateConfig['enable_english']) <br><small>Qty</small> @endif</th>
                                            @if($templateConfig['show_item_total_with_tax'])
                                            <th class="text-left"
                                                style="border-bottom: 2px dashed #000; border-top: 2px dashed #000; padding: 5px 2px;"
                                                width="20%">الإجمالي @if($templateConfig['enable_english']) <br><small>Total</small> @endif</th>
                                            @else
                                            <th class="text-left"
                                                style="border-bottom: 2px dashed #000; border-top: 2px dashed #000; padding: 5px 2px;"
                                                width="20%"></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($previewData['items'] as $item)
                                            <tr>
                                                <td class="text-right">
                                                    @if($templateConfig['show_item_number'])<span class="me-1">{{ $loop->iteration }} -</span>@endif<strong>{{ $item['product_name'] }}</strong>
                                                    @if($templateConfig['show_item_image'] && !empty($item['image']))
                                                    <div class="mt-1 mb-1"><img src="{{ $item['image'] }}" style="max-height: 25px; max-width: 25px; object-fit: contain; border-radius: 2px;"></div>
                                                    @endif
                                                    @if($templateConfig['show_item_barcode'] && !empty($item['barcode']))
                                                        <div class="mt-1">
                                                            <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($item['barcode'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Item Barcode" style="height: 20px;">
                                                        </div>
                                                    @endif
                                                    @if($templateConfig['show_item_discount'])
                                                        <div style="font-size: 0.75rem; color: #dc3545;">الخصم @if($templateConfig['enable_english']) (Discount) @endif: {{ $item['discount'] }}</div>
                                                    @endif
                                                    @if($templateConfig['show_item_subtotal'])
                                                        <div style="font-size: 0.75rem; color: #6b7280;">الخاضع للضريبة @if($templateConfig['enable_english']) (Taxable Amount) @endif: {{ $item['unit_price'] }}</div>
                                                    @endif
                                                    @if($templateConfig['show_item_tax_percent'])
                                                        <div style="font-size: 0.75rem; color: #6b7280;">الضريبة @if($templateConfig['enable_english']) (VAT Rate) @endif: {{ $item['vat_rate'] }}%</div>
                                                    @endif
                                                </td>
                                                <td class="text-center"><div class="fw-bold">{{ $item['quantity'] }} @if($templateConfig['show_item_unit']){{ $item['unit_name'] }}@endif</div></td>
                                                <td class="text-left fw-bold">@if($templateConfig['show_item_total_with_tax']){{ $item['total'] }}@endif</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div style="font-size: 0.85rem; margin-top: 2px; border-top: 1px dashed #000; padding-top: 2px;">
                                    <table class="w-100">
                                        <tr>
                                            <td class="text-right" width="60%">الإجمالي قبل الضريبة @if($templateConfig['enable_english']) <small>(Total Excl. VAT)</small> @endif</td>
                                            <td class="text-left">{{ $previewData['total_exclusive_vat'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">إجمالي الخصم @if($templateConfig['enable_english']) <small>(Total Discount)</small> @endif</td>
                                            <td class="text-left text-danger fw-bold">- {{ $previewData['total_discount'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">ضريبة القيمة المضافة @if($templateConfig['enable_english']) <small>(VAT 15%)</small> @endif</td>
                                            <td class="text-left">+ {{ $previewData['total_vat'] }}</td>
                                        </tr>
                                        @if(isset($previewData['shipping_cost']) && $previewData['shipping_cost'] > 0)
                                        <tr>
                                            <td class="text-right">مصاريف الشحن @if($templateConfig['enable_english']) <small>(Shipping Cost)</small> @endif</td>
                                            <td class="text-left">+ {{ $previewData['shipping_cost'] }}</td>
                                        </tr>
                                        @endif
                                        <tr style="border-top: 2px dashed #000; font-size: 1.1rem;">
                                            <td class="text-right fw-bold text-dark" style="padding-top: 6px;">الإجمالي النهائي @if($templateConfig['enable_english']) <br><small>(Grand Total)</small> @endif</td>
                                            <td class="text-left fw-bold text-dark" style="padding-top: 6px;">{{ $previewData['total_inclusive_vat'] }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="text-center small text-muted mt-4">
                                    <div style="border-top: 2px dashed #000; margin: 8px 0;"></div>

                                    @if(!empty($templateConfig['small_receipt_notes']))
                                        <div class="mt-2 text-center" style="font-size: 0.8rem;">
                                            {{ $templateConfig['small_receipt_notes'] }}</div>
                                    @endif
                                </div>
                            </div>

