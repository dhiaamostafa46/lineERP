<!-- Id Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.id')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->id }}</b>
    </div>
</div>

<!-- document_number Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.document_number')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->document_number }}</b>
    </div>
</div>

<!-- document_date Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.document_date')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->document_date ? $receiving->document_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- store_id Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.store_id')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->store->name ?? '' }}</b>
    </div>
</div>

<!-- tree_account_id Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.tree_account_id')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->account->name ?? '' }}</b>
    </div>
</div>

<!-- status Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.status')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->status_text }}</b>
    </div>
</div>

<!-- total_items Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.total_items')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->total_items }}</b>
    </div>
</div>

<!-- total_quantity Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.total_quantity')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ number_format($receiving->total_quantity, 2) }}</b>
    </div>
</div>

<!-- total_value Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.total_value')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light text-primary fw-bold">{{ number_format($receiving->total_value, 2) }}</b>
    </div>
</div>

<!-- user_id Field -->
<div class="col-sm-6 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.user_id')</p>
    </div>
    <div class="col-8">
        <b class="form-control bg-light">{{ $receiving->user->name ?? '' }}</b>
    </div>
</div>

<!-- notes Field -->
<div class="col-sm-12 row mb-3">
    <div class="col-2 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('store::models/st_receivings.fields.notes')</p>
    </div>
    <div class="col-10">
        <b class="form-control bg-light h-auto min-h-50px">{{ $receiving->notes }}</b>
    </div>
</div>

<!-- attachment Field -->
<div class="col-sm-12 row mb-3">
    <div class="col-2 my-auto">
        <p class="fs-5 fw-bold mb-0">@lang('lang.attachment')</p>
    </div>
    <div class="col-10">
        @if ($receiving->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $receiving->attachment_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-4 me-1"></i> {{ $receiving->attachment }} (@lang('crud.download') / @lang('lang.view'))
                </a>
                @php
                    $ext = strtolower(pathinfo($receiving->attachment, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <a href="{{ $receiving->attachment_url }}" target="_blank" class="d-inline-block ms-2">
                        <img src="{{ $receiving->attachment_url }}" alt="attachment" class="rounded border" style="max-height: 60px; max-width: 100px; object-fit: cover;">
                    </a>
                @endif
            </div>
        @else
            <b class="form-control bg-light text-muted">@lang('lang.no_attachment')</b>
        @endif
    </div>
</div>

<div class="col-sm-12 mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center gy-7 gs-7">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('store::ui.product')</th>
                    <th>@lang('store::ui.unit')</th>
                    <th>@lang('store::ui.quantity')</th>
                    <th>@lang('store::ui.cost')</th>
                    <th>@lang('store::ui.total')</th>
                    <th>@lang('store::models/st_receivings.items.notes')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receiving->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->unit_name }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ number_format($item->total_cost, 2) }}</td>
                    <td>{{ $item->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
