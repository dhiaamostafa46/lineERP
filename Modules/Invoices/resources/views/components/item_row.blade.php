@php
    $langPrefix = $langPrefix ?? 'invoices::models/purchase_invoices';
    $isSale = $isSale ?? str_contains($langPrefix, 'sales');
    $isLinkedReturn = isset($invoice->parent_id) || isset($selectedParentId) || (!empty($item->parent_id));
    
    // Check for validation errors on this item row
    $hasItemError = $errors->has("items.{$index}.quantity") || 
                    $errors->has("items.{$index}.serial") || 
                    $errors->has("items.{$index}.product_id");
    $itemErrorClass = $hasItemError ? 'table-danger border-start border-5 border-danger' : '';
@endphp
<tr class="item-row text-center align-middle {{ $itemErrorClass }}" style="{{ $hasItemError ? 'background-color: rgba(220, 53, 69, 0.05);' : '' }}">
    <td>
        <span class="fw-bold text-muted item-number">{{ $index + 1 }}</span>
        @if($hasItemError)
            <div class="badge bg-danger mt-1" style="font-size: 10px;">
                <i class="bi bi-exclamation-circle"></i> Error
            </div>
        @endif
    </td>
    <td class="pe-3 text-start">
        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id ?? '' }}">
        <input type="hidden" name="items[{{ $index }}][have_sizes]" value="{{ $item->have_sizes ?? 0 }}">
        @if($isLinkedReturn)
            <input type="hidden" name="items[{{ $index }}][serial]" value="{{ $item->serial ?? '' }}" class="item-serial-input">
            @if(! empty($item->id))
                <input type="hidden" name="items[{{ $index }}][parent_item_id]" value="{{ $item->id }}">
            @endif
            @if($errors->has("items.{$index}.serial"))
                <div class="alert alert-danger mt-2 fs-8 py-1 px-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first("items.{$index}.serial") }}
                </div>
            @endif
        @endif
        <div>
            <input type="text" name="items[{{ $index }}][product_name]" class="form-control form-control-sm fs-7 bg-light-soft" value="{{ $item->product_name ?? '' }}" readonly style="text-align: right;">
            @if($errors->has("items.{$index}.product_id"))
                <small class="text-danger fw-bold mt-1 d-block">{{ $errors->first("items.{$index}.product_id") }}</small>
            @endif
        </div>
    </td>
    <td>
        <input type="text" name="items[{{ $index }}][description]" class="form-control form-control-sm fs-7" value="{{ $item->description ?? '' }}" placeholder="{{ __($langPrefix . '.fields.description') }}">
    </td>
    <td>
        @if($isLinkedReturn)
            <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id ?? '' }}">
        @endif

        <select name="{{ $isLinkedReturn ? '' : "items[$index][unit_id]" }}" 
                class="form-select form-select-sm fs-7 item-unit-select {{ $isLinkedReturn ? 'bg-light' : '' }} @if($errors->has("items.{$index}.unit") || $errors->has("items.{$index}.unit_id")) is-invalid @endif" 
                onchange="updateRowPrice(this)"
                {{ $isLinkedReturn ? 'disabled' : '' }}>
            @if(isset($item->formatted_units) && count($item->formatted_units) > 0)
                @foreach($item->formatted_units as $u)
                    <option value="{{ $u->id }}" 
                            data-price="{{ $isSale ? ($u->sale_price ?? 0) : ($u->cost_price ?? 0) }}" 
                            {{ (isset($item->unit_id) && $item->unit_id == $u->id) ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            @elseif(isset($item->product) && $item->product->units && count($item->product->units) > 0)
                @foreach($item->product->units as $u)
                    <option value="{{ $u->id }}" 
                            data-price="{{ $isSale ? ($u->prod_price ?? 0) : ($u->cost_price ?? 0) }}" 
                            {{ (isset($item->unit_id) && $item->unit_id == $u->id) ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            @else
                <option value="{{ $item->unit_id ?? '' }}" selected>----</option>
            @endif
        </select>
        @if($errors->has("items.{$index}.unit") || $errors->has("items.{$index}.unit_id"))
            <div class="invalid-feedback d-block text-danger mt-1 fs-8">
                {{ $errors->first("items.{$index}.unit") ?: $errors->first("items.{$index}.unit_id") }}
            </div>
        @endif
    </td>

    <td>
        <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm fs-7 item-qty text-center" value="{{ number_format($item->quantity ?? 1, 2, '.', '') }}" min="1" step="any" oninput="calcTotals()">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control form-control-sm fs-7 item-price text-center" value="{{ number_format($item->unit_price ?? 0, 2, '.', '') }}" min="0" step="0.01" oninput="calcTotals()">
    </td>
    <td>
        <div class="input-group input-group-sm">
            <input type="number" name="items[{{ $index }}][number_discount]" class="form-control fs-8 item-discount text-center" value="{{ number_format($item->number_discount ?? 0, 2, '.', '') }}" step="0.01" oninput="calcTotals()">
            <select name="items[{{ $index }}][type_discount]" class="form-select fs-8 item-discount-type" style="max-width: 60px" onchange="calcTotals()">
                <option value="1" {{ ($item->type_discount ?? 1) == 1 ? 'selected' : '' }}>%</option>
                <option value="2" {{ ($item->type_discount ?? 1) == 2 ? 'selected' : '' }}>{{ __($langPrefix . '.fields.currency_symbol') }}</option>
            </select>
        </div>
        <input type="hidden" name="items[{{ $index }}][total_discount]" class="item-discount-hidden" value="{{ $item->total_discount ?? 0 }}">
    </td>
    <td>
        <select name="items[{{ $index }}][tax_id]" class="form-select form-select-sm fs-8 item-vat-rate text-center" onchange="calcTotals()">
            <option value="">-----</option>
            @php $matchedOldRate = false; @endphp
            @foreach($taxes ?? [] as $tax_id => $name)
                @php 
                    $taxRate = isset($taxes_data[$tax_id]) ? $taxes_data[$tax_id]['rate'] : 0;
                    $isSelected = ($item->tax_id ?? null) == $tax_id;
                    if (!$isSelected && empty($item->tax_id) && isset($item->vat_rate) && $item->vat_rate == $taxRate && !$matchedOldRate) {
                        $isSelected = true;
                        $matchedOldRate = true;
                    }
                @endphp
                <option value="{{ $tax_id }}" data-rate="{{ $taxRate }}" {{ $isSelected ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="items[{{ $index }}][vat_rate]" class="item-vat-rate-hidden" value="{{ $item->vat_rate ?? 0 }}">
        <input type="hidden" name="items[{{ $index }}][vat_amount]" class="item-vat-amount" value="{{ $item->vat_amount ?? 0 }}">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][subtotal_with_vat]" class="form-control form-control-sm fs-7 item-subtotal item-subtotal-display fw-bold text-primary text-center bg-light" value="{{ number_format($item->subtotal_with_vat ?? 0, 2, '.', '') }}" readonly>
    </td>
    <td>
        <div class="d-flex justify-content-center gap-1">
            <button type="button" class="btn btn-icon btn-sm btn-light-primary border-0 h-30px w-30px" onclick="copyItemRow(this)" title="{{ __('نسخ المنتج') }}">
                <i class="ki-duotone ki-copy fs-5"><span class="path1"></span><span class="path2"></span></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-light-danger border-0 h-30px w-30px" onclick="removeItemRow(this)" title="{{ __('حذف') }}">
                <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
            </button>
        </div>
    </td>
</tr>
