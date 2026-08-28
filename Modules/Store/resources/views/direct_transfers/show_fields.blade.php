<!-- Id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->id }}</b>
    </div>
</div>

<!-- document_number Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.document_number')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->document_number }}</b>
    </div>
</div>

<!-- document_date Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.document_date')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->document_date ? $transfer->document_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- from_store_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.from_store_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->fromStore->name ?? '' }}</b>
    </div>
</div>

<!-- to_store_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.to_store_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->toStore->name ?? '' }}</b>
    </div>
</div>

<!-- status Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->status_text }}</b>
    </div>
</div>

<!-- total_items Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.total_items')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->total_items }}</b>
    </div>
</div>

<!-- total_quantity Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.total_quantity')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->total_quantity }}</b>
    </div>
</div>

<!-- notes Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_direct_transfers.fields.notes')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $transfer->notes }}</b>
    </div>
</div>

<!-- returned_quantity Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::ui.total_returned_qty')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control text-danger">{{ number_format($transfer->returned_quantity, 2) }}</b>
    </div>
</div>

<!-- return_status Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::ui.return_status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">
            {{ $transfer->return_status == 3 ? __('store::ui.full_return') : ($transfer->return_status == 1 ? __('store::ui.partial_return') : __('store::ui.no_return')) }}
        </b>
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
        @if ($transfer->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $transfer->attachment_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-4 me-1"></i> {{ $transfer->attachment }} (@lang('crud.download') / @lang('lang.view'))
                </a>
                @php
                    $ext = strtolower(pathinfo($transfer->attachment, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <a href="{{ $transfer->attachment_url }}" target="_blank" class="d-inline-block ms-2">
                        <img src="{{ $transfer->attachment_url }}" alt="attachment" class="rounded border" style="max-height: 40px; max-width: 80px; object-fit: cover;">
                    </a>
                @endif
            </div>
        @else
            <b class="form-control text-muted">@lang('lang.no_attachment')</b>
        @endif
    </div>
</div>

<div class="col-sm-12 mt-4">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>@lang('store::models/st_direct_transfers.items.product_id')</th>
                <th>@lang('store::models/st_direct_transfers.items.unit_id')</th>
                <th>@lang('store::models/st_direct_transfers.items.quantity')</th>
                @if(!$transfer->is_direct)
                <th>@lang('store::ui.received_quantity')</th>
                <th>@lang('store::ui.returned')</th>
                <th>@lang('store::ui.variance')</th>
                @endif
                <th>@lang('store::models/st_direct_transfers.items.unit_cost')</th>
                <th>@lang('store::models/st_direct_transfers.items.total_cost')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->unit_name }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                @if(!$transfer->is_direct)
                <td class="text-success fw-bold">{{ number_format($item->received_quantity, 2) }}</td>
                <td class="text-danger fw-bold">{{ number_format($item->returned_quantity, 2) }}</td>
                <td class="{{ $item->variance_quantity < 0 ? 'text-danger' : ($item->variance_quantity > 0 ? 'text-success' : 'text-muted') }}">
                    {{ number_format($item->variance_quantity, 2) }}
                </td>
                @endif
                <td>{{ number_format($item->unit_cost, 2) }}</td>
                <td class="fw-bold">{{ number_format($item->total_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
