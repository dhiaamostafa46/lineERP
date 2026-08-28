<div class="col-12" id="store-items-app">
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <!-- Header -->
        <div class="card-header py-3 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-box-seam text-primary"></i>
                @lang('store::models/st_damageds.ui.items_details', ['default' => 'تفاصيل المواد'])
            </h5>
            <div class="d-flex gap-2" style="width: 400px">
                <select id="product_search" class="form-select select2-products">
                    <option value=""></option>
                </select>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="items-table">
                    <thead>
                        <tr class="text-center table-light align-middle text-secondary fw-semibold">
                            <th width="15%">@lang('store::models/st_damageds.items.barcode')</th>
                            <th width="25%" class="text-start">@lang('store::models/st_damageds.items.product_id')</th>
                            <th width="12%">@lang('store::models/st_damageds.items.unit_id')</th>
                            <th width="10%">@lang('store::models/st_damageds.items.quantity')</th>
                            <th width="10%">الكمية الدفترية</th>
                            <th width="12%">@lang('store::models/st_damageds.items.unit_cost')</th>
                            <th width="12%">@lang('store::models/st_damageds.items.total_cost')</th>
                            <th width="4%"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        @php
                            // Load old items if present
                            $oldItems = old('items', isset($damaged) && $damaged->items ? $damaged->items->toArray() : []);
                        @endphp
                    </tbody>
                </table>
            </div>

            <!-- No items -->
            <div id="no-items-placeholder" class="text-center text-muted py-5 d-none">
                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                <h6>@lang('lang.empty_table_hint', ['default' => 'لا توجد أصناف، ابحث في الأعلى للإضافة'])</h6>
            </div>
        </div>

        <!-- Footer totals -->
        <div class="card-footer bg-light-soft text-center py-3">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-end">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-end fw-bold">@lang('store::models/st_damageds.fields.total_items')</td>
                                <td class="text-end">
                                    <span id="total-items">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold">@lang('store::models/st_damageds.fields.total_quantity')</td>
                                <td class="text-end">
                                    <span id="total-quantity">0.00</span>
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-end fw-bold pt-2 text-dark">
                                    <i class="fas fa-calculator me-1"></i>
                                    @lang('store::models/st_damageds.fields.total_value')
                                </td>
                                <td class="text-end pt-2">
                                    <h5 class="text-success fw-bold mb-0"><span id="total-value">0.00</span></h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="item-row-template">
    <tr class="item-row text-center" data-index="__INDEX__">
        <td>
            <input type="text" class="form-control barcode-input text-center" name="items[__INDEX__][barcode]" readonly tabindex="-1">
        </td>
        <td class="text-start">
            <input type="hidden" name="items[__INDEX__][product_id]" class="product-id">
            <input type="hidden" name="items[__INDEX__][product_units]" class="product-units-data">
            <input type="hidden" name="items[__INDEX__][have_sizes]" class="product-type">
            <input type="text" class="form-control text-start border-0 bg-transparent fw-bold product-name-display" name="items[__INDEX__][product_name]" readonly tabindex="-1">
        </td>
        <td>
            <select class="form-select unit-select" name="items[__INDEX__][unit_id]" onchange="updateRowUnit(this)">
            </select>
            <input type="hidden" name="items[__INDEX__][unit_name]">
        </td>
        <td>
            <input type="number" step="any" min="0.01" class="form-control quantity-input text-center" name="items[__INDEX__][quantity]" value="1" oninput="calcTotals()">
        </td>
        <td>
            <input type="number" step="any" class="form-control book_quantity-input text-center bg-light" name="items[__INDEX__][book_quantity]" value="0" readonly tabindex="-1">
        </td>
        <td>
            <input type="number" step="any" min="0" class="form-control cost-input text-center" name="items[__INDEX__][unit_cost]" value="0.00" oninput="calcTotals()">
        </td>
        <td>
            <input type="text" class="form-control total-display fw-bold text-center text-primary bg-light" name="items[__INDEX__][total_cost]" value="0.00" readonly tabindex="-1">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center mx-auto remove-item-btn" style="width: 32px; height: 32px;">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    let itemIndex = document.querySelectorAll('.item-row').length;
    let oldItems = @json($oldItems);

    $(document).ready(function() {
        if (oldItems && oldItems.length > 0) {
            oldItems.forEach(item => restoreItemRow(item));
        }

        // Initialize Product Search using standard AJAX (same as Invoices)
        let $productSelect = $('#product_search');
        if ($productSelect.length) {
            $productSelect.select2({
                placeholder: '+ ابحث عن منتج، لإضافته إلى القائمة...',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                ajax: {
                    url: "{{ route('Lookup.getproducts') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            search_type: 'products',
                            lang: "{{ app()->getLocale() }}",
                            store: $('#store_id').val()
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        let mappedResults = (data.results || data).map(function(item) {
                            // Unified map to handle dynamic structure if any
                            return {
                                id: item.id,
                                text: item.text || item.name,
                                cost_price: item.cost_price || 0,
                                sale_price: item.sale_price || 0,
                                units: item.units || [],
                                barcode: item.barcode || '',
                                quantity: item.quantity || 0,
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
                if (!$('#store_id').val()) {
                    toastr.warning('يرجى تحديد المستودع أولاً');
                    $productSelect.val(null).trigger('change');
                    return;
                }
                
                let data = e.params.data;
                let existingRow = findExistingProductRow(data.id);
                
                if (existingRow) {
                    let qtyInput = existingRow.querySelector('.quantity-input');
                    qtyInput.value = (parseFloat(qtyInput.value) || 0) + 1;
                    calcTotals();
                } else {
                    addItemRow(data);
                }
                $productSelect.val(null).trigger('change');
            });
        }

        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('tr').remove();
            calcTotals();
        });
        
        calcTotals();
    });

    function findExistingProductRow(productId) {
        let rows = document.querySelectorAll('.item-row');
        for (let row of rows) {
            let idInput = row.querySelector('.product-id');
            if (idInput && idInput.value == productId) return row;
        }
        return null;
    }

    function addItemRow(data) {
        $('#no-items-placeholder').addClass('d-none');
        const tbody = document.getElementById('items-container');
        
        let template = document.getElementById('item-row-template').innerHTML.replace(/__INDEX__/g, itemIndex);
        const tr = document.createElement('tbody');
        tr.innerHTML = template;
        const newRow = tr.firstElementChild;

        // Set Product Info
        newRow.querySelector('.product-id').value = data.id;
        newRow.querySelector('.product-name-display').value = data.text;
        newRow.querySelector('.barcode-input').value = data.barcode || '';
        newRow.querySelector('.product-type').value = data.is_size ? 1 : 0;
        newRow.querySelector('.product-units-data').value = JSON.stringify(data.units || []);
        
        const unitSelect = newRow.querySelector('.unit-select');
        let initialCost = parseFloat(data.cost_price) || 0;
        
        if (data.units && data.units.length > 0) {
            let options = '';
            data.units.forEach((u, i) => {
                let cost = u.cost_price || data.cost_price || 0;
                let conv = u.conversion_factor || 1;
                let qty = u.quantity || 0;
                if (i === 0) initialCost = cost;
                options += `<option value="${u.id || ''}" data-cost="${cost}" data-conv="${conv}" data-qty="${qty}">${u.name}</option>`;
            });
            unitSelect.innerHTML = options;
        } else {
            unitSelect.innerHTML = `<option value="" data-cost="${initialCost}" data-conv="1" data-qty="${data.quantity || 0}">حبة</option>`;
        }

        newRow.querySelector('.cost-input').value = initialCost.toFixed(4);
        newRow.querySelector('.book_quantity-input').value = parseFloat(data.quantity || 0).toFixed(2);
        
        tbody.appendChild(newRow);
        itemIndex++;
        calcTotals();
    }

    function restoreItemRow(item) {
        $('#no-items-placeholder').addClass('d-none');
        const tbody = document.getElementById('items-container');
        
        let template = document.getElementById('item-row-template').innerHTML.replace(/__INDEX__/g, itemIndex);
        const tr = document.createElement('tbody');
        tr.innerHTML = template;
        const newRow = tr.firstElementChild;

        newRow.querySelector('.product-id').value = item.product_id;
        newRow.querySelector('.product-name-display').value = item.product_name || item.name || '';
        newRow.querySelector('.barcode-input').value = item.barcode || '';
        newRow.querySelector('.product-type').value = item.have_sizes || item.type || 0;
        newRow.querySelector('.quantity-input').value = item.quantity || 1;
        newRow.querySelector('.cost-input').value = item.unit_cost || 0;
        newRow.querySelector('.book_quantity-input').value = item.book_quantity || 0;
        
        const units = item.product_units ? (typeof item.product_units === 'string' ? JSON.parse(item.product_units) : item.product_units) : [];
        const unitSelect = newRow.querySelector('.unit-select');
        
        if (units && units.length > 0) {
            let options = '';
            units.forEach((u) => {
                let cost = u.cost_price || 0;
                let conv = u.conversion_factor || 1;
                let qty = u.quantity || 0;
                let selected = u.id == item.unit_id ? 'selected' : '';
                options += `<option value="${u.id || ''}" data-cost="${cost}" data-conv="${conv}" data-qty="${qty}" ${selected}>${u.name}</option>`;
            });
            unitSelect.innerHTML = options;
        }
        
        newRow.querySelector('.product-units-data').value = JSON.stringify(units);
        
        tbody.appendChild(newRow);
        itemIndex++;
    }

    function updateRowUnit(select) {
        const row = select.closest('tr');
        const selectedOption = select.options[select.selectedIndex];
        const cost = selectedOption.getAttribute('data-cost') || 0;
        const qty = selectedOption.getAttribute('data-qty') || 0;

        const costInput = row.querySelector('.cost-input');
        if (costInput) {
            costInput.value = parseFloat(cost).toFixed(4);
        }

        const bookQtyInput = row.querySelector('.book_quantity-input');
        if (bookQtyInput) {
            bookQtyInput.value = parseFloat(qty).toFixed(2);
        }

        calcTotals();
    }

    function calcTotals() {
        let totalItems = 0;
        let totalQty = 0;
        let totalVal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            let qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            let cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            let rowTotal = qty * cost;
            
            row.querySelector('.total-display').value = rowTotal.toFixed(2);
            
            totalItems++;
            totalQty += qty;
            totalVal += rowTotal;
        });

        const noItems = document.getElementById('no-items-placeholder');
        if (totalItems === 0 && noItems) {
            noItems.classList.remove('d-none');
        } else if (totalItems > 0 && noItems) {
            noItems.classList.add('d-none');
        }

        document.getElementById('total-items').innerText = totalItems;
        document.getElementById('total-quantity').innerText = totalQty.toFixed(2);
        document.getElementById('total-value').innerText = totalVal.toFixed(2);
    }
