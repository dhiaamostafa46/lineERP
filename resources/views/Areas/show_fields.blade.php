<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Areas.fields.code')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Area->code }}</b>
    </div>
</div>
@foreach (config('langs') as $locale => $language)
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">{{ $language }} — @lang('models/Areas.fields.name')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ optional($Area->translate($locale))->name ?? '' }}</b>
    </div>
</div>
@endforeach
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Areas.fields.status')</p>
    </div>
    <div class="col-8">
        <b class="form-control"><span class="{{ $Area->status_badge }}">{{ $Area->status_text }}</span></b>
    </div>
</div>
<div class="col-sm-12 row mb-3">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Areas.fields.cities_count')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Area->cities()->count() }}</b>
    </div>
</div>
<div class="col-sm-12 row">
    <div class="col-4 my-auto">
        <p class="fs-5">@lang('models/Areas.fields.created_at')</p>
    </div>
    <div class="col-8">
        <b class="form-control">{{ $Area->created_at }}</b>
    </div>
</div>
