<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_units.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($unit) ? $unit->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>


<div class="row">
    <!-- Conversion Factor Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('conversion_factor', __('basicdata::models/db_units.fields.conversion_factor') . ':') !!}
        {!! Form::number('conversion_factor', old('conversion_factor', isset($unit) ? $unit->conversion_factor : 1), ['class' => 'form-control', 'min' => 0, 'step' => 'any']) !!}
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_units.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($unit)->status ?? 0)">
        </x-select2-input>
    </div>

</div>




