<table>
    <thead>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold;">@lang('accusoft::models/as_reports.types.cash_flow_statement_indirect')</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center;">@lang('accusoft::models/as_reports.filters.date_from'): {{ $fromDate }} | @lang('accusoft::models/as_reports.filters.date_to'): {{ $toDate }}</th>
        </tr>
    </thead>
    <tbody>
        {{-- Operating --}}
        <tr><td colspan="3" style="font-weight: bold; background-color: #e9ecef;">@lang('accusoft::models/as_reports.cash_flow.operating_activities')</td></tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.net_income')</td>
            <td>{{ $cashFlow['sections']['operating']['netIncome'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.depreciation')</td>
            <td>{{ $cashFlow['sections']['operating']['adjustments']['depreciation'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.receivables')</td>
            <td>{{ $cashFlow['sections']['operating']['workingCapital']['receivables'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.inventory')</td>
            <td>{{ $cashFlow['sections']['operating']['workingCapital']['inventory'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.payables')</td>
            <td>{{ $cashFlow['sections']['operating']['workingCapital']['payables'] }}</td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_operating')</td>
            <td></td>
            <td>{{ $cashFlow['sections']['operating']['total'] }}</td>
        </tr>

        {{-- Investing --}}
        <tr><td colspan="3" style="font-weight: bold; background-color: #e9ecef;">@lang('accusoft::models/as_reports.cash_flow.investing_activities')</td></tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.fixed_assets')</td>
            <td>{{ $cashFlow['sections']['investing']['fixedAssets'] }}</td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_investing')</td>
            <td></td>
            <td>{{ $cashFlow['sections']['investing']['total'] }}</td>
        </tr>

        {{-- Financing --}}
        <tr><td colspan="3" style="font-weight: bold; background-color: #e9ecef;">@lang('accusoft::models/as_reports.cash_flow.financing_activities')</td></tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.loans')</td>
            <td>{{ $cashFlow['sections']['financing']['loans'] }}</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('accusoft::models/as_reports.cash_flow.capital')</td>
            <td>{{ $cashFlow['sections']['financing']['capital'] }}</td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_financing')</td>
            <td></td>
            <td>{{ $cashFlow['sections']['financing']['total'] }}</td>
        </tr>

        {{-- Reconciliation --}}
        <tr style="font-weight: bold; border-top: 1px solid #000;">
            <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.net_change_in_cash')</td>
            <td>{{ $cashFlow['reconciliation']['netChange'] }}</td>
        </tr>
        <tr>
            <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.beginning_cash')</td>
            <td>{{ $cashFlow['reconciliation']['beginning'] }}</td>
        </tr>
        <tr style="font-weight: bold;">
            <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.ending_cash')</td>
            <td>{{ $cashFlow['reconciliation']['ending'] }}</td>
        </tr>
    </tbody>
</table>
