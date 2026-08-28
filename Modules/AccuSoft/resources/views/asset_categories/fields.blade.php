<div class="col-md-12 mb-3 row">
    @foreach (config('langs') as $locale => $language)
        <div class="form-group col-sm-6 mb-5">
            {!! Form::label($locale . '[name]', $language . ' ' . __('accusoft::models/as_asset_categories.fields.name') . ' *', ['class' => 'form-label required']) !!}
            {!! Form::text($locale . '[name]', isset($category) && $category->hasTranslation($locale) ? $category->translate($locale)->name : null, [
                'class' => 'form-control form-control-solid',
                'required' => 'required'
            ]) !!}
        </div>
    @endforeach
</div>

<div class="col-md-6 mb-5">
    {!! Form::label('default_depreciation_method', __('accusoft::models/as_asset_categories.fields.default_depreciation_method') . ' *', ['class' => 'form-label required']) !!}
    <select name="default_depreciation_method" class="form-select form-select-solid" data-control="select2" required>
        @foreach($depreciationMethods as $key => $method)
            <option value="{{ $key }}" {{ old('default_depreciation_method', $category->default_depreciation_method ?? 'none') == $key ? 'selected' : '' }}>
                {{ $method }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-5">
    {!! Form::label('default_useful_life', __('accusoft::models/as_asset_categories.fields.default_useful_life'), ['class' => 'form-label']) !!}
    {!! Form::number('default_useful_life', old('default_useful_life', $category->default_useful_life ?? ''), ['class' => 'form-control form-control-solid', 'min' => 1]) !!}
</div>

<div class="col-md-6 mb-5">
    {!! Form::label('calculation_type', __('accusoft::models/as_asset_categories.fields.calculation_type') ?? 'نوع المعالجة', ['class' => 'form-label required']) !!}
    <select name="calculation_type" class="form-select form-select-solid" data-control="select2" required>
        <option value="automatic" {{ old('calculation_type', $category->calculation_type ?? 'automatic') == 'automatic' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.automatic') ?? 'تلقائي' }}</option>
        <option value="manual" {{ old('calculation_type', $category->calculation_type ?? 'automatic') == 'manual' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.manual') ?? 'يدوي' }}</option>
    </select>
</div>

<div class="col-md-6 mb-5">
    {!! Form::label('useful_life_type', __('accusoft::models/as_asset_categories.fields.useful_life_type') ?? 'فترات الإهلاك', ['class' => 'form-label required']) !!}
    <select name="useful_life_type" class="form-select form-select-solid" data-control="select2" required>
        <option value="monthly" {{ old('useful_life_type', $category->useful_life_type ?? 'yearly') == 'monthly' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.monthly') ?? 'شهري' }}</option>
        <option value="yearly" {{ old('useful_life_type', $category->useful_life_type ?? 'yearly') == 'yearly' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.yearly') ?? 'سنوي' }}</option>
    </select>
</div>

<div class="col-md-6 mb-5">
    {!! Form::label('status', __('lang.status') . ' *', ['class' => 'form-label required']) !!}
    <select name="status" class="form-select form-select-solid" data-control="select2" required>
        <option value="1" {{ old('status', $category->status ?? 1) == 1 ? 'selected' : '' }}>{{ __('lang.active') }}</option>
        <option value="0" {{ old('status', $category->status ?? 1) == 0 ? 'selected' : '' }}>{{ __('lang.inactive') }}</option>
    </select>
</div>

<div class="col-md-12 mb-5">
    <div class="form-check form-switch form-check-custom form-check-solid mt-4">
        <input class="form-check-input" type="checkbox" name="has_accounting_effect" id="has_accounting_effect" value="1" {{ old('has_accounting_effect', $category->has_accounting_effect ?? true) ? 'checked' : '' }} />
        <label class="form-check-label fw-bold text-gray-700" for="has_accounting_effect">
            {{ __('accusoft::models/as_asset_categories.fields.has_accounting_effect') }}
        </label>
    </div>
</div>

<div class="accounts-section row" style="{{ old('has_accounting_effect', $category->has_accounting_effect ?? true) ? '' : 'display: none;' }}">
    <div class="col-md-4 mb-5">
    {!! Form::label('asset_account_id', __('accusoft::models/as_asset_categories.fields.asset_account_id') . ' *', ['class' => 'form-label required']) !!}
    <select name="asset_account_id" class="form-select form-select-solid account-select" data-control="select2" required>
        @if(old('asset_account_id', $category->asset_account_id ?? ''))
            <option value="{{ old('asset_account_id', $category->asset_account_id ?? '') }}" selected>
                {{ $accounts[old('asset_account_id', $category->asset_account_id ?? '')] ?? '' }}
            </option>
        @endif
    </select>
</div>

    <div class="col-md-4 mb-5">
        {!! Form::label('accumulated_depreciation_account_id', __('accusoft::models/as_asset_categories.fields.accumulated_depreciation_account_id'), ['class' => 'form-label']) !!}
        <select name="accumulated_depreciation_account_id" class="form-select form-select-solid account-select" data-control="select2">
            @if(old('accumulated_depreciation_account_id', $category->accumulated_depreciation_account_id ?? ''))
                <option value="{{ old('accumulated_depreciation_account_id', $category->accumulated_depreciation_account_id ?? '') }}" selected>
                    {{ $accounts[old('accumulated_depreciation_account_id', $category->accumulated_depreciation_account_id ?? '')] ?? '' }}
                </option>
            @endif
        </select>
    </div>

    <div class="col-md-4 mb-5">
    {!! Form::label('depreciation_expense_account_id', __('accusoft::models/as_asset_categories.fields.depreciation_expense_account_id'), ['class' => 'form-label']) !!}
        <select name="depreciation_expense_account_id" class="form-select form-select-solid account-select" data-control="select2">
            @if(old('depreciation_expense_account_id', $category->depreciation_expense_account_id ?? ''))
                <option value="{{ old('depreciation_expense_account_id', $category->depreciation_expense_account_id ?? '') }}" selected>
                    {{ $accounts[old('depreciation_expense_account_id', $category->depreciation_expense_account_id ?? '')] ?? '' }}
                </option>
            @endif
        </select>
    </div>
</div>



@push('scripts')
<script>
    $(document).ready(function() {
        $('#has_accounting_effect').on('change', function() {
            if ($(this).is(':checked')) {
                $('.accounts-section').slideDown();
                // Add required to asset_account_id
                $('select[name="asset_account_id"]').prop('required', true);
            } else {
                $('.accounts-section').slideUp();
                // Remove required from asset_account_id
                $('select[name="asset_account_id"]').prop('required', false);
            }
        });
        
        // Trigger on load
        if (!$('#has_accounting_effect').is(':checked')) {
            $('select[name="asset_account_id"]').prop('required', false);
        }

        // Initialize Select2 with AJAX for account fields
        $('.account-select').select2({
            ajax: {
                url: '{{ route("Lookup.TreeAccounts") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                        lang: '{{ app()->getLocale() }}',
                        all_accounts: '1'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination?.more || false
                        }
                    };
                },
                cache: true
            },
            allowClear: true,
            minimumInputLength: 0,
            dir: 'rtl',
            width: '100%',
            placeholder: '{{ __("lang.please_select") }}',
            language: {
                searching: function() { return '{{ __("lang.searching") ?? "جاري البحث..." }}'; },
                noResults: function() { return '{{ __("lang.no_results") ?? "لا توجد نتائج" }}'; }
            }
        });
    });
</script>
@endpush
