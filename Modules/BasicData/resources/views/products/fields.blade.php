<div class="container-fluid p-0">
    <input type="hidden" name="type" id="type_product" value="{{ isset($product) ? $product->type : ($type ?? 1) }}">

    <!-- Basic Information -->
    <div class="card mb-4 border shadow-xs rounded-3">
        <div class="card-header bg-light py-3">
            <h6 class="card-title mb-0 fw-bold text-gray-800">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                {{ __('basicdata::models/db_products.sections.basic_info') }}
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                            {{ $language }} - {{ __('basicdata::models/db_products.fields.name') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="{{ $locale }}[name]" 
                               value="{{ old($locale.'.name', isset($product) ? $product->translate($locale)?->name : '') }}" 
                               class="form-control form-control-solid fs-7" 
                               required 
                               placeholder="{{ __('basicdata::models/db_products.placeholders.name') }} ({{ $language }})" />
                    </div>
                @endforeach

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.barcode') }}
                    </label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text bg-light text-muted border-0"><i class="fa-solid fa-barcode"></i></span>
                        <input type="text" 
                               name="barcode" 
                               value="{{ old('barcode', $product->barcode ?? '') }}" 
                               class="form-control form-control-solid fs-7" 
                               placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.category_id') }} <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" class="form-select form-select-solid fs-7" required>
                        <option value="">-- @lang('basicdata::lang.select') --</option>
                        @foreach($categories as $catId => $catName)
                            <option value="{{ $catId }}" {{ old('category_id', $product->category_id ?? (array_key_first($categories) ?? '')) == $catId ? 'selected' : '' }}>
                                {{ $catName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.prod_price') }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text bg-light text-primary fw-bold border-0">{{ config('app.currency', 'SAR') }}</span>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               name="prod_price" 
                               value="{{ old('prod_price', $product->prod_price ?? '0.00') }}" 
                               class="form-control form-control-solid fs-7" 
                               required />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.cost_price') }}
                    </label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text bg-light text-muted border-0">{{ config('app.currency', 'SAR') }}</span>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               name="cost_price" 
                               value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" 
                               class="form-control form-control-solid fs-7" />
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.vat') }}
                    </label>
                    <select name="tax_id" class="form-select form-select-solid fs-7">
                        <option value="">-- @lang('basicdata::lang.select') --</option>
                        @foreach($vats as $vId => $vName)
                            <option value="{{ $vId }}" {{ old('tax_id', $product->tax_id ?? (array_key_first($vats) ?? '')) == $vId ? 'selected' : '' }}>
                                {{ $vName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-2 d-block">
                        {{ __('basicdata::models/db_products.fields.status') }}
                    </label>
                    <div class="d-flex align-items-center gap-4 mt-1">
                        @foreach ($statuses as $value => $label)
                            <label class="d-flex align-items-center gap-2 cursor-pointer p-2 px-3 rounded-2 border {{ old('status', $product->status ?? 1) == $value ? 'bg-light-primary border-primary text-primary fw-bold' : 'bg-light border-light text-muted' }}">
                                <input class="form-check-input mt-0" type="radio" name="status" value="{{ $value }}" {{ old('status', $product->status ?? 1) == $value ? 'checked' : '' }}>
                                <span class="fs-7">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                        {{ __('basicdata::models/db_products.fields.img') }}
                    </label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="file" name="img" class="form-control form-control-solid fs-7" accept="image/*" />
                        @if (isset($product) && $product->img)
                            <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0">
                                <img src="{{ $product->imgThumbPath }}" alt="Product Image" class="object-fit-cover w-45px h-45px">
                            </div>
                        @endif
                    </div>
                </div>

                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                            {{ $language }} - {{ __('basicdata::models/db_products.fields.details') }}
                        </label>
                        <textarea name="{{ $locale }}[details]" 
                                  class="form-control form-control-solid fs-7" 
                                  rows="2" 
                                  placeholder="{{ __('basicdata::models/db_products.placeholders.details') }} ({{ $language }})">{{ old($locale.'.details', isset($product) ? $product->translate($locale)?->details : '') }}</textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Units Section -->
    <div class="card mb-4 border shadow-xs rounded-3">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0 fw-bold text-gray-800">
                <i class="fa-solid fa-layer-group me-2 text-success"></i>
                {{ __('basicdata::models/db_products.sections.units') }}
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="p-3 border rounded-3 bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="row g-3 flex-grow-1 align-items-center">
                    <div class="col-md-5">
                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.unit_id') }}:</label>
                        <select name="units[0][unit_id]" class="form-select form-select-solid form-select-sm fs-7" required>
                            <option value="">-- @lang('basicdata::lang.select') --</option>
                            @foreach($units as $uId => $uName)
                                <option value="{{ $uId }}" {{ old('units.0.unit_id', isset($product) && $product->units->first() ? $product->units->first()->unit_id : (array_key_first($units) ?? '')) == $uId ? 'selected' : '' }}>
                                    {{ $uName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.conversion_factor') }}:</label>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               name="units[0][conversion_factor]" 
                               value="{{ old('units.0.conversion_factor', isset($product) && $product->units->first() ? $product->units->first()->conversion_factor : '1.00') }}" 
                               class="form-control form-control-solid form-control-sm fs-7" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.is_base') }}:</label>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="units[0][is_base]" value="1" checked id="base_unit_check">
                            <label class="form-check-label fs-8 text-gray-700 fw-semibold" for="base_unit_check">
                                {{ __('basicdata::models/db_products.unit.is_base') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
