                            <!-- ================== THERMAL DESIGN 1: CLASSIC RECEIPT PREVIEW (DEFAULT) ================== -->
                            <div class="invoice-border shadow-sm" style="width: 100%; box-sizing: border-box; overflow: hidden;">
                                <!-- الرأس (Header) - Centered Stacked -->
                                <div class="text-center">
                                    @if($templateConfig['show_logo'])
                                        <div class="mb-2">
                                            <div
                                                style="width: 80px; height: 40px; background-color: #e5e7eb; display: inline-flex; justify-content: center; align-items: center; border-radius: 4px; color: #9ca3af; font-weight: bold; font-size: 0.7rem; margin: 0 auto;">
                                                <img src="{{ $previewData['company_logo'] ?? '' }}" alt="Logo" style="max-height: 100%; max-width: 100%;"></div>
                                        </div>
                                    @endif
                                    @if($templateConfig['show_company_name'])
                                        <div class="fw-bolder fs-4 mb-1">{{ $previewData['organization_name'] }}</div>
                                    @endif
                                    @if($templateConfig['show_branch_name'])
                                        <div class="fw-bold fs-6 mb-2 text-muted">{{ $previewData['branch_name'] }}</div>
                                    @endif
                                    @if($templateConfig['show_tax_number'])
                                        <div class="small fw-bold">الرقم الضريبي @if($templateConfig['enable_english']) (VAT ID) @endif: {{ $previewData['seller_vat'] }}</div>
                                    @endif
                                    @if($templateConfig['show_company_cr'])
                                        <div class="small fw-bold mt-1">السجل التجاري @if($templateConfig['enable_english']) (CR) @endif: {{ $previewData['seller_cr'] }}</div>
                                    @endif
                                    @if($templateConfig['show_address'])
                                        <div class="small mt-1 text-muted">{{ $previewData['seller_address'] }}</div>
                                    @endif
                                    @if($templateConfig['qr_size'] != 'none' && !empty($previewData['qr_code']))
                                        <div class="text-center mt-3">
                                            <div class="qr-container"
                                                style="width: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; height: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; margin: 0 auto; border: none !important;">
                                                <div
                                                    style="width: 100%; height: 100%; background: #e5e7eb; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.7rem; font-weight: bold; border: 1px solid #ddd;">
                                                    <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center" style="margin-top: 15px;">
                                    <h2 class="fw-bolder mb-1" style="font-size: 1.15rem;">
                                        {{ $previewData['invoice_title_ar'] ?? 'فاتورة ضريبية مبسطة' }}
                                    </h2>
                                    @if($templateConfig['enable_english'])
                                        <h5 class="text-muted mb-2" style="font-size: 0.9rem;">
                                            {{ $previewData['invoice_title_en'] ?? 'Simplified Tax Invoice' }}
                                        </h5>
                                    @endif
                                </div>

                                <div class="line-divider"></div>

                                <!-- بيانات الفاتورة الأساسية -->
                                <div style="font-size: 0.8rem; margin-bottom: 10px;">
                                    <table class="w-100">
                                        <tr>
                                            <td class="data-label" width="40%">رقم الفاتورة @if($templateConfig['enable_english']) <small>(Invoice Number)</small> @endif:</td>
                                            <td class="data-value fw-bold">
                                                #{{ $previewData['invoice_number'] }}
                                                @if($templateConfig['show_small_barcode'] && !empty($previewData['invoice_number']))
                                                    <div class="mt-1">
                                                        <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($previewData['invoice_number'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 25px; max-width: 100%;">
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($templateConfig['show_order_number'] && !empty($previewData['order_number']))
                                            <tr>
                                                <td class="data-label">رقم الطلب @if($templateConfig['enable_english']) <small>(Order Number)</small> @endif:</td>
                                                <td class="data-value fw-bold">#{{ $previewData['order_number'] }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="data-label">تاريخ الإصدار @if($templateConfig['enable_english']) <small>(Issue Date)</small> @endif:</td>
                                            <td class="data-value">{{ $previewData['issue_date'] }}</td>
                                        </tr>
                                    </table>
                                </div>

                                @if($templateConfig['show_customer_data'])
                                    <div
                                        style="margin-bottom: 10px; border-top: 1px dashed #000; padding-top: 10px; font-size: 0.8rem;">
                                        <div class="fw-bold" style="margin-bottom: 4px;">بيانات المشتري @if($templateConfig['enable_english']) / Buyer Info @endif:</div>
                                        <table class="w-100">
                                            <tr>
                                                <td class="data-label" width="40%">الاسم @if($templateConfig['enable_english']) / Name @endif:</td>
                                                <td class="data-value fw-bold">{{ $previewData['customer_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="data-label">الرقم الضريبي @if($templateConfig['enable_english']) / VAT ID @endif:</td>
                                                <td class="data-value">{{ $previewData['customer_tax'] }}</td>
                                            </tr>
                                            @if($templateConfig['show_customer_phone'])
                                                <tr>
                                                    <td class="data-label">الجوال @if($templateConfig['enable_english']) / Phone @endif:</td>
                                                    <td class="data-value">{{ $previewData['customer_phone'] }}</td>
                                                </tr>
                                            @endif
                                            @if($templateConfig['show_customer_cr'])
                                                <tr>
                                                    <td class="data-label">السجل التجاري @if($templateConfig['enable_english']) / CR @endif:</td>
                                                    <td class="data-value">{{ $previewData['customer_cr'] }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                @endif

                                <!-- جدول الأصناف POS (3 أعمدة فقط) -->
                                <table class="table-zatca">
                                    <thead>
                                        <tr>
                                            <th class="text-right" width="50%">الصنف @if($templateConfig['enable_english']) <br><small>Item Name</small> @endif</th>
                                            <th class="text-center" width="25%">الكمية × السعر @if($templateConfig['enable_english']) <br><small>Qty x Price</small> @endif</th>
                                            @if($templateConfig['show_item_total_with_tax'])
                                            <th class="text-left" width="25%">الإجمالي @if($templateConfig['enable_english']) <br><small>Total</small> @endif</th>
                                            @else
                                            <th class="text-left" width="25%"></th>
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
                                                <td class="text-center" style="font-size: 0.8rem;">
                                                    <div class="fw-bold">{{ $item['quantity'] }} @if($templateConfig['show_item_unit']) {{ $item['unit_name'] }}@endif</div>
                                                </td>
                                                <td class="text-left fw-bold">@if($templateConfig['show_item_total_with_tax']){{ $item['total'] }}@endif</td>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- الملخص المالي -->
                                <div
                                    style="font-size: 0.85rem; margin-top: 2px; border-top: 1px dashed #000; padding-top: 2px;">
                                    <table class="w-100">
                                        <tr>
                                            <td class="text-right" width="60%">الإجمالي قبل الضريبة @if($templateConfig['enable_english']) <small>(Total Excl. VAT)</small> @endif</td>
                                            <td class="text-left fw-bold">{{ $previewData['total_exclusive_vat'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">إجمالي الخصم @if($templateConfig['enable_english']) <small>(Total Discount)</small> @endif</td>
                                            <td class="text-left text-danger fw-bold">- {{ $previewData['total_discount'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">ضريبة القيمة المضافة @if($templateConfig['enable_english']) <small>(VAT 15%)</small> @endif</td>
                                            <td class="text-left fw-bold">+ {{ $previewData['total_vat'] }}</td>
                                        </tr>
                                        @if(isset($previewData['shipping_cost']) && $previewData['shipping_cost'] > 0)
                                        <tr>
                                            <td class="text-right">مصاريف الشحن @if($templateConfig['enable_english']) <small>(Shipping Cost)</small> @endif</td>
                                            <td class="text-left fw-bold">+ {{ $previewData['shipping_cost'] }}</td>
                                        </tr>
                                        @endif
                                        <tr style="border-top: 1px dashed #000; font-size: 1rem;">
                                            <td class="text-right fw-bold" style="padding-top: 6px;">الإجمالي النهائي @if($templateConfig['enable_english']) <br><small>(Grand Total)</small> @endif</td>
                                            <td class="text-left fw-bold" style="padding-top: 6px;">
                                                {{ $previewData['total_inclusive_vat'] }}</td>
                                        </tr>
                                    </table>
                                    @if($templateConfig['show_total_in_words'])
                                        <div class="mt-2 text-start fw-bold" style="font-size: 0.8rem;">
                                            {{ $previewData['total_in_words'] }} </div>
                                    @endif
                                </div>

                                
                                <!-- Payment Terms & Validity -->
                                @if(!empty($previewData['payment_terms']) || !empty($previewData['validity_period']))
                                    <div class="mb-4 text-start p-3 border rounded bg-light text-dark text-wrap">
                                        @if(!empty($previewData['payment_terms']))
                                            <div class="mb-2"><strong>شروط الدفع @if(!empty($templateConfig['enable_english'])) / Payment Terms @endif:</strong> <br> {{ $previewData['payment_terms'] }}</div>
                                        @endif
                                        @if(!empty($previewData['validity_period']))
                                            <div><strong>مدة سريان العرض @if(!empty($templateConfig['enable_english'])) / Validity Period @endif:</strong> {{ $previewData['validity_period'] }}</div>
                                        @endif
                                    </div>
                                @endif
                                <!-- التذيل -->
                                <div class="mt-4 text-center small text-muted">
                                    <div class="line-divider"></div>


                                    @if($templateConfig['show_invoice_description'] && !empty($previewData['notes']))
                                        <div class="mt-2 text-center text-dark"
                                            style="font-size: 0.8rem; font-weight: 600; border-top: 1px dashed #000; padding-top: 5px;">
                                            {{ $previewData['notes'] }}
                                        </div>
                                    @endif
                                    @if(!empty($templateConfig['small_receipt_notes']))
                                        <div class="mt-2 text-center" style="font-size: 0.8rem; color: #000; font-weight: 600;">
                                            {{ $templateConfig['small_receipt_notes'] }}</div>
                                    @else
                                        <div class="mt-2 text-center" style="font-size: 0.75rem;">
                                            فاتورة ضريبية مبسطة {{ $previewData['status_text'] }} من هيئة الزكاة والضريبة
                                            والجمارك<br>
                                            Simplified Tax Invoice approved by ZATCA
                                        </div>
                                    @endif
                                </div>
                            </div>
