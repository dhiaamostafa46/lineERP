

<div class="form-group col-sm-6 mb-5 d-none">
    {!! Form::label('code', __('accusoft::models/as_asset.fields.code'), ['class' => 'form-label']) !!}
    {!! Form::hidden('code', isset($asset) ? $asset->code : null, ['class' => 'form-control form-control-solid bg-secondary', 'readonly' => 'readonly', 'placeholder' => __('accusoft::models/as_asset.auto_generated')]) !!}
</div>

<div class="col-md-12 mb-3 row">
    @foreach (config('langs') as $locale => $language)
        <div class="form-group col-sm-6 mb-5">
            {!! Form::label($locale . '[name]', $language . ' ' . __('accusoft::models/as_asset.fields.name') . ' *', ['class' => 'form-label required']) !!}
            {!! Form::text($locale . '[name]', old($locale . '.name', isset($asset) && $asset->hasTranslation($locale) ? $asset->translate($locale)->name : ($defaultName ?? null)), [
                'class' => 'form-control form-control-solid',
                'required' => 'required'
            ]) !!}
        </div>
    @endforeach
</div>


<div class="col-12 mb-5 mt-3">
    <h5 class="text-primary border-bottom pb-2">@lang('accusoft::models/as_asset.asset_info')</h5>
</div>

