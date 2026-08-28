<!-- Id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->id }}</b>
    </div>
</div>

<!-- document_number Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.document_number')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->document_number }}</b>
    </div>
</div>

<!-- document_date Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.document_date')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->document_date ? $reservation->document_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- store_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.store_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->store->name ?? '' }}</b>
    </div>
</div>

<!-- status Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->status_text }}</b>
    </div>
</div>

<!-- total_items Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.total_items')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->total_items }}</b>
    </div>
</div>

<!-- total_quantity Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.total_quantity')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ number_format($reservation->total_quantity, 2) }}</b>
    </div>
</div>

<!-- total_value Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.total_value')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ number_format($reservation->total_value, 2) }}</b>
    </div>
</div>

<!-- notes Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.notes')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->notes }}</b>
    </div>
</div>

<!-- user_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_reservations.fields.user_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $reservation->user->name ?? '' }}</b>
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
        @if ($reservation->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $reservation->attachment_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-4 me-1"></i> {{ $reservation->attachment }} (@lang('crud.download') / @lang('lang.view'))
                </a>
                @php
                    $ext = strtolower(pathinfo($reservation->attachment, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <a href="{{ $reservation->attachment_url }}" target="_blank" class="d-inline-block ms-2">
                        <img src="{{ $reservation->attachment_url }}" alt="attachment" class="rounded border" style="max-height: 40px; max-width: 80px; object-fit: cover;">
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
                <th>@lang('store::models/st_reservations.fields.product_id')</th>
                <th>@lang('store::models/st_reservations.fields.unit_id')</th>
                <th>@lang('store::models/st_reservations.fields.quantity')</th>
                <th>@lang('store::models/st_reservations.fields.unit_cost')</th>
                <th>@lang('store::models/st_reservations.fields.total_cost')</th>
                <th>@lang('store::models/st_reservations.fields.notes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservation->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '' }}</td>
                <td>{{ $item->unit->name ?? '' }}</td>
                <td>{{ number_format($item->quantity, 2) }}</td>
                <td>{{ number_format($item->unit_cost, 2) }}</td>
                <td>{{ number_format($item->total_cost, 2) }}</td>
                <td>{{ $item->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