</script>ntRow.data('product-cost') || 0;
            const productQuantity = currentRow.data('product-quantity') || 0;

            const barcodeInput = currentRow.find('.barcode-input');
            const costInput = currentRow.find('.cost-input');
            const bookQuantityInput = currentRow.find('.book_quantity-input');
            const unitNameInput = currentRow.find('input[name*="[unit_name]"]');
            const productBarcode = currentRow.data('product-barcode');

            if (selectedUnit) {
                console.log('===== Unit Changed =====');
                console.log('Unit:', selectedUnit.name);
                console.log('Is Base:', selectedUnit.is_base);
                console.log('Conversion Factor:', selectedUnit.conversion_factor);
                console.log('Unit Quantity (from API):', selectedUnit.quantity);
                console.log('Base Quantity:', productQuantity);

                if (selectedUnit.barcode && selectedUnit.barcode !== productBarcode) {
                    barcodeInput.val(selectedUnit.barcode);
                } else {
                    barcodeInput.val(productBarcode || '');
                }

                unitNameInput.val(selectedUnit.name);

                let unitCost;
                if (selectedUnit.is_base) {
                    unitCost = productCost;
                } else if (selectedUnit.cost_price && selectedUnit.cost_price > 0) {
                    unitCost = selectedUnit.cost_price;
                } else if (selectedUnit.conversion_factor) {
                    unitCost = productCost * selectedUnit.conversion_factor;
                } else {
                    unitCost = productCost;
                }
                costInput.val(parseFloat(unitCost).toFixed(4));

                const bookQuantity = selectedUnit.quantity || 0;
                bookQuantityInput.val(parseFloat(bookQuantity).toFixed(4));

                console.log('Final Book Quantity:', bookQuantity);
                console.log('========================');

            } else {
                barcodeInput.val(productBarcode || '');
                unitNameInput.val('');
                costInput.val(parseFloat(productCost).toFixed(4));
                bookQuantityInput.val(parseFloat(productQuantity).toFixed(4));
            }

            calculateRow(currentRow);
        });

        row.on('input', '.quantity-input', function() {
            const qty = parseFloat($(this).val());
            const currentRow = $(this).closest('tr');

            if (isNaN(qty) || qty <= 0) {
                $(this).val('0.01');
            }

            calculateRow(currentRow);
        });

        row.on('input', '.book_quantity-input', function() {
            const qty = parseFloat($(this).val());

            if (isNaN(qty)) {
                $(this).val('0.00');
            }
        });

        row.on('input', '.cost-input', function() {
            const cost = parseFloat($(this).val());
            const currentRow = $(this).closest('tr');

            if (isNaN(cost) || cost < 0) {
                $(this).val('0.0000');
            }

            calculateRow(currentRow);
        });

        row.on('click', '.remove-item-btn', function() {
            if (confirm(getLang('are_you_sure'))) {
                $(this).closest('tr').remove();
                updateTotals();

                if ($(".item-row").length === 0) {
                    $("#no-items-placeholder").removeClass("d-none");
                }
            }
        });

        row.on('keypress', '.barcode-input', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const barcode = $(this).val().trim();
                const currentRow = $(this).closest('tr');

                if (barcode) {
                    searchByBarcodeInRow(barcode, currentRow);
                }
            }
        });
    }

    function calculateRow(row) {
        let qty = parseFloat(row.find(".quantity-input").val()) || 0;
        let cost = parseFloat(row.find(".cost-input").val()) || 0;
        let total = qty * cost;

        row.find(".total-display").val(total.toFixed(4));
        updateTotals();
    }

    function updateTotals() {
        let totalItems = $(".item-row").length;

        if (totalItems > 0) {
            $("#no-items-placeholder").addClass("d-none");
        } else {
            $("#no-items-placeholder").removeClass("d-none");
        }

        let totalQty = 0;
        let totalValue = 0;

        $(".item-row").each(function() {
            let qty = parseFloat($(this).find(".quantity-input").val()) || 0;
            let total = parseFloat($(this).find(".total-display").val()) || 0;

            totalQty += qty;
            totalValue += total;
        });

        $("#total-items").text(totalItems);
        $("#total-quantity").text(totalQty.toFixed(2));
        $("#total-value").text(totalValue.toFixed(2));
    }

    // ===== التحقق قبل الحفظ =====
    $('form').on('submit', function(e) {
        let isValid = true;
        let emptyRows = [];
        let negativeRows = [];
        let totalRows = $('.item-row').length;

        if (totalRows === 0) {
            e.preventDefault();
            toastr.error(getLang('no_products_added'));
            return false;
        }

        $('.item-row').each(function(index) {
            const productId = $(this).find('.product-select').val();
            const unitId = $(this).find('.unit-select').val();
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const bookQuantity = parseFloat($(this).find('.book_quantity-input').val()) || 0;
            const productName = $(this).find('input[name*="[product_name]"]').val() || '';
            const unitName = $(this).find('input[name*="[unit_name]"]').val() || '';

            if (!productId || !unitId || quantity <= 0) {
                isValid = false;
                emptyRows.push(index + 1);
                $(this).addClass('table-danger');
            }

            if (bookQuantity <= 0 && inventorySettings.allow_negative_stock) {
                isValid = false;
                negativeRows.push({
                    row: index + 1,
                    product: productName,
                    unit: unitName,
                    quantity: bookQuantity.toFixed(2)
                });
                $(this).addClass('table-warning');
            } else {
                $(this).removeClass('table-danger table-warning');
            }
        });

        if (!isValid) {
            e.preventDefault();

            if (emptyRows.length > 0) {
                toastr.error(getLang('complete_product_unit_data') + ' ' + emptyRows.join(', '));
            }

            if (negativeRows.length > 0) {
                let message = getLang('cannot_submit_negative_stock') + '\n\n';
                negativeRows.forEach(item => {
                    message += getLang('row_details').replace(':row', item.row).replace(':product', item.product).replace(':unit', item.unit).replace(':quantity', item.quantity) + '\n';
                });
                toastr.error(message, '', { timeOut: 10000 });
            }

            return false;
        }

        $('.item-row').removeClass('table-danger table-warning');
        return true;
    });

    // ===== البحث بالباركود =====
    let barcodeBuffer = '';
    let barcodeTimeout = null;

    $(document).on('keypress', function(e) {
        if ($(e.target).is('input, textarea, select')) return;

        clearTimeout(barcodeTimeout);

        if (e.which === 13) {
            e.preventDefault();
            if (barcodeBuffer.length > 0) {
                searchByBarcode(barcodeBuffer);
                barcodeBuffer = '';
            }
        } else {
            barcodeBuffer += String.fromCharCode(e.which);
            barcodeTimeout = setTimeout(() => barcodeBuffer = '', 100);
        }
    });

    function searchByBarcode(barcode) {
        if (!$('#store_id').val()) {
            toastr.error(getLang('please_select_store_first'));
            return;
        }

        if (!productsLoaded || allProducts.length === 0) {
            loadAllProducts();
            setTimeout(() => searchByBarcodeLogic(barcode), 500);
        } else {
            searchByBarcodeLogic(barcode);
        }
    }

    function searchByBarcodeLogic(barcode) {
        let existingRow = null;
        $('.item-row').each(function() {
            if ($(this).find('.barcode-input').val() === barcode) {
                existingRow = $(this);
                return false;
            }
        });

        if (existingRow) {
            let qtyInput = existingRow.find('.quantity-input');
            let currentQty = parseFloat(qtyInput.val()) || 0;
            qtyInput.val(currentQty + 1).trigger('change');
            toastr.success(getLang('quantity_increased') || 'تمت زيادة الكمية بنجاح');
            return;
        }

        let targetRow = $(".item-row").last();

        if (targetRow.length === 0 || targetRow.find('.product-select').val()) {
            addNewRow();
            targetRow = $(".item-row").last();
        }

        setTimeout(() => {
            searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
        }, 100);
    }

    function searchByBarcodeInRow(barcode, targetRow) {
        if (!productsLoaded || allProducts.length === 0) {
            loadAllProducts();
            setTimeout(() => {
                searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
            }, 500);
        } else {
            searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
        }
    }

    function searchInLocalProducts(barcode, productSelect, targetRow) {
        console.log('🔍 Searching for barcode:', barcode);

        let found = false;
        let foundProduct = null;
        let foundUnit = null;

        foundProduct = allProducts.find(p => p.barcode === barcode);

        if (foundProduct) {
            found = true;
            console.log('✓ Product found:', foundProduct.name);
        } else {
            for (let prod of allProducts) {
                if (prod.units && prod.units.length > 0) {
                    foundUnit = prod.units.find(u => u.barcode === barcode);
                    if (foundUnit) {
                        foundProduct = prod;
                        found = true;
                        console.log('✓ Unit found:', foundUnit.name, 'in product:', prod.name);
                        break;
                    }
                }
            }
        }

        if (found && foundProduct) {
            const newOption = new Option(foundProduct.name, foundProduct.id, true, true);
            productSelect.append(newOption).trigger('change');

            productSelect.trigger({
                type: 'select2:select',
                params: {
                    data: {
                        text: foundProduct.name,
                        id: foundProduct.id,
                        barcode: foundProduct.barcode,
                        type: foundProduct.type,
                        cost_price: foundProduct.cost_price,
                        quantity: foundProduct.quantity,
                        product_id: foundProduct.product_id || foundProduct.id,
                        units: foundProduct.units || []
                    }
                }
            });

            if (foundUnit) {
                setTimeout(() => {
                    const unitSelect = targetRow.find('.unit-select');
                    unitSelect.val(foundUnit.id).trigger('change');

                    setTimeout(() => {
                        targetRow.find('.quantity-input').focus().select();
                    }, 100);
                }, 200);
            } else {
                setTimeout(() => {
                    targetRow.find('.quantity-input').focus().select();
                }, 100);
            }
        } else {
            console.log('✗ Product not found');
            toastr.error(getLang('product_not_found') + ': ' + barcode);

            if (!targetRow.find('.product-select').val()) {
                targetRow.remove();
                if ($(".item-row").length === 0) {
                    $("#no-items-placeholder").removeClass("d-none");
                }
            }
        }
    }

    // ===== اختصارات لوحة المفاتيح =====
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.altKey) && e.key === 'n') {
            e.preventDefault();
            if ($('#store_id').val()) {
                addNewRow();
                setTimeout(() => {
                    $(".item-row").last().find('.product-select').select2('open');
                }, 100);
            } else {
                toastr.error(getLang('please_select_store_first'));
            }
        }
    });

    $(document).on('keydown', '.quantity-input, .book_quantity-input, .cost-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const currentRow = $(this).closest('tr');

            if ($(this).hasClass('quantity-input')) {
                currentRow.find('.book_quantity-input').focus().select();
            } else if ($(this).hasClass('book_quantity-input')) {
                currentRow.find('.cost-input').focus().select();
            } else if ($(this).hasClass('cost-input')) {
                const notesInput = currentRow.find('input[name*="[notes]"]');
                if (notesInput.length > 0) {
                    notesInput.focus();
                } else {
                    if ($('#store_id').val()) {
                        addNewRow();
                        setTimeout(() => {
                            $(".item-row").last().find('.product-select').select2('open');
                        }, 100);
                    }
                }
            }
        }
    });

    $(document).on('keydown', 'input[name*="[notes]"]', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($('#store_id').val()) {
                addNewRow();
                setTimeout(() => {
                    $(".item-row").last().find('.product-select').select2('open');
                }, 100);
            }
        }
    });
});
</script>
