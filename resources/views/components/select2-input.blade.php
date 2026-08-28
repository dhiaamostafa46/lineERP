<select class="form-select form-control {{ $class  ?? '' }}" data-control="select2" data-placeholder="{{ $placeholder ?? '' }}"
    name="{{ $name ?? '' }}" id="{{ $id ?? '' }}"
    @if(isset($name) && in_array(str_replace('[]', '', $name), ['branch_id', 'branchId']) && auth()->check() && !auth()->user()->can('global.viewBranches')) disabled @endif>
    <option></option>
    @forelse ($list as $item_id => $item_name)
        @php
            $isSelected = false;
            if (isset($name) && in_array(str_replace('[]', '', $name), ['branch_id', 'branchId']) && auth()->check() && !auth()->user()->can('global.viewBranches')) {
                if (auth()->user()->branch_id == $item_id) {
                    $isSelected = true;
                }
            } elseif (isset($attributes['selected_id']) && $attributes['selected_id'] != '' && $item_id == $attributes['selected_id']) {
                $isSelected = true;
            }
        @endphp
        <option value="{{ $item_id }}" @if($isSelected) selected @endif>
            {{ $item_name }}
        </option>
    @empty
    @endforelse
</select>

@if(isset($name) && in_array(str_replace('[]', '', $name), ['branch_id', 'branchId']) && auth()->check() && !auth()->user()->can('global.viewBranches'))
    <input type="hidden" name="{{ $name }}" value="{{ auth()->user()->branch_id }}">
@endif
