<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="assets-table">
            <thead>
            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                <th class="text-center">@lang('accusoft::models/as_asset.fields.code')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.name')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.asset_type')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.asset_category_id')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.purchase_date')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.purchase_value')</th>
                <th class="text-center">@lang('accusoft::models/as_asset.fields.status')</th>
                <th class="text-center table-action">@lang('crud.action')</th>
            </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600">
            @foreach($assets as $asset)
                <tr>
                    <td class="text-center">{{ $asset->code }}</td>
                    <td class="text-center">{{ $asset->name }}</td>
                    <td class="text-center">
                        <span class="badge badge-light-{{ \Modules\AccuSoft\App\Models\Asset::getDepreciationStatusColors()[$asset->depreciation_status] ?? 'secondary' }}">
                            {{ \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses()[$asset->depreciation_status] ?? $asset->depreciation_status }}
                        </span>
                    </td>
                    <td class="text-center">{{ $asset->assetCategory->name ?? '-' }}</td>
                    <td class="text-center">{{ $asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE && $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE ? number_format($asset->purchase_value, 2) : '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-light-{{ $asset->status_color }}">
                            {{ $asset->status_label }}
                        </span>
                    </td>
                    <td style="width: 120px" class="table-action text-center">
                        <div class='btn-group'>
                            <a href="{{ route('accusoft.assets.show', [$asset->id]) }}" class="btn btn-sm btn-primary float-right " title="@lang('lang.show')">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            @if($asset->status != \Modules\AccuSoft\App\Models\Asset::STATUS_DISPOSED)
                                <a href="{{ route('accusoft.assets.edit', [$asset->id]) }}" class="btn btn-sm btn-primary float-right mx-1" title="@lang('lang.edit')">
                                    <i class="fa-solid fa-edit"></i>
                                </a>


                            @endif

                            <!-- {!! Form::open(['route' => ['accusoft.assets.destroy', $asset->id], 'method' => 'delete', 'style' => 'display:inline']) !!}
                                <button type="submit" class="btn btn-sm btn-primary " title="@lang('lang.delete')" onclick="return confirm('@lang('lang.are_you_sure')')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            {!! Form::close() !!} -->
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $assets])
        </div>
    </div>
</div>
