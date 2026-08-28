<div class="row">



    <div class="row">
        @foreach (config('langs') as $locale => $language)
            <!-- Name Field -->
            <div class="form-group col-sm-6 mb-3">
                {!! Form::label($locale . '[name]', $language . ' ' . __('accusoft::models/as_cost_centers.fields.name') . ':') !!}
                {!! Form::text($locale . '[name]', isset($accountMapping) ? $accountMapping->translate($locale)->name : null, [
                    'class' => 'form-control',
                ]) !!}
            </div>
        @endforeach
    </div>
    <!-- Name Field -->


    <!-- Tree Account Id Field -->
    <div class="form-group col-sm-12 mb-5">
        {!! Form::label('account_id', __('accusoft::models/as_account_mappings.fields.account_id') . ':') !!}
        {!! Form::select('account_id', $accounts, @optional($accountMapping)->account_id, [
            'class' => 'form-control',
            'required',
            'data-control' => 'select2',
        ]) !!}
    </div>

</div>
