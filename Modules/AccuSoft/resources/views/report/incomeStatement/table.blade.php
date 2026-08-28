<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-bordered text-center" id="AS-Accusoft-table">
            <thead>
                <tr>
                    <th class="text-start ps-4"> @lang('accusoft::models/as_reports.columns.account_name') </th>
                    <th class="text-end pe-4" style="width: 200px;"> @lang('accusoft::models/as_reports.columns.balance')  </th>
                </tr>
            </thead>
            <tbody>

                {{-- الإيرادات --}}
                
                @forelse ($incomeStatement['revenues'] as $account)
                    @if((isset($is_pdf) || isset($is_excel)) && $account['level'] > ($level ?? 1)) @continue @endif
                    <tr class="account-row {{ $account['is_leaf'] ? 'leaf-row' : 'parent-account' }}"
                        data-account-id="{{ $account['account_id'] }}"
                        data-parent-id="{{ $account['parent_id'] ?? '' }}"
                        data-level="{{ $account['level'] }}"
                        data-is-leaf="{{ $account['is_leaf'] ? 'true' : 'false' }}">
                        <td class="text-start" style="padding-inline-start: {{ ($account['level'] - 1) * 30 + 16 }}px;">
                            @if(!$account['is_leaf'])
                                <i class="fas fa-chevron-down toggle-icon me-2 {{ isset($is_pdf) || isset($is_excel) ? 'd-none' : '' }}"></i>
                                <i class="fas fa-folder text-warning me-1"></i>
                            @else
                                <i class="fas fa-file-alt text-primary me-1 ms-4 {{ isset($is_pdf) || isset($is_excel) ? 'd-none' : '' }}"></i>
                                @if(isset($is_pdf) || isset($is_excel))
                                    <span style="display:inline-block; width:20px;"></span>
                                @endif
                            @endif
                            @if(!isset($is_pdf) && !isset($is_excel) && !empty($account['account_id']))
                                <a href="{{ route('accusoft.reports.accountstatement', ['accountId' => $account['account_id'], 'fromDate' => $fromDate ?? null, 'toDate' => $toDate ?? null]) }}" target="_blank" class="text-dark text-hover-primary text-decoration-none" title="فتح كشف الحساب في تبويب جديد">
                                    <strong>{{ $account['account_name'] }}</strong>
                                    <span class="account-code text-muted">({{ $account['account_code'] }})</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square fs-8 text-primary ms-1 no-print"></i>
                                </a>
                            @else
                                <strong>{{ $account['account_name'] }}</strong>
                                <span class="account-code">({{ $account['account_code'] }})</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span dir="ltr">{{ number_format($account['balance'], 2) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-2">@lang('lang.no_data')</td></tr>
                @endforelse
               
                {{-- المصروفات --}}
                
                @forelse ($incomeStatement['expenses'] as $account)
                    @if((isset($is_pdf) || isset($is_excel)) && $account['level'] > ($level ?? 1)) @continue @endif
                    <tr class="account-row {{ $account['is_leaf'] ? 'leaf-row' : 'parent-account' }}"
                        data-account-id="{{ $account['account_id'] }}"
                        data-parent-id="{{ $account['parent_id'] ?? '' }}"
                        data-level="{{ $account['level'] }}"
                        data-is-leaf="{{ $account['is_leaf'] ? 'true' : 'false' }}">
                        <td class="text-start" style="padding-inline-start: {{ ($account['level'] - 1) * 30 + 16 }}px;">
                            @if(!$account['is_leaf'])
                                <i class="fas fa-chevron-down toggle-icon me-2 {{ isset($is_pdf) || isset($is_excel) ? 'd-none' : '' }}"></i>
                                <i class="fas fa-folder text-warning me-1"></i>
                            @else
                                <i class="fas fa-file-alt text-primary me-1 ms-4 {{ isset($is_pdf) || isset($is_excel) ? 'd-none' : '' }}"></i>
                                @if(isset($is_pdf) || isset($is_excel))
                                    <span style="display:inline-block; width:20px;"></span>
                                @endif
                            @endif
                            @if(!isset($is_pdf) && !isset($is_excel) && !empty($account['account_id']))
                                <a href="{{ route('accusoft.reports.accountstatement', ['accountId' => $account['account_id'], 'fromDate' => $fromDate ?? null, 'toDate' => $toDate ?? null]) }}" target="_blank" class="text-dark text-hover-primary text-decoration-none" title="فتح كشف الحساب في تبويب جديد">
                                    <strong>{{ $account['account_name'] }}</strong>
                                    <span class="account-code text-muted">({{ $account['account_code'] }})</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square fs-8 text-primary ms-1 no-print"></i>
                                </a>
                            @else
                                <strong>{{ $account['account_name'] }}</strong>
                                <span class="account-code">({{ $account['account_code'] }})</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span dir="ltr">{{ number_format($account['balance'], 2) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-2">@lang('lang.no_data')</td></tr>
                @endforelse
               

                @if(empty($incomeStatement['revenues']) && empty($incomeStatement['expenses']))
                    <tr>
                        <td colspan="2" class="text-center py-3 text-muted">  @lang('lang.no_data')  </td>
                    </tr>
                @endif

            </tbody>
            <tfoot class="fw-bold">
                <tr>
                    <td class="text-start ps-4 py-3">@lang('accusoft::models/as_reports.totals.total_revenue')</td>
                    <td class="text-end pe-4 py-3">
                        <span dir="ltr">{{ number_format($incomeStatement['totalRevenue'], 2) }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-start ps-4 py-3">@lang('accusoft::models/as_reports.totals.total_expense')</td>
                    <td class="text-end pe-4 py-3">
                        <span dir="ltr">{{ number_format($incomeStatement['totalExpense'], 2) }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-start ps-4 py-3">@lang('accusoft::models/as_reports.totals.net_income')</td>
                    <td class="text-end pe-4 py-3">
                        <span dir="ltr">{{ number_format($incomeStatement['netIncome'], 2) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
