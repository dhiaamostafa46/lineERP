<!-- <div class="col-12">
    <div class="card custom-card shadow-sm border-0 rounded-4">


        <div class="card-header custom-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-white">
            </h5>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-primary float-right" id="add-item-btn">
                    @lang('crud.add_item')
                </button>
            </div>
        </div>


        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table mb-0" id="items-table">
                    <thead>
                        <tr class="text-center bg-light fw-bold">
                            <th>@lang('store::models/st_damageds.items.barcode')</th>
                            <th>@lang('store::models/st_damageds.items.product_id')</th>
                            <th>@lang('store::models/st_damageds.items.unit_id')</th>
                            <th>@lang('store::models/st_damageds.items.quantity')</th>
                            <th>@lang('store::models/st_damageds.items.book_quantity')</th>
                            <th>@lang('store::models/st_damageds.items.unit_cost')</th>
                            <th>@lang('store::models/st_damageds.items.total_cost')</th>
                            <th>@lang('store::models/st_damageds.items.notes')</th>
                            <th>@lang('crud.action')</th>
                        </tr>
                    </thead>
                    <tbody id="items-container"></tbody>
                </table>
            </div>


            <div id="no-items-placeholder" class="text-center text-muted py-5">
                <i class="fas fa-box-open fa-4x mb-3 opacity-50"></i>
                <h6>@lang('lang.no_items')</h6>
                <small>@lang('lang.click_to_add')</small>
            </div>
        </div>

      
        <div class="card-footer bg-white border-top py-3">
            <div class="row">
                <div class="col-md-4 offset-md-8">
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
                                    <h5 class="text-success fw-bold mb-0" id="total-value">0.00</h5>
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
    <tr class="item-row" data-index="__INDEX__">
        <td>
            <input type="text" class="form-control form-control-md barcode-input" name="items[__INDEX__][barcode]">
        </td>

        <td>
            <select class="form-select form-select-md product-select livesearchprodect"
                name="items[__INDEX__][product_id]" data-index="__INDEX__">
                <option value="">@lang('lang.select')</option>
            </select>
            <input type="hidden" name="items[__INDEX__][product_name]">
            <input type="hidden" name="items[__INDEX__][type]" class="product-type">
            <input type="hidden" name="items[__INDEX__][product_units]" class="product-units-data">
        </td>

        <td>
            <select class="form-select form-select-md unit-select" name="items[__INDEX__][unit_id]" disabled>
                <option value="">@lang('lang.select')</option>
            </select>
            <input type="hidden" name="items[__INDEX__][unit_name]">
        </td>

        <td>
            <input type="number" step="any"  class="form-control form-control-md quantity-input"
                name="items[__INDEX__][quantity]" value="1">
        </td>
        <td>
            <input type="number" step="any"  class="form-control form-control-md book_quantity-input"
                name="items[__INDEX__][book_quantity]" value="1">
        </td>

        <td>
            <input type="number" step="any" min="0" class="form-control form-control-md cost-input"
                name="items[__INDEX__][unit_cost]" value="0.00">
        </td>

        <td>
            <input type="text" class="form-control form-control-md total-display fw-bold"
                name="items[__INDEX__][total_cost]" value="0.00" readonly>
        </td>

        <td>
            <input type="text" class="form-control form-control-md" name="items[__INDEX__][notes]"
                placeholder="@lang('crud.notes')">
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-sm btn-primary remove-item-btn">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>



