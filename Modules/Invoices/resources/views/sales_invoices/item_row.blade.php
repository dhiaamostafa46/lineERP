<tr class="item-row text-center">
    <td class="pe-3 text-start">
        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
        <input type="text" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item->product_name }}" readonly style="text-align: right;">
    </td>
    <td>
        <select name="items[{{ $index }}][unit]" class="form-select @error("items.{$index}.unit") is-invalid @enderror" onchange="updateRowPrice(this)">
            @if($item->product && $item->product->units && count($item->product->units) > 0)
                @foreach($item->product->units as $u)
                    <option value="{{ $u->name }}" data-price="{{ $u->cost_price }}" {{ (isset($item->unit) && $item->unit == $u->name) ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            @else
                <option value="{{ $item->unit ?? __('invoices::models/sales_invoices.ui.piece') }}" selected>
                    {{ $item->unit ?? __('invoices::models/sales_invoices.ui.piece') }}
                </option>
            @endif
        </select>
        @error("items.{$index}.unit")
            <div class="invalid-feedback d-block text-danger mt-1 fs-8">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty text-center" value="{{ $item->quantity }}" min="1" step="any" oninput="calcTotals()">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price text-center" value="{{ $item->unit_price }}" min="0" step="0.01" oninput="calcTotals()">
    </td>
    <td>
        <div class="input-group">
            <input type="number" name="items[{{ $index }}][discount_value]" class="form-control item-discount text-center" value="{{ $item->discount_type == 'percent' ? (($item->quantity * $item->unit_price) > 0 ? ($item->discount_amount / ($item->quantity * $item->unit_price)) * 100 : 0) : $item->discount_amount }}" step="0.01" oninput="calcTotals()">
            <select name="items[{{ $index }}][discount_type]" class="form-select item-discount-type" style="max-width: 70px" onchange="calcTotals()">
                <option value="fixed" {{ ($item->discount_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>{{ __('invoices::models/sales_invoices.fields.currency_symbol') }}</option>
                <option value="percent" {{ ($item->discount_type ?? '') == 'percent' ? 'selected' : '' }}>%</option>
            </select>
        </div>
        <input type="hidden" name="items[{{ $index }}][discount_amount]" class="item-discount-hidden" value="{{ $item->discount_amount }}">
    </td>
    <td>
        <select name="items[{{ $index }}][tax_id]" class="form-select item-vat-rate text-center" onchange="calcTotals()">
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
        <input type="hidden" name="items[{{ $index }}][vat_rate]" class="item-vat-rate-hidden" value="{{ $item->vat_rate }}">
        <input type="hidden" name="items[{{ $index }}][vat_amount]" class="item-vat-amount" value="{{ $item->vat_amount }}">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][subtotal_with_vat]" class="form-control item-subtotal item-subtotal-display fw-bold text-primary text-center" value="{{ $item->subtotal_with_vat }}" readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn-icon-danger" onclick="removeItemRow(this)"><i class="bi bi-x-lg"></i></button>
    </td>
</tr>
