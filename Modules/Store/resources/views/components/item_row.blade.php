@php
    $selectedUnitQty = 0;
    if (isset($item->formatted_units) && count($item->formatted_units) > 0) {
        foreach ($item->formatted_units as $u) {
            if (($u->id ?? null) == ($item->unit_id ?? null)) {
                $selectedUnitQty = $u->available_quantity ?? ($u->quantity ?? 0);
                break;
            }
        }
        if ($selectedUnitQty == 0 && count($item->formatted_units) > 0 && !isset($item->unit_id)) {
            $selectedUnitQty = $item->formatted_units[0]->available_quantity ?? ($item->formatted_units[0]->quantity ?? 0);
        }
    } else {
        $selectedUnitQty = $item->available_quantity ?? ($item->system_quantity ?? ($item->book_quantity ?? 0));
    }
@endphp
<tr class="item-row text-center">
    <td class="pe-3 text-start">
        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id ?? '' }}">
        <input type="hidden" name="items[{{ $index }}][have_sizes]" value="{{ $item->have_sizes ?? 0 }}">
        <input type="hidden" name="items[{{ $index }}][status]" value="{{ $item->status ?? '' }}">
        <input type="hidden" name="items[{{ $index }}][product_units]"
            value="{{ json_encode($item->formatted_units ?? []) }}">
        <input type="text" name="items[{{ $index }}][product_name]" class="form-control bg-light text-end"
            value="{{ ($item->product->name ?? null) ?: ($item->product_name ?? '---') }}" readonly style="text-align: right;">
    </td>
    <td>
        @if(isset($isTransferIn) && $isTransferIn)
            <select class="form-select item-unit bg-light" disabled tabindex="-1">
                @if (isset($item->formatted_units) && count($item->formatted_units) > 0)
                    @foreach ($item->formatted_units as $unit)
                        <option value="{{ $unit->id }}" {{ ($item->unit_id ?? null) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                @else
                    <option selected>{{ $item->unit_name ?? __('store::ui.basic_unit') }}</option>
                @endif
            </select>
            <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id ?? '' }}">
        @else
            <select name="items[{{ $index }}][unit_id]" class="form-select item-unit"
                onchange="updateRowPrice(this)">
                @if (isset($item->formatted_units) && count($item->formatted_units) > 0)
                    @foreach ($item->formatted_units as $unit)
                        <option value="{{ $unit->id }}" data-price="{{ number_format($unit->cost_price, 2, '.', '') }}"
                            data-qty="{{ number_format($unit->available_quantity ?? ($unit->quantity ?? 0), 2, '.', '') }}"
                            {{ ($item->unit_id ?? null) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                @else
                    <option value="{{ $item->unit_id ?? '' }}"
                        data-price="{{ number_format($item->unit_cost ?? 0, 2, '.', '') }}"
                        data-qty="{{ number_format($item->available_quantity ?? ($item->system_quantity ?? ($item->book_quantity ?? 0)), 2, '.', '') }}"
                        selected>
                        {{ $item->unit_name ?? __('store::ui.basic_unit') }}
                    </option>
                @endif
            </select>
        @endif
    </td>

    @if ($isSettlement)
        <td>
            <input type="text" name="items[{{ $index }}][system_quantity]"
                class="form-control item-sys-qty text-start bg-light"
                value="{{ number_format($selectedUnitQty, 2, '.', '') }}" readonly>
        </td>
        <td>
            <input type="number" name="items[{{ $index }}][actual_quantity]"
                class="form-control item-act-qty text-start"
                value="{{ number_format($item->actual_quantity ?? 0, 2, '.', '') }}" min="0" step="any"
                oninput="calcTotals()">
        </td>
        <td>
            <input type="text" name="items[{{ $index }}][variance_quantity]"
                class="form-control item-var-qty text-start bg-light fw-bold"
                value="{{ number_format($item->variance_quantity ?? 0, 2, '.', '') }}" readonly>
        </td>
        <td>
            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control item-cost text-start"
                value="{{ number_format($item->unit_cost ?? 0, 2, '.', '') }}" min="0" step="0.01"
                oninput="calcTotals()">
        </td>
        <td>
            <input type="text" name="items[{{ $index }}][total_cost]"
                class="form-control item-total fw-bold text-primary text-start bg-light"
                value="{{ number_format($item->total_cost ?? 0, 2, '.', '') }}" readonly>
        </td>
        <td class="text-center">
            <button type="button"
                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-0 mx-auto"
                style="width: 32px; height: 32px;" onclick="removeItemRow(this)"><i class="bi bi-trash"></i></button>
        </td>
    @elseif(isset($isTransferIn) && $isTransferIn)
        {{-- الكمية المرسلة (قراءة فقط) --}}
        <td>
            <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item->quantity ?? 0 }}">
            <input type="text" name="items[{{ $index }}][sent_quantity]"
                class="form-control item-sent-qty text-start bg-light"
                value="{{ number_format($item->quantity ?? 0, 2, '.', '') }}" readonly
                tabindex="-1">
        </td>
        @php
            $sentQty = $item->quantity ?? 0;
            $docStatus = $document->status ?? 0;
            $previouslyReceived = ($docStatus == 6) ? ($item->received_quantity ?? 0) : 0;
            $remainingQty = max(0, $sentQty - $previouslyReceived);
            $defaultCurrentRecv = ($docStatus == 3) ? ($item->received_quantity ?? 0) : $remainingQty;
        @endphp
        {{-- الكمية المستلمة سابقاً --}}
        <td>
            <input type="text" name="items[{{ $index }}][previously_received]"
                class="form-control item-prev-recv text-start bg-light"
                value="{{ number_format($previouslyReceived, 2, '.', '') }}" readonly tabindex="-1">
        </td>
        {{-- الكمية المستلمة الآن --}}
        @if(isset($isReturnMode) && $isReturnMode)
            <input type="hidden" name="items[{{ $index }}][current_received]" class="item-qty item-recv-qty" value="0">
            <input type="hidden" name="items[{{ $index }}][received_quantity]" class="item-total-recv" value="{{ $previouslyReceived }}">
        @else
            <td>
                <input type="number" name="items[{{ $index }}][current_received]"
                    class="form-control item-qty item-recv-qty text-start"
                    value="{{ number_format($defaultCurrentRecv, 2, '.', '') }}"
                    min="0" max="{{ $sentQty - $previouslyReceived }}" step="any" 
                    oninput="calcTransferVariance(this); calcTotals()">
                <input type="hidden" name="items[{{ $index }}][received_quantity]" class="item-total-recv" value="{{ $previouslyReceived + $defaultCurrentRecv }}">
            </td>
        @endif

        {{-- الكمية المرتجعة --}}
        @if(isset($isReturnMode) && $isReturnMode)
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light-danger border-danger"><i class="bi bi-arrow-return-left text-danger"></i></span>
                    <input type="number" name="items[{{ $index }}][returned_quantity]"
                        class="form-control item-ret-qty border-danger text-start fw-bold"
                        value="{{ number_format($item->returned_quantity ?? 0, 2, '.', '') }}" 
                        max="{{ $sentQty - $previouslyReceived }}" min="0" step="any"
                        oninput="validateReturnQty(this); calcTotals()">
                </div>
            </td>
        @else
            <input type="hidden" name="items[{{ $index }}][returned_quantity]" class="item-ret-qty" value="{{ $item->returned_quantity ?? 0 }}">
        @endif
        {{-- الفارق --}}
        <td>
            <input type="text" name="items[{{ $index }}][variance_quantity]"
                class="form-control item-var-qty text-start fw-bold {{ ($item->variance_quantity ?? 0) < 0 ? 'text-danger' : (($item->variance_quantity ?? 0) > 0 ? 'text-success' : '') }}"
                value="{{ number_format($item->variance_quantity ?? 0, 2, '.', '') }}" readonly tabindex="-1">
        </td>

        {{-- التكلفة --}}
        <td>
            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control item-cost text-start bg-light"
                value="{{ number_format($item->unit_cost ?? 0, 2, '.', '') }}" readonly tabindex="-1">
        </td>
        {{-- الإجمالي --}}
        <td>
            <input type="text" name="items[{{ $index }}][total_cost]"
                class="form-control item-total fw-bold text-primary text-start bg-light"
                value="{{ number_format($item->total_cost ?? 0, 2, '.', '') }}" readonly tabindex="-1">
        </td>
        {{-- ملاحظة الصنف --}}
        <td>
            <input type="text" name="items[{{ $index }}][notes]" class="form-control"
                value="{{ $item->notes ?? '' }}" placeholder="{{ __('store::models/st_direct_transfers.fields.notes') }}...">
        </td>
        <input type="hidden" name="items[{{ $index }}][book_quantity]" value="{{ $selectedUnitQty }}">
    @else
        @if ($showBookQuantity)
            <td>
                <input type="number" name="items[{{ $index }}][system_quantity]"
                    class="form-control item-sys-qty text-start bg-light"
                    value="{{ number_format($selectedUnitQty, 2, '.', '') }}" readonly>
            </td>
        @endif
        <td>
            <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty text-start"
                value="{{ number_format((isset($item->quantity) && (float)$item->quantity > 0) ? $item->quantity : 1, 2, '.', '') }}" min="0.01" step="any"
                required oninput="calcTotals()">
        </td>
        <td>
            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control item-cost text-start"
                value="{{ number_format($item->unit_cost ?? 0, 2, '.', '') }}" min="0" step="0.01"
                required oninput="calcTotals()">
        </td>
        <td>
            <input type="text" name="items[{{ $index }}][total_cost]"
                class="form-control item-total fw-bold text-primary text-start bg-light"
                value="{{ number_format($item->total_cost ?? 0, 2, '.', '') }}" readonly>
        </td>
        <td class="text-center">
            <button type="button"
                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-0 mx-auto"
                style="width: 32px; height: 32px;" onclick="removeItemRow(this)"><i class="bi bi-trash"></i></button>
        </td>
    @endif
</tr>