<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 0;
    let allProducts = [];
    let productsLoaded = false;
    let storeSelected = 0;
    let typepage = 'damaged';

    // ===== إعدادات المخزون =====
    let inventorySettings = {
        allow_negative_stock: false, // القيمة الافتراضية
        costing_method: 'weighted_average',
        auto_calculate_cost: true
    };

    // ===== تحميل إعدادات المخزون من الخادم =====
    loadInventorySettings();

    function loadInventorySettings() {
        $.ajax({
            url: '/store/inventory-settings', // تحديث المسار حسب نظامك
            dataType: 'json',
            success: function(data) {
                inventorySettings = data;
                console.log('✓ Inventory settings loaded:', inventorySettings);
                console.log('Allow negative stock:', inventorySettings.allow_negative_stock ? 'YES' : 'NO');
            },
            error: function(xhr, status, error) {
                console.warn('⚠ Failed to load inventory settings, using defaults');
                // استخدام القيم الافتراضية
                inventorySettings = {
                    allow_negative_stock: false,
                    costing_method: 'weighted_average',
                    auto_calculate_cost: true
                };
            }
        });
    }

    /**
     * معالجة الكمية حسب allow_negative_stock
     *
     * @param {number} quantity - الكمية المطلوب معالجتها
     * @returns {number} - الكمية بعد المعالجة
     */
    function processQuantity(quantity) {
        const qty = parseFloat(quantity) || 0;

        if (inventorySettings.allow_negative_stock) {
            // السماح بالمخزون السالب: إرجاع الكمية كما هي
            return qty;
        }

        // عدم السماح بالمخزون السالب: إرجاع 0 للكميات السالبة
        return Math.max(0, qty);
    }

    /**
     * حساب كمية الوحدة من الكمية الأساسية
     *
     * @param {number} baseQuantity - الكمية بالوحدة الأساسية
     * @param {number} conversionFactor - معامل التحويل
     * @param {boolean} isBaseUnit - هل هي الوحدة الأساسية؟
     * @returns {number} - الكمية بالوحدة المحددة
     */
    function calculateUnitQuantity(baseQuantity, conversionFactor, isBaseUnit = false) {
        baseQuantity = parseFloat(baseQuantity) || 0;
        conversionFactor = parseFloat(conversionFactor) || 1;

        if (isBaseUnit) {
            // الوحدة الأساسية: نفس الكمية
            return processQuantity(baseQuantity);
        }

        if (conversionFactor <= 0) {
            return processQuantity(baseQuantity);
        }

        // الكمية بالوحدة = الكمية الأساسية ÷ معامل التحويل
        const unitQty = baseQuantity / conversionFactor;
        return processQuantity(unitQty);
    }

    /**
     * معالجة جميع كميات المنتج (الأساسية والوحدات)
     */
    function processProductQuantities(product) {
        // معالجة الكمية الأساسية
        product.quantity = processQuantity(product.quantity || 0);

        // معالجة كميات جميع الوحدات
        if (product.units && Array.isArray(product.units)) {
            product.units = product.units.map(unit => {
                // حساب كمية الوحدة من الكمية الأساسية
                unit.quantity = calculateUnitQuantity(
                    product.quantity,
                    unit.conversion_factor || 1,
                    unit.is_base || false
                );
                return unit;
            });
        }

        return product;
    }

    /**
     * عرض الكمية مع تنسيق مناسب
     */
    function formatQuantityDisplay(quantity) {
        const qty = parseFloat(quantity) || 0;

        if (qty < 0) {
            if (inventorySettings.allow_negative_stock) {
                // عرض الكمية السالبة باللون الأحمر
                return `<span class="text-danger fw-bold">${qty.toFixed(2)}</span>`;
            } else {
                // عرض 0 مع الإشارة للكمية الأصلية
                return `<span class="text-warning">0.00 <small>(كانت: ${qty.toFixed(2)})</small></span>`;
            }
        }

        if (qty === 0) {
            return '<span class="text-muted">0.00</span>';
        }

        return `<span class="text-success">${qty.toFixed(2)}</span>`;
    }

    // ===== منع إضافة صف جديد بدون اختيار المستودع =====
    checkStoreSelection();

    $('#store_id').on('change', function() {
        checkStoreSelection();
        storeSelected = $('#store_id').val();
        loadAllProducts();
        updateTotals();
    });

    function checkStoreSelection() {
        const storeSelectedactive = $('#store_id').val();
        const addButton = $('#add-item-btn');

        if (!storeSelectedactive) {
            addButton.prop('disabled', true)
                .attr('title', '@lang('crud.please_select_store_first')')
                .addClass('disabled');

            storeSelected = 0;

            $('.item-row').each(function() {
                $(this).remove();
            });
            $('#no-items-placeholder').removeClass('d-none');
        } else {
            addButton.prop('disabled', false)
                .removeAttr('title')
                .removeClass('disabled');

            storeSelected = storeSelectedactive;
            loadAllProducts();
        }
    }

    function loadAllProducts() {
        if (!storeSelected) return;

        $.ajax({
            url: '/store/getproducts',
            dataType: 'json',
            data: {
                store: storeSelected,
                typepage: typepage
            },
            success: function(data) {
                // معالجة جميع المنتجات حسب إعدادات المخزون
                allProducts = data.map(product => processProductQuantities(product));
                productsLoaded = true;

                console.log('✓ Products loaded:', allProducts.length);
                console.log('✓ Sample product quantities:', {
                    name: allProducts[0]?.name,
                    base_quantity: allProducts[0]?.quantity,
                    units: allProducts[0]?.units?.map(u => ({
                        name: u.name,
                        quantity: u.quantity,
                        conversion: u.conversion_factor
                    }))
                });
            },
            error: function(xhr, status, error) {
                console.error('✗ Failed to load products:', status, error);
                toastr.error('@lang('crud.error_loading_products')');
            }
        });
    }

    // ===== تحميل البيانات القديمة =====
    const oldItems = @json(old('items', isset($damaged) && $damaged->items ? $damaged->items->toArray() : []));

    if (oldItems && oldItems.length > 0) {
        storeSelected = $('#store_id').val();

        if (storeSelected) {
            $.ajax({
                url: '/store/getproducts',
                dataType: 'json',
                data: {
                    store: storeSelected,
                    typepage: typepage
                },
                success: function(data) {
                    allProducts = data.map(product => processProductQuantities(product));
                    productsLoaded = true;

                    oldItems.forEach(function(item) {
                        addRowWithData(item);
                    });
                    updateTotals();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load products for old items:', status, error);
                }
            });
        }
    }

    $("#add-item-btn").on("click", function() {
        if (!$('#store_id').val()) {
            toastr.error('@lang('crud.please_select_store_first')');
            return;
        }
        addNewRow();
    });

    function addNewRow() {
        $("#no-items-placeholder").addClass("d-none");

        let template = $("#item-row-template").html().replace(/__INDEX__/g, rowIndex);
        let newRow = $(template);
        $("#items-container").append(newRow);

        const productSelect = newRow.find('.livesearchprodect');

        if (productsLoaded && allProducts.length > 0) {
            productSelect.select2({
                placeholder: '@lang('crud.search_for_product')',
                allowClear: true,
                width: '100%',
                data: allProducts.map(function(item) {
                    return {
                        text: item.name,
                        id: item.id,
                        barcode: item.barcode,
                        type: item.type,
                        cost_price: item.cost_price,
                        quantity: item.quantity,
                        product_id: item.product_id || item.id,
                        units: item.units || []
                    };
                }),
                templateResult: formatProduct,
                templateSelection: formatProductSelection,
                language: {
                    noResults: function() {
                        return '@lang('lang.no_data')';
                    }
                }
            });
        } else {
            productSelect.select2({
                placeholder: '@lang('crud.search_for_product')',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: `/store/getproducts`,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            store: storeSelected,
                            typepage: typepage
                        };
                    },
                    processResults: function(data) {
                        if (!productsLoaded) {
                            allProducts = data.map(product => processProductQuantities(product));
                            productsLoaded = true;
                        }

                        return {
                            results: $.map(data, function(item) {
                                const processedProduct = processProductQuantities(item);
                                return {
                                    text: processedProduct.name,
                                    id: processedProduct.id,
                                    barcode: processedProduct.barcode,
                                    type: processedProduct.type,
                                    cost_price: processedProduct.cost_price,
                                    quantity: processedProduct.quantity,
                                    product_id: processedProduct.product_id || processedProduct.id,
                                    units: processedProduct.units || []
                                }
                            })
                        };
                    },
                    cache: false
                },
                minimumInputLength: 0,
                language: {
                    inputTooShort: function() {
                        return '@lang('crud.start_typing')';
                    },
                    searching: function() {
                        return '@lang('crud.searching')...';
                    },
                    noResults: function() {
                        return '@lang('lang.no_data')';
                    }
                },
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            });
        }

        attachRowEventListeners(newRow);
        rowIndex++;
        updateTotals();
    }

    function addRowWithData(itemData) {
        $("#no-items-placeholder").addClass("d-none");

        let template = $("#item-row-template").html().replace(/__INDEX__/g, rowIndex);
        let newRow = $(template);
        $("#items-container").append(newRow);

        const productSelect = newRow.find('.livesearchprodect');

        if (productsLoaded && allProducts.length > 0) {
            productSelect.select2({
                placeholder: '@lang('crud.search_for_product')',
                allowClear: true,
                width: '100%',
                data: allProducts.map(function(item) {
                    return {
                        text: item.name,
                        id: item.id,
                        barcode: item.barcode,
                        type: item.type,
                        cost_price: item.cost_price,
                        quantity: item.quantity,
                        product_id: item.product_id || item.id,
                        units: item.units || []
                    };
                }),
                templateResult: formatProduct,
                templateSelection: formatProductSelection,
                language: {
                    noResults: function() {
                        return '@lang('lang.no_data')';
                    }
                }
            });

            if (itemData.product_id) {
                const product = allProducts.find(p => p.id == itemData.product_id);
                if (product) {
                    const newOption = new Option(
                        itemData.product_name || product.name,
                        itemData.product_id,
                        true,
                        true
                    );
                    productSelect.append(newOption).trigger('change');

                    newRow.data('product-type', product.type);
                    newRow.data('product-barcode', product.barcode);
                    newRow.data('product-cost', product.cost_price);
                    newRow.data('product-id', product.id);
                    newRow.data('product-quantity', product.quantity);
                    newRow.data('units', product.units || []);

                    // حفظ بيانات الوحدات في hidden field
                    newRow.find('.product-units-data').val(JSON.stringify(product.units || []));

                    newRow.find('.barcode-input').val(itemData.barcode || product.barcode || '');
                    newRow.find('input[name*="[product_name]"]').val(itemData.product_name || product.name);
                    newRow.find('input[name*="[type]"]').val(itemData.type || product.type);

                    const unitSelect = newRow.find('.unit-select');
                    unitSelect.prop('disabled', false).empty();
                    unitSelect.append(`<option value="">@lang('crud.select')</option>`);

                    const units = product.units || [];
                    if (units.length > 0) {
                        units.forEach(unit => {
                            const selected = unit.id == itemData.unit_id ? 'selected' : '';
                            unitSelect.append(
                                `<option value="${unit.id}"
                                    data-cost="${unit.cost_price || 0}"
                                    data-conversion="${unit.conversion_factor || 1}"
                                    data-quantity="${unit.quantity || 0}"
                                    data-is-base="${unit.is_base ? '1' : '0'}"
                                    data-barcode="${unit.barcode || ''}" ${selected}>${unit.name}</option>`
                            );
                        });

                        if (itemData.unit_id) {
                            unitSelect.val(itemData.unit_id);
                            const selectedUnit = units.find(u => u.id == itemData.unit_id);
                            if (selectedUnit) {
                                newRow.find('input[name*="[unit_name]"]').val(selectedUnit.name);

                                const unitCost = selectedUnit.cost_price || product.cost_price || 0;
                                newRow.find('.cost-input').val(parseFloat(unitCost).toFixed(4));

                                // الكمية الدفترية من بيانات الوحدة (معالجة مسبقاً)
                                newRow.find('.book_quantity-input').val(parseFloat(selectedUnit.quantity || 0).toFixed(4));

                                if (selectedUnit.barcode && selectedUnit.barcode !== product.barcode) {
                                    newRow.find('.barcode-input').val(selectedUnit.barcode);
                                }
                            }
                        } else {
                            newRow.find('.cost-input').val(parseFloat(product.cost_price || 0).toFixed(4));
                            newRow.find('.book_quantity-input').val(parseFloat(product.quantity || 0).toFixed(4));
                        }
                    } else {
                        unitSelect.prop('disabled', true).append(`<option value="">@lang('lang.no_data')</option>`);
                        newRow.find('.cost-input').val(parseFloat(product.cost_price || 0).toFixed(4));
                        newRow.find('.book_quantity-input').val(parseFloat(product.quantity || 0).toFixed(4));
                    }

                    newRow.find('.quantity-input').val(parseFloat(itemData.quantity || 1).toFixed(2));
                    calculateRow(newRow);
                    newRow.find('input[name*="[notes]"]').val(itemData.notes || '');
                }
            }
        }

        attachRowEventListeners(newRow);
        rowIndex++;
    }

    function formatProduct(product) {
        if (!product.id) {
            return product.text;
        }

        const qty = processQuantity(product.quantity || 0);
        const qtyClass = qty < 0 ? 'text-danger' : (qty === 0 ? 'text-warning' : 'text-success');

        var $product = $(
            '<div class="d-flex justify-content-between align-items-center">' +
            '<span>' + product.text + '</span>' +
            '<div class="d-flex gap-2 align-items-center">' +
            '<small class="text-muted">' + (product.barcode || '') + '</small>' +
            '<small class="' + qtyClass + ' fw-bold">(' + qty.toFixed(2) + ')</small>' +
            '</div>' +
            '</div>'
        );
        return $product;
    }

    function formatProductSelection(product) {
        return product.text || product.name;
    }

    function attachRowEventListeners(row) {
        row.on('select2:select', '.product-select', function(e) {
            const selectedData = e.params.data;
            const currentRow = $(this).closest('tr');
            const unitSelect = currentRow.find('.unit-select');
            const costInput = currentRow.find('.cost-input');
            const barcodeInput = currentRow.find('.barcode-input');
            const bookQuantityInput = currentRow.find('.book_quantity-input');
            const productNameInput = currentRow.find('input[name*="[product_name]"]');
            const productTypeInput = currentRow.find('input[name*="[type]"]');
            const unitNameInput = currentRow.find('input[name*="[unit_name]"]');

            // حفظ بيانات المنتج
            currentRow.data('product-type', selectedData.type);
            currentRow.data('product-barcode', selectedData.barcode);
            currentRow.data('product-cost', selectedData.cost_price);
            currentRow.data('product-id', selectedData.product_id);
            currentRow.data('product-quantity', selectedData.quantity || 0);
            currentRow.data('units', selectedData.units || []);

            // حفظ الوحدات في hidden field
            currentRow.find('.product-units-data').val(JSON.stringify(selectedData.units || []));

            barcodeInput.val(selectedData.barcode || '');
            costInput.val(parseFloat(selectedData.cost_price || 0).toFixed(4));
            bookQuantityInput.val(parseFloat(selectedData.quantity || 0).toFixed(4));
            productNameInput.val(selectedData.text);
            productTypeInput.val(selectedData.type);

            unitSelect.prop('disabled', true).empty().append(`<option value="">@lang('crud.select')</option>`);
            unitNameInput.val('');

            const units = selectedData.units || [];

            console.log('===== Product Selected =====');
            console.log('Product:', selectedData.text);
            console.log('Base quantity:', selectedData.quantity);
            console.log('Allow negative:', inventorySettings.allow_negative_stock);
            console.log('Units:', units.map(u => ({ name: u.name, qty: u.quantity, factor: u.conversion_factor })));

            if (units && units.length > 0) {
                unitSelect.prop('disabled', false).empty();
                unitSelect.append(`<option value="">@lang('crud.select')</option>`);

                units.forEach(unit => {
                    unitSelect.append(
                        `<option value="${unit.id}"
                                data-cost="${unit.cost_price || 0}"
                                data-conversion="${unit.conversion_factor || 1}"
                                data-quantity="${unit.quantity || 0}"
                                data-is-base="${unit.is_base ? '1' : '0'}"
                                data-barcode="${unit.barcode || ''}">${unit.name} (${parseFloat(unit.quantity || 0).toFixed(2)})</option>`
                    );
                });

                if (units.length === 1) {
                    unitSelect.val(units[0].id).trigger('change');
                }
            } else {
                unitSelect.prop('disabled', true).empty().append(`<option value="">@lang('lang.no_data')</option>`);
            }

            calculateRow(currentRow);
        });

        row.on('select2:clear', '.product-select', function() {
            const currentRow = $(this).closest('tr');

            currentRow.find('.product-units-data').val('');
            currentRow.find('.barcode-input').val('');
            currentRow.find('.cost-input').val('0.0000');
            currentRow.find('.book_quantity-input').val('0.0000');
            currentRow.find('input[name*="[product_name]"]').val('');
            currentRow.find('input[name*="[type]"]').val('');
            currentRow.find('input[name*="[unit_name]"]').val('');

            currentRow.find('.unit-select')
                .prop('disabled', true)
                .empty()
                .append(`<option value="">@lang('crud.select')</option>`);

            calculateRow(currentRow);
        });

        // ===== تحديث التكلفة والكمية الدفترية عند تغيير الوحدة =====
        row.on('change', '.unit-select', function() {
            const selectedUnitId = $(this).val();
            const currentRow = $(this).closest('tr');
            const unitsData = currentRow.data('units') || [];
            const selectedUnit = unitsData.find(u => u.id == selectedUnitId);
            const productCost = currentRow.data('product-cost') || 0;
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

                // تحديث الباركود
                if (selectedUnit.barcode && selectedUnit.barcode !== productBarcode) {
                    barcodeInput.val(selectedUnit.barcode);
                } else {
                    barcodeInput.val(productBarcode || '');
                }

                unitNameInput.val(selectedUnit.name);

                // ===== حساب التكلفة =====
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

                // ===== تعيين الكمية الدفترية =====
                // الكمية معالجة مسبقاً من الـ API حسب allow_negative_stock
                const bookQuantity = selectedUnit.quantity || 0;
                bookQuantityInput.val(parseFloat(bookQuantity).toFixed(4));

                console.log('Final Book Quantity:', bookQuantity);
                console.log('========================');

            } else {
                // لا توجد وحدة محددة
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
            if (confirm('@lang('crud.are_you_sure')')) {
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


    // $('form').on('submit', function(e) {
    //     let isValid = true;
    //     let emptyRows = [];
    //     let totalRows = $('.item-row').length;

    //     if (totalRows === 0) {
    //         e.preventDefault();
    //         toastr.error('@lang('crud.no_products_added')');
    //         return false;
    //     }

    //     $('.item-row').each(function(index) {
    //         const productId = $(this).find('.product-select').val();
    //         const unitId = $(this).find('.unit-select').val();
    //         const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;

    //         if (!productId || !unitId || quantity <= 0) {
    //             isValid = false;
    //             emptyRows.push(index + 1);
    //             $(this).addClass('table-danger');
    //         }
    //     });

    //     if (!isValid) {
    //         e.preventDefault();
    //         toastr.error('@lang('crud.complete_product_unit_data') ' + emptyRows.join(', '));
    //         return false;
    //     }

    //     return true;
    // });


    // ===== التحقق قبل الحفظ =====


// ===== التحقق قبل الحفظ =====
    $('form').on('submit', function(e) {
        let isValid = true;
        let emptyRows = [];
        let negativeRows = [];
        let totalRows = $('.item-row').length;



        if (totalRows === 0) {
            e.preventDefault();
            toastr.error('@lang('crud.no_products_added')');
            return false;
        }

        $('.item-row').each(function(index) {
            const productId = $(this).find('.product-select').val();
            const unitId = $(this).find('.unit-select').val();
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const bookQuantity = parseFloat($(this).find('.book_quantity-input').val()) || 0;
            const productName = $(this).find('input[name*="[product_name]"]').val() || '';
            const unitName = $(this).find('input[name*="[unit_name]"]').val() || '';



            // التحقق من البيانات الأساسية
            if (!productId || !unitId || quantity <= 0) {
                isValid = false;
                emptyRows.push(index + 1);
                $(this).addClass('table-danger');
            }









            // التحقق من الكمية الدفترية السالبة
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
                toastr.error('@lang('crud.complete_product_unit_data') ' + emptyRows.join(', '));
            }

            if (negativeRows.length > 0) {
                let message = '@lang("crud.cannot_submit_negative_stock")\n\n';
                negativeRows.forEach(item => {
                    message += `الصف ${item.row}: ${item.product} - ${item.unit} (${item.quantity})\n`;
                });
                toastr.error(message, '', { timeOut: 10000 });
            }

            return false;
        }

        // إزالة أي تنسيقات تحذيرية قبل الإرسال
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
            toastr.error('@lang('crud.please_select_store_first')');
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

        // البحث في باركود المنتجات الرئيسية
        foundProduct = allProducts.find(p => p.barcode === barcode);

        if (foundProduct) {
            found = true;
            console.log('✓ Product found:', foundProduct.name);
        } else {
            // البحث في باركود الوحدات
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
            toastr.error('@lang('crud.product_not_found'): ' + barcode);

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
                toastr.error('@lang('crud.please_select_store_first')');
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
</script> -->
