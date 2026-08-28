@php
    $langPrefix = $langPrefix ?? 'invoices::models/purchase_invoices';
    $invoice = $invoice ?? (isset($purchaseInvoice) ? $purchaseInvoice : null);
    $isSale = $isSale ?? str_contains($langPrefix, 'sales');
@endphp

<!-- Card 2: Items Table -->
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-header py-3 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-box-seam text-primary"></i>
            {{ __($langPrefix . '.ui.items_details') }}
        </h5>
        <div class="d-flex gap-2" style="width: 400px">
            <select id="product_search" class="form-select select2-products">
                <option value=""></option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="items_table">
                <thead>
                    <tr class="text-center table-light align-middle text-secondary fw-semibold">
                        <th width="5%">#</th>
                        <th width="20%" class="text-start">{{ __($langPrefix . '.fields.product_name') }}</th>
                        <th width="15%">{{ __($langPrefix . '.fields.description') }}</th>
                        <th width="10%">{{ __($langPrefix . '.fields.unit') }}</th>
                        <th width="8%">{{ __($langPrefix . '.fields.quantity') }}</th>
                        <th width="10%">{{ __($langPrefix . '.fields.unit_price') }}</th>
                        <th width="12%">{{ __($langPrefix . '.fields.discount_amount') }}</th>
                        <th width="10%">{{ __($langPrefix . '.fields.vat_rate') }}</th>
                        <th width="10%">{{ __($langPrefix . '.fields.subtotal_with_vat') }}</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody id="items_body">
                    <!-- Dynamic Rows Here -->
                    @php
                        $oldItems = old('items');
                        $itemsToRender = [];
                        // Helper to mimic ProductService unit formatting
                        if (!function_exists('formatUnitsForInvoice')) {
                            function formatUnitsForInvoice($itemModel, $isSize = false)
                            {
                                $units = [];
                                $costPrice = $itemModel->cost_price ?? 0;
                                $salePrice = $itemModel->sale_price ?? $itemModel->prod_price ?? 0;
                                $productUnits = $isSize ? $itemModel->product->units ?? [] : $itemModel->units ?? [];

                                foreach ($productUnits as $pUnit) {
                                    $factor = $pUnit->conversion_factor ?: 1;
                                    $name = '---';
                                    if ($pUnit->unit) {
                                        $name = $pUnit->unit->name ?? '---';
                                    }

                                    $units[] = (object) [
                                        'id' => $pUnit->unit_id,
                                        'name' => $name,
                                        'cost_price' => round($costPrice * $factor, 4),
                                        'sale_price' => round($salePrice * $factor, 4),
                                        'prod_price' => round($salePrice * $factor, 4),
                                    ];
                                }
                                return $units;
                            }
                        }

                        if ($oldItems && is_array($oldItems)) {
                            // Extract IDs
                            $productIds = collect($oldItems)
                                ->where('have_sizes', '!=', 1)
                                ->pluck('product_id')
                                ->filter()
                                ->unique()
                                ->toArray();
                            $sizeIds = collect($oldItems)
                                ->where('have_sizes', 1)
                                ->pluck('product_id')
                                ->filter()
                                ->unique()
                                ->toArray();

                            $products = \App\Models\BasicDataApp\Product::with('units.unit')
                                ->whereIn('id', $productIds)
                                ->get()
                                ->keyBy('id');
                            $sizes = \App\Models\BasicDataApp\ProductSize::with('product.units.unit')
                                ->whereIn('id', $sizeIds)
                                ->get()
                                ->keyBy('id');

                            foreach ($oldItems as $idx => $oldItem) {
                                $obj = (object) $oldItem;
                                $isSize = !empty($obj->have_sizes);
                                $itemModel = $isSize ? $sizes->get($obj->product_id) : $products->get($obj->product_id);

                                if ($itemModel) {
                                    $obj->formatted_units = formatUnitsForInvoice($itemModel, $isSize);
                                } else {
                                    $obj->formatted_units = [];
                                }

                                if (!isset($obj->unit)) {
                                    $obj->unit = (object) ['name' => __($langPrefix . '.ui.piece')];
                                }
                                $itemsToRender[] = $obj;
                            }
                        } elseif (isset($invoice) && $invoice->items && count($invoice->items) > 0) {
                            $productsRequired = $invoice->items
                                ->where('have_sizes', false)
                                ->pluck('product_id')
                                ->unique()
                                ->toArray();
                            $sizesRequired = $invoice->items
                                ->where('have_sizes', true)
                                ->pluck('product_id')
                                ->unique()
                                ->toArray();

                            $products = \App\Models\BasicDataApp\Product::with('units.unit')
                                ->whereIn('id', $productsRequired)
                                ->get()
                                ->keyBy('id');
                            $sizes = \App\Models\BasicDataApp\ProductSize::with('product.units.unit')
                                ->whereIn('id', $sizesRequired)
                                ->get()
                                ->keyBy('id');

                            foreach ($invoice->items as $item) {
                                $isSize = $item->have_sizes;
                                $itemModel = $isSize
                                    ? $sizes->get($item->product_id)
                                    : $products->get($item->product_id);

                                if ($itemModel) {
                                    $item->formatted_units = formatUnitsForInvoice($itemModel, $isSize);
                                } else {
                                    $item->formatted_units = [];
                                }
                                $itemsToRender[] = $item;
                            }
                        }
                    @endphp

                    @if (count($itemsToRender) > 0)
                        @foreach ($itemsToRender as $index => $item)
                            @include('invoices::components.item_row', [
                                'index' => $index,
                                'item' => $item,
                                'langPrefix' => $langPrefix,
                            ])
                        @endforeach
                    @else
                        <tr id="empty_row">
                            <td colspan="10" class="text-center p-5 text-muted">
                                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                                {{ __($langPrefix . '.ui.empty_table_hint') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light-soft text-center row">
            <div class="col-md-6">

            </div>
            <div class="col-md-6">
                <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
                    <div class="card-header py-3 px-4 bg-transparent border-bottom">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-calculator text-primary"></i>
                            {{ __($langPrefix . '.ui.summary') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">{{ __($langPrefix . '.ui.total_before_vat_discount') }}:</span>
                            <span class="fw-bold"><span id="lbl_total_exclusive">0.00</span> </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-danger">{{ __($langPrefix . '.ui.total_discounts') }}:</span>
                            <span class="text-danger fw-bold"><span id="lbl_total_discount">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">{{ __($langPrefix . '.ui.total_vat_label') }}:</span>
                            <span class="fw-bold"><span id="lbl_total_vat">0.00</span></span>
                        </div>
                        <div id="summary_shipping_section" style="display: none;">
                            <div class="d-flex justify-content-between mb-2 fs-6">
                                <span class="text-muted">{{ __($langPrefix . '.fields.shipping_cost') }}:</span>
                                <span class="fw-bold"><span id="lbl_shipping_cost">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-6">
                                <span class="text-muted">{{ __($langPrefix . '.fields.shipping_vat_amount') }}:</span>
                                <span class="fw-bold"><span id="lbl_summary_shipping_vat">0.00</span></span>
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between mt-3 pt-3 border-top border-primary border-2 text-primary fs-5 fw-bold">
                            <span>{{ __($langPrefix . '.ui.final_total') }}:</span>
                            <span><span id="lbl_total_inclusive_display">0.00</span> <small
                                     class="fs-7 text-gray-500"></small></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
