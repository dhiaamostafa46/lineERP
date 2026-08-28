<div class="row">
    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_units.fields.name') . ':', ['class' => 'form-label fw-bold']) !!}
            {!! Form::text($locale . '[name]', isset($unit) ? $unit->translate($locale)->name : null, [
                'class' => 'form-control',
                'placeholder' => __('basicdata::models/db_units.fields.name'),
            ]) !!}
        </div>
    @endforeach
</div>

<!-- Hidden Conversion Factor Field -->
<input type="hidden" name="conversion_factor" value="{{ old('conversion_factor', isset($unit) ? $unit->conversion_factor : 1) }}">

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_units.fields.status') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input name="status" :placeholder="__('basicdata::lang.select')" :list="$statuses" :selected_id="old('status', @optional($unit)->status ?? 1)">
        </x-select2-input>
    </div>
</div>
