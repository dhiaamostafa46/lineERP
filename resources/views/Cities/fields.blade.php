<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('area_id', __('models/Cities.fields.area') . ':') !!}
        {!! Form::select(
            'area_id',
            ['' => __('models/Cities.placeholders.select_area')]
                + $areas->mapWithKeys(fn ($a) => [$a->id => $a->code . ' — ' . $a->name])->all(),
            old('area_id', @optional($City)->area_id),
            ['class' => 'form-control', 'required' => true],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('models/Cities.fields.code') . ':') !!}
        {!! Form::text('code', old('code', @optional($City)->code), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row">
    @foreach (config('langs') as $locale => $language)
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label($locale . '[name]', $language . ' ' . __('models/Cities.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($City) ? optional($City->translate($locale))->name : null, [
            'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('models/Cities.fields.status') . ':') !!}
        {!! Form::select('status', $statuses, old('status', @optional($City)->status ?? \App\Models\City::STATUS_ACTIVE), [
            'class' => 'form-control',
            'placeholder' => __('hr::lang.select_status'),
        ]) !!}
    </div>
</div>
