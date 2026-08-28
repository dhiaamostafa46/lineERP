<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_kitchens.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($kitchen) ? $kitchen->translate($locale)->name : null, [
                'class' => 'form-control',
                'required'
            ]) !!}
        </div>
    @endforeach
</div>

<div class="row">
    <!-- Conversion Factor Field -->
   <div class="form-group col-sm-6 mb-3">
    {!! Form::label('barcode', __('basicdata::models/db_kitchens.fields.barcode') . ':') !!}
    {!! Form::text('barcode', isset($kitchen) ? $kitchen->barcode : null, [
        'class' => 'form-control',
        'pattern' => '[A-Za-z0-9]+',
        'title' => 'يمكنك إدخال أرقام وحروف فقط',
       
    ]) !!}
</div>

    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('basicdata::models/db_kitchens.fields.status') . ':') !!}
        <x-select2-input
            name="status"
            :placeholder="__('hr::lang.select_status')"
            :list="$statuses"
            :selected_id="old('status', @optional($kitchen)->status ?? 0)">
        </x-select2-input>
    </div>
</div>