<div class="col-12 mb-5">
    @php
        $isUsed = isset($asset) && method_exists($asset, 'isUsedInAccounting') ? $asset->isUsedInAccounting() : false;
        $hasPostedDepreciations = isset($asset) && method_exists($asset, 'hasPostedDepreciations') ? $asset->hasPostedDepreciations() : false;
    @endphp
    
    @if($hasPostedDepreciations)
        <div class="alert alert-danger p-4 mb-4 d-flex align-items-center">
            <i class="fa-solid fa-lock fs-2 text-danger me-3"></i>
            <div>@lang('accusoft::models/as_asset.cannot_change_asset_after_depreciation')</div>
        </div>
    @elseif($isUsed)
        <div class="alert alert-warning p-4 mb-4 d-flex align-items-center">
            <i class="fa-solid fa-triangle-exclamation fs-2 text-warning me-3"></i>
            <div>{{ __('accusoft::models/as_asset.cannot_change_asset_type_used') }}</div>
        </div>
    @endif

    @if($isUsed)
        <!-- Hidden input to ensure the value is still submitted when radios are disabled -->
        <input type="hidden" name="depreciation_status" value="{{ $asset->depreciation_status }}">
    @endif

    <div class="d-flex gap-5">
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="radio" name="depreciation_status" value="{{ \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE }}" id="type_none" {{ old('depreciation_status', $asset->depreciation_status ?? \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE ? 'checked' : '' }} {{ $isUsed ? 'disabled' : '' }}/>
            <label class="form-check-label fw-bold" for="type_none">{{ \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses()[\Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE] }}</label>
        </div>
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="radio" name="depreciation_status" value="{{ \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY }}" id="type_category" {{ old('depreciation_status', $asset->depreciation_status ?? \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY ? 'checked' : '' }} {{ $isUsed ? 'disabled' : '' }}/>
            <label class="form-check-label fw-bold" for="type_category">{{ \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses()[\Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CATEGORY] }}</label>
        </div>
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="radio" name="depreciation_status" value="{{ \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CUSTOM }}" id="type_custom" {{ old('depreciation_status', $asset->depreciation_status ?? \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_NONE) == \Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CUSTOM ? 'checked' : '' }} {{ $isUsed ? 'disabled' : '' }}/>
            <label class="form-check-label fw-bold" for="type_custom">{{ \Modules\AccuSoft\App\Models\Asset::getDepreciationStatuses()[\Modules\AccuSoft\App\Models\Asset::DEPRECIATION_STATUS_CUSTOM] }}</label>
        </div>
    </div>
</div>

<div class="row w-100 m-0 p-0">
    <div class="form-group col-sm-6 mb-5" id="section_cost_center">
        {!! Form::label('cost_center_id', __('accusoft::models/as_asset.fields.cost_center_id') ?? __('accusoft::models/as_asset.cost_center') . ' *', ['class' => 'form-label required']) !!}
        <x-select2-input name="cost_center_id" :placeholder="__('accusoft::models/as_asset.fields.cost_center_id') ?? 'اختر مركز التكلفة'" :list="$cost_centers ?? []" :selected_id="old('cost_center_id', isset($asset) ? $asset->cost_center_id : null)">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-5" id="section_categories">
        {!! Form::label('asset_category_id', __('accusoft::models/as_asset.fields.categories') ?? __('accusoft::models/as_asset.fields.asset_category_id') . ' *', ['class' => 'form-label required']) !!}
        <x-select2-input name="asset_category_id" :placeholder="__('accusoft::models/as_asset.fields.categories') ?? 'اختر الفئة'" :list="$categories ?? []" :selected_id="old('asset_category_id', isset($asset) ? $asset->asset_category_id : null)">
        </x-select2-input>
    </div>

    <div class="form-group col-sm-6 mb-5" id="section_parent_account">
        {!! Form::label('parent_account_id', __('accusoft::models/as_asset.fields.parent_account_id') ?? __('accusoft::models/as_asset.fields.parent_account') . ' *', ['class' => 'form-label required']) !!}
        <x-select2-input name="parent_account_id" :placeholder="__('accusoft::models/as_asset.fields.parent_account_id') ?? 'اختر الحساب الرئيسي'" :list="$fixedassets ?? []" :selected_id="old('parent_account_id', isset($asset) ? $asset->parent_account_id : null)">
        </x-select2-input>
    </div>
</div>

<div id="section_purchase_info" class="row w-100 m-0 p-0">
    <div class="col-12 mb-5 mt-3">
        <h5 class="text-primary border-bottom pb-2">@lang('accusoft::models/as_asset.purchase_and_depreciation_data')</h5>
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('purchase_date', __('accusoft::models/as_asset.fields.purchase_date') . ' *', ['class' => 'form-label required']) !!}
        {!! Form::date('purchase_date', old('purchase_date', isset($asset) ? ($asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : date('Y-m-d')) : date('Y-m-d')), array_merge(['class' => 'form-control form-control-solid', 'required', 'id' => 'purchase_date'], $hasPostedDepreciations ? ['readonly' => 'readonly'] : [])) !!}
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('purchase_value', __('accusoft::models/as_asset.fields.purchase_value') . ' *', ['class' => 'form-label required']) !!}
        {!! Form::number('purchase_value', null, array_merge(['class' => 'form-control form-control-solid', 'step' => '0.01', 'required', 'id' => 'purchase_value'], $hasPostedDepreciations ? ['readonly' => 'readonly'] : [])) !!}
    </div>


    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('tax_amount', __('accusoft::models/as_asset.fields.tax_amount') . ' *', ['class' => 'form-label required']) !!}
        <select name="tax_amount" class="form-select form-control" data-control="select2" data-placeholder="{{ __('accusoft::models/as_asset.fields.tax_amount') ?? 'اختر الضريبة' }}" {{ $hasPostedDepreciations ? 'disabled' : '' }}>
            <option></option>
            @foreach ($taxes ?? [] as $id => $name)
                <option value="{{ $id }}" {{ old('tax_amount', $asset->tax_amount ?? null) == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        @if($hasPostedDepreciations)
            <input type="hidden" name="tax_amount" value="{{ $asset->tax_amount }}">
        @endif
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('tax_type', __('accusoft::models/as_asset.tax_calculation_type') . ' *', ['class' => 'form-label required']) !!}
        <select name="tax_type" class="form-select form-select-solid" data-control="select2" required {{ $hasPostedDepreciations ? 'disabled' : '' }}>
            <option value="exclusive" {{ old('tax_type', $asset->tax_type ?? 'exclusive') == 'exclusive' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.tax_exclusive') }}</option>
            <option value="inclusive" {{ old('tax_type', $asset->tax_type ?? 'exclusive') == 'inclusive' ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.tax_inclusive') }}</option>
        </select>
        @if($hasPostedDepreciations)
            <input type="hidden" name="tax_type" value="{{ $asset->tax_type }}">
        @endif
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('payment_account_id', __('accusoft::models/as_asset.payment_account') . ' *', ['class' => 'form-label required']) !!}
        <x-select2-input name="payment_account_id" :placeholder="__('accusoft::models/as_asset.payment_account') ?? 'اختر حساب الدفع'" :list="$paymentAccounts ?? []" :selected_id="old('payment_account_id', $asset->payment_account_id ?? null)">
        </x-select2-input>
    </div>


    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('salvage_value', __('accusoft::models/as_asset.fields.salvage_value') . ' *', ['class' => 'form-label required']) !!}
        {!! Form::number('salvage_value', old('salvage_value', isset($asset) ? $asset->salvage_value : 0), array_merge(['class' => 'form-control form-control-solid', 'step' => '0.01', 'required', 'id' => 'salvage_value'], $hasPostedDepreciations ? ['readonly' => 'readonly'] : [])) !!}
    </div>
</div>

<div class="row w-100 m-0 p-0 depreciation_details">
    <div class="col-12 mb-5 mt-3">
        <h5 class="text-primary border-bottom pb-2">@lang('accusoft::models/as_asset.advanced_depreciation_data')</h5>
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('calculation_type', __('accusoft::models/as_asset.calculation_type'), ['class' => 'form-label required']) !!}
        <select name="calculation_type" class="form-select form-select-solid" data-control="select2">
            <option value="automatic" {{ (old('calculation_type', $asset->calculation_type ?? '') == 'automatic') ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.automatic') }}</option>
            <option value="manual" {{ (old('calculation_type', $asset->calculation_type ?? '') == 'manual') ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.manual') }}</option>
        </select>
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('useful_life_type', __('accusoft::models/as_asset.fields.useful_life_type'), ['class' => 'form-label required']) !!}
        <select name="useful_life_type" class="form-select form-select-solid" data-control="select2">
            <option value="monthly" {{ (old('useful_life_type', $asset->useful_life_type ?? '') == 'monthly') ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.monthly') }}</option>
            <option value="yearly" {{ (old('useful_life_type', $asset->useful_life_type ?? '') == 'yearly') ? 'selected' : '' }}>{{ __('accusoft::models/as_asset.yearly') }}</option>
        </select>
    </div>

    <div class="form-group col-sm-4 mb-5">
        {!! Form::label('depreciation_method', __('accusoft::models/as_asset.fields.depreciation_method'), ['class' => 'form-label required']) !!}
        {!! Form::select('depreciation_method', \Modules\AccuSoft\App\Models\Asset::getDepreciationMethods(), old('depreciation_method', isset($asset) ? $asset->depreciation_method : \Modules\AccuSoft\App\Models\Asset::METHOD_STRAIGHT_LINE), array_merge(['class' => 'form-control form-select-solid', 'data-control' => 'select2', 'id' => 'depreciation_method'], $hasPostedDepreciations ? ['disabled' => 'disabled'] : [])) !!}
        @if($hasPostedDepreciations)
            <input type="hidden" name="depreciation_method" value="{{ $asset->depreciation_method }}">
        @endif
    </div>

    <div class="form-group col-sm-4 mb-5 useful-life-section">
        {!! Form::label('useful_life', __('accusoft::models/as_asset.fields.useful_life'), ['class' => 'form-label required']) !!}
        {!! Form::number('useful_life', old('useful_life', isset($asset) ? $asset->useful_life : null), ['class' => 'form-control form-control-solid', 'id' => 'useful_life', 'min' => 1]) !!}
    </div>
</div>



@push('scripts')
<script>
    $(document).ready(function() {
        function toggleDepreciationFields() {
            var type = $('input[name="depreciation_status"]:checked').val();
            
            if(type === 'none') {
                $('#section_categories').hide();
                $('#section_parent_account').hide();
                $('.depreciation_details').hide();
                $('#section_purchase_info').hide();
                $('#section_cost_center').hide();
                // Disable required fields
                $('#section_categories select').prop('required', false);
                $('#section_parent_account select').prop('required', false);
                $('.depreciation_details select, .depreciation_details input').prop('required', false);
                $('#section_purchase_info input').prop('required', false);
                $('#section_purchase_info select').prop('required', false);
                $('#section_cost_center select').prop('required', false);
            } else if(type === 'category') {
                $('#section_categories').show();
                $('#section_parent_account').hide();
                $('.depreciation_details').hide();
                $('#section_purchase_info').show();
                $('#section_cost_center').show();
                // Disable required fields
                $('#section_categories select').prop('required', true);
                $('#section_parent_account select').prop('required', false);
                $('.depreciation_details select, .depreciation_details input').prop('required', false);
                $('#section_purchase_info input#purchase_date, #section_purchase_info input#purchase_value, #section_purchase_info input#salvage_value').prop('required', true);
                $('#section_purchase_info select').prop('required', true);
                $('#section_cost_center select').prop('required', true);
            } else if(type === 'custom') {
                $('#section_categories').hide();
                $('#section_parent_account').show();
                $('.depreciation_details').show();
                $('#section_purchase_info').show();
                $('#section_cost_center').show();
                // Disable required fields
                $('#section_categories select').prop('required', false);
                $('#section_parent_account select').prop('required', true);
                $('.depreciation_details select, .depreciation_details input').prop('required', true);
                $('#section_purchase_info input#purchase_date, #section_purchase_info input#purchase_value, #section_purchase_info input#salvage_value').prop('required', true);
                $('#section_purchase_info select').prop('required', true);
                $('#section_cost_center select').prop('required', true);
            }
            $('#depreciation_method').trigger('change');
        }

        $('input[name="depreciation_status"]').on('change', toggleDepreciationFields);
        toggleDepreciationFields(); // Initial run
        // Initialize Select2 with AJAX for account fields
        $('.account-select').select2({
            ajax: {
                url: '{{ route("Lookup.TreeAccounts") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    let accountType = $(this).data('account-type');
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                        lang: '{{ app()->getLocale() }}',
                        account_type: accountType ? accountType : null
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



        $('#depreciation_method').on('change', function() {
            let method = $(this).val();
            var type = $('input[name="depreciation_status"]:checked').val();
            // Method 0 is "None"
            if (method === '0' || method === 'none' || method === '' || type === 'none' || type === 'category') {
                $('.useful-life-section input').prop('required', false);
                $('.useful-life-section label').removeClass('required');
            } else {
                $('.useful-life-section input').prop('required', true);
                $('.useful-life-section label').addClass('required');
            }
        });

        // Initialize state on load
        setTimeout(function() {
            $('#depreciation_method').trigger('change');
        }, 100);
    });
</script>
@endpush

