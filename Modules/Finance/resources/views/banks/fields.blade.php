<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('finance::models/fnc_bank.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($bank) ? $bank->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>
<div class="row">


    <!-- Account Number Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('account_number', __('finance::models/fnc_bank.fields.account_number') . ':') !!}
        {!! Form::text('account_number', isset($bank) ? $bank->account_number : null, [
            'class' => 'form-control',
            'maxlength' => '15',
            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '');"
        ]) !!}
    </div>

    <!-- IBAN Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('iban', __('finance::models/fnc_bank.fields.iban') . ':') !!}
        {!! Form::text('iban', isset($bank) ? $bank->iban : null, [
            'class' => 'form-control',
           
            'placeholder' => 'Example: SA0000000000000'
        ]) !!}
    </div>

    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('payment_method', __('finance::models/fnc_bank.fields.payment_method') . ':') !!} <span class="text-danger">*</span>
        <x-select2-input name="payment_method" :placeholder="__('hr::lang.select_payment_method')" :list="config('payment_methods.bank')" :selected_id="old('payment_method', $bank->payment_method ?? array_key_first(config('payment_methods.bank')))" required="true">
        </x-select2-input>
    </div>


    <!-- Status Field -->
    <div class="form-group col-sm-6 mb-3">
        {!! Form::label('status', __('finance::models/fnc_bank.fields.status') . ':') !!}
        <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', isset($bank) ? $bank->status : 0)">
        </x-select2-input>
    </div>
</div>
