@props([
    'name' => 'account_id',
    'placeholder' => 'اختر حساب',
    'selected_id' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'class' => '',
])

@php
    $current_id = old($name, $selected_id);
    $uniqueId = 'select2_' . uniqid();
@endphp

<select
    id="{{ $uniqueId }}"
    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
    class="form-control select2-account {{ $class }}"
    data-placeholder="{{ $placeholder }}"
    @if($current_id) data-selected="{{ $current_id }}" @endif
    @if ($required) required @endif
    @if ($disabled) disabled @endif
    @if ($multiple) multiple @endif
>
    {{-- ✅ إضافة option فارغ للـ placeholder --}}
    @if(!$required && !$multiple)
        <option value=""></option>
    @endif
</select>
