<div class="front-card-header d-flex align-items-center justify-content-between flex-wrap gap-3" x-data="bulkManager('{{ $resource ?? '' }}')">
    
    <!-- Search Input -->
    <div class="position-relative flex-grow-1" style="max-width: 320px;">
        {!! Form::open(['route' => $route, 'method' => 'GET', 'id' => 'frontSearchForm']) !!}
            @if(isset($hiddenInputs) && is_array($hiddenInputs))
                @foreach($hiddenInputs as $hName => $hValue)
                    <input type="hidden" name="{{ $hName }}" value="{{ $hValue }}">
                @endforeach
            @endif
            <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted fs-7"></i>
            <input type="text" 
                   name="name" 
                   value="{{ request('name') }}" 
                   class="form-control front-search-input" 
                   placeholder="{{ $placeholder ?? __('crud.search') }}..." 
                   onchange="document.getElementById('frontSearchForm').submit()" />
        {!! Form::close() !!}
    </div>

    <!-- Right Controls: Bulk Actions + Export + Filter (Icon Only) -->
    <div class="d-flex align-items-center gap-2">
        
        <!-- Bulk Actions Container (Shown dynamically when checkboxes are checked, right next to Filter) -->
        <div id="bulkActionsGroup" class="d-none align-items-center gap-2 p-1 bg-light rounded-3 border">
            <span class="badge bg-primary text-white fs-8 px-2 py-1 mx-1" id="bulkCount">0</span>
            
            <!-- Bulk Activate -->
            <button type="button" 
                    class="btn btn-sm btn-icon btn-light-success rounded-2" 
                    onclick="triggerBulkStatus(1, '{{ route(str_replace('.index', '.bulkStatus', $route)) }}')" 
                    title="@lang('basicdata::lang.active')" 
                    data-bs-toggle="tooltip">
                <i class="fa-solid fa-bolt fs-7"></i>
            </button>

            <!-- Bulk Deactivate -->
            <button type="button" 
                    class="btn btn-sm btn-icon btn-light-warning rounded-2" 
                    onclick="triggerBulkStatus(0, '{{ route(str_replace('.index', '.bulkStatus', $route)) }}')" 
                    title="@lang('basicdata::lang.inactive')" 
                    data-bs-toggle="tooltip">
                <i class="fa-solid fa-ban fs-7"></i>
            </button>

            <!-- Bulk Delete -->
            <button type="button" 
                    class="btn btn-sm btn-icon btn-light-danger rounded-2" 
                    onclick="triggerBulkDelete('{{ route(str_replace('.index', '.bulkDelete', $route)) }}')" 
                    title="@lang('crud.delete')" 
                    data-bs-toggle="tooltip">
                <i class="fa-solid fa-trash-can fs-7"></i>
            </button>
        </div>

        <!-- Filter Dropdown (Icon Only) -->
        <div class="dropdown">
            @php
                $activeFiltersCount = (request('name') ? 1 : 0) + (request('status') !== null && request('status') !== '' ? 1 : 0) + (request('category_id') ? 1 : 0);
            @endphp
            <button type="button" 
                    class="front-btn-filter dropdown-toggle p-2 {{ $activeFiltersCount > 0 ? 'show text-primary border-primary' : '' }}" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="Filter">
                <i class="fa-solid fa-sliders fs-7"></i>
                @if($activeFiltersCount > 0)
                    <span class="badge rounded-pill bg-primary text-white fs-9 px-1 ms-1">{{ $activeFiltersCount }}</span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end front-filter-dropdown shadow-lg">
                {!! Form::open(['route' => $route, 'method' => 'GET']) !!}
                    @if(isset($hiddenInputs) && is_array($hiddenInputs))
                        @foreach($hiddenInputs as $hName => $hValue)
                            <input type="hidden" name="{{ $hName }}" value="{{ $hValue }}">
                        @endforeach
                    @endif

                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <h5 class="front-filter-title">Filter {{ $title ?? '' }}</h5>
                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary p-0" onclick="this.closest('.dropdown-menu').classList.remove('show')">
                            <i class="fa-solid fa-xmark text-muted fs-6"></i>
                        </button>
                    </div>

                    @if(isset($statuses))
                        <div class="mb-3">
                            <span class="front-filter-section-title">@lang('basicdata::lang.status')</span>
                            <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="request('status')"></x-select2-input>
                        </div>
                    @endif

                    @if(isset($categoriesList))
                        <div class="mb-3">
                            <span class="front-filter-section-title">@lang('basicdata::models/db_products.fields.category_id')</span>
                            <x-select2-input name="category_id" :placeholder="__('hr::lang.select_category')" :list="$categoriesList" :selected_id="request('category_id')"></x-select2-input>
                        </div>
                    @endif

                    <div class="mb-4">
                        <span class="front-filter-section-title">PAGINATION</span>
                        {!! Form::select('pagination', config('statusSystem.pagination', [10 => 10, 25 => 25, 50 => 50, 100 => 100]), request('pagination') ?? null, ['class' => 'form-select form-select-sm fs-7']) !!}
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary front-btn-primary w-100 justify-content-center">Apply</button>
                        @if($activeFiltersCount > 0)
                            <a href="{{ route($route) }}" class="btn btn-light front-btn-filter" title="Reset"><i class="fa-solid fa-rotate-left fs-8"></i></a>
                        @endif
                    </div>
                {!! Form::close() !!}
            </div>
        </div>

        <!-- Export Dropdown (Icon Only) -->
        <div class="dropdown">
            <button type="button" class="front-btn-export dropdown-toggle p-2" data-bs-toggle="dropdown" aria-expanded="false" title="Export">
                <i class="fa-solid fa-arrow-down-to-bracket fs-7"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-2 rounded-2" style="font-size: 13px;">
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="window.print(); return false;"><i class="fa-solid fa-print text-muted fs-7"></i><span>@lang('crud.print')</span></a></li>
                @if(isset($excelRoute))
                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route($excelRoute) }}"><i class="fa-solid fa-file-excel text-success fs-7"></i><span>Excel</span></a></li>
                @endif
                @if(isset($pdfRoute))
                    <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route($pdfRoute) }}"><i class="fa-solid fa-file-pdf text-danger fs-7"></i><span>PDF</span></a></li>
                @endif
            </ul>
        </div>

    </div>
</div>

<script>
    function updateBulkUI() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        const bulkGroup = document.getElementById('bulkActionsGroup');
        const bulkCount = document.getElementById('bulkCount');
        
        if (bulkGroup && bulkCount) {
            if (count > 0) {
                bulkCount.textContent = count;
                bulkGroup.classList.remove('d-none');
                bulkGroup.classList.add('d-flex');
            } else {
                bulkGroup.classList.add('d-none');
                bulkGroup.classList.remove('d-flex');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('change', function(e) {
            if (e.target && (e.target.classList.contains('row-checkbox') || e.target.id === 'check-all')) {
                if (e.target.id === 'check-all') {
                    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
                }
                updateBulkUI();
            }
        });
    });

    function getSelectedIds() {
        const ids = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
            if (cb.value) ids.push(cb.value);
        });
        return ids;
    }

    function triggerBulkDelete(url) {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        if (!confirm('هل أنت متأكد من حذف ' + ids.length + ' عنصر؟')) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء الحذف');
            }
        })
        .catch(() => location.reload());
    }

    function triggerBulkStatus(status, url) {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(() => location.reload());
    }
</script>
