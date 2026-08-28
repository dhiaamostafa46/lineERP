<div class="row mb-3 align-items-center">
    <div class="col-4 col-sm-3 text-gray-700 fw-semibold">
        <p class="fs-6 mb-0">{{ $label }}</p>
    </div>

    <div class="col-8 col-sm-9">
        @if ($slot->isNotEmpty())
            {{-- لو تم تمرير محتوى داخل المكون --}}
            <div class="form-control bg-light">{{ $slot }}</div>
        @else
            <span class="form-control bg-light text-gray-800">{{ $value ?? '—' }}</span>
        @endif
    </div>
</div>
