@php
    $getProductUrl = $getProductUrl ?? route('Lookup.getproducts');
    $isSettlement = $isSettlement ?? false;
    $isTransferIn = $isTransferIn ?? false;
    $showBookQuantity = $showBookQuantity ?? false;
@endphp
<script>
    let itemIndex = document.querySelectorAll('.item-row').length;
    let isSettlement = {{ $isSettlement ? 'true' : 'false' }};
    let isTransferIn = {{ $isTransferIn ? 'true' : 'false' }};
    let isReturnMode = {{ ($isReturnMode ?? false) ? 'true' : 'false' }};
    let showBookQuantity = {{ $showBookQuantity ? 'true' : 'false' }};
    let searchType = "{{ $searchType ?? ($showBookQuantity ? 'location' : 'products') }}";

    $(document).ready(function() {
        if ($('.select2-general').length) {
            $('.select2-general').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        let $productSelect = $('#product_search');
        if ($productSelect.length) {
            $productSelect.select2({
                placeholder: "{{ __('store::ui.search_product_placeholder') }}",
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    errorLoading: function() {
                        return "{{ __('store::ui.error_loading_products') }}";
                    },
                    searching: function() {
                        return "{{ __('store::ui.searching') }}";
                    },
                    noResults: function() {
                        return "{{ __('store::ui.no_results_found') }}";
                    },
                    inputTooShort: function() {
                        return "{{ __('store::ui.input_too_short') }}";
                    }
                },
                ajax: {
                    url: "{{ $getProductUrl }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            search_type: searchType,
                            lang: "{{ app()->getLocale() }}",
                            store: $('#store_id').val() || $('#from_store_id').val() || ''
                        };
                    },
                    processResults: function(data, params) {
                        console.log(data);


                        params.page = params.page || 1;
                        let resultsArray = Array.isArray(data.results) ? data.results : (data
                            .data || []);
                        let mappedResults = resultsArray.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text || item.name,
                                cost_price: item.cost_price || 0,
                                available_quantity: item.quantity || 0,
                                units: item.units || [],
                                is_size: item.is_size || item.have_sizes || 0
                            };
                        });

                        return {
                            results: mappedResults,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            });

            $productSelect.on('select2:select', function(e) {
                let storeId = $('#store_id').val() || $('#from_store_id').val();
                if (!storeId) {
                    toastr.warning("{{ __('store::ui.select_store_first') }}");
                    $productSelect.val(null).trigger('change');
                    return;
                }

                let data = e.params.data;
                let isSize = data.is_size ? 1 : 0;
                let existingRow = findExistingProductRow(data.id, isSize);

                if (existingRow && !isSettlement) {
                    let qtyInput = existingRow.querySelector('.item-qty');
                    qtyInput.value = (parseFloat(qtyInput.value) || 0) + 1;
                    calcTotals();
                } else if (!existingRow) {
                    addItemBlock(data.id, data.text, data.units, 1, data.cost_price, data
                        .available_quantity, data.is_size ? 1 : 0);
                }
                $productSelect.val(null).trigger('change');
            });
        }

        $(document).on('change', '#store_id, #from_store_id', function() {
            $('#product_search').val(null).trigger('change');
            let rows = document.querySelectorAll('.item-row');
            if (rows.length > 0 && confirm("{{ __('store::ui.change_store_warning') }}")) {
                rows.forEach(r => r.remove());
                calcTotals();
            } else if (rows.length > 0) {
                // Revert select?
            }
        });

        if (isTransferIn) {
            $('.item-recv-qty').each(function() {
                calcTransferVariance(this);
            });
        }
        calcTotals();

        // Barcode Scanner Logic
        let barcodeBuffer = "";
        let lastKeyTime = Date.now();

        window.addEventListener("keydown", function(e) {
            const currentTime = Date.now();
            
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
            let storeId = $('#store_id').val() || $('#from_store_id').val();
            if (!storeId) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning("{{ __('store::ui.select_store_first') }}");
                }
                return;
            }

            $.ajax({
                url: "{{ $getProductUrl }}",
                method: "GET",
                data: {
                    q: code,
                    search_type: searchType,
                    lang: "{{ app()->getLocale() }}",
                    store: storeId
                },
                success: function(data) {
                    let resultsArray = Array.isArray(data.results) ? data.results : (data.data || []);
                    if (resultsArray.length > 0) {
                        let product = resultsArray.find(p => p.barcode === code) || resultsArray[0];
                        
                        let matchedUnit = (product.units || []).find(u => u.barcode === code);
                        let matchedUnitId = matchedUnit ? matchedUnit.id : null;
                        
                        let isSize = (product.is_size || product.have_sizes) ? 1 : 0;
                        let existingRow = findExistingProductRow(product.id, isSize, matchedUnitId);
                        
                        if (existingRow) {
                            let qtyInput = existingRow.querySelector(isSettlement ? '.item-act-qty' : '.item-qty');
                            if (qtyInput) {
                                qtyInput.value = (parseFloat(qtyInput.value) || 0) + 1;
                                $(qtyInput).trigger('change').trigger('input');
                                
                                if (typeof validateRowQty === 'function') validateRowQty(qtyInput);
                                if (typeof calcTransferVariance === 'function' && typeof isTransferIn !== 'undefined' && isTransferIn) calcTransferVariance(qtyInput);
                            }
                            calcTotals();
                            if (typeof toastr !== 'undefined') {
                                toastr.success(typeof getLang === 'function' ? getLang('quantity_increased') : 'تمت زيادة الكمية بنجاح');
                            }
                        } else if (!existingRow) {
                            addItemBlock(
                                product.id, 
                                product.text || product.name, 
                                product.units || [], 
                                1, 
                                product.cost_price || 0, 
                                product.quantity || 0, 
                                isSize,
                                matchedUnitId
                            );
                        }
                        
                        $('#product_search').val(null).trigger('change');
                    }
                }
            });
        }
    });

    function findExistingProductRow(productId, isSize, unitId = null) {
        if (!productId) return null;
        let sizeFlag = isSize ? '1' : '0';
        let rows = document.querySelectorAll('.item-row');
        for (let row of rows) {
            let idInput = row.querySelector('input[name*="[product_id]"]');
            let sizeInput = row.querySelector('input[name*="[have_sizes]"]');
            let rowIsSize = sizeInput ? sizeInput.value : '0';
            
            let unitSelect = row.querySelector('.item-unit');
            let rowUnitId = unitSelect ? unitSelect.value : null;

            if (idInput && idInput.value == productId && rowIsSize == sizeFlag) {
                if (unitId && rowUnitId && rowUnitId != unitId) continue;
                return row;
            }
        }
        return null;
    }

    function addItemBlock(id, name, units, qty, price, bookQuantity, isSize, preselectedUnitId = null) {
        const tbody = document.getElementById('items_body');
        const emptyRow = document.getElementById('empty_row');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.className = 'item-row text-center';

        let initialPrice = parseFloat(price) || 0;
        let initialBookQty = parseFloat(bookQuantity) || 0;
        let unitOptions = '';

        if (units && units.length > 0) {
            units.forEach((u, i) => {
                let isSelected = preselectedUnitId ? (u.id == preselectedUnitId) : (i === 0);
                let unitPrice = u.cost_price || initialPrice;
                let unitQty = u.available_quantity !== undefined ? u.available_quantity : initialBookQty;
                let unitConv = u.conversion_factor || 1;
                unitOptions +=
                    `<option value="${u.id || ''}" data-price="${unitPrice}" data-qty="${unitQty}" data-conv="${unitConv}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
                if (isSelected) {
                    initialPrice = unitPrice;
                    initialBookQty = unitQty;
                }
            });
        } else {
            unitOptions =
                `<option value="" data-price="${initialPrice}" data-qty="${initialBookQty}" data-conv="1" selected>{{ __('store::ui.basic_unit') }}</option>`;
        }

        let qtyCols = '';
        if (isSettlement) {
            qtyCols = `
                <td><input type="number" name="items[${itemIndex}][system_quantity]" class="form-control item-sys-qty text-center bg-light" value="${initialBookQty.toFixed(2)}" readonly tabindex="-1"></td>
                <td><input type="number" name="items[${itemIndex}][actual_quantity]" class="form-control item-act-qty text-center" value="${initialBookQty.toFixed(2)}" min="0" step="any" oninput="calcTotals()"></td>
                <td><input type="number" name="items[${itemIndex}][variance_quantity]" class="form-control item-var-qty text-center bg-light fw-bold" value="0.00" readonly tabindex="-1"></td>
            `;
        } else if (isTransferIn) {
            // وضع الاستلام المخزني: sent_quantity (readonly) + received_quantity + variance + book_qty
            qtyCols = `
                <td><input type="number" name="items[${itemIndex}][sent_quantity]" class="form-control item-sent-qty text-center bg-light" value="${qty}" readonly tabindex="-1"></td>
                <td><input type="number" name="items[${itemIndex}][received_quantity]" class="form-control item-qty item-recv-qty text-center" value="${qty}" min="0" step="any" oninput="calcTransferVariance(this); calcTotals()"></td>
                <td><input type="number" name="items[${itemIndex}][variance_quantity]" class="form-control item-var-qty text-center bg-light" value="0.00" readonly tabindex="-1"></td>
                <td><input type="number" name="items[${itemIndex}][book_quantity]" class="form-control item-book-qty text-center bg-light" value="${initialBookQty.toFixed(2)}" readonly tabindex="-1"></td>
            `;
        } else {
            if (showBookQuantity) {
                qtyCols +=
                    `<td><input type="number" name="items[${itemIndex}][system_quantity]" class="form-control item-sys-qty text-center bg-light" value="${initialBookQty.toFixed(2)}" readonly tabindex="-1"></td>`;
            }
            let maxAttr = (showBookQuantity && !isSettlement) ? `max="${initialBookQty}"` : '';
            qtyCols +=
                `<td><input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty text-center" value="${qty}" min="1" step="any" ${maxAttr} oninput="validateRowQty(this);calcTotals()"></td>`;
        }

        tr.innerHTML = `
            <td class="pe-3 text-start">
                <input type="hidden" name="items[${itemIndex}][product_id]" value="${id}">
                <input type="hidden" name="items[${itemIndex}][have_sizes]" value="${isSize ? 1 : 0}">
                <input type="hidden" name="items[${itemIndex}][product_units]" value='${JSON.stringify(units)}'>
                <input type="text" name="items[${itemIndex}][product_name]" class="form-control bg-light" value="${name}" readonly style="text-align: right;" tabindex="-1">
            </td>
            <td>
                <select name="items[${itemIndex}][unit_id]" class="form-select item-unit" onchange="updateRowPrice(this)">${unitOptions}</select>
            </td>
            ${qtyCols}
            <td><input type="number" name="items[${itemIndex}][unit_cost]" class="form-control item-cost text-center" value="${initialPrice.toFixed(2)}" min="0" step="0.01" oninput="calcTotals()"></td>
            <td><input type="number" name="items[${itemIndex}][total_cost]" class="form-control item-total fw-bold text-primary text-center bg-light" value="0.00" readonly tabindex="-1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-0 mx-auto" style="width: 32px; height: 32px;" onclick="removeItemRow(this)"><i class="bi bi-trash"></i></button>
            </td>
        `;

        tbody.appendChild(tr);
        itemIndex++;
        calcTotals();
    }

    function updateRowPrice(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const qty = selectedOption.getAttribute('data-qty') || 0;

        const priceInput = row.querySelector('.item-cost');
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
        }

        const sysQtyInput = row.querySelector('.item-sys-qty');
        if (sysQtyInput) {
            sysQtyInput.value = parseFloat(qty).toFixed(2);
        }

        const actQtyInput = row.querySelector('.item-act-qty');
        if (actQtyInput) {
            actQtyInput.value = parseFloat(qty).toFixed(2);
        }

        // تحديث الحد الأقصى للكمية إذا كان مفعل
        const qtyInput = row.querySelector('.item-qty');
        if (qtyInput && showBookQuantity && !isSettlement) {
            qtyInput.setAttribute('max', qty);
            validateRowQty(qtyInput);
        }

        calcTotals();
    }

    function validateRowQty(input) {
        if (!showBookQuantity || isSettlement) return;

        const max = parseFloat(input.getAttribute('max')) || 0;
        let val = parseFloat(input.value) || 0;

        if (val > max) {
            toastr.error("{{ __('store::ui.qty_exceeds_book_qty') ?? 'الكمية لا يمكن أن تزيد عن الكمية المتاحة' }} (" +
                max + ")");
            input.value = max;
        }
    }

    function calcTotals() {
        let totalItems = 0;
        let totalQty = 0;
        let totalCost = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            let activeQty = 0;
            if (isSettlement) {
                let varQtyInput = row.querySelector('.item-var-qty');
                let variance = 0;
                
                let sysQtyInput = row.querySelector('.item-sys-qty');
                let actQtyInput = row.querySelector('.item-act-qty');
                
                if (sysQtyInput && actQtyInput) {
                    let bookQty = parseFloat(sysQtyInput.value) || 0;
                    let actualQty = parseFloat(actQtyInput.value) || 0;
                    variance = actualQty - bookQty;
                } else {
                    let qtyInput = row.querySelector('.item-qty');
                    variance = qtyInput ? (parseFloat(qtyInput.value) || 0) : 0;
                }

                if (varQtyInput) {
                    varQtyInput.value = variance.toFixed(2);
                    if (variance < 0) {
                        varQtyInput.classList.remove('text-success');
                        varQtyInput.classList.add('text-danger');
                    } else if (variance > 0) {
                        varQtyInput.classList.remove('text-danger');
                        varQtyInput.classList.add('text-success');
                    } else {
                        varQtyInput.classList.remove('text-danger', 'text-success');
                    }
                }
                activeQty = Math.abs(variance);
            } else if (isTransferIn) {
                if (isReturnMode) {
                    activeQty = parseFloat(row.querySelector('.item-ret-qty')?.value) || 0;
                } else {
                    activeQty = parseFloat(row.querySelector('.item-recv-qty')?.value) || 0;
                }
            } else {
                activeQty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
            }

            let costInput = row.querySelector('.item-cost');
            let cost = costInput ? (parseFloat(costInput.value) || 0) : 0;
            let rowTotal = activeQty * cost;

            let totalDisplay = row.querySelector('.item-total');
            if (totalDisplay) totalDisplay.value = rowTotal.toFixed(2);

            totalItems++;
            totalQty += activeQty;
            totalCost += rowTotal;
        });

        const noItems = document.getElementById('empty_row');
        if (totalItems === 0 && noItems) {
            noItems.classList.remove('d-none');
        } else if (totalItems > 0 && noItems) {
            noItems.classList.add('d-none');
        }

        let lblTotalItems = document.getElementById('lbl_total_items');
        let lblTotalQty = document.getElementById('lbl_total_quantity');
        let lblTotalCost = document.getElementById('lbl_total_cost');

        if (lblTotalItems) lblTotalItems.innerText = totalItems;
        if (lblTotalQty) lblTotalQty.innerText = totalQty.toFixed(2);
        if (lblTotalCost) lblTotalCost.innerText = totalCost.toFixed(2);

        let inputTotalItems = document.getElementById('total_items_input');
        let inputTotalQty = document.getElementById('total_quantity_input');
        let inputTotalValue = document.getElementById('total_value_input');

        if (inputTotalItems) inputTotalItems.value = totalItems;
        if (inputTotalQty) inputTotalQty.value = totalQty;
        if (inputTotalValue) inputTotalValue.value = totalCost;
    }

    function removeItemRow(btn) {
        btn.closest('tr').remove();
        calcTotals();
        if (document.querySelectorAll('.item-row').length === 0) {
            const tbody = document.getElementById('items_body');
            tbody.innerHTML = `
                <tr id="empty_row">
                    <td colspan="8" class="text-center p-5 text-muted">
                        <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                        {{ __('store::ui.empty_table_hint') }}
                    </td>
                </tr>`;
        }
    }

    // حساب الفارق عند تعديل الكمية المستلمة في وضع الاستلام المخزني
    function calcTransferVariance(currentInput) {
        const row = currentInput.closest('tr');
        const sentQty = parseFloat(row.querySelector('.item-sent-qty')?.value) || 0;
        const prevRecv = parseFloat(row.querySelector('.item-prev-recv')?.value) || 0;
        const returned = parseFloat(row.querySelector('.item-ret-qty')?.value) || 0;
        
        let currentRecv = parseFloat(currentInput.value) || 0;

        // التحقق من عدم تجاوز الكمية المرسلة (المتبقي فعلياً للارسال)
        const maxPossible = sentQty - (prevRecv + returned);
        if (currentRecv > maxPossible) {
            currentRecv = maxPossible;
            currentInput.value = currentRecv.toFixed(2);
            // Optionally: show a toast/warning
        }
        if (currentRecv < 0) {
            currentRecv = 0;
            currentInput.value = "0.00";
        }

        const totalHandled = prevRecv + currentRecv + returned;
        const variance = totalHandled - sentQty;

        const varInput = row.querySelector('.item-var-qty');
        const totalRecvHidden = row.querySelector('.item-total-recv');

        if (totalRecvHidden) totalRecvHidden.value = (prevRecv + currentRecv);

        if (varInput) {
            varInput.value = variance.toFixed(2);
            varInput.classList.toggle('text-danger', variance < 0);
            varInput.classList.toggle('text-success', variance > 0);
            varInput.classList.toggle('text-secondary', variance === 0);
        }
        
        // تحديث الحد الأقصى للمرتجع ديناميكياً بناءً على ما تم استلامه الآن
        const retInput = row.querySelector('.item-ret-qty');
        if (retInput) {
            retInput.max = (sentQty - (prevRecv + currentRecv)).toFixed(2);
        }
    }

    function validateReturnQty(input) {
        const row = input.closest('tr');
        const sentQty = parseFloat(row.querySelector('.item-sent-qty')?.value) || 0;
        const prevRecv = parseFloat(row.querySelector('.item-prev-recv')?.value) || 0;
        const currentRecv = parseFloat(row.querySelector('.item-recv-qty')?.value) || 0;
        
        const maxReturn = sentQty - (prevRecv + currentRecv);
        
        let val = parseFloat(input.value) || 0;
        if (val < 0) val = 0;
        if (val > maxReturn) {
            val = maxReturn;
            // Optionally alert the user
        }
        input.value = val.toFixed(2);
        
        // تحديث الفارق
        calcTransferVariance(row.querySelector('.item-recv-qty') || input);
    }
</script>
