<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">
                @lang('accusoft::models/as_reports.reports.journal_entries')
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                @lang('accusoft::models/as_reports.columns.from_date'): {{ $fromDate ?? '-' }} - @lang('accusoft::models/as_reports.columns.to_date'): {{ $toDate ?? '-' }}
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.columns.entry_number')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.columns.date')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.filters.entry_type')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.filters.account')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.columns.description')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.columns.debit')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_reports.columns.credit')</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($reportData) && !empty($reportData['items']))
            @foreach($reportData['items'] as $item)
                <tr>
                    <td>{{ $item['entry_number'] }}</td>
                    <td>{{ $item['entry_date'] }}</td>
                    <td>{{ $item['entry_type'] }}</td>
                    <td>{{ $item['account_name'] }} ({{ $item['account_code'] }})</td>
                    <td>
                        {{ $item['description'] ?? '-' }}
                        @if ($item['cost_center'])
                            - {{ $item['cost_center'] }}
                        @endif
                    </td>
                    <td>{{ $item['debit'] }}</td>
                    <td>{{ $item['credit'] }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="5" style="text-align: left; font-weight: bold;">@lang('accusoft::models/as_reports.columns.net_balance')</th>
                <th style="font-weight: bold;">{{ $reportData['total_debit'] }}</th>
                <th style="font-weight: bold;">{{ $reportData['total_credit'] }}</th>
            </tr>
        @else
            <tr>
                <td colspan="7" style="text-align: center;">@lang('lang.no_data')</td>
            </tr>
        @endif
    </tbody>
</table>
