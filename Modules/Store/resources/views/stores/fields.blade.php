{{-- <div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('store::models/st_stores.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($store) ? $store->translate($locale)->name : null, [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
    @endforeach
</div> --}}

<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('store::models/st_stores.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($store) ? $store->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[address]', $language . ' ' . __('store::models/st_stores.fields.address') . ':') !!}
            {!! Form::text($locale . '[address]', isset($store) ? $store->translate($locale)->address : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>



<div class="row">
    <!-- BranchId Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('branch_id', __('store::models/st_stores.fields.branch_id').':') !!}
        <x-select2-input name="branch_id" :placeholder="__('store::models/st_stores.fields.branch_id')" :list="$branches" :selected_id="old('branch_id', @optional($store)->branch_id)">
        </x-select2-input>
    </div>

    <!-- Manager User Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('manager_user_id', __('store::models/st_stores.fields.manager_user_id').':') !!}
        <x-select2-input name="manager_user_id" :placeholder="__('store::models/st_stores.fields.manager_user_id')" :list="$managers" :selected_id="old('manager_user_id', @optional($store)->manager_user_id)">
        </x-select2-input>
    </div>
</div>

<div class="row">
    <!-- Type Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('type', __('store::models/st_stores.fields.type').':') !!}
        <x-select2-input name="type" :placeholder="__('store::models/st_stores.fields.type')" :list="$types" :selected_id="old('type', @optional($store)->type)">
        </x-select2-input>
    </div>
</div>

<div class="row">
    <!-- Is Active Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('store::models/st_stores.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($store)->status ?? 1)">
        </x-select2-input>
    </div>
</div>




