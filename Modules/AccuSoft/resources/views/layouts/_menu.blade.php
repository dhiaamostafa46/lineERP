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
<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-bullet">
            <i class="ki-duotone ki-calculator fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </span>
        <span class="menu-title">@lang('accusoft::lang.accusoft')</span>
        <span class="menu-arrow"></span>
    </span>




    <div class="menu-sub menu-sub-accordion">
        <!----------------------------------------------Start accounts   soft--- --
            1- شجرة الحسابات --
            2- مركز التكلفة --
            3-القيود اليومية --
            4- السنوات المالية --
            5- الاصول الثابتة
            6-البنوك والصناديق

        ---------------------------------------------------------------------------->


        @can('accusoft.TreeAccounts.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.TreeAccounts*') ? 'active' : '' }}"
                    href="{{ route('accusoft.TreeAccounts.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-sitemap"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/as_tree_account.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.CostCenter.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.CostCenter*') ? 'active' : '' }}"
                    href="{{ route('accusoft.CostCenter.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-chart-pie"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/as_cost_centers.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.JournalEntry.index')
            @php
                $pendingCount = \Illuminate\Support\Facades\Cache::remember('accusoft_menu_pending_journals', 60, function() {
                    return \App\Models\AccuSoft\JournalEntry::where('status', \App\Models\AccuSoft\JournalEntry::STATUS_PENDING)->count();
                });
            @endphp
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.JournalEntry*') ? 'active' : '' }}"
                    href="{{ route('accusoft.JournalEntry.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-book-open"></i>
                    </span>
                    <span class="menu-title d-flex align-items-center">
                        @lang('accusoft::models/as_journal_entries.plural')
                        @if($pendingCount > 0)
                            <span class="badge rounded-pill bg-danger ms-2"
                                  style="font-size: 0.7rem; animation: pulse 1.5s infinite;">
                                {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                            </span>
                            <style>
                                @keyframes pulse {
                                    0%   { transform: scale(1); }
                                    50%  { transform: scale(1.1); }
                                    100% { transform: scale(1); }
                                }
                            </style>
                        @endif
                    </span>
                </a>
            </div>
           
        @endcan



        
                    

        @can('accusoft.assets.index')
            @php
                $unactivatedAssetsCount = \Illuminate\Support\Facades\Cache::remember('accusoft_menu_unactivated_assets', 60, function() {
                    $count = 0;
                    if (class_exists(\Modules\HR\App\Models\HrAsset::class)) {
                        $count += \Modules\HR\App\Models\HrAsset::doesntHave('financialAsset')->count();
                    }
                    return $count;
                });
            @endphp
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.assets*') ? 'active' : '' }}"
                    href="{{ route('accusoft.assets.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-building"></i>
                    </span>
                    <span class="menu-title d-flex align-items-center">
                        @lang('accusoft::models/as_asset.plural')
                        @if($unactivatedAssetsCount > 0)
                            <span class="badge rounded-pill bg-danger ms-2"
                                  style="font-size: 0.7rem; animation: pulse 1.5s infinite;">
                                {{ $unactivatedAssetsCount > 99 ? '99+' : $unactivatedAssetsCount }}
                            </span>
                        @endif
                    </span>
                </a>
            </div>
        @endcan

      


        @can('accusoft.Setting.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.Setting*') ? 'active' : '' }}"
                    href="{{ route('accusoft.Setting.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/as_setting.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.reports.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.reports*') ? 'active' : '' }}"
                    href="{{ route('accusoft.reports.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/as_reports.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.banks.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.banks*') ? 'active' : '' }}"
                    href="{{ route('accusoft.banks.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-university"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/banks.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.receipt_vouchers.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.receipt_vouchers*') ? 'active' : '' }}"
                    href="{{ route('accusoft.receipt_vouchers.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/receipt_vouchers.plural')</span>
                </a>
            </div>
        @endcan

        @can('accusoft.payment_vouchers.index')
            <div class="menu-item">
                <a class="menu-link {{ Route::is('accusoft.payment_vouchers*') ? 'active' : '' }}"
                    href="{{ route('accusoft.payment_vouchers.index') }}">
                    <span class="menu-bullet">
                        <i class="nav-icon fas fa-file-invoice"></i>
                    </span>
                    <span class="menu-title">@lang('accusoft::models/payment_vouchers.plural')</span>
                </a>
            </div>
        @endcan

       

    </div>
</div>
@endcanany
