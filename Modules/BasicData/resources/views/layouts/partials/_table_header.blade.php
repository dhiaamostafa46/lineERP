<div class="front-card-header">
    <!-- Search Input -->
    <div class="position-relative flex-grow-1" style="max-width: 340px;">
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

    <!-- Right Controls: Export & Filter -->
    <div class="d-flex align-items-center gap-2">
        <!-- Export Dropdown -->
        <div class="dropdown">
            <button type="button" class="front-btn-export dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-arrow-down-to-bracket fs-8"></i>
                <span>Export</span>
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

        <!-- Filter Dropdown -->
        <div class="dropdown">
            @php
                $activeFiltersCount = (request('name') ? 1 : 0) + (request('status') !== null && request('status') !== '' ? 1 : 0) + (request('category_id') ? 1 : 0);
            @endphp
            <button type="button" 
                    class="front-btn-filter dropdown-toggle {{ $activeFiltersCount > 0 ? 'show text-primary border-primary' : '' }}" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="outside"
                    aria-expanded="false">
                <i class="fa-solid fa-sliders fs-8"></i>
                <span>Filter</span>
                @if($activeFiltersCount > 0)
                    <span class="badge rounded-pill bg-primary text-white fs-9 px-2 py-0 ms-1">{{ $activeFiltersCount }}</span>
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
    </div>
</div>
