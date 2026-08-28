<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.code')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->code }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.city')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($Company->city)->name ?? '—' }} @if ($Company->city)
                ({{ $Company->city->code }})
            @endif
        </b>
    </div>
</div>
@foreach (config('langs') as $locale => $language)
    <div class="col-sm-12 row mb-3">
        <div class="col-4 my-auto">
            <p class="fs-5">{{ $language }} — @lang('models/Companies.fields.name')</p>
        </div>
        <div class="col-8">
            <b class="form-control">{{ optional($Company->translate($locale))->name ?? '' }}</b>
        </div>
    </div>
@endforeach
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.phone')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->phone ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.email')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->email ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.contact_person')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->contact_person ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.address')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->address ?? '—' }}</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.status')</p>
    </div>
    <div class="col-8">
        <b class="form-control"><span class="{{ $Company->status_badge }}">{{ $Company->status_text }}</span></b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Companies.fields.created_at')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Company->created_at }}</b>
    </div>
</div>
