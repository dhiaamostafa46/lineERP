<div class="col-12">
    <h3 class="mb-5 text-primary">@lang('pos::models/devices.fields.basic_info')</h3>
    <div class="row">
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('name', __('pos::models/devices.fields.name').':') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
                </div>
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('branch_id', __('pos::models/devices.fields.branch_id').':') !!}
                    <x-select2-input name="branch_id" :placeholder="__('pos::models/devices.fields.select_branch')" :list="$branches" :selected_id="old('branch_id', @optional($device)->branch_id ?? $defaultBranchId ?? null)"></x-select2-input>
                </div>
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('store_id', __('pos::models/devices.fields.store_id').':') !!}
                    <x-select2-input name="store_id" :placeholder="__('pos::models/devices.fields.select_store')" :list="$stores" :selected_id="old('store_id', @optional($device)->store_id ?? $defaultStoreId ?? null)"></x-select2-input>
                </div>
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('default_customer_id', __('invoices::models/sales_invoices.fields.customer_id').':') !!}
                    @php
                        $selectedCustomer = old('default_customer_id', @optional($device)->default_customer_id ?? null);
                        $selectedCustomerName = $selectedCustomer ? (\App\Models\invApp\InvCustomer::find($selectedCustomer)->name ?? '') : '';
                    @endphp
                    {!! Form::select('default_customer_id', $selectedCustomer ? [$selectedCustomer => $selectedCustomerName] : [], $selectedCustomer, ['class' => 'form-select select2-ajax-customers', 'data-selected' => $selectedCustomer, 'placeholder' => __('lang.select'), 'required' => 'required']) !!}
                </div>
                
                {!! Form::hidden('is_active', old('is_active', isset($device) ? $device->is_active : 1)) !!}
                
                <div class="form-group col-sm-6 mb-3">
                    <div class="form-check form-switch mt-8">
                        {!! Form::hidden('is_users_linked', 0, ['class' => 'form-check-input']) !!}
                        {!! Form::checkbox('is_users_linked', '1', old('is_users_linked', @optional($device)->is_users_linked ?? false), ['class' => 'form-check-input', 'id' => 'is_users_linked', 'onchange' => 'toggleUsersSelect()']) !!}
                        {!! Form::label('is_users_linked', __('pos::models/devices.fields.is_users_linked'), ['class' => 'form-check-label']) !!}
                    </div>
                </div>
                <div class="form-group col-sm-12 mb-3" id="users_select_container" style="display: {{ old('is_users_linked', @optional($device)->is_users_linked ?? false) ? 'block' : 'none' }};">
                    {!! Form::label('linked_users', __('pos::models/devices.fields.linked_users')) !!}
                    @php
                        $selectedUsers = old('linked_users', @optional($device)->linked_users ? (is_array(@optional($device)->linked_users) ? @optional($device)->linked_users : json_decode(@optional($device)->linked_users, true)) : []);
                    @endphp
                    {!! Form::select('linked_users[]', $users, $selectedUsers, ['class' => 'form-select', 'data-control' => 'select2', 'multiple' => 'multiple', 'data-placeholder' => __('lang.select')]) !!}
                </div>
            </div>
            
            <hr class="my-8">
            <h3 class="mb-5 text-primary">@lang('accusoft::models/as_setting.accounting_guidance')</h3>
            <div class="row">
                
                @php
                    $accFields = [
                        'sales_account_id' => 'accusoft::models/as_setting.sales_account',
                        'discount_account_id' => 'accusoft::models/as_setting.sales_discount_account',
                        'shortage_account_id' => 'accusoft::models/as_setting.shortage_account',
                        'overage_account_id' => 'accusoft::models/as_setting.overage_account',
                        'vat_account_id' => 'accusoft::models/as_setting.sales_tax_account',
                        'cogs_account_id' => 'accusoft::models/as_setting.cost_of_sales_account',
                        'main_safe_account_id' => 'accusoft::models/as_setting.main_safe_account',
                        'expense_account_id' => 'accusoft::models/as_setting.expense_account',
                    ];
                @endphp
                @foreach($accFields as $field => $label)
                    <div class="form-group col-sm-6 mb-3">
                        {!! Form::label($field, __($label).':') !!}
                        <x-select2-input name="{{ $field }}" :placeholder="__('lang.select')" :list="$accounts" :selected_id="old($field, @optional($device)->$field ?? ($default_accounts[$field] ?? null))"></x-select2-input>
                    </div>
                @endforeach
               
            </div>

            <hr class="my-8">
            <h3 class="mb-5 text-primary">@lang('accusoft::models/as_setting.general_settings')</h3>
            <div class="row">
                @php
                    $opsBooleans = [
                        'allow_negative_stock' => 'pos::models/settings.allow_negative_stock',
                        'auto_print_receipt' => 'pos::models/settings.auto_print',
                        'allow_price_modification' => 'pos::models/settings.allow_price_modification',
                        'allow_discount_modification' => 'pos::models/settings.allow_discount_modification',
                        'show_available_qty' => 'pos::models/settings.show_available_qty',
                        'enable_pos_returns' => 'pos::models/settings.enable_returns',
                        'enable_cash_movements' => 'pos::models/settings.enable_cash_movements',
                        'auto_journal_entry'=>'accusoft::models/as_setting.auto_journal_entry',
                        'prices_include_tax' => 'pos::models/settings.prices_include_tax',
                        'send_to_zatca_phase2' => 'pos::models/settings.send_to_zatca_phase2',
                    ];
                @endphp
                <div class="form-group col-sm-6 mb-3">
                    {!! Form::label('print_copies_count', __('pos::models/settings.print_copies_count').':') !!}
                    {!! Form::number('print_copies_count', old('print_copies_count', @optional($device)->print_copies_count ?? 1), ['class' => 'form-control', 'min' => 1]) !!}
                </div>
                <div class="col-sm-12"></div>
                @foreach($opsBooleans as $field => $label)
                    <div class="form-group col-sm-4 mb-3">
                        <div class="form-check form-switch mt-4">
                            {!! Form::hidden($field, 0, ['class' => 'form-check-input']) !!}
                            @php $defaultVal = ($field === 'prices_include_tax') ? false : true; @endphp
                            {!! Form::checkbox($field, '1', old($field, @optional($device)->$field ?? $defaultVal), ['class' => 'form-check-input', 'id' => $field]) !!}
                            {!! Form::label($field, __($label), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                @endforeach
                
            </div>

            <hr class="my-8">
            <h3 class="mb-5 text-primary">@lang('pos::models/payment_methods.plural')</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="bg-light-primary text-gray-800 fw-bold">
                            <th style="width: 250px;">@lang('pos::models/payment_methods.fields.name')</th>
                            <th>@lang('pos::models/payment_methods.fields.account_id')</th>
                            <th class="text-center" style="width: 100px;">@lang('pos::models/payment_methods.fields.is_active')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($fixedMethods as $type => $name)
                            @php
                                $defaultForType = null;
                                if (!isset($device)) {
                                    if ($type === 'cash') $defaultForType = $default_accounts['cash_account_id'] ?? null;
                                    elseif (in_array($type, ['card', 'transfer', 'installment'])) $defaultForType = $default_accounts['bank_account_id'] ?? null;
                                    elseif ($type === 'credit') $defaultForType = $default_accounts['customer_account_id'] ?? null;
                                }
                                
                                $accountId = old('payment_accounts.' . $type, isset($deviceMethods[$type]) ? $deviceMethods[$type]->account_id : $defaultForType);
                                $isActive = old('payment_active.' . $type, isset($deviceMethods[$type]) ? $deviceMethods[$type]->is_active : true);
                            @endphp
                            <tr>
                                <td class="align-middle fw-bold fs-5">{{ $name }}</td>
                                <td>
                                    @if( in_array($type, [ 'cash', 'card', 'transfer']))
                                        <x-select2-input name="payment_accounts[{{ $type }}]" :placeholder="__('lang.select')" :list="$accounts" :selected_id="$accountId"></x-select2-input>
                                    @else
                                       
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type="hidden" name="payment_active[{{ $type }}]" value="0">
                                        <input type="checkbox" name="payment_active[{{ $type }}]" value="1" class="form-check-input" {{ $isActive ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
</div>

@push('scripts')
<script>
    const allStores = @json($all_stores);
    
    function initSelect2Account($select) {
        if ($select.hasClass('select2-hidden-accessible')) return;

        let placeholder = $select.data('placeholder') || 'اختر حساب';
        let selectedId = $select.data('selected') || null;

        function initializeSelect2($el, ph) {
            $el.select2({
                ajax: {
                    url: '{{ route("Lookup.TreeAccounts") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            lang: '{{ app()->getLocale() }}',
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination?.more || false
                            }
                        };
                    },
                    cache: true
                },
                placeholder: ph,
                allowClear: true,
                minimumInputLength: 0,
                dir: 'rtl',
                width: '100%',
                language: {
                    searching: function() { return 'جاري البحث...'; },
                    noResults: function() { return 'لا توجد نتائج'; },
                    loadingMore: function() { return 'جاري تحميل المزيد...'; }
                }
            });
        }

        if (selectedId) {
            $.ajax({
                url: '{{ route("Lookup.TreeAccounts") }}',
                data: {
                    id: selectedId,
                    lang: '{{ app()->getLocale() }}',
                },
                dataType: 'json',
                beforeSend: function() {
                    $select.prop('disabled', true);
                }
            }).done(function(data) {
                if (data.results && data.results.length) {
                    let acc = data.results[0];
                    let option = new Option(acc.text, acc.id, true, true);
                    $select.append(option);
                }
            }).always(function() {
                $select.prop('disabled', false);
                initializeSelect2($select, placeholder);
            });
        } else {
            initializeSelect2($select, placeholder);
        }
    }

    $(document).ready(function() {
        $('.select2-account').each(function() {
            initSelect2Account($(this));
        });

        $('#branch_id').on('change', function() {
            let branchId = $(this).val();
            let storeSelect = $('#store_id');
            let currentStore = storeSelect.val();
            
            storeSelect.empty();
            storeSelect.append('<option value="">@lang("pos::models/devices.fields.select_store")</option>');
            
            let hasCurrentStore = false;
            allStores.forEach(s => {
                if (s.branch_id == branchId) {
                    storeSelect.append(`<option value="${s.id}">${s.name}</option>`);
                    if (s.id == currentStore) hasCurrentStore = true;
                }
            });
            
            if (hasCurrentStore) {
                storeSelect.val(currentStore);
            }
            storeSelect.trigger('change');
        });
        
        // Trigger on load if branch is selected
        if ($('#branch_id').val()) {
            $('#branch_id').trigger('change');
        }
    });

    function toggleUsersSelect() {
        var isLinked = document.getElementById('is_users_linked').checked;
        document.getElementById('users_select_container').style.display = isLinked ? 'block' : 'none';
    }
</script>
@endpush
