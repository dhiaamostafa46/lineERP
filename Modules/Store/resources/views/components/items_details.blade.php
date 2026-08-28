@php
    $langPrefix = $langPrefix ?? 'store::models/st_opening_balances';
    $document = $document ?? (isset($openingBalance) ? $openingBalance : null);
    $showBookQuantity = $showBookQuantity ?? false;
    $isSettlement = $isSettlement ?? false;
    $isTransferIn = $isTransferIn ?? false;
    $currentStoreId = old('from_store_id', old('store_id', $document->from_store_id ?? ($document->store_id ?? ($store_id ?? null))));
@endphp

<style>
    #items_table input.form-control {
        min-width: 85px;
        padding-left: 5px;
        padding-right: 5px;
        font-size: 0.9rem;
    }
    #items_table th, #items_table td {
        vertical-align: middle;
        padding: 8px 4px !important;
    }
    /* Hide arrows from number inputs */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<!-- Card 2: Items Table -->
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-header py-3 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-box-seam text-primary"></i>
            {{ __('store::ui.items_details') ?? 'تفاصيل الأصناف' }}
        </h5>
        @if(!$isTransferIn)
        <div class="d-flex gap-2" style="width: 400px">
            <select id="product_search" class="form-select select2-products">
                <option value=""></option>
            </select>
        </div>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="items_table">
                <thead>
                    <tr class="text-center table-light align-middle text-secondary fw-semibold">
                        <th width="20%" class="text-start">{{ __('store::ui.product') }}</th>
                        <th width="12%">{{ __('store::ui.unit') }}</th>
                        @if ($isSettlement)
                            <th width="10%">{{ __('store::ui.book_quantity') }}</th>
                            <th width="10%">{{ __('store::ui.actual_quantity') }}</th>
                            <th width="10%">{{ __('store::ui.variance') }}</th>
                            <th width="10%">{{ __('store::ui.cost') }}</th>
                            <th width="10%">{{ __('store::ui.total') }}</th>
                        @elseif($isTransferIn)
                            <th width="8%">{{ __('store::ui.sent_quantity') }}</th>
                            <th width="8%">{{ __('store::ui.previously_received') }}</th>
                            @if(isset($isReturnMode) && $isReturnMode)
                                <th width="12%" class="text-danger">{{ __('store::ui.returned') ?? 'المرتجع' }}</th>
                            @else
                                <th width="8%">{{ __('store::ui.current_received') }}</th>
                            @endif
                            <th width="8%">{{ __('store::ui.variance') }}</th>
                            <th width="10%">{{ __('store::ui.cost') }}</th>
                            <th width="10%">{{ __('store::ui.total') }}</th>
                            <th width="18%">{{ __('store::models/st_direct_transfers.fields.notes') ?? 'ملاحظة' }}</th>
                        @else
                            @if ($showBookQuantity)
                                <th width="12%">{{ __('store::ui.book_quantity') }}</th>
                            @endif
                            <th width="12%">{{ __('store::ui.quantity') }}</th>
                            <th width="12%">{{ __('store::ui.cost') }}</th>
                            <th width="12%">{{ __('store::ui.total') }}</th>
                        @endif
                        <th width="4%"></th>
                    </tr>
                </thead>
                <tbody id="items_body">
                    <!-- Dynamic Rows Here -->
                    @php
                        $oldItems = old('items');
                        $itemsToRender = [];

                        if (!function_exists('formatUnitsForStore')) {
                            function formatUnitsForStore($itemModel, $isSize = false)
                            {
                                $units = [];
                                $costPrice = $itemModel->cost_price ?? 0;
                                $productUnits = $isSize ? $itemModel->product->units ?? [] : $itemModel->units ?? [];

                                foreach ($productUnits as $pUnit) {
                                    $factor = $pUnit->conversion_factor ?: 1;
                                    $name = '---';
                                    if ($pUnit->unit) {
                                        $name = $pUnit->unit->name ?? '---';
                                    }

                                    // Use pUnit->id (ProductUnit primary key) to match the model relationship
                                    // and what gets stored in the unit_id column.
                                    $units[] = (object) [
                                        'id' => $pUnit->id,
                                        'name' => $name,
                                        'conversion_factor' => $factor,
                                        'cost_price' => round(($itemModel->average_cost ?? $costPrice) * $factor, 2),
                                        'sale_price' => round(($itemModel->sale_price ?? 0) * $factor, 2),
                                        'available_quantity' => round(($itemModel->available_quantity ?? ($itemModel->quantity ?? 0)) / ($factor ?: 1), 2),
                                        'barcode' => $pUnit->barcode ?? null,
                                    ];
                                }
                                return $units;
                            }
                        }

                        if ($oldItems && is_array($oldItems)) {
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

                            $pStocks = collect();
                            $sStocks = collect();
                            if ($currentStoreId) {
                                $pStocks = \App\Models\StoreApp\Stock::where('store_id', $currentStoreId)
                                    ->whereIn('product_id', $productIds)
                                    ->where('is_size', false)
                                    ->get()
                                    ->keyBy('product_id');
                                $sStocks = \App\Models\StoreApp\Stock::where('store_id', $currentStoreId)
                                    ->whereIn('product_id', $sizeIds)
                                    ->where('is_size', true)
                                    ->get()
                                    ->keyBy('product_id');
                            }

                            foreach ($oldItems as $idx => $oldItem) {
                                $obj = (object) $oldItem;
                                $isSize = !empty($obj->have_sizes);
                                $itemModel = $isSize ? $sizes->get($obj->product_id) : $products->get($obj->product_id);

                                if ($itemModel) {
                                    $stock = $isSize ? $sStocks->get($itemModel->id) : $pStocks->get($itemModel->id);
                                    if ($stock) {
                                        $itemModel->available_quantity = $stock->current_quantity;
                                        $itemModel->average_cost = $stock->average_cost;
                                    } else {
                                        $itemModel->available_quantity = 0;
                                    }
                                    $obj->formatted_units = formatUnitsForStore($itemModel, $isSize);

                                    if ($isSize) {
                                        $obj->productSize = $itemModel;
                                        $obj->product = $itemModel->product ?? null;
                                    } else {
                                        $obj->product = $itemModel;
                                    }

                                    $selectedUnitId = $obj->unit_id ?? null;
                                    $selectedUnitObj = null;
                                    if ($selectedUnitId && count($obj->formatted_units) > 0) {
                                        foreach ($obj->formatted_units as $u) {
                                            if (($u->id ?? null) == $selectedUnitId) {
                                                $selectedUnitObj = $u;
                                                break;
                                            }
                                        }
                                    }
                                    if (!$selectedUnitObj && count($obj->formatted_units) > 0) {
                                        $selectedUnitObj = $obj->formatted_units[0];
                                        $obj->unit_id = $selectedUnitObj->id;
                                    }

                                    if ($selectedUnitObj) {
                                        $obj->available_quantity = $selectedUnitObj->available_quantity;
                                        $obj->system_quantity = $selectedUnitObj->available_quantity;
                                        $obj->book_quantity = $selectedUnitObj->available_quantity;
                                    } else {
                                        $obj->available_quantity = 0;
                                        $obj->system_quantity = 0;
                                        $obj->book_quantity = 0;
                                    }
                                } else {
                                    $obj->formatted_units = [];
                                    $obj->available_quantity = 0;
                                    $obj->system_quantity = 0;
                                    $obj->book_quantity = 0;
                                }
                                $itemsToRender[] = $obj;
                            }
                        } elseif (isset($document) && $document->items && count($document->items) > 0) {
                            $productsRequired = [];
                            $sizesRequired = [];

                            foreach ($document->items as $item) {
                                if ($item->have_sizes) {
                                    $sizesRequired[] = $item->product_id;
                                } else {
                                    $productsRequired[] = $item->product_id;
                                }
                            }

                            $pStocks = collect();
                            $sStocks = collect();
                            if ($currentStoreId) {
                                if (!empty($productsRequired)) {
                                    $pStocks = \App\Models\StoreApp\Stock::where('store_id', $currentStoreId)
                                        ->whereIn('product_id', $productsRequired)
                                        ->where('is_size', false)
                                        ->get()
                                        ->keyBy('product_id');
                                }
                                if (!empty($sizesRequired)) {
                                    $sStocks = \App\Models\StoreApp\Stock::where('store_id', $currentStoreId)
                                        ->whereIn('product_id', $sizesRequired)
                                        ->where('is_size', true)
                                        ->get()
                                        ->keyBy('product_id');
                                }
                            }

                            foreach ($document->items as $item) {
                                $isSize = $item->have_sizes;
                                $itemModel = $isSize ? $item->productSize : $item->product;

                                if ($itemModel) {
                                    $stock = $isSize ? $sStocks->get($itemModel->id) : $pStocks->get($itemModel->id);
                                    if ($stock) {
                                        $itemModel->available_quantity = $stock->current_quantity;
                                        $itemModel->average_cost = $stock->average_cost;
                                    }
                                    // Try to use the stored unit JSON snapshot from the database if it exists
                                    $storedUnits = null;
                                    if (!empty($item->unit)) {
                                        $decoded = is_string($item->unit) ? json_decode($item->unit) : $item->unit;
                                        if (is_array($decoded) && count($decoded) > 0) {
                                            $storedUnits = $decoded;
                                        }
                                    }

                                    if ($storedUnits) {
                                        $item->formatted_units = array_map(function($u) {
                                            $uObj = (object)$u;
                                            if (!isset($uObj->name) && isset($uObj->unit)) {
                                                $uObj->name = $uObj->unit->name ?? '---';
                                            }
                                            // Ensure cost_price exists for the blade view
                                            if (!isset($uObj->cost_price)) {
                                                $uObj->cost_price = 0;
                                            }
                                            return $uObj;
                                        }, (array)$storedUnits);
                                    } else {
                                        $item->formatted_units = formatUnitsForStore($itemModel, $isSize);
                                    }
                                } else {
                                    $item->formatted_units = [];
                                }
                                $itemsToRender[] = $item;
                            }
                        }
                    @endphp



                    @if (count($itemsToRender) > 0)
                        @foreach ($itemsToRender as $index => $item)
                            @include('store::components.item_row', [
                                'index' => $index,
                                'item' => $item,
                                'isSettlement' => $isSettlement,
                                'showBookQuantity' => $showBookQuantity,
                                'isTransferIn' => $isTransferIn,
                                'isReturnMode' => $isReturnMode ?? false,
                            ])
                        @endforeach
                    @else
                        <tr id="empty_row">
                            <td colspan="{{ $isSettlement ? 8 : ($isTransferIn ? (isset($isReturnMode) && $isReturnMode ? 11 : 10) : 7) }}" class="text-center p-5 text-muted">
                                <i class="bi bi-cart-plus fs-2 mb-2 d-block"></i>
                                {{ __('store::ui.empty_table_hint') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light-soft text-center row">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <div class="card border-0 rounded-3 shadow-sm mb-0 bg-white">
                    <div class="card-header py-2 px-4 bg-transparent border-bottom">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-calculator text-primary"></i>
                            {{ __('store::ui.summary') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">{{ __('store::ui.total_items') }}:</span>
                            <span class="fw-bold"><span id="lbl_total_items">0</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-6">
                            <span class="text-muted">{{ __('store::ui.total_quantity') }}:</span>
                            <span class="fw-bold"><span id="lbl_total_quantity">0.00</span></span>
                        </div>
                        <div
                            class="d-flex justify-content-between mt-3 pt-3 border-top border-primary border-2 text-primary fs-5 fw-bold">
                            <span>{{ __('store::ui.total_cost') }}:</span>
                            <span><span id="lbl_total_cost">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="total_items" id="total_items_input" value="0">
            <input type="hidden" name="total_quantity" id="total_quantity_input" value="0">
            <input type="hidden" name="total_value" id="total_value_input" value="0">
        </div>
    </div>
</div>

@push('scripts')
    @include('store::components.store_scripts', [
        'getProductUrl' => route('Lookup.getproducts'),
        'isSettlement' => $isSettlement,
        'isTransferIn' => $isTransferIn,
        'isReturnMode' => $isReturnMode ?? false,
        'showBookQuantity' => $showBookQuantity,
        'searchType' => $searchType ?? ( ($showBookQuantity && !$isSettlement) ? 'location' : 'products' ),
    ])
@endpush
