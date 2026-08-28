@canany([
    'accusoft.TreeAccounts.index',
    'accusoft.CostCenter.index',
    'accusoft.JournalEntry.index',
    'accusoft.Setting.index',
    'accusoft.reports.index',
    'accusoft.banks.index',
    'accusoft.receipt_vouchers.index',
    'accusoft.payment_vouchers.index',
    'accusoft.assets.index'
])

    @php
        $isAccuSoftActive = Route::is('accusoft.*');
        $pendingCount = \Illuminate\Support\Facades\Cache::remember('accusoft_menu_pending_journals', 60, function() {
            if (class_exists('\App\Models\AccuSoft\JournalEntry')) {
                return \App\Models\AccuSoft\JournalEntry::where('status', \App\Models\AccuSoft\JournalEntry::STATUS_PENDING ?? 1)->count();
            }
            return 0;
        });
    @endphp

    <div x-data="{ open: {{ $isAccuSoftActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isAccuSoftActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge icon-accounting">
                    <i class="fas fa-calculator"></i>
                </div>
                <span class="line-menu-title">@lang('accusoft::lang.accusoft')</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($pendingCount > 0)
                    <span class="badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                    </span>
                @endif
                <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
            </div>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('accusoft.TreeAccounts.index')
                <a class="line-sub-item {{ Route::is('accusoft.TreeAccounts*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.TreeAccounts.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/as_tree_account.plural')</span>
                </a>
            @endcan

            @can('accusoft.CostCenter.index')
                <a class="line-sub-item {{ Route::is('accusoft.CostCenter*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.CostCenter.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/as_cost_centers.plural')</span>
                </a>
            @endcan

            @can('accusoft.JournalEntry.index')
                <a class="line-sub-item {{ Route::is('accusoft.JournalEntry*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.JournalEntry.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span class="d-flex align-items-center justify-content-between flex-grow-1">
                        @lang('accusoft::models/as_journal_entries.plural')
                        @if($pendingCount > 0)
                            <span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 0.65rem;">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </span>
                </a>
            @endcan

            @can('accusoft.assets.index')
                <a class="line-sub-item {{ Route::is('accusoft.assets*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.assets.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/as_asset.plural')</span>
                </a>
            @endcan

            @can('accusoft.receipt_vouchers.index')
                <a class="line-sub-item {{ Route::is('accusoft.receipt_vouchers*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.receipt_vouchers.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/receipt_vouchers.plural')</span>
                </a>
            @endcan

            @can('accusoft.payment_vouchers.index')
                <a class="line-sub-item {{ Route::is('accusoft.payment_vouchers*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.payment_vouchers.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/payment_vouchers.plural')</span>
                </a>
            @endcan

            @can('accusoft.banks.index')
                <a class="line-sub-item {{ Route::is('accusoft.banks*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.banks.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/banks.plural')</span>
                </a>
            @endcan

            @can('accusoft.reports.index')
                <a class="line-sub-item {{ Route::is('accusoft.reports*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.reports.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/as_reports.plural')</span>
                </a>
            @endcan

            @can('accusoft.Setting.index')
                <a class="line-sub-item {{ Route::is('accusoft.Setting*') ? 'active-sub' : '' }}"
                    href="{{ route('accusoft.Setting.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('accusoft::models/as_setting.plural')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
