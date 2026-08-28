@props(['column', 'title'])

@php
    $currentSort = request('sort_by');
    $currentDir = request('sort_dir', 'desc');
    $isActive = ($currentSort === $column);
    $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    
    $params = array_merge(request()->query(), [
        'sort_by' => $column,
        'sort_dir' => $nextDir
    ]);
    
    $url = request()->url() . '?' . http_build_query($params);
    
    $iconClass = 'fa-solid fa-sort text-muted opacity-40 ms-1';
    if ($isActive) {
        $iconClass = $currentDir === 'asc' ? 'fa-solid fa-sort-up text-primary ms-1' : 'fa-solid fa-sort-down text-primary ms-1';
    }
@endphp

<a href="{{ $url }}" class="text-muted text-hover-primary d-inline-flex align-items-center text-decoration-none user-select-none" wire:navigate>
    <span>{{ $title }}</span>
    <i class="{{ $iconClass }}" style="font-size: 10px;"></i>
</a>
