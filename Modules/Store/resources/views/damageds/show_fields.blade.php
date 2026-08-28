<!-- Id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.id')
        </p>
    </div>

    <div class="col-8">
        <b class="form-control">{{ $damaged->id }}</b>
    </div>
</div>



<!-- document_number Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.document_number')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->document_number }}</b>
    </div>
</div>

<!-- document_date Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.document_date')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->document_date ? $damaged->document_date->format('Y-m-d') : '' }}</b>
    </div>
</div>

<!-- store_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.store_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->store->name ?? '' }}</b>
    </div>
</div>

<!-- status Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.status')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->status_text }}</b>
    </div>
</div>

<!-- total_items Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.total_items')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->total_items }}</b>
    </div>
</div>

<!-- total_quantity Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.total_quantity')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->total_quantity }}</b>
    </div>
</div>

<!-- total_value Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.total_value')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ number_format($damaged->total_value, 2) }}</b>
    </div>
</div>

<!-- notes Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.notes')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->notes }}</b>
    </div>
</div>

<!-- user_id Field -->
<div class="col-sm-6 row">
    <div class="col-4 my-auto">
        <p class="fs-5 ">
            @lang('store::models/st_damageds.fields.user_id')
        </p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $damaged->user->name ?? '' }}</b>
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
        @if ($damaged->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $damaged->attachment_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-file fs-4 me-1"></i> {{ $damaged->attachment }} (@lang('crud.download') / @lang('lang.view'))
                </a>
                @php
                    $ext = strtolower(pathinfo($damaged->attachment, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    <a href="{{ $damaged->attachment_url }}" target="_blank" class="d-inline-block ms-2">
                        <img src="{{ $damaged->attachment_url }}" alt="attachment" class="rounded border" style="max-height: 40px; max-width: 80px; object-fit: cover;">
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
                <th>@lang('store::models/st_damageds.items.product_id')</th>
                <th>@lang('store::models/st_damageds.items.unit_id')</th>
                <th>@lang('store::models/st_damageds.items.quantity')</th>
                <th>@lang('store::models/st_damageds.items.unit_cost')</th>
                <th>@lang('store::models/st_damageds.items.total_cost')</th>
                <th>@lang('store::models/st_damageds.items.notes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($damaged->items as $item)
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
