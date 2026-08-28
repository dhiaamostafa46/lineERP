<div class="card shadow-sm mt-5">
    <div class="card-header">
        <h3 class="card-title">@lang('accusoft::models/as_reports.reports.assets')</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle gs-0 gy-4 text-center">
                <thead class="bg-light">
                    <tr class="fw-bold text-muted">
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
                            <td class="text-end" dir="ltr">{{ number_format($asset->purchase_value, 2) }}</td>
                            <td class="text-end" dir="ltr">{{ number_format($asset->total_depreciation, 2) }}</td>
                            <td class="text-end" dir="ltr">{{ number_format($asset->current_book_value, 2) }}</td>
                            <td>{{ $asset->status_label ?? $asset->status_text }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">@lang('accusoft::models/as_reports.messages.no_data')</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(isset($assets) && count($assets) > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">@lang('accusoft::models/as_reports.columns.total')</td>
                            <td class="text-end text-primary" dir="ltr">{{ number_format($totalPurchaseValue, 2) }}</td>
                            <td class="text-end text-danger" dir="ltr">{{ number_format($totalDepreciation, 2) }}</td>
                            <td class="text-end text-success" dir="ltr">{{ number_format($totalBookValue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
