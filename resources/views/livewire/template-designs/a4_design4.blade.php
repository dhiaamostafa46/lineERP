                            <!-- ================== A4 DESIGN 4: VERTICAL SPLIT PREVIEW ================== -->
                            <div class="invoice-border" style="border-top: 6px solid #0f172a; padding: 30px;">
                                <!-- Title & Logo block -->
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h1 class="fw-bolder mb-1 text-dark" style="font-size: 2.2rem; letter-spacing: -1px;">
                                            {{ $previewData['invoice_title_ar'] ?? 'فاتورة ضريبية' }}
                                        </h1>
                                        @if($templateConfig['enable_english'])
                                            <h5 class="text-muted mb-0">
                                                {{ $previewData['invoice_title_en'] ?? 'Tax Invoice' }}
                                            </h5>
                                        @endif
                                        <div class="fs-4 mt-2">رقم الفاتورة @if($templateConfig['enable_english']) <small>(Invoice Number)</small> @endif: <strong
                                                class="text-dark">#{{ $previewData['invoice_number'] }}</strong></div>
                                        @if($templateConfig['show_small_barcode'] && !empty($previewData['invoice_number']))
                                            <div class="mt-2">
                                                <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($previewData['invoice_number'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode" style="height: 25px; max-width: 100%;">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($templateConfig['show_logo'])
                                            <div
                                                style="width: 100px; height: 50px; background-color: #e5e7eb; display: inline-flex; justify-content: center; align-items: center; border-radius: 4px; color: #9ca3af; font-weight: bold; font-size: 0.8rem; margin-bottom: 10px;">
                                                <img src="{{ $previewData['company_logo'] ?? '' }}" alt="Logo" style="max-height: 100%; max-width: 100%;"></div>
                                        @endif
                                        <div class="text-end d-block mt-2">
                                            <table class="table-sm table-borderless float-end" style="text-align: right;">
                                                <tr>
                                                    <td class="data-label text-start" style="padding: 2px 10px;">تاريخ الإصدار @if($templateConfig['enable_english']) / Issue Date @endif:
                                                    </td>
                                                    <td class="data-value text-start fw-bold" style="padding: 2px 10px;">
                                                        {{ $previewData['issue_date'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="data-label text-start" style="padding: 2px 10px;">رقم المرجع @if($templateConfig['enable_english']) / Reference No. @endif:
                                                    </td>
                                                    <td class="data-value text-start fw-bold" style="padding: 2px 10px;">
                                                        #{{ $previewData['customer_invoice_number'] }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="line-divider"></div>

                                <!-- Meta Details Vertical split -->
                                <div class="row mb-5">
                                    <!-- Left Side: Client & QR -->
                                    <div class="col-7">
                                        <h6 class="fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 0.05em;">بيانات المشتري @if($templateConfig['enable_english']) / Buyer Info @endif:</h6>
                                        @if($templateConfig['show_customer_data'])
                                            <table class="w-100">
                                                <tr>
                                                    <td class="data-label text-start" width="45%">الاسم @if($templateConfig['enable_english']) / Name @endif:</td>
                                                    <td class="data-value text-start fw-bolder">{{ $previewData['customer_name'] }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="data-label text-start">الرقم الضريبي @if($templateConfig['enable_english']) / VAT ID @endif:</td>
                                                    <td class="data-value text-start">{{ $previewData['customer_tax'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="data-label text-start">العنوان @if($templateConfig['enable_english']) / Address @endif:</td>
                                                    <td class="data-value text-start text-muted small">
                                                        {{ $previewData['customer_address_full'] }}</td>
                                                </tr>
                                                @if($templateConfig['show_customer_cr'])
                                                    <tr>
                                                        <td class="data-label text-start">السجل التجاري @if($templateConfig['enable_english']) / CR @endif:</td>
                                                        <td class="data-value text-start">{{ $previewData['customer_cr'] }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @endif
                                    </div>
                                    <!-- Right Side: Org details -->
                                    <div class="col-5 text-end border-start ps-5">
                                        <h6 class="fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 0.05em;">بيانات البائع @if($templateConfig['enable_english']) / Seller Info @endif:</h6>
                                        @if($templateConfig['show_company_name'])
                                            <div class="fw-bolder fs-4 mb-1">{{ $previewData['organization_name'] }}</div>
                                        @endif
                                        @if($templateConfig['show_branch_name'])
                                            <div class="fw-bold fs-6 mb-2 text-muted">{{ $previewData['branch_name'] }}</div>
                                        @endif
                                        @if($templateConfig['show_tax_number'])
                                            <div class="small fw-bold">الرقم الضريبي @if($templateConfig['enable_english']) (VAT ID) @endif: {{ $previewData['seller_vat'] }}
                                            </div>
                                        @endif
                                        @if($templateConfig['show_company_cr'])
                                            <div class="small fw-bold mt-1">السجل التجاري @if($templateConfig['enable_english']) (CR) @endif: {{ $previewData['seller_cr'] }}
                                            </div>
                                        @endif
                                        @if($templateConfig['show_address'])
                                            <div class="small mt-1 text-muted">{{ $previewData['seller_address'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- جدول الأصناف -->
                                <table class="table-zatca mb-0">
                                    <thead>
                                        <tr>
                                            @if($templateConfig['show_item_number'])
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;" width="3%">#</th>
                                            @endif
                                            @if($templateConfig['show_item_image'])
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;" width="5%">صورة @if($templateConfig['enable_english']) <br><small>Image</small> @endif</th>
                                            @endif
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                class="text-start" width="16%">اسم الصنف @if($templateConfig['enable_english']) <br><small>Item Name</small> @endif</th>
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                class="text-start" width="12%">الوصف @if($templateConfig['enable_english']) <br><small>Description</small> @endif</th>
                                            @if($templateConfig['show_item_unit'])
                                                <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                    width="6%">الوحدة @if($templateConfig['enable_english']) <br><small>Unit</small> @endif</th>
                                            @endif
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="9%">سعر الوحدة @if($templateConfig['enable_english']) <br><small>Unit Price</small> @endif</th>
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="6%">الكمية @if($templateConfig['enable_english']) <br><small>Quantity</small> @endif</th>
                                            @if($templateConfig['show_item_discount'])
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="7%">الخصم @if($templateConfig['enable_english']) <br><small>Discount</small> @endif</th>
                                            @endif
                                            @if($templateConfig['show_item_subtotal'])
                                                <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                    width="9%">الخاضع للضريبة @if($templateConfig['enable_english']) <br><small>Taxable Amount</small> @endif</th>
                                            @endif
                                            @if($templateConfig['show_item_tax_percent'])
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="8%">الضريبة @if($templateConfig['enable_english']) <br><small>VAT Rate</small> @endif</th>
                                            @endif
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="9%">مبلغ الضريبة @if($templateConfig['enable_english']) <br><small>VAT Amount</small> @endif</th>
                                            @if($templateConfig['show_item_total_with_tax'])
                                            <th style="border-bottom: 2px solid #0f172a; border-top: 1px solid #e2e8f0; background: #fff !important; color: #0f172a;"
                                                width="10%">الإجمالي @if($templateConfig['enable_english']) <br><small>Total</small> @endif</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($previewData['items'] as $item)
                                            <tr>
                                                @if($templateConfig['show_item_number'])
                                                <td style="border-bottom: 1px solid #e2e8f0;">{{ $loop->iteration }}</td>
                                                @endif
                                                @if($templateConfig['show_item_image'])
                                                <td style="border-bottom: 1px solid #e2e8f0;" class="text-center">
                                                    @if(!empty($item['image']))
                                                        <img src="{{ $item['image'] }}" style="max-height: 40px; max-width: 40px;">
                                                    @endif
                                                </td>
                                                @endif
                                                <td style="border-bottom: 1px solid #e2e8f0;" class="text-start">
                                                    <div class="fw-bold">{{ $item['product_name'] }}</div>
                                                    @if($templateConfig['show_item_barcode'] && !empty($item['barcode']))
                                                        <div class="mt-1">
                                                            <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($item['barcode'], \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Item Barcode" style="height: 20px;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="border-bottom: 1px solid #e2e8f0;" class="text-start">
                                                    {{ $item['description'] }}</td>
                                                @if($templateConfig['show_item_unit'])
                                                    <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['unit_name'] }}</td>
                                                @endif
                                                <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['unit_price'] }}</td>
                                                <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['quantity'] }}</td>
                                                @if($templateConfig['show_item_discount'])
                                                <td style="border-bottom: 1px solid #e2e8f0;" class="text-danger small">
                                                    {{ $item['discount'] }}</td>
                                                @endif
                                                @if($templateConfig['show_item_subtotal'])
                                                    <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['unit_price'] }}</td>
                                                @endif
                                                @if($templateConfig['show_item_tax_percent'])
                                                <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['vat_rate'] }}%</td>
                                                @endif
                                                <td style="border-bottom: 1px solid #e2e8f0;">{{ $item['vat_amount'] }}</td>
                                                @if($templateConfig['show_item_total_with_tax'])
                                                <td style="border-bottom: 1px solid #e2e8f0;" class="fw-bolder">{{ $item['total'] }}
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- الملخص المالي -->
                                <div class="row mt-1 g-10 justify-content-end" style="margin-left: 0; margin-right: 0;">
                                    <div class="col-7  text-start">
                                        @if($templateConfig['qr_size'] != 'none' && !empty($previewData['qr_code']))
                                            <div class="qr-container mb-3" style="width: {{ $templateConfig['qr_size'] == 'small' ? '100px' : ($templateConfig['qr_size'] == 'large' ? '180px' : '140px') }}; height: {{ $templateConfig['qr_size'] == 'small' ? '100px' : ($templateConfig['qr_size'] == 'large' ? '180px' : '140px') }}; display: inline-block; vertical-align: top;">
                                                <div
                                                    style="width: 100%; height: 100%; background: #e5e7eb; display: flex; justify-content: center; align-items: center; color: #9ca3af; font-size: 0.8rem; font-weight: bold;">
                                                    <img src="{{ (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->render($previewData['qr_code']) }}" alt="QR" style="width: 100%; height: 100%;"></div>
                                            </div>
                                        @endif

                                        @if($templateConfig['show_payment_methods'] && !empty($previewData['payment_method']))
                                            <div class="mt-2">
                                                <span class="fw-bold me-2" style="font-size: 0.85rem;">تفاصيل السداد @if($templateConfig['enable_english']) <small>(Payment Details)</small> @endif:</span>
                                                <span class="text-muted small">{{ $previewData['payment_method'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-5">
                                        <table class="w-100 border border-dark">
                                            <tr>
                                                <td
                                                    class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">
                                                    الإجمالي قبل الضريبة @if($templateConfig['enable_english']) <small>(Total Excl. VAT)</small> @endif</td>
                                                <td class="p-2 border-bottom border-dark text-end fw-bold">{{ $previewData['total_exclusive_vat'] }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">
                                                    إجمالي الخصم @if($templateConfig['enable_english']) <small>(Total Discount)</small> @endif</td>
                                                <td class="p-2 border-bottom border-dark text-end text-danger fw-bold">-
                                                    {{ $previewData['total_discount'] }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">
                                                    ضريبة القيمة المضافة @if($templateConfig['enable_english']) <small>(VAT 15%)</small> @endif</td>
                                                <td class="p-2 border-bottom border-dark text-end fw-bold">+ {{ $previewData['total_vat'] }}</td>
                                            </tr>
                                            @if(isset($previewData['shipping_cost']) && $previewData['shipping_cost'] > 0)
                                            <tr>
                                                <td
                                                    class="p-2 border-bottom border-dark data-label bg-row text-start text-dark">
                                                    مصاريف الشحن @if($templateConfig['enable_english']) <small>(Shipping Cost)</small> @endif</td>
                                                <td class="p-2 border-bottom border-dark text-end fw-bold">+
                                                    {{ $previewData['shipping_cost'] }}</td>
                                            </tr>
                                            @endif
                                            <tr style="border-top: 2px solid #000;">
                                                <td class="p-2 text-start fw-bold text-dark">الإجمالي النهائي @if($templateConfig['enable_english']) <small>(Grand Total)</small> @endif</td>
                                                <td class="p-2 text-end fw-bold text-dark">{{ $previewData['total_inclusive_vat'] }}</td>
                                            </tr>
                                        </table>
                                        @if($templateConfig['show_total_in_words'])
                                            <div class="mt-2 text-start fw-bold"> {{ $previewData['total_in_words'] }}</div>
                                        @endif
                                    </div>
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
                                <div class="mt-8 text-center small text-muted">
                                    <div class="line-divider"></div>
                                    <div class="d-flex justify-content-between px-3 fw-bold text-dark">
                                        @if($templateConfig['show_seller_name'])
                                            <span>الكاشير @if($templateConfig['enable_english']) (Cashier) @endif: {{ $previewData['created_by_name'] }}</span>
                                        @else
                                            <span></span>
                                        @endif


                                    </div>
                                    @if(!empty($templateConfig['small_receipt_notes']))
                                        <div class="mt-3 text-center fw-semibold text-dark">
                                            {{ $templateConfig['small_receipt_notes'] }}</div>
                                    @endif

                                </div>
                            </div>

                            

