<div class="card shadow-sm border border-gray-200 mb-5" style="border-radius: 1rem;">
    <div class="card-body py-4 px-5">
        <!-- Top Bar Row -->
        <div class="row align-items-center g-3">
            <!-- Title & Branding -->
            <div class="col-lg-4 col-md-12">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px me-3 p-2 rounded-3 d-flex align-items-center justify-content-center" style="background: rgba(27, 50, 91, 0.07); color: #1B325B;">
                        <i class="ki-outline ki-element-11 fs-2" style="color: #1B325B;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bolder fs-4 mb-0" style="color: #1B325B;">{{ __('lang.welcome_back') }}, {{ auth()->user()->name }}</h2>
                        <span class="text-gray-500 fs-8 fw-semibold">{{ __('lang.executive_dashboard') }} (<span style="color: #1B325B; font-weight: 700;">Evix</span> ERP)</span>
                    </div>
                </div>
            </div>

            <!-- Filters Controls -->
            <div class="col-lg-8 col-md-12">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <!-- Branch Filter (Select2) -->
                    <div class="w-180px" wire:ignore
                        x-data="{
                            init() {
                                let el = $(this.$refs.branchSelect);
                                if (typeof $.fn.select2 !== 'undefined') {
                                    el.select2({ minimumResultsForSearch: 0, width: '100%' });
                                    el.on('change', () => {
                                        $wire.set('branchId', el.val());
                                    });
                                }
                            }
                        }">
                        <select x-ref="branchSelect" wire:model.live="branchId" id="executive_branch_filter" class="form-select form-select-solid form-select-sm fw-semibold" data-placeholder="{{ __('lang.all_branches') }}">
                            <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>{{ __('lang.all_branches') }}</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Store Filter (Select2) -->
                    <div class="w-180px" wire:ignore
                        x-data="{
                            init() {
                                let el = $(this.$refs.storeSelect);
                                if (typeof $.fn.select2 !== 'undefined') {
                                    el.select2({ minimumResultsForSearch: 0, width: '100%' });
                                    el.on('change', () => {
                                        $wire.set('storeId', el.val());
                                    });
                                }
                            }
                        }">
                        <select x-ref="storeSelect" wire:model.live="storeId" id="executive_store_filter" class="form-select form-select-solid form-select-sm fw-semibold" data-placeholder="{{ __('lang.all_stores') }}">
                            <option value="all" {{ $storeId === 'all' ? 'selected' : '' }}>{{ __('lang.all_stores') }}</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Period Filter (Select2) -->
                    <div class="w-180px" wire:ignore
                        x-data="{
                            init() {
                                let el = $(this.$refs.periodSelect);
                                if (typeof $.fn.select2 !== 'undefined') {
                                    el.select2({ minimumResultsForSearch: Infinity, width: '100%' });
                                    el.on('change', () => {
                                        $wire.set('period', el.val());
                                    });
                                }
                            }
                        }">
                        <select x-ref="periodSelect" wire:model.live="period" id="executive_period_filter" class="form-select form-select-solid form-select-sm fw-bold">
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>{{ __('lang.today') }}</option>
                            <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>{{ __('lang.yesterday') }}</option>
                            <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>{{ __('lang.this_week') }}</option>
                            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>{{ __('lang.this_month') }}</option>
                            <option value="this_quarter" {{ $period === 'this_quarter' ? 'selected' : '' }}>{{ __('lang.this_quarter') }}</option>
                            <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>{{ __('lang.this_year') }}</option>
                        </select>
                    </div>

                    <!-- Refresh Button -->
                    <button wire:click="applyFilters" type="button" class="btn btn-sm fw-bold px-4" style="background: rgba(27, 50, 91, 0.08); color: #1B325B; border: 1px solid rgba(27, 50, 91, 0.15);">
                        <i class="ki-outline ki-arrows-loop fs-4 me-1" style="color: #1B325B;"></i> {{ __('lang.refresh') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
