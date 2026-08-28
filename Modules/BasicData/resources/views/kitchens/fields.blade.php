<div class="row">
    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_kitchens.fields.name') . ':', ['class' => 'form-label fw-bold']) !!}
            {!! Form::text($locale . '[name]', isset($kitchen) ? $kitchen->translate($locale)->name : null, [
                'class' => 'form-control',
                'placeholder' => __('basicdata::models/db_kitchens.fields.name'),
                'required',
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <!-- Barcode Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('barcode', __('basicdata::models/db_kitchens.fields.barcode') . ':', ['class' => 'form-label fw-bold']) !!}
        {!! Form::text('barcode', isset($kitchen) ? $kitchen->barcode : null, [
            'class' => 'form-control',
            'placeholder' => __('basicdata::models/db_kitchens.fields.barcode'),
        ]) !!}
    </div>

    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_kitchens.fields.status') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input
            name="status"
            :placeholder="__('basicdata::lang.select')"
            :list="$statuses"
            :selected_id="old('status', @optional($kitchen)->status ?? 1)">
        </x-select2-input>
    </div>
</div>
