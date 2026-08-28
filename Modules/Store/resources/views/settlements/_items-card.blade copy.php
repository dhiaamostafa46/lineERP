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
                            <th>@lang('store::models/st_settlements.items.barcode')</th>
                            <th>@lang('store::models/st_settlements.items.product_id')</th>
                            <th>@lang('store::models/st_settlements.items.unit_id')</th>
                            <th>@lang('store::models/st_settlements.items.quantity')</th>
                            <th>@lang('store::models/st_settlements.items.unit_cost')</th>
                            <th>@lang('store::models/st_settlements.items.total_cost')</th>
                            <th>@lang('store::models/st_settlements.items.notes')</th>
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
                                <td class="text-end fw-bold">@lang('store::models/st_settlements.fields.total_items')</td>
                                <td class="text-end">
                                    <span id="total-items">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold">@lang('store::models/st_settlements.fields.total_quantity')</td>
                                <td class="text-end">
                                    <span id="total-quantity">0.00</span>
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-end fw-bold pt-2 text-dark">
                                    <i class="fas fa-calculator me-1"></i>
                                    @lang('store::models/st_settlements.fields.total_value')
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
            <input type="text" name="items[__INDEX__][type]" class="product-type">
           <input type="hidden" name="items[__INDEX__][product_units]" class="product-units-data">
        </td>

        <td>
            <select class="form-select form-select-md unit-select" name="items[__INDEX__][unit_id]" disabled>
                <option value="">@lang('lang.select')</option>
            </select>
            <input type="hidden" name="items[__INDEX__][unit_name]">
        </td>

        <td>
            <input type="number" step="0.01" min="0.01" class="form-control form-control-md quantity-input"
                name="items[__INDEX__][quantity]" value="1">
        </td>

        <td>
            <input type="number" step="0.0001" min="0" class="form-control form-control-md cost-input"
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
        let typepage = 'settlement';

        // ===== 1. منع إضافة صف جديد بدون اختيار المستودع =====
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

                storeSelected = $('#store_id').val();
                loadAllProducts();
                updateTotals();

                // إخفاء جميع الصفوف الموجودة
                $('.item-row').each(function() {
                    $(this).remove();
                });
                $('#no-items-placeholder').removeClass('d-none');
            } else {
                addButton.prop('disabled', false)
                    .removeAttr('title')
                    .removeClass('disabled');
            }
        }

        function loadAllProducts() {
            $.ajax({
                url: '/store/getproducts',
                dataType: 'json',
                data: {
                    store: storeSelected,
                    typepage: typepage
                },
                success: function(data) {
                    allProducts = data;
                    productsLoaded = true;
                    console.log('All products loaded:', allProducts.length);
                    console.log('Sample product with units:', allProducts[0]);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load products:', status, error);
                }
            });
        }

        // ===== تحميل البيانات القديمة (عند التعديل أو validation error) =====
        const oldItems = @json(old('items', isset($settlement) && $settlement->items ? $settlement->items->toArray() : []));

        if (oldItems && oldItems.length > 0) {
            storeSelected = $('#store_id').val();

            if (storeSelected) {
                // تحميل المنتجات أولاً
                $.ajax({
                    url: '/store/getproducts',
                    dataType: 'json',
                    data: {
                        store: storeSelected,
                        typepage: typepage
                    },
                    success: function(data) {
                        allProducts = data;
                        productsLoaded = true;
                        console.log('Products loaded for old items:', allProducts.length);

                        // إضافة الصفوف بعد تحميل المنتجات
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
            // التحقق من المستودع قبل الإضافة
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
                                allProducts = data;
                                productsLoaded = true;
                            }
                            console.log('Products loaded via AJAX:', data);

                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.name,
                                        id: item.id,
                                        barcode: item.barcode,
                                        type: item.type,
                                        cost_price: item.cost_price,
                                        product_id: item.product_id || item.id,
                                        units: item.units || []
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

        // ===== دالة إضافة صف مع بيانات (للبيانات القديمة) =====
        function addRowWithData(itemData) {
            $("#no-items-placeholder").addClass("d-none");

            let template = $("#item-row-template").html().replace(/__INDEX__/g, rowIndex);
            let newRow = $(template);
            $("#items-container").append(newRow);

            const productSelect = newRow.find('.livesearchprodect');

            // إعداد Select2
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

                // تعيين المنتج
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

                        // حفظ بيانات المنتج في الصف
                        newRow.data('product-type', product.type);
                        newRow.data('product-unit', product.units);
                        newRow.data('product-barcode', product.barcode);
                        newRow.data('product-cost', product.cost_price);
                        newRow.data('product-id', product.id);
                        newRow.data('units', product.units || []);

                        // تعيين البيانات الأساسية
                        newRow.find('.barcode-input').val(itemData.barcode || product.barcode || '');
                        newRow.find('input[name*="[product_name]"]').val(itemData.product_name || product.name);
                        newRow.find('input[name*="[type]"]').val(itemData.type || product.type);

                        // تعيين الوحدات
                        const unitSelect = newRow.find('.unit-select');
                        unitSelect.prop('disabled', false).empty();
                        unitSelect.append(`<option value="">@lang('crud.select')</option>`);

                        const units = product.units || [];
                        if (units.length > 0) {
                            units.forEach(unit => {
                                const selected = unit.id == itemData.unit_id ? 'selected' : '';
                                unitSelect.append(
                                    `<option value="${unit.id}" data-cost="${unit.cost_price || 0}" data-barcode="${unit.barcode || ''}" ${selected}>${unit.name}</option>`
                                );
                            });

                            if (itemData.unit_id) {
                                unitSelect.val(itemData.unit_id);
                                const selectedUnit = units.find(u => u.id == itemData.unit_id);
                                if (selectedUnit) {
                                    newRow.find('input[name*="[unit_name]"]').val(selectedUnit.name);

                                    // تعيين التكلفة من الوحدة
                                    const unitCost = selectedUnit.cost_price || product.cost_price || 0;
                                    newRow.find('.cost-input').val(parseFloat(unitCost).toFixed(4));

                                    // تحديث الباركود إذا كان للوحدة باركود مختلف
                                    if (selectedUnit.barcode && selectedUnit.barcode !== product.barcode) {
                                        newRow.find('.barcode-input').val(selectedUnit.barcode);
                                    }
                                }
                            } else {
                                // إذا لم تكن هناك وحدة محددة، استخدم تكلفة المنتج
                                newRow.find('.cost-input').val(parseFloat(product.cost_price || 0).toFixed(4));
                            }
                        } else {
                            unitSelect.prop('disabled', true).append(
                                `<option value="">@lang('lang.no_data')</option>`
                            );
                            newRow.find('.cost-input').val(parseFloat(product.cost_price || 0).toFixed(4));
                        }

                        // تعيين الكمية والتكلفة الإجمالية
                        newRow.find('.quantity-input').val(parseFloat(itemData.quantity || 1).toFixed(2));

                        // حساب التكلفة الإجمالية
                        calculateRow(newRow);

                        // تعيين الملاحظات
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

            var $product = $(
                '<div class="d-flex justify-content-between align-items-center">' +
                '<span>' + product.text + '</span>' +
                '<small class="text-muted ms-2">' + (product.barcode || '') + '</small>' +
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
                const productNameInput = currentRow.find('input[name*="[product_name]"]');
                const productTypeInput = currentRow.find('input[name*="[type]"]');

                const unitNameInput = currentRow.find('input[name*="[unit_name]"]');


                const productUnitsData = JSON.stringify(selectedData.units || []);
                 currentRow.find('.product-units-data').val(productUnitsData);

                currentRow.data('product-type', selectedData.type);

                currentRow.data('product-barcode', selectedData.barcode);
                currentRow.data('product-cost', selectedData.cost_price);
                currentRow.data('product-id', selectedData.product_id);

                barcodeInput.val(selectedData.barcode || '');

                // تعيين تكلفة المنتج الأساسية
                costInput.val(parseFloat(selectedData.cost_price || 0).toFixed(4));

                productNameInput.val(selectedData.text);
                productTypeInput.val(selectedData.type);


                unitSelect.prop('disabled', true).empty().append(
                    `<option value="">@lang('crud.select')</option>`
                );
                unitNameInput.val('');

                const units = selectedData.units || [];
                currentRow.data('units', units);

                console.log('Product units:', units);

                if (units && units.length > 0) {
                    unitSelect.prop('disabled', false).empty();
                    unitSelect.append(`<option value="">@lang('crud.select')</option>`);

                    units.forEach(unit => {
                        unitSelect.append(
                            `<option value="${unit.id}"
                                data-cost="${unit.cost_price || 0}"
                                data-conversion="${unit.conversion_factor || 1}"
                                data-barcode="${unit.barcode || ''}">${unit.name}</option>`
                        );
                    });

                    if (units.length === 1) {
                        unitSelect.val(units[0].id).trigger('change');
                    }
                } else {
                    unitSelect.prop('disabled', true).empty().append(
                        `<option value="">@lang('lang.no_data')</option>`
                    );
                }

                calculateRow(currentRow);
            });

            row.on('select2:clear', '.product-select', function() {
                const currentRow = $(this).closest('tr');
                const unitSelect = currentRow.find('.unit-select');
                const costInput = currentRow.find('.cost-input');
                const barcodeInput = currentRow.find('.barcode-input');
                const productNameInput = currentRow.find('input[name*="[product_name]"]');
                const productTypeInput = currentRow.find('input[name*="[type]"]');
                const unitNameInput = currentRow.find('input[name*="[unit_name]"]');

                barcodeInput.val('');
                costInput.val('0.0000');
                productNameInput.val('');
                productTypeInput.val('');
                unitNameInput.val('');

                unitSelect.prop('disabled', true).empty().append(
                    `<option value="">@lang('crud.select')</option>`
                );

                calculateRow(currentRow);
            });

            // ===== التغيير الرئيسي: تحديث التكلفة عند اختيار الوحدة =====
            row.on('change', '.unit-select', function() {
                const selectedUnitId = $(this).val();
                const currentRow = $(this).closest('tr');
                const unitsData = currentRow.data('units') || [];
                const selectedUnit = unitsData.find(u => u.id == selectedUnitId);
                const productCost = currentRow.data('product-cost') || 0;

                const barcodeInput = currentRow.find('.barcode-input');
                const costInput = currentRow.find('.cost-input');
                const unitNameInput = currentRow.find('input[name*="[unit_name]"]');
                const productBarcode = currentRow.data('product-barcode');

                if (selectedUnit) {
                    console.log('Selected unit:', selectedUnit);

                    // تحديث الباركود
                    if (selectedUnit.barcode && selectedUnit.barcode !== productBarcode) {
                        barcodeInput.val(selectedUnit.barcode);
                    } else {
                        barcodeInput.val(productBarcode || '');
                    }

                    // تحديث اسم الوحدة
                    unitNameInput.val(selectedUnit.name);

                    // ===== تحديث التكلفة بناءً على تكلفة الوحدة =====
                    // إذا كان للوحدة تكلفة محددة، استخدمها
                    // وإلا احسب التكلفة بناءً على معامل التحويل
                    let unitCost;
                    if (selectedUnit.cost_price && selectedUnit.cost_price > 0) {
                        unitCost = selectedUnit.cost_price;
                        console.log('Using unit cost_price:', unitCost);
                    } else if (selectedUnit.conversion_factor) {
                        unitCost = productCost * selectedUnit.conversion_factor;
                        console.log('Calculating cost: ', productCost, ' × ', selectedUnit
                            .conversion_factor, ' = ', unitCost);
                    } else {
                        unitCost = productCost;
                        console.log('Using product cost:', unitCost);
                    }

                    costInput.val(parseFloat(unitCost).toFixed(4));
                } else {
                    // إذا لم يتم اختيار وحدة، استخدم تكلفة المنتج الأساسية
                    barcodeInput.val(productBarcode || '');
                    unitNameInput.val('');
                    costInput.val(parseFloat(productCost).toFixed(4));
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

            // ===== البحث بالباركود داخل الصف =====
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

        // ===== 2. التحقق قبل الحفظ من وجود المنتج والوحدة =====
        $('form').on('submit', function(e) {
            let isValid = true;
            let emptyRows = [];
            let totalRows = $('.item-row').length;

            if (totalRows === 0) {
                e.preventDefault();
                toastr.error('@lang('crud.no_products_added')');
                $('#add-item-btn').addClass('btn-danger').removeClass('btn-primary');

                setTimeout(function() {
                    $('#add-item-btn').removeClass('btn-danger').addClass('btn-primary');
                }, 2000);

                return false;
            }

            $('.item-row').each(function(index) {
                const productId = $(this).find('.product-select').val();
                const unitId = $(this).find('.unit-select').val();
                const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;

                if (!productId || !unitId || quantity <= 0) {
                    isValid = false;
                    emptyRows.push(index + 1);
                    $(this).addClass('table-danger');

                    if (!productId) {
                        $(this).find('.product-select').next('.select2-container').addClass(
                            'border border-danger');
                    }
                    if (!unitId) {
                        $(this).find('.unit-select').addClass('border border-danger');
                    }
                    if (quantity <= 0) {
                        $(this).find('.quantity-input').addClass('border border-danger');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();

                if (emptyRows.length === totalRows) {
                    toastr.error('@lang('crud.all_rows_incomplete')');
                } else {
                    toastr.error('@lang('crud.complete_product_unit_data') ' + emptyRows.join(', '));
                }

                setTimeout(function() {
                    $('.item-row').removeClass('table-danger');
                    $('.select2-container').removeClass('border border-danger');
                    $('.unit-select').removeClass('border border-danger');
                    $('.quantity-input').removeClass('border border-danger');
                }, 3000);

                if (emptyRows.length > 0) {
                    const firstErrorRow = $('.item-row').eq(emptyRows[0] - 1);
                    $('html, body').animate({
                        scrollTop: firstErrorRow.offset().top - 100
                    }, 500);
                }

                return false;
            }

            $('.item-row').each(function() {
                const productId = $(this).find('.product-select').val();
                if (!productId) {
                    $(this).remove();
                }
            });

            if ($('.item-row').length === 0) {
                e.preventDefault();
                toastr.error('@lang('crud.no_products_added')');
                return false;
            }

            return true;
        });

        // ===== البحث بالباركود عبر Scanner - محسّن =====
        let barcodeBuffer = '';
        let barcodeTimeout = null;

        $(document).on('keypress', function(e) {
            if ($(e.target).is('input, textarea, select')) {
                return;
            }

            clearTimeout(barcodeTimeout);

            if (e.which === 13) {
                e.preventDefault();
                if (barcodeBuffer.length > 0) {
                    searchByBarcode(barcodeBuffer);
                    barcodeBuffer = '';
                }
            } else {
                barcodeBuffer += String.fromCharCode(e.which);

                barcodeTimeout = setTimeout(function() {
                    barcodeBuffer = '';
                }, 100);
            }
        });

        // ===== دالة البحث المحسّنة بالباركود =====
        function searchByBarcode(barcode) {
            if (!$('#store_id').val()) {
                toastr.error('@lang('crud.please_select_store_first')');
                return;
            }

            console.log('Searching for barcode:', barcode);

            // التأكد من تحميل المنتجات أولاً
            if (!productsLoaded || allProducts.length === 0) {
                loadAllProducts();
                setTimeout(function() {
                    searchByBarcodeLogic(barcode);
                }, 500);
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

            searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
        }

        // ===== البحث بالباركود داخل صف محدد =====
        function searchByBarcodeInRow(barcode, targetRow) {
            console.log('Searching barcode in specific row:', barcode);

            if (!productsLoaded || allProducts.length === 0) {
                loadAllProducts();
                setTimeout(function() {
                    searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
                }, 500);
            } else {
                searchInLocalProducts(barcode, targetRow.find('.product-select'), targetRow);
            }
        }

        // ===== البحث في المصفوفة المحلية - محسّن =====
        function searchInLocalProducts(barcode, productSelect, targetRow) {
            console.log('Searching in', allProducts.length, 'products for barcode:', barcode);

            let found = false;
            let foundProduct = null;
            let foundUnit = null;

            // 1. البحث في باركود المنتجات الرئيسية
            foundProduct = allProducts.find(p => p.barcode === barcode);

            if (foundProduct) {
                found = true;
                console.log('✓ Product found by main barcode:', foundProduct.name);
            } else {
                // 2. البحث في باركود الوحدات
                for (let prod of allProducts) {
                    if (prod.units && prod.units.length > 0) {
                        foundUnit = prod.units.find(u => u.barcode === barcode);
                        if (foundUnit) {
                            foundProduct = prod;
                            found = true;
                            console.log('✓ Unit found by barcode:', foundUnit.name, 'in product:', prod.name);
                            break;
                        }
                    }
                }
            }

            if (found && foundProduct) {
                // إضافة المنتج إلى Select2
                const newOption = new Option(foundProduct.name, foundProduct.id, true, true);
                productSelect.append(newOption).trigger('change');

                // تفعيل حدث select2
                productSelect.trigger({
                    type: 'select2:select',
                    params: {
                        data: {
                            text: foundProduct.name,
                            id: foundProduct.id,
                            barcode: foundProduct.barcode,
                            type: foundProduct.type,
                            cost_price: foundProduct.cost_price,
                            product_id: foundProduct.product_id || foundProduct.id,
                            units: foundProduct.units || []
                        }
                    }
                });

                // إذا تم العثور على وحدة محددة، اختارها
                if (foundUnit) {
                    setTimeout(function() {
                        const unitSelect = targetRow.find('.unit-select');
                        unitSelect.val(foundUnit.id).trigger('change');

                        // التركيز على حقل الكمية
                        setTimeout(function() {
                            targetRow.find('.quantity-input').focus().select();
                        }, 100);
                    }, 200);
                } else {
                    // التركيز على حقل الكمية
                    setTimeout(function() {
                        targetRow.find('.quantity-input').focus().select();
                    }, 100);
                }
            } else {
                console.log('✗ Product not found for barcode:', barcode);
                toastr.error('@lang('crud.product_not_found'): ' + barcode);

                // حذف الصف إذا كان فارغاً
                if (!targetRow.find('.product-select').val()) {
                    targetRow.remove();
                    if ($(".item-row").length === 0) {
                        $("#no-items-placeholder").removeClass("d-none");
                    }
                }
            }
        }

        // ===== اختصار لوحة المفاتيح =====
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.altKey) && e.key === 'n') {
                e.preventDefault();
                if ($('#store_id').val()) {
                    addNewRow();
                    $(".item-row").last().find('.product-select').select2('open');
                } else {
                    toastr.error('@lang('crud.please_select_store_first')');
                }
            }
        });

        $(document).on('keydown', '.quantity-input, .cost-input', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const currentRow = $(this).closest('tr');
                const nextInput = currentRow.find('.cost-input, .quantity-input').not(this).first();

                if (nextInput.length > 0) {
                    nextInput.focus().select();
                } else {
                    if ($('#store_id').val()) {
                        addNewRow();
                        setTimeout(function() {
                            $(".item-row").last().find('.product-select').select2('open');
                        }, 100);
                    }
                }
            }
        });
    });


    function searchInLocalProducts(barcode, productSelect, targetRow) {
        // البحث في باركود المنتجات الرئيسية
        foundProduct = allProducts.find(p => p.barcode === barcode);

        // البحث في باركود الوحدات
        for (let prod of allProducts) {
            if (prod.units && prod.units.length > 0) {
                foundUnit = prod.units.find(u => u.barcode === barcode);
            }
        }
    }

    row.on('change', '.unit-select', function() {
        const selectedUnit = unitsData.find(u => u.id == selectedUnitId);

        // إذا كان للوحدة تكلفة محددة
        if (selectedUnit.cost_price && selectedUnit.cost_price > 0) {
            unitCost = selectedUnit.cost_price;
        }
        // أو احسب من معامل التحويل
        else if (selectedUnit.conversion_factor) {
            unitCost = productCost * selectedUnit.conversion_factor;
        }
        // أو استخدم تكلفة المنتج
        else {
            unitCost = productCost;
        }

        costInput.val(parseFloat(unitCost).toFixed(4));
    });

    unitSelect.append(
        `<option value="${unit.id}"
            data-cost="${unit.cost_price || 0}"
            data-conversion="${unit.conversion_factor || 1}"
            data-barcode="${unit.barcode || ''}">${unit.name}</option>`
    );
</script> -->
