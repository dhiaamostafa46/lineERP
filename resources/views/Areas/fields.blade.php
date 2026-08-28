<div class="row">
    @foreach (config('langs') as $locale => $language)
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label($locale . '[name]', $language . ' ' . __('models/Areas.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($Area) ? optional($Area->translate($locale))->name : null, [
            'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('models/Areas.fields.code') . ':') !!}
        {!! Form::text('code', old('code', @optional($Area)->code), ['class' => 'form-control']) !!}
    </div>
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('models/Areas.fields.status') . ':') !!}
        {!! Form::select('status', $statuses, old('status', @optional($Area)->status ?? \App\Models\Area::STATUS_ACTIVE), [
            'class' => 'form-control',
            'placeholder' => __('hr::lang.select_status'),
        ]) !!}
    </div>
</div>
