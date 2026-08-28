<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_categories.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($Category) ? $Category->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>


<div class="row">
    <!-- Sort Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('sort', __('basicdata::models/db_categories.fields.sort') . ':') !!}
        {!! Form::number('sort', old('sort', isset($Category) ? $Category->sort : 1), ['class' => 'form-control', 'min' => 1]) !!}
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_categories.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($Category)->status ?? 1)">
        </x-select2-input>
    </div>

     <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('basicdata::models/db_categories.fields.type') . ':') !!}
        <x-select2-input name="type" :placeholder="__('hr::lang.select_status')" :list="$types" :selected_id="old('type', @optional($Category)->type ?? 1)">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('parent_id', __('basicdata::models/db_categories.fields.parent_id') . ':') !!}
        <x-select2-input name="parent_id" :placeholder="__('basicdata::models/db_categories.fields.parent_id')" :list="$parent_categories" :selected_id="old('parent_id', @optional($Category)->parent_id)">
        </x-select2-input>
    </div>


    <!-- Image Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('img', __('basicdata::models/db_categories.fields.img') . ':') !!}
        {!! Form::file('img', ['class' => 'form-control']) !!}

        @if (isset($Category) && $Category->img)
            <div class="mt-2">
                <img src="{{ $Category->imgThumbPath }}" alt="Category Image" style="max-height:100px;">
            </div>
        @endif
    </div>
</div>
