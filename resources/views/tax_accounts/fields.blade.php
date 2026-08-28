<div class="row">
    @foreach (config('langs') as $locale => $language)
        <!-- Name Field -->
        <div class="form-group col-sm-6 mb-3">
            {!! Form::label($locale . '[name]', $language . ' ' . __('models/tax_accounts.fields.name') . ':') !!}
            {!! Form::text($locale . '[name]', isset($taxAccount) ? $taxAccount->translate($locale)->name : null, [
                'class' => 'form-control',
            ]) !!}
        </div>
    @endforeach
</div>

<!-- Rate Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('rate', __('models/tax_accounts.fields.rate') . ':') !!}
    {!! Form::number('rate', null, ['class' => 'form-control', 'step' => '0.01']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('status', __('models/tax_accounts.fields.status') . ':') !!}
    {!! Form::select('status', $statuses, null, ['class' => 'form-control custom-select']) !!}
</div>
