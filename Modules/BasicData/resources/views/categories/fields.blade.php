<div class="row">
    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_categories.fields.name') . ':', ['class' => 'form-label fw-bold']) !!}
            {!! Form::text($locale . '[name]', isset($Category) ? $Category->translate($locale)->name : null, [
                'class' => 'form-control',
                'placeholder' => __('basicdata::models/db_categories.fields.name'),
            ]) !!}
        </div>
    @endforeach
</div>

<!-- Hidden Sort Field -->
<input type="hidden" name="sort" value="{{ old('sort', isset($Category) ? $Category->sort : 1) }}">

<div class="row">
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_categories.fields.status') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input name="status" :placeholder="__('basicdata::lang.select')" :list="$statuses" :selected_id="old('status', @optional($Category)->status ?? 1)">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('basicdata::models/db_categories.fields.type') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input name="type" :placeholder="__('basicdata::lang.select')" :list="$types" :selected_id="old('type', @optional($Category)->type ?? 1)">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('parent_id', __('basicdata::models/db_categories.fields.parent_id') . ':', ['class' => 'form-label fw-bold']) !!}
        <x-select2-input name="parent_id" :placeholder="__('basicdata::models/db_categories.fields.parent_id')" :list="$parent_categories" :selected_id="old('parent_id', @optional($Category)->parent_id)">
        </x-select2-input>
    </div>

    <!-- Image Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('img', __('basicdata::models/db_categories.fields.img') . ':', ['class' => 'form-label fw-bold']) !!}
        {!! Form::file('img', ['class' => 'form-control', 'accept' => 'image/*']) !!}

        @if (isset($Category) && $Category->img)
            <div class="mt-2">
                <img src="{{ $Category->imgThumbPath }}" alt="Category Image" class="img-thumbnail" style="max-height:80px;">
            </div>
        @endif
    </div>
</div>
