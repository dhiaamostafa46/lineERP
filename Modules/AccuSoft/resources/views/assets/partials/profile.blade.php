<!--begin::Asset Profile Card-->
<div class="card card-flush mb-5 mb-xl-8 border-0 shadow-sm">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800 fs-3 mb-1">{{ $asset->name }}</span>
            <span class="text-muted fw-semibold fs-7">
                <i class="fa-solid fa-barcode text-muted me-1"></i> @lang('accusoft::models/as_asset.fields.code'): {{ $asset->code }}
            </span>
        </h3>
        <div class="card-toolbar">
            @if($asset->status == \Modules\AccuSoft\App\Models\Asset::STATUS_ACTIVE)
                <span class="badge badge-success px-3 py-2 fs-7 fw-bold">@lang('lang.active')</span>
            @elseif($asset->status == \Modules\AccuSoft\App\Models\Asset::STATUS_DISPOSED)
                <span class="badge badge-danger px-3 py-2 fs-7 fw-bold">@lang('lang.disposed')</span>
            @else
                <span class="badge badge-warning px-3 py-2 fs-7 fw-bold">{{ $asset->status }}</span>
            @endif
        </div>
    </div>
    
    <div class="card-body pt-5">
        <div class="d-flex flex-column gap-5">
            @if($asset->depreciation_status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY)
            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.asset_category_id')</div>
                <div class="text-gray-900 fw-bolder">{{ $asset->assetCategory->name ?? '-' }}</div>
            </div>
            <div class="separator separator-dashed my-1"></div>
            @elseif($asset->depreciation_status == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CUSTOM && $asset->parent_account_id)
            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.parent_account')</div>
                <div class="text-gray-900 fw-bolder">{{ \App\Models\AccuSoft\TreeAccounts::find($asset->parent_account_id)->name ?? '-' }}</div>
            </div>
            <div class="separator separator-dashed my-1"></div>
            @endif
            
            @if($asset->depreciation_status != \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE)
            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.purchase_date')</div>
                <div class="text-gray-900 fw-bolder">{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '-' }}</div>
            </div>
            <div class="separator separator-dashed my-1"></div>

            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.useful_life')</div>
                <div class="text-gray-900 fw-bolder">{{ $asset->useful_life }} @lang('accusoft::models/as_asset.years') ({{ $asset->useful_life_type == 'yearly' ? __('accusoft::models/as_asset.yearly') : __('accusoft::models/as_asset.monthly') }})</div>
            </div>
            <div class="separator separator-dashed my-1"></div>

            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.depreciation_method')</div>
                <div class="text-gray-900 fw-bolder">
                    {{ \Modules\AccuSoft\App\Models\Asset::getDepreciationMethods()[$asset->depreciation_method] ?? '-' }}
                </div>
            </div>
            <div class="separator separator-dashed my-1"></div>

            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.calculation_type')</div>
                <div class="text-gray-900 fw-bolder">
                    {{ $asset->calculation_type == 'automatic' ? __('accusoft::models/as_asset.automatic') : __('accusoft::models/as_asset.manual') }}
                </div>
            </div>
            <div class="separator separator-dashed my-1"></div>

            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.salvage_value')</div>
                <div class="text-gray-900 fw-bolder">{{ number_format($asset->salvage_value, 2) }}</div>
            </div>
            <div class="separator separator-dashed my-1"></div>
            @endif
            
            @if($asset->costCenter)
            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.fields.cost_center')</div>
                <div class="text-gray-900 fw-bolder">{{ $asset->costCenter->name }}</div>
            </div>
            <div class="separator separator-dashed my-1"></div>
            @endif

            @if($asset->assetable)
            <div class="d-flex flex-stack fs-5">
                <div class="text-gray-500 fw-semibold">@lang('accusoft::models/as_asset.linked_to')</div>
                <div class="text-gray-900 fw-bolder">
                    @if(class_basename($asset->assetable) == 'HrAsset')
                        <span class="badge badge-light-primary">@lang('accusoft::models/as_asset.hr_assets')</span>
                        {{ collect($asset->assetable->translations)->firstWhere('locale', app()->getLocale())->name ?? $asset->assetable->name }}
                    @elseif(class_basename($asset->assetable) == 'vc_vehicle')
                        <span class="badge badge-light-info">@lang('accusoft::models/as_asset.vehicle_assets')</span>
                        {{ $asset->assetable->name ?? $asset->assetable->plate_number }}
                    @else
                        <span class="badge badge-light-secondary">{{ class_basename($asset->assetable) }}</span>
                        {{ $asset->assetable->name ?? '-' }}
                    @endif
                </div>
            </div>
            <div class="separator separator-dashed my-1"></div>
            @endif
        </div>
    </div>
</div>
<!--end::Asset Profile Card-->
