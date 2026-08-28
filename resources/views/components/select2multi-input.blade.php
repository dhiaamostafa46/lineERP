{{-- Component file: resources/views/components/select2multi-input.blade.php --}}
<select class="js-example-basic-multiple form-select"
        data-control="select2"
        data-placeholder="{{ $placeholder ?? 'Select options' }}"
        name="{{ $name ?? '' }}[]"
        data-allow-clear="true"
        id="{{ $id ?? '' }}"
        multiple="multiple">
    <option></option>
    @forelse ($list as $item_id => $item_name)
    <option value="{{ $item_id }}"
            @if(is_array($selectedId ?? null) && in_array($item_id, $selectedId)) selected
            @elseif(!is_array($selectedId ?? null) && $item_id == $selectedId) selected
            @endif>
        {{ $item_name }}
    </option>
    @empty
    <option disabled>No options available</option>
    @endforelse
</select>
