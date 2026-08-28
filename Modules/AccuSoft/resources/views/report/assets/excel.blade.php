<table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center; font-weight: bold; font-size: 16px;">
                @lang('accusoft::models/as_reports.reports.assets')
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: right;">@lang('accusoft::models/as_reports.filters.date_from'): {{ $fromDate ?? '-' }}</th>
            <th colspan="5" style="text-align: left;">@lang('accusoft::models/as_reports.filters.date_to'): {{ $toDate ?? '-' }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.code')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.name')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.asset_accounting_classification')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.categories')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.depreciation_method')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.purchase_date')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.purchase_value')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.total_depreciation')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.current_book_value')</th>
            <th style="font-weight: bold;">@lang('accusoft::models/as_asset.fields.status')</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPurchaseValue = 0;
            $totalDepreciation = 0;
            $totalBookValue = 0;
            $depreciationStatuses = \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses();
            $depreciationMethods = \Modules\AccuSoft\App\Models\AssetCategory::getDepreciationMethods();
        @endphp
        @forelse($assets ?? [] as $asset)
            @php
                $totalPurchaseValue += $asset->purchase_value;
                $totalDepreciation += $asset->total_depreciation;
                $totalBookValue += $asset->current_book_value;
                $methodValue = $asset->depreciation_status == 'category' ? ($asset->assetCategory->default_depreciation_method ?? '') : $asset->depreciation_method;
            @endphp
            <tr>
                <td>{{ $asset->code }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $depreciationStatuses[$asset->depreciation_status] ?? '' }}</td>
                <td>{{ $asset->assetCategory->name ?? '' }}</td>
                <td>{{ $depreciationMethods[$methodValue] ?? '' }}</td>
                <td>{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}</td>
                <td data-format="#,##0.00">{{ $asset->purchase_value }}</td>
                <td data-format="#,##0.00">{{ $asset->total_depreciation }}</td>
                <td data-format="#,##0.00">{{ $asset->current_book_value }}</td>
                <td>{{ $asset->status_label ?? $asset->status_text }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align: center;">@lang('accusoft::models/as_reports.messages.no_data')</td>
            </tr>
        @endforelse
    </tbody>
    @if(isset($assets) && count($assets) > 0)
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">@lang('accusoft::models/as_reports.columns.total')</td>
                <td style="font-weight: bold;" data-format="#,##0.00">{{ $totalPurchaseValue }}</td>
                <td style="font-weight: bold;" data-format="#,##0.00">{{ $totalDepreciation }}</td>
                <td style="font-weight: bold;" data-format="#,##0.00">{{ $totalBookValue }}</td>
                <td></td>
            </tr>
        </tfoot>
    @endif
</table>
