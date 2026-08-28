                            <!-- ================== THERMAL DESIGN 2: MODERN POS PREVIEW ================== -->
                            <div class="invoice-border" style="padding: 20px 8px 20px 8px; width: 100%; box-sizing: border-box;">
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
                                        <div class="qr-container mt-3"
                                            style="width: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; height: {{ $templateConfig['qr_size'] == 'small' ? '80px' : ($templateConfig['qr_size'] == 'large' ? '140px' : '110px') }}; margin: 0 auto;">
                                            <div
                                                style="width: 100%; height: 100%; background: #e5e7eb; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.7rem; font-weight: bold; border: 1px solid #ddd;">
                                                <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;"></div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center" style="margin-top: 15px;">
                                    <div class="mock-thermal-d2-box"
                                        style="width: auto; height: auto; border: 1px solid #000; border-radius: 4px; padding: 4px 10px; display: inline-block; font-weight: bold;">
                                        {{ $previewData['invoice_title_ar'] ?? 'فاتورة ضريبية مبسطة' }}
                                    </div>
                                    @if($templateConfig['enable_english'])
                                        <h5 class="text-muted mb-2 mt-1" style="font-size: 0.9rem;">
                                            {{ $previewData['invoice_title_en'] ?? 'Simplified Tax Invoice' }}
                                        </h5>
                                    @endif
                                </div>

                                <div style="border-top: 1px dotted #000; margin: 10px 0;"></div>

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
                                        <tr>
                                            <td class="data-label">تاريخ الإصدار @if($templateConfig['enable_english']) <small>(Issue Date)</small> @endif:</td>
                                            <td class="data-value">{{ $previewData['issue_date'] }}</td>
                                        </tr>
                                    </table>
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

                                <table class="table-zatca mb-0">
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
                                                    <div class="fw-bold">x {{ $item['quantity'] }} @if($templateConfig['show_item_unit']) {{ $item['unit_name'] }}@endif</div>
                                                    <span class="text-muted">{{ $item['unit_price'] }}</span>
                                                </td>
                                                <td class="text-left fw-bold">@if($templateConfig['show_item_total_with_tax']){{ $item['total'] }}@endif</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div
                                    style="font-size: 0.85rem; margin-top: 2px; border-top: 1px dotted #000; padding-top: 2px;">
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
                                        <tr style="border-top: 1px dotted #000; font-size: 1rem;">
                                            <td class="text-right fw-bold text-dark" style="padding-top: 6px;">الإجمالي النهائي @if($templateConfig['enable_english']) <br><small>(Grand Total)</small> @endif</td>
                                            <td class="text-left fw-bold text-dark" style="padding-top: 6px;">{{ $previewData['total_inclusive_vat'] }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="mt-1 text-center small text-muted">
                                    <div style="border-top: 1px dotted #000; margin: 10px 0;"></div>

                                    @if(!empty($templateConfig['small_receipt_notes']))
                                        <div class="mt-2 text-center" style="font-size: 0.8rem; font-weight: 600; color: #000;">
                                            {{ $templateConfig['small_receipt_notes'] }}</div>
                                    @endif
                                </div>
                            </div>
