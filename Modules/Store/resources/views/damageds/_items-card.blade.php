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
        // استعادة الكمية الدفترية من قيمة unit_quantity (الرصيد في المخزن وقت الإنشاء)
        // نعيد حسابها الآن من المنتج، لكن نعرض ما تم حفظه كـ book_quantity إن وجد
        const savedBookQty = item.book_quantity ?? item.quantity ?? 0;
        newRow.querySelector('.book_quantity-input').value = parseFloat(savedBookQty).toFixed(2);
        
        // Load current stock for this product/unit from server to show latest book qty
        const storeId = document.getElementById('store_id')?.value;
        const productId = item.product_id;
        const unitId = item.unit_id;
        if (storeId && productId && unitId) {
            fetch(`/store/getproducts?store=${storeId}&product_id=${productId}`)
                .then(r => r.json())
                .then(data => {
                    const found = Array.isArray(data) ? data.find(p => p.id == productId) : null;
                    if (found) {
                        const unit = found.units?.find(u => u.id == unitId);
                        const bq = unit ? unit.quantity : found.quantity;
                        newRow.querySelector('.book_quantity-input').value = parseFloat(bq || 0).toFixed(2);
                    }
                })
                .catch(() => {});
        }
        
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
});
</script>
