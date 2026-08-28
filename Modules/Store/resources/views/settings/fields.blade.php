<!-- Costing Method Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('costing_method', __('store::models/st_setting.fields.costing_method').':') !!}
    <x-select2-input name="costing_method" :placeholder="__('store::models/st_setting.fields.costing_method')" :list="$CostingMethods" :selected_id="old('costing_method', @optional($setting)->costing_method)">
    </x-select2-input>
</div>

<!-- Default Transfer Type Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('default_transfer_type', __('store::models/st_setting.fields.default_transfer_type').':') !!}
    <x-select2-input name="default_transfer_type" :placeholder="__('store::models/st_setting.fields.default_transfer_type')" :list="__('store::models/st_setting.types')" :selected_id="old('default_transfer_type', @optional($setting)->default_transfer_type)">
    </x-select2-input>
</div>

<!-- Stock Transfer Prefix Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('stock_transfer_prefix', __('store::models/st_setting.fields.stock_transfer_prefix').':') !!}
    {!! Form::text('stock_transfer_prefix', old('stock_transfer_prefix', @optional($setting)->stock_transfer_prefix), ['class' => 'form-control']) !!}
</div>

<!-- Stocktake Prefix Field -->
<div class="form-group col-sm-6 mb-3">
    {!! Form::label('stocktake_prefix', __('store::models/st_setting.fields.stocktake_prefix').':') !!}
    {!! Form::text('stocktake_prefix', old('stocktake_prefix', @optional($setting)->stocktake_prefix), ['class' => 'form-control']) !!}
</div>

<div class="clearfix"></div>

<!-- Allow Negative Stock Field -->
<div class="form-group col-sm-6 mb-3">
    <div class="form-check">
        {!! Form::hidden('allow_negative_stock', 0) !!}
        {!! Form::checkbox('allow_negative_stock', 1, old('allow_negative_stock', @optional($setting)->allow_negative_stock), ['class' => 'form-check-input']) !!}
        {!! Form::label('allow_negative_stock', __('store::models/st_setting.fields.allow_negative_stock'), ['class' => 'form-check-label']) !!}
    </div>
</div>

<!-- Auto Calculate Cost Field -->
<div class="form-group col-sm-6 mb-3">
    <div class="form-check">
        {!! Form::hidden('auto_calculate_cost', 0) !!}
        {!! Form::checkbox('auto_calculate_cost', 1, old('auto_calculate_cost', @optional($setting)->auto_calculate_cost), ['class' => 'form-check-input']) !!}
        {!! Form::label('auto_calculate_cost', __('store::models/st_setting.fields.auto_calculate_cost'), ['class' => 'form-check-label']) !!}
    </div>
</div>

<!-- Stock Valuation Enabled Field -->
<div class="form-group col-sm-6 mb-3">
    <div class="form-check">
        {!! Form::hidden('stock_valuation_enabled', 0) !!}
        {!! Form::checkbox('stock_valuation_enabled', 1, old('stock_valuation_enabled', @optional($setting)->stock_valuation_enabled), ['class' => 'form-check-input']) !!}
        {!! Form::label('stock_valuation_enabled', __('store::models/st_setting.fields.stock_valuation_enabled'), ['class' => 'form-check-label']) !!}
    </div>
</div>

<!-- Auto Serial Number Field -->
<div class="form-group col-sm-6 mb-3">
    <div class="form-check">
        {!! Form::hidden('auto_serial_number', 0) !!}
        {!! Form::checkbox('auto_serial_number', 1, old('auto_serial_number', @optional($setting)->auto_serial_number), ['class' => 'form-check-input']) !!}
        {!! Form::label('auto_serial_number', __('store::models/st_setting.fields.auto_serial_number'), ['class' => 'form-check-label']) !!}
    </div>
</div>
