@php
    $langPrefix = $langPrefix ?? 'invoices::models/purchase_invoices';
    $getProductUrl = $getProductUrl ?? route('Lookup.getproducts');
    $invoiceSettings = \Modules\Invoices\App\Helpers\InvoiceHelper::getSettings();
    $pricesIncludeVat = $invoiceSettings->prices_include_vat ? 'true' : 'false';

    // dd($pricesIncludeVat);
    $isSale = $isSale ?? false;
@endphp
<script>
    const pricesIncludeVat = {{ $pricesIncludeVat }};
    const isSale = {{ $isSale ? 'true' : 'false' }};
    // Initialize itemIndex based on existing rows if editing, otherwise 0
    let itemIndex = document.querySelectorAll('.item-row').length;
    const globalTaxes = @json($taxes_data ?? []);
   
    const paymentOptions = @json($payments ?? []);

    const translations = {
        piece: "{{ __($langPrefix . '.ui.piece') }}",
        payment_method_placeholder: "{{ __($langPrefix . '.fields.payment_method_placeholder') }}",
        payment_method_label: "{{ __($langPrefix . '.fields.payment_method_label') }}",
        payment_amount_label: "{{ __($langPrefix . '.fields.payment_amount_label') }}",
        currency_symbol: "{{ __($langPrefix . '.fields.currency_symbol') }}",
        min_item_error: "{{ __($langPrefix . '.ui.min_item_error') }}",
        description: "{{ __($langPrefix . '.fields.description') }}",
        payment_status_credit_full: "{{ Lang::has($langPrefix . '.ui.payment_status_credit_full') ? __($langPrefix . '.ui.payment_status_credit_full') : 'طريقة السداد: آجل بالكامل (على حساب العميل)' }}",
        payment_status_credit_desc: "{{ Lang::has($langPrefix . '.ui.payment_status_credit_desc') ? __($langPrefix . '.ui.payment_status_credit_desc') : 'لم يتم اختيار وسيلة دفع، سيتم تسجيل إجمالي الفاتورة كـ دَيْن مستحق على الحساب.' }}"
    };

    $(document).ready(function() {
        if ($('.select2-supplier').length) {
            $('.select2-supplier').select2({
                theme: 'bootstrap-5'
            });
        }
        if ($('.select2-general').length) {
            $('.select2-general').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        
        @php
            $oldPayments = old(
                'payments',
                isset($invoice) && $invoice->payments
                    ? $invoice->payments
                        ->map(function ($p) {
                            return ['account_id' => $p->account_id, 'amount' => (float) $p->amount, 'payment_method_code' => $p->payment_method_code ?? null];
                        })
                        ->toArray()
                    : (isset($salesInvoice) && $salesInvoice->payments
                        ? $salesInvoice->payments
                            ->map(function ($p) {
                                return ['account_id' => $p->account_id, 'amount' => (float) $p->amount, 'payment_method_code' => $p->payment_method_code ?? null];
                            })
                            ->toArray()
                        : (isset($purchaseInvoice) && $purchaseInvoice->payments
                            ? $purchaseInvoice->payments
                                ->map(function ($p) {
                                    return ['account_id' => $p->account_id, 'amount' => (float) $p->amount, 'payment_method_code' => $p->payment_method_code ?? null];
                                })
                                ->toArray()
                            : (isset($salesReturn) && $salesReturn->payments
                                ? $salesReturn->payments
                                    ->map(function ($p) {
                                        return ['account_id' => $p->account_id, 'amount' => (float) $p->amount, 'payment_method_code' => $p->payment_method_code ?? null];
                                    })
                                    ->toArray()
                                : [])
                            )
                        ),
            );
        @endphp
        // Initial payments if exist
        let oldPayments = @json($oldPayments);
        let paymentsArray = Object.values(oldPayments);
        let paymentsCount = paymentsArray.length;

        if (paymentsCount > 0) {
            paymentsArray.forEach((p, index) => {
                if (p.account_id || p.amount > 0) {
                    let isAutoSync = (paymentsCount === 1);
                    addPayment(p.account_id, parseFloat(p.amount) || 0, isAutoSync, p.payment_method_code);
                }
            });
        }

        // Initialize dynamic product search with AJAX
        let $productSelect = $('#product_search');
        if ($productSelect.length) {
            $productSelect.select2({
                placeholder: '+ ابحث عن منتج، أو أضف سطر فارغ...',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                ajax: {
                    url: "{{ $getProductUrl }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        let requestData = {
                            q: params.term || '',
                            page: params.page || 1,
                            search_type: isSale ? 'location' : 'products',
                            lang: "{{ app()->getLocale() }}",
                            store: "",
                            is_sale: isSale,
                            // purchase_invoice_id: $('[name="parent_id"]').val() || ''
                        };

                        return requestData;
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        // استخراج النتائج من مصفوفة results التي تعيدها الخدمة4
                        let mappedResults = data.results.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text, // الخدمة تعيد النص منسقاً جاهزاً
                                cost_price: item.cost_price || 0,
                                sale_price: item.sale_price || 0,
                                available_quantity: item.quantity || 0,
                                base_unit: (item.units && item.units.length > 0) ? item
                                    .units[0]
                                    .name : '--',
                                units: item.units || [],
                                tax_id: item.tax_id || '',
                                is_size: item.is_size || false
                            };

                        });

                        return {
                            results: mappedResults,
                            pagination: {
                                more: data.pagination && data.pagination
                                    .more // الوصول لخاصية more داخل كائن pagination
                            }
                        };
                    },
                    cache: true
                }
            });

            // Removed branch restriction requirement as per user request

            $productSelect.on('select2:select', function(e) {
                let data = e.params.data;
                let isSize = data.is_size ? 1 : 0;

                // البحث عن المنتج في الجدول قبل الإضافة
                let existingRow = findExistingProductRow(data.id, isSize);
                if (existingRow) {
                    let qtyInput = existingRow.querySelector('.item-qty');
                    qtyInput.value = (parseFloat(qtyInput.value) || 0) + 1;
                    calcTotals();
                } else {
                    let selectedPrice = isSale ? data.sale_price : data.cost_price;
                    let selectedTax = data.tax_id || '';
                    addItemBlock(data.id, data.text, data.units, 1, selectedPrice, selectedTax, data.is_size ?
                        1 : 0);
                }
                $productSelect.val(null).trigger('change');
            });
        }

        // تحديث البحث عند تغيير الفاتورة الأصلية أو المورد
        $(document).on('change', '[name="parent_id"], [name="supplier_id"]', function() {
            // إعادة تعيين حقل البحث لإجبار Select2 على جلب بيانات جديدة بناءً على الفاتورة الجديدة
            $('#product_search').val(null).trigger('change');
        });

        // إطلاق الحسابات النهائية بعد تهيئة المدفوعات والمنتجات
        calcTotals();
        updateRowIndices();
    });

    function findExistingProductRow(productId, isSize) {
        if (!productId) return null;
        let sizeFlag = isSize ? '1' : '0';
        let rows = document.querySelectorAll('.item-row');
        for (let row of rows) {
            let idInput = row.querySelector('input[name*="[product_id]"]');
            let sizeInput = row.querySelector('input[name*="[have_sizes]"]');
            let rowIsSize = sizeInput ? sizeInput.value : '0';
            if (idInput && idInput.value == productId && rowIsSize == sizeFlag) return row;
        }
        return null;
    }

    function addBlankRow() {
        addItemBlock('', '', [], 1, 0, 15, 0);
    }

    function addItemBlock(id, name, units, qty, price, selectedVat, isSize) {
        const tbody = document.getElementById('items_body');
        const emptyRow = document.getElementById('empty_row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = 'item-row text-center';

        // Pricing Logic based on user requirement:
        let initialPrice = price;
        let unitOptions = '';

        if (units && units.length > 0) {
            units.forEach((u, i) => {
                let isFirst = (i === 0);
                let unitPrice = isSale ? (u.sale_price || 0) : (u.cost_price || 0);
                unitOptions +=
                    `<option value="${u.id || ''}" data-price="${unitPrice}" ${isFirst ? 'selected' : ''}>${u.name}</option>`;
                if (isFirst) initialPrice = unitPrice;
            });
        } else {
            unitOptions = `<option value="" data-price="${price}" selected>-----</option>`;
            initialPrice = price;
        }

        let taxOptions = '';
        let isAnySelected = false;
        
        let taxesArray = Object.entries(globalTaxes);
        taxesArray.forEach(([taxId, taxObj], index) => {
            let isSelected = (taxId == selectedVat);
            if (isSelected) isAnySelected = true;
            
            taxOptions += `<option value="${taxId}" data-rate="${taxObj.rate}" ${isSelected ? 'selected' : ''}>${taxObj.name}</option>`;
        });

        // If nothing was selected and we have options, select the first one
        if (!isAnySelected && taxesArray.length > 0) {
            taxOptions = '';
            taxesArray.forEach(([taxId, taxObj], index) => {
                let isSelected = (index === 0);
                taxOptions += `<option value="${taxId}" data-rate="${taxObj.rate}" ${isSelected ? 'selected' : ''}>${taxObj.name}</option>`;
            });
        }

        if (taxOptions === '') {
            taxOptions =
                `<option value="" data-rate="15" ${selectedVat == 15 ? 'selected' : ''}>15%</option><option value="" data-rate="0" ${selectedVat == 0 ? 'selected' : ''}>0%</option>`;
        }

        tr.innerHTML = '<td><span class="fw-bold text-muted item-number"></span></td>' +
            '<td class="pe-3 text-start">' +
            '<input type="hidden" name="items[' + itemIndex + '][product_id]" value="' + id + '">' +
            '<input type="hidden" name="items[' + itemIndex + '][have_sizes]" value="' + isSize + '">' +
            '<input type="text" name="items[' + itemIndex +
            '][product_name]" class="form-control form-control-sm fs-7 bg-light-soft" placeholder="اسم المنتج" value="' + name +
            '" readonly style="text-align: right;">' +
            '</td>' +
            '<td><input type="text" name="items[' + itemIndex + '][description]" class="form-control form-control-sm fs-7" placeholder="' + (translations.description || 'الوصف') + '" value=""></td>' +
            '<td><select name="items[' + itemIndex +
            '][unit_id]" class="form-select form-select-sm fs-7 item-unit-select" onchange="updateRowPrice(this)">' +
            unitOptions + '</select></td>' +

            '<td><input type="number" name="items[' + itemIndex +
            '][quantity]" class="form-control form-control-sm fs-7 item-qty text-center" value="' + qty +
            '" min="1" step="any" oninput="calcTotals()"></td>' +
            '<td><input type="number" name="items[' + itemIndex +
            '][unit_price]" class="form-control form-control-sm fs-7 item-price text-center" value="' + initialPrice +
            '" min="0" step="0.01" oninput="calcTotals()"></td>' +
            '<td>' +
            '<div class="input-group input-group-sm">' +
            '<input type="number" name="items[' + itemIndex +
            '][number_discount]" class="form-control fs-8 item-discount text-center" value="0" step="0.01" oninput="calcTotals()">' +
            '<select name="items[' + itemIndex +
            '][type_discount]" class="form-select fs-8 item-discount-type" style="max-width: 60px" onchange="calcTotals()">' +
            '<option value="1">%</option>' +
            '<option value="2">' + translations.currency_symbol + '</option>' +
            '</select>' +
            '</div>' +
            '<input type="hidden" name="items[' + itemIndex +
            '][total_discount]" class="item-discount-hidden" value="0">' +
            '</td>' +
            '<td>' +
            '<select name="items[' + itemIndex +
            '][tax_id]" class="form-select form-select-sm fs-8 item-vat-rate text-center" onchange="calcTotals()">' +
            taxOptions +
            '</select>' +
            '<input type="hidden" name="items[' + itemIndex + '][vat_rate]" class="item-vat-rate-hidden" value="0">' +
            '<input type="hidden" name="items[' + itemIndex + '][vat_amount]" class="item-vat-amount" value="0">' +
            '</td>' +
            '<td><input type="number" name="items[' + itemIndex +
            '][subtotal_with_vat]" class="form-control form-control-sm fs-7 item-subtotal item-subtotal-display fw-bold text-primary text-center bg-light" value="0" readonly></td>' +
            '<td>' +
            '<div class="d-flex justify-content-center gap-1">' +
            '<button type="button" class="btn btn-icon btn-sm btn-light-primary border-0 h-30px w-30px" onclick="copyItemRow(this)"><i class="ki-duotone ki-copy fs-5"><span class="path1"></span><span class="path2"></span></i></button>' +
            '<button type="button" class="btn btn-icon btn-sm btn-light-danger border-0 h-30px w-30px" onclick="removeItemRow(this)"><i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></button>' +
            '</div>' +
            '</td>';

        tbody.appendChild(tr);
        itemIndex++;
        updateRowIndices();
        calcTotals();
        itemIndex++;
        calcTotals();
    }

    function updateRowPrice(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;

        const priceInput = row.querySelector('.item-price');
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
            calcTotals();
        }
    }

    function updateShippingVatRate(select) {
        let rate = select.options[select.selectedIndex].getAttribute('data-rate') || 0;
        let rateInput = document.getElementById('shipping_vat_rate');
        if (rateInput) {
            rateInput.value = rate;
        }
        calcTotals();
    }

    function calcTotals() {
        const isPricesIncludeVat = document.getElementById('prices_include_vat') ? document.getElementById('prices_include_vat').checked : pricesIncludeVat;
        let totalNetEntered = 0;
        let linesData = [];

        document.querySelectorAll('.item-row').forEach((row) => {
            let qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            let price = parseFloat(row.querySelector('.item-price').value) || 0;
            let discountVal = parseFloat(row.querySelector('.item-discount').value) || 0;
            let discountType = row.querySelector('.item-discount-type').value;
            let vatRateSelect = row.querySelector('.item-vat-rate');
            let vatRate = 0;
            if (vatRateSelect && vatRateSelect.options.length > 0 && vatRateSelect.selectedIndex >= 0) {
                vatRate = parseFloat(vatRateSelect.options[vatRateSelect.selectedIndex].getAttribute('data-rate')) || 0;
            }
            let vatRateHidden = row.querySelector('.item-vat-rate-hidden');
            if (vatRateHidden) vatRateHidden.value = vatRate;

            let rowGrossEntered = Math.round(qty * price * 100) / 100;
            let lineDiscEntered = (discountType == '1') ? Math.round(rowGrossEntered * (discountVal / 100) *
                100) / 100 : discountVal;
            if (lineDiscEntered > rowGrossEntered) lineDiscEntered = rowGrossEntered;

            let netLineEntered = Math.round((rowGrossEntered - lineDiscEntered) * 100) / 100;

            totalNetEntered += netLineEntered;

            linesData.push({
                row: row,
                vatRate: vatRate,
                rowGrossEntered: rowGrossEntered,
                lineDiscEntered: lineDiscEntered,
                netLineEntered: netLineEntered
            });
        });

        let totalInvoiceDiscInput = document.getElementById('total_invoice_discount_input');
        let totalInvoiceDiscTypeInput = document.getElementById('total_invoice_discount_type');
        let invDiscVal = totalInvoiceDiscInput ? (parseFloat(totalInvoiceDiscInput.value) || 0) : 0;
        let invDiscType = totalInvoiceDiscTypeInput ? totalInvoiceDiscTypeInput.value : '1';

        let globalDiscEntered = 0;
        if (invDiscType == '1') {
            globalDiscEntered = Math.round(totalNetEntered * (invDiscVal / 100) * 100) / 100;
        } else {
            globalDiscEntered = invDiscVal;
        }
        if (globalDiscEntered > totalNetEntered) globalDiscEntered = totalNetEntered;

        let netInvoiceEntered = totalNetEntered - globalDiscEntered;
        let globalDiscountFactor = totalNetEntered > 0 ? (netInvoiceEntered / totalNetEntered) : 1;

        let sumBaseExclusive = 0;
        let sumDiscExclusive = 0;
        let sumVatUnrounded = 0;
        let sumVat = 0;
        let finalInvoiceTotal = 0;

        let shippingCostInput = document.getElementById('shipping_cost');
        let shippingTaxIdInput = document.getElementById('shipping_tax_id');
        let shippingVatRateHidden = document.getElementById('shipping_vat_rate_hidden');
        let shippingCost = shippingCostInput ? (parseFloat(shippingCostInput.value) || 0) : 0;
        let shippingVatRate = 0;
        if (shippingTaxIdInput) {
            let val = shippingTaxIdInput.value;
            if (globalTaxes[val] && globalTaxes[val].rate !== undefined) {
                shippingVatRate = parseFloat(globalTaxes[val].rate);
            } else if (shippingTaxIdInput.options.length > 0 && shippingTaxIdInput.selectedIndex >= 0) {
                shippingVatRate = parseFloat(shippingTaxIdInput.options[shippingTaxIdInput.selectedIndex].getAttribute('data-rate')) || 0;
            } else {
                shippingVatRate = parseFloat(val) || 0;
            }
            if (shippingVatRateHidden) shippingVatRateHidden.value = shippingVatRate;
        }
        let shippingVatAmount = shippingCost * (shippingVatRate / 100);

        linesData.forEach(data => {
            let finalNetEntered = data.netLineEntered * globalDiscountFactor;

            let baseExclusive, finalNetExclusive, vatAmount, unroundedVat;
            let totalLineDiscEntered = data.rowGrossEntered - finalNetEntered;
            let totalLineDiscExclusive;

            if (isPricesIncludeVat && data.vatRate > 0) {
                let divFactor = 1 + (data.vatRate / 100);
                baseExclusive = Math.round((data.rowGrossEntered / divFactor) * 100) / 100;
                finalNetExclusive = Math.round((finalNetEntered / divFactor) * 100) / 100;
                unroundedVat = finalNetEntered - finalNetExclusive;
                vatAmount = Math.round(unroundedVat * 100) / 100;
                totalLineDiscExclusive = Math.round((totalLineDiscEntered / divFactor) * 100) / 100;
            } else {
                baseExclusive = data.rowGrossEntered;
                finalNetExclusive = finalNetEntered;
                unroundedVat = finalNetExclusive * (data.vatRate / 100);
                vatAmount = Math.round(unroundedVat * 100) / 100;
                totalLineDiscExclusive = totalLineDiscEntered;
            }

            let finalSubtotalWithVat = Math.round((finalNetExclusive + vatAmount) * 100) / 100;

            sumBaseExclusive += baseExclusive;
            sumDiscExclusive += totalLineDiscExclusive;
            sumVatUnrounded += unroundedVat;
            sumVat += vatAmount;
            finalInvoiceTotal += finalSubtotalWithVat;

            data.row.querySelector('.item-vat-amount').value = vatAmount.toFixed(2);
            data.row.querySelector('.item-subtotal').value = finalSubtotalWithVat.toFixed(2);
            data.row.querySelector('.item-discount-hidden').value = data.lineDiscEntered.toFixed(2);
        });

        let totalInvoiceVat = Math.round((sumVatUnrounded + shippingVatAmount) * 100) / 100;
        let totalNetExclusive = Math.round((sumBaseExclusive - sumDiscExclusive) * 100) / 100;
        let totalInvoiceInclusive = Math.round((totalNetExclusive + totalInvoiceVat + shippingCost) * 100) / 100;

        let lblTotalExclusive = document.getElementById('lbl_total_exclusive');
        let lblTotalDiscount = document.getElementById('lbl_total_discount');
        let lblTotalVat = document.getElementById('lbl_total_vat');
        let lblTotalInclusive = document.getElementById('lbl_total_inclusive_display');
        let lblShippingCost = document.getElementById('lbl_shipping_cost');
        let lblShippingVatAmount = document.getElementById('lbl_shipping_vat_amount');
        let lblSummaryShippingVat = document.getElementById('lbl_summary_shipping_vat');

        if (lblTotalExclusive) lblTotalExclusive.innerText = sumBaseExclusive.toFixed(2);
        if (lblTotalDiscount) lblTotalDiscount.innerText = sumDiscExclusive.toFixed(2);
        if (lblTotalVat) lblTotalVat.innerText = totalInvoiceVat.toFixed(2);
        if (lblTotalInclusive) lblTotalInclusive.innerText = totalInvoiceInclusive.toFixed(2);
        if (lblShippingCost) lblShippingCost.innerText = shippingCost.toFixed(2);
        if (lblShippingVatAmount) lblShippingVatAmount.value = shippingVatAmount.toFixed(2);
        if (lblSummaryShippingVat) lblSummaryShippingVat.innerText = shippingVatAmount.toFixed(2);

        // Toggle shipping section in summary
        let summaryShippingSection = document.getElementById('summary_shipping_section');
        if (summaryShippingSection) {
            if (shippingCost > 0) {
                summaryShippingSection.style.display = 'block';
            } else {
                summaryShippingSection.style.display = 'none';
            }
        }

        // Update hidden shipping vat amount
        let inputShippingVatAmount = document.getElementById('shipping_vat_amount');
        if (inputShippingVatAmount) inputShippingVatAmount.value = shippingVatAmount.toFixed(2);

        let inputTotalExclusive = document.getElementById('total_exclusive_vat');
        let inputTotalDiscount = document.getElementById('total_discount');
        let inputTotalVat = document.getElementById('total_vat');
        let inputTotalInclusive = document.getElementById('total_inclusive_vat');

        // Update hidden inputs
        if (inputTotalExclusive) inputTotalExclusive.value = sumBaseExclusive.toFixed(2);
        if (inputTotalDiscount) inputTotalDiscount.value = sumDiscExclusive.toFixed(2);
        if (inputTotalVat) inputTotalVat.value = totalInvoiceVat.toFixed(2);
        if (inputTotalInclusive) inputTotalInclusive.value = totalInvoiceInclusive.toFixed(2);

        let paymentRows = document.querySelectorAll('.payment-row');
        if (paymentRows.length === 1) {
            let pAmt = paymentRows[0].querySelector('.payment-amount');
            // التحديث التلقائي إذا كان الحقل "تلقائي" أو قيمته صفر أو عند التحميل الأول (بدون سمة data-auto)
            if (pAmt && (pAmt.getAttribute('data-auto') === 'true' || parseFloat(pAmt.value) === 0 || pAmt.getAttribute(
                    'data-auto') === null)) {
                pAmt.value = finalInvoiceTotal.toFixed(2);
                pAmt.setAttribute('data-auto', 'true');
            }
        }
        checkPaymentBalance();
    }

    function addPayment(selectedAccountId = '', amount = 0, isAuto = null, existingCode = null) {
        const container = document.getElementById('payments_container');
        if (!container) return;

        let maxTotalInput = document.getElementById('total_inclusive_vat');
        let totalExpected = maxTotalInput ? (parseFloat(maxTotalInput.value) || 0) : 0;

        let currentSum = 0;
        document.querySelectorAll('.payment-amount').forEach(inp => {
            currentSum += parseFloat(inp.value) || 0;
        });

        // إذا كان المبلغ مجهولاً وهناك متبقي مستحق، نقترح المبلغ المتبقي تلقائياً
        if ((!amount || amount === 0) && totalExpected > currentSum) {
            amount = Math.round((totalExpected - currentSum) * 100) / 100;
        }

        const div = document.createElement('div');
        div.className = 'payment-row row g-3 mb-3 align-items-end border-bottom pb-3';

        let autoAttr = isAuto === null ? 'false' : isAuto.toString();

        let optionsHtml = `<option value="">${translations.payment_method_placeholder}</option>`;
        Object.values(paymentOptions).forEach(data => {
            let selected = (String(data.id) === String(selectedAccountId)) ? 'selected' : '';
            optionsHtml += `<option value="${data.id}" data-method="${data.payment_method}" ${selected}>${data.name}</option>`;
        });

        let initialPaymentMethod = existingCode;
        if (!initialPaymentMethod) {
            let selectedOptionData = Object.values(paymentOptions).find(d => String(d.id) === String(selectedAccountId));
            initialPaymentMethod = selectedOptionData ? selectedOptionData.payment_method : '';
        }

        div.innerHTML = `
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted">${translations.payment_method_label}</label>
                <select class="form-select payment-method" onchange="this.nextElementSibling.value = this.options[this.selectedIndex].getAttribute('data-method') || ''; checkPaymentBalance()">${optionsHtml}</select>
                <input type="hidden" class="payment-method-code" value="${initialPaymentMethod}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">${translations.payment_amount_label}</label>
                <div class="input-group">
                    <input type="number" class="form-control payment-amount fw-bold" value="${amount}" step="0.01" 
                           data-auto="${autoAttr}"
                           oninput="this.setAttribute('data-auto', 'false'); checkPaymentBalance()">
                    <span class="input-group-text bg-light">${translations.currency_symbol}</span>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px;" onclick="removePaymentRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>`;
        container.appendChild(div);
        assignPaymentNames();
        checkPaymentBalance();
    }

    function updateRowIndices() {
        let rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            let numSpan = row.querySelector('.item-number');
            if (numSpan) {
                numSpan.textContent = index + 1;
            }
        });
    }

    function copyItemRow(btn) {
        let originalRow = btn.closest('tr');
        let newRow = originalRow.cloneNode(true);
        
        let inputs = newRow.querySelectorAll('input, select');
        inputs.forEach(inp => {
            if (inp.name) {
                inp.name = inp.name.replace(/\[\d+\]/, '[' + itemIndex + ']');
            }
        });
        
        document.getElementById('items_body').appendChild(newRow);
        itemIndex++;
        updateRowIndices();
        calcTotals();
    }

    function removeItemRow(btn) {
        if (document.querySelectorAll('.item-row').length > 1) {
            btn.closest('tr').remove();
            updateRowIndices();
            calcTotals();
        } else {
            alert(translations.min_item_error);
        }
    }

    function removePaymentRow(btn) {
        let row = btn.closest('.payment-row');
        if (row) {
            row.remove();
            assignPaymentNames();
            checkPaymentBalance();
        }
    }

    function assignPaymentNames() {
        document.querySelectorAll('.payment-row').forEach((row, index) => {
            let method = row.querySelector('.payment-method');
            let amount = row.querySelector('.payment-amount');
            let code = row.querySelector('.payment-method-code');
            if (method) method.name = `payments[${index}][account_id]`;
            if (amount) amount.name = `payments[${index}][amount]`;
            if (code) code.name = `payments[${index}][payment_method_code]`;
        });
    }

    function checkPaymentBalance() {
        let maxTotalInput = document.getElementById('total_inclusive_vat');
        let totalExpected = maxTotalInput ? (parseFloat(maxTotalInput.value) || 0) : 0;
        let sumPayments = 0;
        document.querySelectorAll('.payment-row').forEach(row => {
            let selectInp = row.querySelector('.payment-method');
            let amtInp = row.querySelector('.payment-amount');
            let accountId = selectInp ? selectInp.value : '';
            let amt = amtInp ? (parseFloat(amtInp.value) || 0) : 0;
            if (accountId !== '' && amt > 0) {
                sumPayments += amt;
            }
        });

        const card = document.getElementById('payment_status_card');
        const title = document.getElementById('payment_status_title');
        const desc = document.getElementById('payment_status_desc');

        if (!card) return;

        if (sumPayments <= 0.009) {
            // لم يتم اختيار وسيلة دفع معتمدة -> إظهار كرت الآجل
            card.classList.remove('d-none');
            if (title) title.textContent = translations.payment_status_credit_full;
            if (desc) desc.textContent = translations.payment_status_credit_desc;
        } else {
            // عند اختيار وسيلة دفع -> يختفي كرت الآجل تماماً
            card.classList.add('d-none');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        assignPaymentNames();
        calcTotals();

        // Barcode Scanner Logic
        let barcodeBuffer = "";
        let lastKeyTime = Date.now();

        window.addEventListener("keydown", function(e) {
            const currentTime = Date.now();
            
            // If the gap between keys is small (less than 50ms), it's likely a barcode scanner
            // We use a slightly larger buffer (100ms) to be safe
            if (currentTime - lastKeyTime > 100) {
                barcodeBuffer = "";
            }

            if (e.key === "Enter") {
                if (barcodeBuffer.length >= 3) {
                    e.preventDefault();
                    e.stopPropagation();
                    processBarcode(barcodeBuffer);
                    barcodeBuffer = "";
                }
                return;
            }

            if (e.key.length === 1) {
                barcodeBuffer += e.key;
            }
            
            lastKeyTime = currentTime;
        });

        function processBarcode(code) {
            // Find product by barcode via AJAX
            $.ajax({
                url: "{{ $getProductUrl }}",
                method: "GET",
                data: {
                    q: code,
                    search_type: isSale ? 'location' : 'products',
                    lang: "{{ app()->getLocale() }}",
                    is_sale: isSale
                },
                success: function(data) {
                    if (data.results && data.results.length > 0) {
                        // Find an exact barcode match if possible, otherwise take the first result
                        let product = data.results.find(p => p.barcode === code) || data.results[0];
                        
                        let isSize = product.is_size ? 1 : 0;
                        let existingRow = findExistingProductRow(product.id, isSize);
                        
                        if (existingRow) {
                            let qtyInput = existingRow.querySelector('.item-qty');
                            qtyInput.value = (parseFloat(qtyInput.value) || 0) + 1;
                            calcTotals();
                        } else {
                            let selectedPrice = isSale ? product.sale_price : product.cost_price;
                            let selectedTax = product.tax_id || '';
                            addItemBlock(
                                product.id, 
                                product.text, 
                                product.units || [], 
                                1, 
                                selectedPrice, 
                                selectedTax, 
                                isSize
                            );
                        }
                        
                        // Clear the search input if focused to avoid duplicate entry
                        $('#product_search').val(null).trigger('change');
                        
                        // Optional: play a success sound or show a toast
                        console.log("Barcode processed:", code);
                    } else {
                        console.warn("Product not found for barcode:", code);
                    }
                },
                error: function() {
                    console.error("Error fetching product by barcode");
                }
            });
        }
    });
</script>
