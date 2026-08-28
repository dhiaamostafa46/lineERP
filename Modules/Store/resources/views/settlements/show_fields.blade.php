<!-- Id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $settlement->id }}</b>
    </div>
</div>



<!-- document_number Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.document_number')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->document_number }}</b>
    </div>
</div>

<!-- document_date Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.document_date')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->document_date ? $settlement->document_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- store_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.store_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->store->name ?? '' }}</b>
    </div>
</div>

<!-- status Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->status_text }}</b>
    </div>
</div>

<!-- total_items Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.total_items')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->total_items }}</b>
    </div>
</div>

<!-- total_quantity Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.total_quantity')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ number_format($settlement->total_quantity, 2) }}</b>
    </div>
</div>

<!-- total_value Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.total_value')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ number_format($settlement->total_value, 2) }}</b>
    </div>
</div>

<!-- notes Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.notes')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->notes }}</b>
    </div>
</div>

<!-- user_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_settlements.fields.user_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $settlement->user->name ?? '' }}</b>
    </div>
</div>

<!-- attachment Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('lang.attachment')
        </p>
    </div>
    <div class="col-8">
        @if ($settlement->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $settlement->attachment_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-4 me-1"></i> {{ $settlement->attachment }} (@lang('crud.download') / @lang('lang.view'))
                </a>
                @php
                    $ext = strtolower(pathinfo($settlement->attachment, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <a href="{{ $settlement->attachment_url }}" target="_blank" class="d-inline-block ms-2">
                        <img src="{{ $settlement->attachment_url }}" alt="attachment" class="rounded border" style="max-height: 40px; max-width: 80px; object-fit: cover;">
                    </a>
                @endif
            </div>
        @else
            <b class="form-control text-muted">@lang('lang.no_attachment')</b>
        @endif
    </div>
</div>


<div class="col-sm-12 mt-4">

    <table class="table table-striped table-bordered text-center gy-7 gs-7">
        <thead>
            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                <th>@lang('store::models/st_settlement_items.fields.product_id')</th>
                <th>@lang('store::models/st_settlement_items.fields.unit_id')</th>
                <th>@lang('store::models/st_settlement_items.fields.system_quantity')</th>
                <th>@lang('store::models/st_settlement_items.fields.actual_quantity')</th>
                <th>@lang('store::models/st_settlement_items.fields.variance_quantity')</th>
                <th>@lang('store::models/st_settlement_items.fields.unit_cost')</th>
                <th>@lang('store::models/st_settlement_items.fields.total_cost')</th>
                <th>@lang('store::models/st_settlement_items.fields.notes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settlement->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->unit_name }}</td>
                <td>{{ number_format($item->system_quantity, 2) }}</td>
                <td>{{ number_format($item->actual_quantity, 2) }}</td>
                <td>{{ number_format($item->variance_quantity, 2) }}</td>
                <td>{{ number_format($item->unit_cost, 2) }}</td>
                <td>{{ number_format($item->total_cost, 2) }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
