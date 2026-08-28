<div class="row">
    @foreach (config('langs') as $locale => $language)
    <!-- Name Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('name', $language .' '. __('hr::models/hr_departments.fields.name') . ':') !!}
        {!! Form::text($locale . '[name]', isset($department) ? $department->translate($locale)->name : null, [
        'class' => 'form-control',
        ]) !!}
    </div>
    @endforeach
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_jobs.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
        :selected_id="old('status', @optional($department)->status??0)">
    </x-select2-input>
</div>

<!-- Code Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('code', __('hr::models/hr_departments.fields.code') . ':') !!}
    {!! Form::text('code', null, ['class' => 'form-control']) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type', __('hr::models/hr_departments.fields.type') . ':') !!}
    <x-select2-input name="type" :placeholder="__('hr::lang.select_type')" :list="$types"
        :selected_id="old('type', @optional($department)->type??0)">
    </x-select2-input>
</div>

<!-- Parent Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('parent_id', __('hr::models/hr_departments.fields.parent_id') . ':') !!}
    <x-select2-input name="parent_id" :placeholder="__('hr::models/hr_departments.fields.select_department')"
        :list="$parents" :selected_id="old('parent_id', @optional($department)->parent_id??0)">
    </x-select2-input>
</div>

<!-- Owner Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('owner_id', __('hr::models/hr_departments.fields.owner_id') . ':') !!}
    <x-select2-input name="owner_id" :placeholder="__('hr::models/hr_departments.fields.select_manager')"
        :list="$owners" :selected_id="old('owner_id', @optional($department)->owner_id??0)">
    </x-select2-input>
</div>
