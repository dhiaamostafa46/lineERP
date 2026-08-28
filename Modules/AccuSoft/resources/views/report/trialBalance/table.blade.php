<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7" id="AS-Accusoft-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th rowspan="2" class="text-center">@lang('accusoft::models/as_reports.columns.account_name')</th>
                    <th colspan="2" class="text-center">@lang('accusoft::models/as_reports.columns.opening_balance')</th>
                    <th colspan="2" class="text-center">@lang('accusoft::models/as_reports.columns.period_balance')</th>
                    <th colspan="2" class="text-center">@lang('accusoft::models/as_reports.columns.closing_balance')</th>
                </tr>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_debit')</th>
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_credit')</th>
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_debit')</th>
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_credit')</th>
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_debit')</th>
                    <th class="text-center">@lang('accusoft::models/as_reports.columns.total_credit')</th>
                </tr>
            </thead>

```
        <tbody>
            @forelse ($trialBalance['accounts'] as $account)

                @if((isset($is_pdf) || isset($is_excel)) && $account['level'] > ($level ?? 1))
                    @continue
                @endif

                <tr class="account-row {{ $account['is_leaf'] ? 'leaf-row' : 'parent-account' }}"
                    data-account-id="{{ $account['account_id'] }}"
                    data-parent-id="{{ $account['parent_id'] ?? '' }}"
                    data-level="{{ $account['level'] }}"
                    data-is-leaf="{{ $account['is_leaf'] ? 'true' : 'false' }}">

                    {{-- اسم الحساب --}}
                    <td class="text-start" style="padding-inline-start: {{ ($account['level'] - 1) * 30 + 16 }}px;">

                        @if (!$account['is_leaf'])
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

                    {{-- رصيد افتتاحي --}}
                    <td>{{ number_format($account['opening_debit_balance'], 2) }}</td>
                    <td>{{ number_format($account['opening_credit_balance'], 2) }}</td>

                    {{-- حركة الفترة --}}
                    <td>{{ number_format($account['total_period_debit'], 2) }}</td>
                    <td>{{ number_format($account['total_period_credit'], 2) }}</td>

                    {{-- الرصيد الختامي --}}
                    <td>{{ number_format($account['closing_debit_balance'], 2) }}</td>
                    <td>{{ number_format($account['closing_credit_balance'], 2) }}</td>

                </tr>

            @empty
                <tr>
                    <td colspan="7" class="text-center">لا توجد بيانات</td>
                </tr>
            @endforelse
        </tbody>

        {{-- الإجماليات --}}
        <tfoot>
            <tr class="fw-bold bg-light-success">
                <td>@lang('accusoft::models/as_reports.columns.total') </td>
                <td>{{ number_format($trialBalance['totals']['opening_debit'], 2) }}</td>
                <td>{{ number_format($trialBalance['totals']['opening_credit'], 2) }}</td>
                <td>{{ number_format($trialBalance['totals']['period_debit'], 2) }}</td>
                <td>{{ number_format($trialBalance['totals']['period_credit'], 2) }}</td>
                <td>{{ number_format($trialBalance['totals']['closing_debit_balance'], 2) }}</td>
                <td>{{ number_format($trialBalance['totals']['closing_credit_balance'], 2) }}</td>
            </tr>
        </tfoot>

    </table>
</div>

</div>
