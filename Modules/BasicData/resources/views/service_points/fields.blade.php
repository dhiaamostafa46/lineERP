<div class="row">
    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_service_points.fields.name') . ':', ['class' => 'form-label fw-bold']) !!}
            {!! Form::text($locale . '[name]', isset($servicePoint) ? $servicePoint->translate($locale)->name : null, [
                'class' => 'form-control',
                'placeholder' => __('basicdata::models/db_service_points.fields.name'),
                'required',
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <!-- Code Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('basicdata::models/db_service_points.fields.code') . ':', ['class' => 'form-label fw-bold']) !!}
        {!! Form::text('code', isset($servicePoint) ? $servicePoint->code : null, [
            'class' => 'form-control',
            'placeholder' => __('basicdata::models/db_service_points.fields.code'),
        ]) !!}
    </div>

    <!-- Type Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('basicdata::models/db_service_points.fields.type') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input
            name="type"
            :placeholder="__('basicdata::lang.select')"
            :list="$types"
            :selected_id="old('type', @optional($servicePoint)->type ?? 1)">
        </x-select2-input>
    </div>
</div>

<div class="row">
    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_service_points.fields.status') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input
            name="status"
            :placeholder="__('basicdata::lang.select')"
            :list="$statuses"
            :selected_id="old('status', @optional($servicePoint)->status ?? 1)">
        </x-select2-input>
    </div>
</div>
