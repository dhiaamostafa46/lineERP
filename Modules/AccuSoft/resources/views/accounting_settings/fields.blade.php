<div class="row gy-4">

    {{-- ================= General Settings ================= --}}
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-light border-bottom">
                <h6 class="card-title mb-0 py-1 text-primary">
                    <i class="fas fa-cog me-2"></i>@lang('accusoft::models/as_accounting_settings.sections.general')
                </h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="form-group col-12">
                        {!! Form::label('currency', __('accusoft::models/as_accounting_settings.fields.currency'), ['class' => 'form-label']) !!}
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            {!! Form::text('currency', null, [
    'class' => 'form-control',
    'placeholder' => 'SAR',
    'readonly' => true,
]) !!}
                        </div>
                    </div>

                    <div class="form-group col-12">
                        {!! Form::label('decimal_places', __('accusoft::models/as_accounting_settings.fields.decimal_places'), ['class' => 'form-label']) !!}
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-coins"></i></span>
                            {!! Form::number('decimal_places', null, [
    'class' => 'form-control',
    'min' => 0,
    'max' => 6,
]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Journal Settings ================= --}}
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-light border-bottom">
                <h6 class="card-title mb-0 py-1 text-primary">
                    <i class="fas fa-book me-2"></i>@lang('accusoft::models/as_accounting_settings.sections.journal')
                </h6>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="form-group col-md-6">
                        {!! Form::label('journal_prefix', __('accusoft::models/as_accounting_settings.fields.journal_prefix'), ['class' => 'form-label']) !!}
                        {!! Form::text('journal_prefix', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'JE']) !!}
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('journal_next_number', __('accusoft::models/as_accounting_settings.fields.journal_next_number'), ['class' => 'form-label']) !!}
                        {!! Form::number('journal_next_number', null, ['class' => 'form-control form-control-sm', 'min' => 1]) !!}
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-check form-switch mb-2">
                            {!! Form::hidden('allow_backdated_entries', 0) !!}
                            {!! Form::checkbox('allow_backdated_entries', 1, null, ['class' => 'form-check-input', 'id' => 'allow_backdated_entries']) !!}
                            {!! Form::label('allow_backdated_entries', __('accusoft::models/as_accounting_settings.fields.allow_backdated_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch mb-2">
                            {!! Form::hidden('allow_future_dated_entries', 0) !!}
                            {!! Form::checkbox('allow_future_dated_entries', 1, null, ['class' => 'form-check-input', 'id' => 'allow_future_dated_entries']) !!}
                            {!! Form::label('allow_future_dated_entries', __('accusoft::models/as_accounting_settings.fields.allow_future_dated_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('hr_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('hr_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'hr_auto_post_journal_entries']) !!}
                            {!! Form::label('hr_auto_post_journal_entries', __('hr::lang.auto_post_journal_entries', ['default' => 'الترحيل التلقائي لعمليات الموارد البشرية']), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('vehicle_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('vehicle_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'vehicle_auto_post_journal_entries']) !!}
                            {!! Form::label('vehicle_auto_post_journal_entries', __('accusoft::models/as_accounting_settings.fields.vehicle_auto_post_journal_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('driver_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('driver_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'driver_auto_post_journal_entries']) !!}
                            {!! Form::label('driver_auto_post_journal_entries', __('accusoft::models/as_accounting_settings.fields.driver_auto_post_journal_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('store_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('store_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'store_auto_post_journal_entries']) !!}
                            {!! Form::label('store_auto_post_journal_entries', __('accusoft::models/as_accounting_settings.fields.store_auto_post_journal_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('sales_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('sales_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'sales_auto_post_journal_entries']) !!}
                            {!! Form::label('sales_auto_post_journal_entries', __('accusoft::models/as_accounting_settings.fields.sales_auto_post_journal_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                        <div class="form-check form-switch border-top pt-2">
                            {!! Form::hidden('purchase_auto_post_journal_entries', 0) !!}
                            {!! Form::checkbox('purchase_auto_post_journal_entries', 1, null, ['class' => 'form-check-input', 'id' => 'purchase_auto_post_journal_entries']) !!}
                            {!! Form::label('purchase_auto_post_journal_entries', __('accusoft::models/as_accounting_settings.fields.purchase_auto_post_journal_entries'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Security & Locking ================= --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-bottom">
                <h6 class="card-title mb-0 py-1 text-primary">
                    <i class="fas fa-lock me-2"></i>@lang('accusoft::models/as_accounting_settings.sections.security')
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center gy-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('lock_period_pwd_enabled', 1, null, ['class' => 'form-check-input', 'id' => 'lock_period_pwd_enabled']) !!}
                            {!! Form::label('lock_period_pwd_enabled', __('accusoft::models/as_accounting_settings.fields.lock_period_pwd_enabled'), ['class' => 'form-check-label fw-bold']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('lock_period_pwd', __('accusoft::models/as_accounting_settings.fields.lock_period_pwd'), ['class' => 'form-label']) !!}
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                {!! Form::password('lock_period_pwd', ['class' => 'form-control', 'placeholder' => '******']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>