<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('finance::models/fnc_safe.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($bank) ? $bank->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>
<div class="row">



    {!! Form::hidden('payment_method', array_key_first(config('payment_methods.cash'))) !!}




    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('finance::models/fnc_safe.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', isset($bank) ? $bank->status : 0)">
        </x-select2-input>
    </div>
</div>
