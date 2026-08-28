<!--begin::Financial Dashboard-->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <div class="col-md-4">
        <div class="card card-flush shadow-sm bg-light-primary border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <i class="fa-solid fa-money-bill-wave text-primary fs-2hx mb-4"></i>
                <div class="d-flex flex-column">
                    <div class="fs-6 fw-semibold text-primary opacity-75 mb-1">@lang('accusoft::models/as_asset.fields.purchase_value')</div>
                    <div class="fs-2 fw-bolder text-primary">{{ number_format($asset->purchase_value, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-flush shadow-sm bg-light-danger border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <i class="fa-solid fa-chart-line text-danger fs-2hx mb-4"></i>
                <div class="d-flex flex-column">
                    <div class="fs-6 fw-semibold text-danger opacity-75 mb-1">@lang('accusoft::models/as_asset.fields.total_depreciation')</div>
                    <div class="fs-2 fw-bolder text-danger">{{ number_format($asset->total_depreciation, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-flush shadow-sm bg-light-success border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <i class="fa-solid fa-vault text-success fs-2hx mb-4"></i>
                <div class="d-flex flex-column">
                    <div class="fs-6 fw-semibold text-success opacity-75 mb-1">@lang('accusoft::models/as_asset.fields.current_book_value')</div>
                    <div class="fs-2 fw-bolder text-success">{{ number_format($asset->current_book_value ?? $asset->purchase_value, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Financial Dashboard-->
