<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('city_id', __('models/Companies.fields.city') . ':') !!}
        {!! Form::select(
            'city_id',
            ['' => __('models/Companies.placeholders.select_city')]
                + $cities->mapWithKeys(fn ($c) => [$c->id => $c->code . ' — ' . $c->name])->all(),
            old('city_id', @optional($Company)->city_id),
            ['class' => 'form-control'],
        ) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('models/Companies.fields.code') . ':') !!}
        {!! Form::text('code', old('code', @optional($Company)->code), ['class' => 'form-control', 'required' => true]) !!}
    </div>
</div>

<div class="row">
    @foreach (config('langs') as $locale => $language)
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('models/Companies.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($Company) ? optional($Company->translate($locale))->name : null, [
                'class' => 'form-control',
                'required' => true,
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('phone', __('models/Companies.fields.phone') . ':') !!}
        {!! Form::text('phone', old('phone', @optional($Company)->phone), ['class' => 'form-control']) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('email', __('models/Companies.fields.email') . ':') !!}
        {!! Form::email('email', old('email', @optional($Company)->email), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('contact_person', __('models/Companies.fields.contact_person') . ':') !!}
        {!! Form::text('contact_person', old('contact_person', @optional($Company)->contact_person), ['class' => 'form-control']) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('models/Companies.fields.status') . ':') !!}
        {!! Form::select(
            'status',
            $statuses,
            old('status', @optional($Company)->status ?? \App\Models\Company::STATUS_ACTIVE),
            ['class' => 'form-control', 'placeholder' => __('hr::lang.select_status')],
        ) !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-12 mb-3">
        {!! Form::label('address', __('models/Companies.fields.address') . ':') !!}
        {!! Form::textarea('address', old('address', @optional($Company)->address), ['class' => 'form-control', 'rows' => 3]) !!}
    </div>
</div>
