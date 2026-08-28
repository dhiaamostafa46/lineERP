<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>@lang('accusoft::models/as_reports.types.cash_flow_statement_indirect')</title>
    <style>
        body { font-family: 'Amiri', 'DejaVu Sans', sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        .bg-light { background-color: #f8f9fa; }
        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .header { margin-bottom: 30px; text-align: center; }
        .bg-secondary { background-color: #e9ecef; }
        .ps-8 { padding-right: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>@lang('accusoft::models/as_reports.types.cash_flow_statement_indirect')</h1>
        <p>@lang('accusoft::models/as_reports.filters.date_from'): {{ $fromDate }} | @lang('accusoft::models/as_reports.filters.date_to'): {{ $toDate }}</p>
    </div>

    <table class="table">
        <tbody>
            {{-- Operating Activities --}}
            <tr class="fw-bold bg-secondary">
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.operating_activities')</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.net_income')</td>
                <td>{{ number_format($cashFlow['sections']['operating']['netIncome'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.depreciation')</td>
                <td>{{ number_format($cashFlow['sections']['operating']['adjustments']['depreciation'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.receivables')</td>
                <td>{{ number_format($cashFlow['sections']['operating']['workingCapital']['receivables'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.inventory')</td>
                <td>{{ number_format($cashFlow['sections']['operating']['workingCapital']['inventory'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.payables')</td>
                <td>{{ number_format($cashFlow['sections']['operating']['workingCapital']['payables'], 2) }}</td>
                <td></td>
            </tr>
            <tr class="fw-bold">
                <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_operating')</td>
                <td></td>
                <td>{{ number_format($cashFlow['sections']['operating']['total'], 2) }}</td>
            </tr>

            {{-- Investing Activities --}}
            <tr class="fw-bold bg-secondary">
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.investing_activities')</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.fixed_assets')</td>
                <td>{{ number_format($cashFlow['sections']['investing']['fixedAssets'], 2) }}</td>
                <td></td>
            </tr>
            <tr class="fw-bold">
                <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_investing')</td>
                <td></td>
                <td>{{ number_format($cashFlow['sections']['investing']['total'], 2) }}</td>
            </tr>

            {{-- Financing Activities --}}
            <tr class="fw-bold bg-secondary">
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.financing_activities')</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.loans')</td>
                <td>{{ number_format($cashFlow['sections']['financing']['loans'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="ps-8">@lang('accusoft::models/as_reports.cash_flow.capital')</td>
                <td>{{ number_format($cashFlow['sections']['financing']['capital'], 2) }}</td>
                <td></td>
            </tr>
            <tr class="fw-bold">
                <td>@lang('accusoft::models/as_reports.cash_flow.net_cash_financing')</td>
                <td></td>
                <td>{{ number_format($cashFlow['sections']['financing']['total'], 2) }}</td>
            </tr>

            <tr class="fw-bold" style="font-size: 1.2em; border-top: 2px solid #000;">
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.net_change_in_cash')</td>
                <td>{{ number_format($cashFlow['reconciliation']['netChange'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.beginning_cash')</td>
                <td>{{ number_format($cashFlow['reconciliation']['beginning'], 2) }}</td>
            </tr>
            <tr class="fw-bold">
                <td colspan="2">@lang('accusoft::models/as_reports.cash_flow.ending_cash')</td>
                <td>{{ number_format($cashFlow['reconciliation']['ending'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
