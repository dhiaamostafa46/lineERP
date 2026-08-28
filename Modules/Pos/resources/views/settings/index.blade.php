@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Pos Settings') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card">
            {!! Form::model($setting, ['route' => ['pos.settings.update'], 'method' => 'post']) !!}

            <div class="card-body">
                <div class="row">
                    <!-- Default Customer -->
                    <div class="form-group col-sm-6">
                        {!! Form::label('default_customer_id', __('Default Customer').':') !!}
                        {!! Form::select('default_customer_id', $customers, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Customer')]) !!}
                    </div>

                    <!-- Max Discount -->
                    <div class="form-group col-sm-6">
                        {!! Form::label('max_discount_percent', __('Max Discount Percent').':') !!}
                        {!! Form::number('max_discount_percent', null, ['class' => 'form-control', 'step' => '0.01']) !!}
                    </div>

                    <!-- Accounts -->
                    <h5 class="col-sm-12 mt-4 text-primary"><i class="fas fa-book"></i> {{ __('Accounting integration') }}</h5>
                    <hr class="col-sm-12">
                    
                    <div class="form-group col-sm-4">
                        {!! Form::label('main_safe_account_id', __('Main Safe Account').':') !!}
                        {!! Form::select('main_safe_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>
                    
                    <div class="form-group col-sm-4">
                        {!! Form::label('sales_account_id', __('Sales Account').':') !!}
                        {!! Form::select('sales_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-4">
                        {!! Form::label('discount_account_id', __('Discount Account').':') !!}
                        {!! Form::select('discount_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-4">
                        {!! Form::label('shortage_account_id', __('Cash Shortage Account').':') !!}
                        {!! Form::select('shortage_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-4">
                        {!! Form::label('overage_account_id', __('Cash Overage Account').':') !!}
                        {!! Form::select('overage_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-4">
                        {!! Form::label('vat_account_id', __('VAT Account').':') !!}
                        {!! Form::select('vat_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-6">
                        {!! Form::label('cogs_account_id', __('COGS Account').':') !!}
                        {!! Form::select('cogs_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <div class="form-group col-sm-6">
                        {!! Form::label('inventory_account_id', __('Inventory Account').':') !!}
                        {!! Form::select('inventory_account_id', $accounts, null, ['class' => 'form-control custom-select', 'placeholder' => __('Select Account')]) !!}
                    </div>

                    <!-- Operational Settings -->
                    <h5 class="col-sm-12 mt-4 text-primary"><i class="fas fa-cogs"></i> {{ __('Operational Settings') }}</h5>
                    <hr class="col-sm-12">
                    
                    <div class="form-group col-sm-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('auto_journal_entry', 1, $setting->auto_journal_entry, ['class' => 'custom-control-input', 'id' => 'auto_journal_entry']) !!}
                            <label class="custom-control-label" for="auto_journal_entry">{{ __('Auto Journal Entry') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('allow_negative_stock', 1, $setting->allow_negative_stock, ['class' => 'custom-control-input', 'id' => 'allow_negative_stock']) !!}
                            <label class="custom-control-label" for="allow_negative_stock">{{ __('Allow Negative Stock') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('auto_print', 1, $setting->auto_print, ['class' => 'custom-control-input', 'id' => 'auto_print']) !!}
                            <label class="custom-control-label" for="auto_print">{{ __('Auto Print Receipt') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('auto_open_drawer', 1, $setting->auto_open_drawer, ['class' => 'custom-control-input', 'id' => 'auto_open_drawer']) !!}
                            <label class="custom-control-label" for="auto_open_drawer">{{ __('Auto Open Drawer') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3 mt-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('allow_price_modification', 1, $setting->allow_price_modification, ['class' => 'custom-control-input', 'id' => 'allow_price_modification']) !!}
                            <label class="custom-control-label" for="allow_price_modification">{{ __('Allow Price Modification') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3 mt-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('allow_discount_modification', 1, $setting->allow_discount_modification, ['class' => 'custom-control-input', 'id' => 'allow_discount_modification']) !!}
                            <label class="custom-control-label" for="allow_discount_modification">{{ __('Allow Discount Modification') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3 mt-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('show_available_qty', 1, $setting->show_available_qty, ['class' => 'custom-control-input', 'id' => 'show_available_qty']) !!}
                            <label class="custom-control-label" for="show_available_qty">{{ __('Show Available Qty') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-3 mt-3">
                        <div class="custom-control custom-switch">
                            {!! Form::checkbox('enable_returns', 1, $setting->enable_returns, ['class' => 'custom-control-input', 'id' => 'enable_returns']) !!}
                            <label class="custom-control-label" for="enable_returns">{{ __('Enable POS Returns') }}</label>
                        </div>
                    </div>

                    <div class="form-group col-sm-6 mt-4">
                        {!! Form::label('print_copies_count', __('Print Copies Count').':') !!}
                        {!! Form::number('print_copies_count', null, ['class' => 'form-control', 'step' => '1', 'min' => '1']) !!}
                    </div>

                    <div class="form-group col-sm-6 mt-4">
                        {!! Form::label('session_timeout_minutes', __('Session Timeout (Minutes)').':') !!}
                        {!! Form::number('session_timeout_minutes', null, ['class' => 'form-control', 'step' => '1', 'min' => '0']) !!}
                        <small class="text-muted">{{ __('Set to 0 to disable') }}</small>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
