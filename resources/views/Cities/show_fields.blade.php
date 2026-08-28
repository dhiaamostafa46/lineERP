<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Cities.fields.area')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($City->area)->name ?? '—' }} ({{ optional($City->area)->code ?? '—' }})</b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Cities.fields.code')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $City->code }}</b>
    </div>
</div>
@foreach (config('langs') as $locale => $language)
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">{{ $language }} — @lang('models/Cities.fields.name')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($City->translate($locale))->name ?? '' }}</b>
    </div>
</div>
@endforeach
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Cities.fields.status')</p>
    </div>
    <div class="col-8">
        <b class="form-control"><span class="{{ $City->status_badge }}">{{ $City->status_text }}</span></b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Cities.fields.created_at')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $City->created_at }}</b>
    </div>
</div>
