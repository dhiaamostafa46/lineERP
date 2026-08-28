<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_service_points.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($servicePoint) ? $servicePoint->translate($locale)->name : null, [
                'class' => 'form-control',
                'required'
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <!-- Code Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('code', __('basicdata::models/db_service_points.fields.code') . ':') !!}
        {!! Form::text('code', isset($servicePoint) ? $servicePoint->code : null, [
            'class' => 'form-control',
            'pattern' => '[A-Za-z0-9]+',
            'title' => 'يمكنك إدخال أرقام وحروف فقط',
           
        ]) !!}
    </div>

    <!-- Type Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('basicdata::models/db_service_points.fields.type') . ':') !!}
        <x-select2-input
            name="type"
            :placeholder="__('hr::lang.select_type')"
            :list="$types"
            :selected_id="old('type', @optional($servicePoint)->type ?? 1)">
        </x-select2-input>
    </div>
</div>

<div class="row">
    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_service_points.fields.status') . ':') !!}
        <x-select2-input
            name="status"
            :placeholder="__('hr::lang.select_status')"
            :list="$statuses"
            :selected_id="old('status', @optional($servicePoint)->status ?? 1)">
        </x-select2-input>
    </div>
</div>
