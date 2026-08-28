<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ $name ?? __('accusoft::models/as_reports.reports.assets') }}</title>
    <style>
        body { font-family: 'XBRiyaz', 'Amiri', 'aealarabiya', sans-serif; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: center; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>{{ $name ?? __('accusoft::models/as_reports.reports.assets') }}</h2>
        <p>
            <strong>@lang('accusoft::models/as_reports.filters.date_from'):</strong> {{ $fromDate ?? '-' }}
            &nbsp; | &nbsp;
            <strong>@lang('accusoft::models/as_reports.filters.date_to'):</strong> {{ $toDate ?? '-' }}
        </p>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>@lang('accusoft::models/as_asset.fields.code')</th>
                    <th>@lang('accusoft::models/as_asset.fields.name')</th>
                    <th>@lang('accusoft::models/as_asset.asset_accounting_classification')</th>
                    <th>@lang('accusoft::models/as_asset.fields.categories')</th>
                    <th>@lang('accusoft::models/as_asset.fields.depreciation_method')</th>
                    <th>@lang('accusoft::models/as_asset.fields.purchase_date')</th>
                    <th>@lang('accusoft::models/as_asset.fields.purchase_value')</th>
                    <th>@lang('accusoft::models/as_asset.fields.total_depreciation')</th>
                    <th>@lang('accusoft::models/as_asset.fields.current_book_value')</th>
                    <th>@lang('accusoft::models/as_asset.fields.status')</th>
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
                        <td class="text-right">{{ number_format($asset->purchase_value, 2) }}</td>
                        <td class="text-right">{{ number_format($asset->total_depreciation, 2) }}</td>
                        <td class="text-right">{{ number_format($asset->current_book_value, 2) }}</td>
                        <td>{{ $asset->status_label ?? $asset->status_text }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">@lang('accusoft::models/as_reports.messages.no_data')</td>
                    </tr>
                @endforelse
            </tbody>
            @if(isset($assets) && count($assets) > 0)
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>@lang('accusoft::models/as_reports.columns.total')</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalPurchaseValue, 2) }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalDepreciation, 2) }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($totalBookValue, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</body>
</html>
