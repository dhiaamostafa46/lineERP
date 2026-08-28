<div class="card shadow-sm mt-5">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-300px rounded-start">@lang('accusoft::models/as_reports.columns.description')</th>
                        <th class="text-end min-w-150px">@lang('accusoft::models/as_reports.columns.total')</th>
                        <th class="text-end pe-4 rounded-end">@lang('accusoft::models/as_reports.columns.total')</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Operating Activities --}}
                    <tr class="fw-bold bg-secondary">
                        <td colspan="3" class="ps-4">@lang('accusoft::models/as_reports.cash_flow.operating_activities')</td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.net_income')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['netIncome'], 2) }}</td>
                        <td></td>
                    </tr>
                    
                    {{-- Adjustments --}}
                    <tr>
                        <td class="ps-8 italic">@lang('accusoft::models/as_reports.cash_flow.depreciation')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['adjustments']['depreciation'], 2) }}</td>
                        <td></td>
                    </tr>

                    {{-- Working Capital --}}
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.receivables')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['workingCapital']['receivables'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.inventory')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['workingCapital']['inventory'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.other_assets')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['workingCapital']['otherAssets'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.payables')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['workingCapital']['payables'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.other_liabilities')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['operating']['workingCapital']['otherLiabilities'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="fw-bold border-top">
                        <td class="ps-4">@lang('accusoft::models/as_reports.cash_flow.net_cash_operating')</td>
                        <td></td>
                        <td class="text-end pe-4">{{ number_format($cashFlow['sections']['operating']['total'], 2) }}</td>
                    </tr>

                    {{-- Investing Activities --}}
                    <tr class="fw-bold bg-secondary mt-5">
                        <td colspan="3" class="ps-4">@lang('accusoft::models/as_reports.cash_flow.investing_activities')</td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.fixed_assets')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['investing']['fixedAssets'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="fw-bold border-top">
                        <td class="ps-4">@lang('accusoft::models/as_reports.cash_flow.net_cash_investing')</td>
                        <td></td>
                        <td class="text-end pe-4">{{ number_format($cashFlow['sections']['investing']['total'], 2) }}</td>
                    </tr>

                    {{-- Financing Activities --}}
                    <tr class="fw-bold bg-secondary mt-5">
                        <td colspan="3" class="ps-4">@lang('accusoft::models/as_reports.cash_flow.financing_activities')</td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.loans')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['financing']['loans'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.capital')</td>
                        <td class="text-end">{{ number_format($cashFlow['sections']['financing']['capital'], 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="fw-bold border-top">
                        <td class="ps-4">@lang('accusoft::models/as_reports.cash_flow.net_cash_financing')</td>
                        <td></td>
                        <td class="text-end pe-4">{{ number_format($cashFlow['sections']['financing']['total'], 2) }}</td>
                    </tr>

                    {{-- Reconciliation --}}
                    <tr class="fw-bold border-top-2 border-primary mt-10">
                        <td class="ps-4 fs-5">@lang('accusoft::models/as_reports.cash_flow.net_change_in_cash')</td>
                        <td></td>
                        <td class="text-end pe-4 fs-5">{{ number_format($cashFlow['reconciliation']['netChange'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4">@lang('accusoft::models/as_reports.cash_flow.beginning_cash')</td>
                        <td></td>
                        <td class="text-end pe-4">{{ number_format($cashFlow['reconciliation']['beginning'], 2) }}</td>
                    </tr>
                    <tr class="fw-bold fs-4 text-primary">
                        <td class="ps-4">@lang('accusoft::models/as_reports.cash_flow.ending_cash')</td>
                        <td></td>
                        <td class="text-end pe-4">{{ number_format($cashFlow['reconciliation']['ending'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
