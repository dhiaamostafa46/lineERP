@foreach (config('langs') as $locale => $language)
<!-- Name Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label($locale . '[name]', $language . ' ' . __('hr::models/hr_assets.fields.name') . ':') !!}
    {!! Form::text($locale . '[name]', isset($asset) ? $asset->translate($locale)->name : null, [
    'class' => 'form-control',
    ]) !!}
</div>
@endforeach


<!-- Department Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('department_id', __('hr::models/hr_assets.fields.department_id') . ':') !!}
    <x-select2-input name="department_id" :placeholder="__('hr::lang.select_department')" :list="$departments"
        :selected_id="old('department_id', @optional($asset)->department_id??0)">
    </x-select2-input>
</div>


<!-- Type Id Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('type_id', __('hr::models/hr_assets.fields.type_id') . ':') !!}
    <x-select2-input name="type_id" :placeholder="__('hr::lang.select_type')" :list="$types"
        :selected_id="old('type_id', @optional($asset)->type_id??0)">
    </x-select2-input>
</div>


<!-- Is New Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('is_new', __('hr::models/hr_assets.fields.is_new') . ':') !!}
    {!! Form::select('is_new', ['1' => __('lang.yes'), '0' => __('lang.no')], null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('hr::models/hr_assets.fields.status') . ':') !!}
    <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses"
        :selected_id="old('status', @optional($asset)->status??0)">
    </x-select2-input>
</div>

<!-- Note Field -->
<div class="form-group col-sm-12 mb-3">
    {!! Form::label('note', __('hr::models/hr_assets.fields.note') . ':') !!}
    {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
</div>
